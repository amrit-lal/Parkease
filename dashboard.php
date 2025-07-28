<?php
include('includes/db_connect.php');
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['user_name'];
$profile_img = $_SESSION['user_img'] ?? 'default.png';

$total = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE user_id = $user_id")->fetch_assoc()['total'];
$pending = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE user_id = $user_id AND status='Pending'")->fetch_assoc()['total'];
$booked = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE user_id = $user_id AND status='Booked'")->fetch_assoc()['total'];

$bookings = $conn->query("
  SELECT b.*, s.location, s.slot_date, s.start_time, s.end_time, s.category, 
         p.price as slot_price, b.created_at as request_time
  FROM bookings b
  JOIN slots s ON b.slot_id = s.id
  LEFT JOIN pricing p ON s.category = p.category
  WHERE b.user_id = $user_id
  ORDER BY b.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard | Parkease</title>
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
      display: flex;
      flex-direction: column;
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

    /* Dashboard Container */
    .dashboard-container {
      padding: 2rem 0;
      max-width: 1400px;
      margin: 0 auto;
      flex: 1;
    }

    /* Profile Section */
    .profile-box {
      background: white;
      padding: 2rem;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
      text-align: center;
      margin-bottom: 2rem;
      transition: all 0.3s ease;
      border: 1px solid rgba(0, 0, 0, 0.05);
      position: relative;
      overflow: hidden;
    }

    .profile-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
    }

    .profile-box::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 8px;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
    }

    .profile-box img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid white;
      margin-bottom: 1rem;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }

    .profile-box:hover img {
      transform: scale(1.05);
    }

    .profile-box h4 {
      color: var(--primary-dark);
      font-weight: 600;
      margin-bottom: 0.5rem;
      font-size: 1.5rem;
    }

    .profile-box p {
      color: var(--dark-gray);
    }

    /* Stats Cards */
    .card-stat {
      background: white;
      padding: 1.5rem;
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
      text-align: center;
      margin-bottom: 1.5rem;
      transition: all 0.3s ease;
      border: none;
      position: relative;
      overflow: hidden;
    }

    .card-stat:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    .card-stat::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 6px;
      height: 100%;
      background: var(--primary);
    }

    .card-stat.total::before {
      background: linear-gradient(to bottom, var(--primary), var(--primary-light));
    }

    .card-stat.pending::before {
      background: linear-gradient(to bottom, var(--warning), #ffb347);
    }

    .card-stat.booked::before {
      background: linear-gradient(to bottom, var(--success), #4facfe);
    }

    .card-stat small {
      color: var(--dark-gray);
      font-size: 0.9rem;
      display: block;
      margin-bottom: 0.5rem;
    }

    .card-stat h4 {
      font-weight: 700;
      margin-top: 0.5rem;
      font-size: 2rem;
      color: var(--dark);
    }

    /* Action Buttons */
    .action-buttons .btn {
      margin: 0.5rem;
      min-width: 200px;
      font-weight: 500;
      border-radius: 50px;
      padding: 0.75rem 1.5rem;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      border: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .action-buttons .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .action-buttons .btn-primary {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .action-buttons .btn-outline-primary {
      border: 2px solid var(--primary);
      color: var(--primary);
      background: transparent;
    }

    .action-buttons .btn-outline-primary:hover {
      background: var(--primary);
      color: white;
    }

    .action-buttons .btn-outline-danger {
      border: 2px solid var(--danger);
      color: var(--danger);
      background: transparent;
    }

    .action-buttons .btn-outline-danger:hover {
      background: var(--danger);
      color: white;
    }

    /* Booking Cards */
    .booking-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      transition: all 0.3s ease;
      border-left: 4px solid var(--primary);
      position: relative;
      overflow: hidden;
    }

    .booking-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    .booking-card h6 {
      font-weight: 600;
      color: var(--primary-dark);
      margin-bottom: 1rem;
      font-size: 1.2rem;
      display: flex;
      align-items: center;
    }

    .booking-card h6 i {
      margin-right: 10px;
      color: var(--primary);
    }

    .badge {
      padding: 0.5em 1em;
      font-size: 0.85em;
      border-radius: 50px;
      font-weight: 500;
      letter-spacing: 0.5px;
    }

    .bg-warning {
      background: linear-gradient(135deg, var(--warning), #ffb347) !important;
    }

    .bg-success {
      background: linear-gradient(135deg, var(--success), #4facfe) !important;
    }

    .bg-danger {
      background: linear-gradient(135deg, var(--danger), #ff6b6b) !important;
    }

    .bg-secondary {
      background: linear-gradient(135deg, var(--dark-gray), #adb5bd) !important;
    }

    .booking-card p strong {
      color: var(--dark-gray);
      font-weight: 500;
    }

    .booking-card p {
      margin-bottom: 0.75rem;
      color: var(--dark);
    }

    /* Right Panel */
    .right-panel {
      position: sticky;
      top: 20px;
    }

    .right-panel img {
      width: 100%;
      border-radius: 16px;
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
      transition: all 0.5s ease;
    }

    .right-panel img:hover {
      transform: scale(1.02);
    }

    /* Section Headers */
    .section-header {
      position: relative;
      margin-bottom: 1.5rem;
      padding-bottom: 0.5rem;
      color: var(--primary-dark);
    }

    .section-header::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 60px;
      height: 4px;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      border-radius: 2px;
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

    /* Animations */
    .animate-delay-1 {
      animation-delay: 0.2s;
    }

    .animate-delay-2 {
      animation-delay: 0.4s;
    }

    .animate-delay-3 {
      animation-delay: 0.6s;
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
      .right-panel {
        margin-top: 3rem;
      }
      
      .dashboard-container {
        padding: 1.5rem;
      }
    }

    @media (max-width: 768px) {
      .profile-box img {
        width: 100px;
        height: 100px;
      }
      
      .action-buttons .btn {
        width: 100%;
        margin: 0.5rem 0;
      }
      
      .card-stat h4 {
        font-size: 1.5rem;
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
          <span>Parkease</span>
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

  <!-- Main Content -->
  <div class="dashboard-container container">
    <div class="row">
      <!-- LEFT PANEL -->
      <div class="col-lg-8">
        <div class="profile-box animate__animated animate__fadeIn">
          <img src="uploads/<?= $profile_img ?>" alt="User Profile" class="animate__animated animate__zoomIn">
          <h4 class="animate__animated animate__fadeInUp">Welcome, <?= $name ?></h4>
          <p class="text-muted animate__animated animate__fadeInUp animate-delay-1">Your Parking Dashboard</p>
        </div>

        <div class="row">
          <div class="col-md-4 animate__animated animate__fadeInUp animate-delay-1">
            <div class="card-stat total">
              <small>Total Bookings</small>
              <h4><?= $total ?></h4>
            </div>
          </div>
          <div class="col-md-4 animate__animated animate__fadeInUp animate-delay-2">
            <div class="card-stat pending">
              <small>Pending</small>
              <h4 class="text-warning"><?= $pending ?></h4>
            </div>
          </div>
          <div class="col-md-4 animate__animated animate__fadeInUp animate-delay-3">
            <div class="card-stat booked">
              <small>Booked</small>
              <h4 class="text-success"><?= $booked ?></h4>
            </div>
          </div>
        </div>

        <div class="action-buttons text-center mt-4">
          <a href="book_slot.php" class="btn btn-primary btn-lg animate__animated animate__fadeInLeft animate-delay-1">
            <i class="fas fa-parking me-2"></i>Book Slot
          </a>
          <a href="booking_history.php" class="btn btn-outline-primary btn-lg animate__animated animate__fadeInLeft animate-delay-2">
            <i class="fas fa-history me-2"></i>History
          </a>
          <a href="post_testimonial.php" class="btn btn-outline-primary btn-lg animate__animated animate__fadeInLeft animate-delay-3">
            <i class="fas fa-comment me-2"></i>Your Testimonial
          </a>
          <a href="logout.php" class="btn btn-outline-danger btn-lg animate__animated animate__fadeInLeft animate-delay-1">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
          </a>
        </div>

        <div class="mt-5 animate__animated animate__fadeIn">
          <h5 class="section-header"><i class="fas fa-clipboard-list me-2"></i>Your Booking Status</h5>
          <?php if ($bookings->num_rows == 0): ?>
            <div class="alert alert-info animate__animated animate__fadeIn">
              No bookings yet. <a href="book_slot.php" class="alert-link">Book your first slot now!</a>
            </div>
          <?php endif; ?>

          <?php while ($row = $bookings->fetch_assoc()): ?>
            <div class="booking-card animate__animated animate__fadeInUp">
              <h6><i class="fas fa-map-marker-alt me-2"></i><?= $row['location'] ?> (<?= $row['category'] ?>)</h6>
              <p><strong>Request Time:</strong> <?= date('Y-m-d H:i:s', strtotime($row['request_time'])) ?></p>
              <p><strong>Date:</strong> <?= $row['slot_date'] ?> | <strong>Time:</strong> <?= $row['start_time'] ?> - <?= $row['end_time'] ?></p>
              <?php if ($row['slot_price']): ?>
                <p><strong>Price:</strong> ₹<?= number_format($row['slot_price'], 2) ?></p>
              <?php endif; ?>
              <p>
                <strong>Status:</strong> 
                <?php
                  $status = $row['status'];
                  $badge = 'secondary';
                  if ($status == 'Pending') $badge = 'warning';
                  elseif ($status == 'Booked') $badge = 'success';
                  elseif ($status == 'Rejected') $badge = 'danger';
                ?>
                <span class="badge bg-<?= $badge ?>"><?= $status ?></span>
              </p>
              <?php if ($status == 'Booked' && $row['booking_code']): ?>
                <p><strong><i class="fas fa-ticket-alt me-2"></i>Booking ID:</strong> <?= $row['booking_code'] ?></p>
              <?php endif; ?>
            </div>
          <?php endwhile; ?>
        </div>
      </div>

      <!-- RIGHT PANEL -->
      <div class="col-lg-4">
        <div class="right-panel animate__animated animate__fadeInRight">
          <img src="images/about-img.jpg" alt="Parking Illustration" class="img-fluid">
          <div class="card-stat mt-3 animate__animated animate__fadeInRight animate-delay-1">
            <h6><i class="fas fa-info-circle me-2"></i>Quick Tips</h6>
            <ul class="list-unstyled">
              <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Book in advance for best slots</li>
              <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Check your booking status regularly</li>
              <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Arrive on time for your reservation</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="main-footer">
    <div class="container">
      <div class="row">
        <div class="col-md-6 text-center text-md-start">
          <h5 class="mb-3"><i class="fas fa-parking me-2"></i> Parkease</h5>
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
        &copy; <?= date('Y') ?> Parkease. All rights reserved.
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