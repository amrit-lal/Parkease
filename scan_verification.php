<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

  <title>Parkease</title>

  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
  <!-- nice selecy -->
  <link rel="stylesheet" href="css/nice-select.min.css">
  <!--owl slider stylesheet -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <!-- font awesome style -->
  <link href="css/font-awesome.min.css" rel="stylesheet" />
  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="sub_page">

  <div class="hero_area">
    <div class="bg-box">
      <img src="images/slider-bg.jpg" alt="">
    </div>
    <!-- header section strats -->
    <header class="header_section">
      <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container ">
          <a class="navbar-brand" href="index.html">
            <span>
              Parkease
            </span>
          </a>

          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class=""> </span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link" href="index.PHP">Home </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="about.PHP"> About</a>
              </li>
              <li class="nav-item ">
                <a class="nav-link" href="pricing.PHP">Pricing </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="why.PHP">Why Us</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="testimonial.PHP">Testimonial</a>
              </li>
              <li class="nav-item ">
                <a class="nav-link" href="register.PHP">REGISTER <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item">
              </li>
              
              <li class="nav-item">
                <a class="nav-link" href="admin_login.PHP">ADMIN</a>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="guard_login.PHP">GUARD</a>
              </li>
            </ul>
            <form class="form-inline">
              <button class="btn  my-2 my-sm-0 nav_search-btn" type="submit">
                <i class="fa fa-search" aria-hidden="true"></i>
              </button>
            </form>
          </div>
        </nav>
      </div>
    </header>
    <!-- end header section -->
  </div>

<?php
session_start();
if (!isset($_SESSION['guard_logged_in'])) {
    header("Location: guard_login.php");
    exit();
}

include('includes/db_connect.php');

// Safely get guard information from session
$guard_id = $_SESSION['guard_id'] ?? null;
$guard_name = $_SESSION['guard_name'] ?? 'Unknown Guard';
$guard_unique_id = $_SESSION['guard_unique_id'] ?? 'N/A';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_code'])) {
    $booking_code = trim($_POST['booking_code']);
    $verification_time = date('Y-m-d H:i:s');
    
    // Get booking details with slot status
    $booking = $conn->query("SELECT b.*, s.status as slot_status 
                            FROM bookings b
                            JOIN slots s ON b.slot_id = s.id
                            WHERE b.booking_code='".$conn->real_escape_string($booking_code)."' 
                            AND b.status IN ('Booked', 'Occupied')")->fetch_assoc();
    
    if ($booking) {
        try {
            $conn->begin_transaction();
            
            if ($booking['status'] === 'Booked') {
                // Check-in process
                $conn->query("UPDATE bookings SET status='Occupied' WHERE id=".(int)$booking['id']);
                $conn->query("UPDATE slots SET status='Occupied' WHERE id=".(int)$booking['slot_id']);
                $action = "check-in";
                $action_icon = "🔑";
                $action_text = "Checked In";
            } else {
                // Check-out process
                $conn->query("UPDATE bookings SET status='Completed', checkout_time='$verification_time' WHERE id=".(int)$booking['id']);
                $conn->query("UPDATE slots SET status='Available' WHERE id=".(int)$booking['slot_id']);
                $action = "check-out";
                $action_icon = "🚗";
                $action_text = "Checked Out";
            }
            
            // Record verification
            $stmt = $conn->prepare("INSERT INTO verifications (booking_id, guard_id, verification_time, action) 
                                   VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $booking['id'], $guard_id, $verification_time, $action);
            $stmt->execute();
            
            $conn->commit();
            
            $msg = "<div class='alert alert-success'>
                      {$action_icon} Booking {$action_text} successfully!<br>
                      <strong>Booking Code:</strong> ".htmlspecialchars($booking['booking_code'])."<br>
                      <strong>User ID:</strong> ".(int)$booking['user_id']."<br>
                      <strong>Slot ID:</strong> ".(int)$booking['slot_id']."<br>
                      <strong>Time:</strong> ".htmlspecialchars($booking['start_time'])." - ".htmlspecialchars($booking['end_time'])."<br>
                      <strong>{$action_text} by:</strong> ".htmlspecialchars($guard_name)." (".htmlspecialchars($guard_unique_id).") at ".htmlspecialchars($verification_time)."
                    </div>";
        } catch (Exception $e) {
            $conn->rollback();
            $msg = "<div class='alert alert-danger'>❌ Error processing verification: ".htmlspecialchars($e->getMessage())."</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>❌ Invalid booking code or already completed.</div>";
    }
}
?>

<div class="container my-5" style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">📱 Scan Verification</h2>
        <div>
            <span class="badge bg-info">Guard: <?= htmlspecialchars($guard_name) ?> (<?= htmlspecialchars($guard_unique_id) ?>)</span>
            <a href="guard_logout.php" class="btn btn-outline-danger ms-2">Logout</a>
        </div>
    </div>
    
    <?= $msg ?>
    
    <div class="card shadow-sm p-4 mb-4">
        <form method="POST">
            <div class="mb-3">
                <label for="booking_code" class="form-label">Scan Booking Code:</label>
                <input type="text" name="booking_code" id="booking_code" class="form-control form-control-lg" 
                       placeholder="PASXXXXXX" required autofocus>
                <small class="text-muted">Scan for both check-in and check-out</small>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100">
                <i class="fa fa-qrcode me-2"></i> Process Booking
            </button>
        </form>
    </div>
</div>

<?php include("footer.php"); ?>