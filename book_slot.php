<?php
include('includes/db_connect.php');
session_start();

// Set timezone to match your location
date_default_timezone_set('Asia/Kolkata'); // Change to your timezone

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";
$current_datetime = new DateTime(); // Current date and time object
$today = $current_datetime->format('Y-m-d');
$current_time = $current_datetime->format('H:i:s');

// Book the slot
if (isset($_POST['slot_id'])) {
    $slot_id = $_POST['slot_id'];

    // Check if slot is available
    $slot_check = $conn->query("SELECT * FROM slots WHERE id = $slot_id AND status = 'Available'");
    
    if ($slot_check->num_rows == 0) {
        $msg = "<div class='alert alert-warning text-center'>⚠️ This slot is no longer available.</div>";
    } else {
        // Check for existing bookings
        $booking_check = $conn->query("SELECT * FROM bookings WHERE slot_id = $slot_id AND status IN ('Pending', 'Booked', 'Occupied')");
        
        if ($booking_check->num_rows > 0) {
            $msg = "<div class='alert alert-warning text-center'>⚠️ This slot has already been booked.</div>";
        } else {
            $slot = $slot_check->fetch_assoc();
            $stmt = $conn->prepare("INSERT INTO bookings (user_id, slot_id, slot_date, start_time, end_time, category, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
            $stmt->bind_param("iissss", $user_id, $slot_id, $slot['slot_date'], $slot['start_time'], $slot['end_time'], $slot['category']);
            
            if ($stmt->execute()) {
                // Update slot status to Pending
                $conn->query("UPDATE slots SET status='Pending' WHERE id=$slot_id");
                $msg = "<div class='alert alert-success text-center'>✅ Booking request sent. Awaiting admin approval.</div>";
            } else {
                $msg = "<div class='alert alert-danger text-center'>❌ Error creating booking. Please try again.</div>";
            }
        }
    }
}

// Load filtered slots
$available_slots = [];
if (!empty($_POST['filter_date']) && !empty($_POST['filter_category'])) {
    $slot_date = $_POST['filter_date'];
    $category = $_POST['filter_category'];

    // Get all potentially available slots
    $query = $conn->prepare("
        SELECT s.* FROM slots s
        WHERE s.slot_date = ? 
        AND s.category = ? 
        AND (
            (s.status = 'Available') 
            OR 
            (s.status = 'Booked' AND NOT EXISTS (
                SELECT 1 FROM bookings b 
                WHERE b.slot_id = s.id 
                AND b.status IN ('Pending', 'Booked', 'Occupied')
            ))
        )
        ORDER BY s.start_time ASC
    ");
    $query->bind_param("ss", $slot_date, $category);
    $query->execute();
    $result = $query->get_result();
    
    // Filter results based on current time if viewing today
    while ($slot = $result->fetch_assoc()) {
        if ($slot_date == $today) {
            // Create DateTime objects for comparison
            $slot_start = DateTime::createFromFormat('H:i:s', $slot['start_time']);
            $current_time_obj = DateTime::createFromFormat('H:i:s', $current_time);
            
            // Only include if slot start time is after current time
            if ($slot_start > $current_time_obj) {
                $available_slots[] = $slot;
            }
        } else {
            // For future dates, include all
            $available_slots[] = $slot;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Slot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --primary-dark: #3a0ca3;
            --secondary: #7209b7;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #e9ecef;
            --dark-gray: #6c757d;
            --body-bg: #f5f7fb;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: var(--body-bg);
            min-height: 100vh;
        }

        /* Header Styles */
        .main-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 1rem 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 100;
        }

        .brand-logo {
            font-weight: 700;
            font-size: 1.5rem;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .brand-logo i {
            margin-right: 10px;
            font-size: 1.8rem;
        }

        /* Main Container */
        .booking-container {
            padding: 2rem 0;
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary-dark);
            font-weight: 700;
            position: relative;
            padding-bottom: 1rem;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 2px;
        }

        /* Filter Card */
        .filter-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .filter-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .filter-card label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
            color: var(--dark-gray);
        }

        /* Slot Cards */
        .slot-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .slot-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: var(--primary);
        }

        .slot-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
        }

        .slot-card h5 {
            color: var(--primary-dark);
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .slot-card h5 i {
            margin-right: 10px;
            color: var(--primary);
        }

        .slot-card p {
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .slot-card p strong {
            color: var(--dark-gray);
            font-weight: 500;
        }

        .slot-card .btn {
            width: 100%;
            margin-top: 1rem;
            border-radius: 50px;
            padding: 0.75rem;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .slot-card .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success), #4facfe);
        }

        /* No Slots */
        .no-slots {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        }

        .no-slots i {
            font-size: 3rem;
            color: var(--danger);
            margin-bottom: 1rem;
        }

        /* Alert Styles */
        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Footer Styles */
        .main-footer {
            background: linear-gradient(135deg, var(--primary-dark), var(--dark));
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            margin: 0 1rem;
        }

        .footer-links a:hover {
            color: white;
            text-decoration: underline;
        }

        .social-icons a {
            color: white;
            font-size: 1.5rem;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            transform: translateY(-3px);
            color: var(--primary-light);
        }

        @media (max-width: 768px) {
            .booking-container {
                padding: 1.5rem;
            }
            
            .filter-card {
                padding: 1.5rem;
            }
            
            .slot-card {
                padding: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="dashboard.php" class="brand-logo animate__animated animate__fadeInLeft">
                    <i class="fas fa-parking"></i>
                    <span>parkease</span>
                </a>
                <div class="d-flex align-items-center">
                    <a href="profile.php" class="text-white me-3 animate__animated animate__fadeIn">
                        <i class="fas fa-user-circle me-1"></i> Profile
                    </a>
                    <a href="logout.php" class="text-white animate__animated animate__fadeIn">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="booking-container container">
        <h2 class="page-title animate__animated animate__fadeIn"><i class="fas fa-parking me-2"></i>Book a Parking Slot</h2>

        <?php if ($msg) echo $msg; ?>

        <!-- Filter Form -->
        <div class="filter-card animate__animated animate__fadeIn">
            <form method="POST">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="filter_date">Select Date</label>
                        <input type="date" name="filter_date" id="filter_date" class="form-control" required 
                            min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['filter_date'] ?? '') ?>">
                    </div>
                    <div class="col-md-5">
                        <label for="filter_category">Select Category</label>
                        <select name="filter_category" id="filter_category" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="2W" <?= (($_POST['filter_category'] ?? '') == '2W' )? 'selected' : '' ?>>2-Wheeler</option>
                            <option value="4W" <?= (($_POST['filter_category'] ?? '') == '4W' )? 'selected' : '' ?>>4-Wheeler</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark));">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Slot Results -->
        <?php if (!empty($_POST['filter_date']) && !empty($_POST['filter_category'])): ?>
            <h5 class="mb-3 animate__animated animate__fadeIn">Available Slots:</h5>
            <?php if (!empty($available_slots)): ?>
                <div class="row g-4">
                    <?php foreach ($available_slots as $slot): ?>
                        <div class="col-md-4 animate__animated animate__fadeInUp">
                            <div class="slot-card">
                                <h5><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($slot['location']) ?></h5>
                                <p><strong>Date:</strong> <?= date('M j, Y', strtotime($slot['slot_date'])) ?></p>
                                <p><strong>Time:</strong> <?= date('g:i A', strtotime($slot['start_time'])) ?> - <?= date('g:i A', strtotime($slot['end_time'])) ?></p>
                                <p><strong>Category:</strong> <?= htmlspecialchars($slot['category']) ?></p>
                                <form method="POST">
                                    <input type="hidden" name="slot_id" value="<?= $slot['id'] ?>">
                                    <button class="btn btn-success"><i class="fas fa-check-circle me-2"></i>Book Now</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-slots alert alert-danger animate__animated animate__fadeIn">
                    <i class="fas fa-times-circle"></i>
                    <h5>No slots available</h5>
                    <p class="text-muted">No parking slots available for this date and category.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="text-center mt-4 animate__animated animate__fadeIn">
            <a href="dashboard.php" class="btn btn-outline-secondary" style="border-radius: 50px;">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <h5 class="mb-3"><i class="fas fa-parking me-2"></i> FLEXIpark</h5>
                    <p class="small">Smart parking solutions for modern cities.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-links mb-3">
                        <a href="about.php">About Us</a>
                        <a href="contact.php">Contact</a>
                        <a href="privacy.php">Privacy Policy</a>
                        <a href="terms.php">Terms of Service</a>
                    </div>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-3 bg-light opacity-25">
            <div class="text-center small">
                &copy; <?= date('Y') ?> FLEXIpark. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add animation on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const animateElements = document.querySelectorAll('.animate__animated');
            
            const animateOnScroll = function() {
                animateElements.forEach(element => {
                    const elementPosition = element.getBoundingClientRect().top;
                    const windowHeight = window.innerHeight;
                    
                    if (elementPosition < windowHeight - 100) {
                        const animationClass = element.classList.item(1);
                        element.classList.add(animationClass);
                    }
                });
            };
            
            // Initial check
            animateOnScroll();
            
            // Check on scroll
            window.addEventListener('scroll', animateOnScroll);
        });
    </script>
</body>
</html>