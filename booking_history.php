<?php
include('includes/db_connect.php');
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

$bookings = $conn->query("
  SELECT b.*, s.location 
  FROM bookings b
  JOIN slots s ON b.slot_id = s.id
  WHERE b.user_id = $user_id
  ORDER BY b.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking History | Parkease</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
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
    .history-container {
      padding: 2rem 0;
      max-width: 1200px;
      margin: 0 auto;
    }

    .history-header {
      text-align: center;
      margin-bottom: 2.5rem;
      position: relative;
      padding-bottom: 1rem;
    }

    .history-header::after {
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

    .history-header h2 {
      color: var(--primary-dark);
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .history-header p {
      color: var(--dark-gray);
      font-size: 1.1rem;
    }

    /* Table Wrapper - Desktop */
    .table-wrapper {
      background: white;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
      padding: 1.5rem;
      overflow-x: auto;
      margin-bottom: 2rem;
      border: 1px solid rgba(0, 0, 0, 0.05);
      position: relative;
      overflow: hidden;
      display: block;
    }

    .table-wrapper::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 8px;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th {
      background-color: var(--primary);
      color: white;
      font-weight: 600;
      padding: 1rem;
      text-align: left;
    }

    td {
      padding: 1rem;
      border-bottom: 1px solid var(--gray);
      vertical-align: middle;
    }

    tr:hover {
      background-color: rgba(67, 97, 238, 0.05);
    }

    /* Mobile Card Slider */
    .mobile-slider {
      display: none;
      background: white;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
      margin-bottom: 2rem;
      position: relative;
      overflow: hidden;
    }

    .mobile-slider::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 8px;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
    }

    .booking-card {
      background: white;
      padding: 1.5rem;
      border-radius: 12px;
      height: auto;
    }

    .booking-card .card-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 0.75rem;
      padding-bottom: 0.75rem;
      border-bottom: 1px solid var(--gray);
    }

    .booking-card .card-row:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }

    .booking-card .card-label {
      font-weight: 600;
      color: var(--dark-gray);
      flex: 1;
    }

    .booking-card .card-value {
      flex: 2;
      text-align: right;
    }

    /* Badge Styles */
    .badge {
      padding: 0.5em 1em;
      font-size: 0.85em;
      border-radius: 50px;
      font-weight: 500;
      letter-spacing: 0.5px;
      border: none;
    }

    .badge.Pending {
      background: linear-gradient(135deg, var(--warning), #ffb347);
      color: white;
    }

    .badge.Booked {
      background: linear-gradient(135deg, var(--success), #4facfe);
      color: white;
    }

    .badge.Occupied {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white;
    }

    .badge.Completed {
      background: linear-gradient(135deg, var(--dark-gray), #adb5bd);
      color: white;
    }

    .badge.Rejected {
      background: linear-gradient(135deg, var(--danger), #ff6b6b);
      color: white;
    }

    .badge.Cancelled {
      background: linear-gradient(135deg, var(--warning), #f9c74f);
      color: #000;
      border: 1px solid rgba(0,0,0,0.1);
    }

    /* Button Styles */
    .btn-outline-secondary {
      border-radius: 50px;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      transition: all 0.3s ease;
      border: 2px solid var(--dark-gray);
      color: var(--dark-gray);
      background: transparent;
    }

    .btn-outline-secondary:hover {
      background: var(--dark-gray);
      color: white;
    }

    /* Text Styles */
    .timestamp {
      font-size: 0.85rem;
      color: var(--dark-gray);
    }

    .booking-code {
      font-family: monospace;
      color: var(--primary-dark);
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .copy-btn {
      background: none;
      border: none;
      color: var(--primary);
      cursor: pointer;
      margin-left: 5px;
      transition: all 0.3s ease;
    }

    .copy-btn:hover {
      color: var(--primary-dark);
      transform: scale(1.1);
    }

    /* Swiper Styles */
    .swiper-pagination-bullet {
      background: var(--dark-gray);
      opacity: 0.5;
      width: 10px;
      height: 10px;
    }

    .swiper-pagination-bullet-active {
      background: var(--primary);
      opacity: 1;
    }

    .swiper-button-next,
    .swiper-button-prev {
      color: var(--primary);
      background: rgba(255, 255, 255, 0.8);
      width: 40px;
      height: 40px;
      border-radius: 50%;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .swiper-button-next::after,
    .swiper-button-prev::after {
      font-size: 1.2rem;
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

    @media (max-width: 992px) {
      .table-wrapper {
        display: none;
      }
      
      .mobile-slider {
        display: block;
        padding: 1rem 0 3rem;
      }
      
      .history-container {
        padding: 1.5rem;
      }
      
      .booking-card {
        padding: 1rem;
      }
    }

    @media (max-width: 768px) {
      .history-header h2 {
        font-size: 1.5rem;
      }
      
      .history-header p {
        font-size: 1rem;
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
          <span>Paspark</span>
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
  <div class="history-container container">
    <div class="history-header animate__animated animate__fadeIn">
      <h2><i class="fas fa-history me-2"></i>Your Booking History</h2>
      <p>Track your previous parking activity below.</p>
    </div>

    <?php if ($bookings->num_rows > 0): ?>
      <!-- Desktop Table -->
      <div class="table-wrapper animate__animated animate__fadeIn">
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Booking Code</th>
              <th>Date</th>
              <th>Time</th>
              <th>Location</th>
              <th>Category</th>
              <th>Status</th>
              <th>Timestamps</th>
            </tr>
          </thead>
          <tbody>
            <?php $count = 1; while ($row = $bookings->fetch_assoc()): ?>
              <tr class="animate__animated animate__fadeInUp">
                <td><?= $count++ ?></td>
                <td>
                  <?php if (!empty($row['booking_code'])): ?>
                    <span class="booking-code"><?= $row['booking_code'] ?></span>
                    <button class="copy-btn" title="Copy to clipboard" onclick="copyToClipboard('<?= $row['booking_code'] ?>')">
                      <i class="far fa-copy"></i>
                    </button>
                  <?php else: ?>
                    <span class="text-muted">N/A</span>
                  <?php endif; ?>
                </td>
                <td><?= date('M j, Y', strtotime($row['slot_date'])) ?></td>
                <td><?= date('h:i A', strtotime($row['start_time'])) ?> - <?= date('h:i A', strtotime($row['end_time'])) ?></td>
                <td><?= $row['location'] ?></td>
                <td><?= $row['category'] ?></td>
                <td><span class="badge <?= $row['status'] ?>"><?= $row['status'] ?></span></td>
                <td class="timestamp">
                  <div><strong>Booked:</strong> <?= date('M j, h:i A', strtotime($row['created_at'])) ?></div>
                  <?php if (in_array($row['status'], ['Completed', 'Rejected', 'Cancelled']) && !empty($row['updated_at'])): ?>
                    <div><strong><?= $row['status'] ?>:</strong> <?= date('M j, h:i A', strtotime($row['updated_at'])) ?></div>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <!-- Mobile Slider -->
      <div class="mobile-slider animate__animated animate__fadeIn">
        <div class="swiper bookingSwiper">
          <div class="swiper-wrapper">
            <?php 
              // Reset pointer and count for mobile slider
              $bookings->data_seek(0);
              $count = 1;
              while ($row = $bookings->fetch_assoc()): 
            ?>
              <div class="swiper-slide">
                <div class="booking-card">
                  <div class="card-row">
                    <span class="card-label">#</span>
                    <span class="card-value"><?= $count++ ?></span>
                  </div>
                  <div class="card-row">
                    <span class="card-label">Booking Code</span>
                    <span class="card-value">
                      <?php if (!empty($row['booking_code'])): ?>
                        <span class="booking-code"><?= $row['booking_code'] ?></span>
                        <button class="copy-btn" title="Copy to clipboard" onclick="copyToClipboard('<?= $row['booking_code'] ?>')">
                          <i class="far fa-copy"></i>
                        </button>
                      <?php else: ?>
                        <span class="text-muted">N/A</span>
                      <?php endif; ?>
                    </span>
                  </div>
                  <div class="card-row">
                    <span class="card-label">Date</span>
                    <span class="card-value"><?= date('M j, Y', strtotime($row['slot_date'])) ?></span>
                  </div>
                  <div class="card-row">
                    <span class="card-label">Time</span>
                    <span class="card-value"><?= date('h:i A', strtotime($row['start_time'])) ?> - <?= date('h:i A', strtotime($row['end_time'])) ?></span>
                  </div>
                  <div class="card-row">
                    <span class="card-label">Location</span>
                    <span class="card-value"><?= $row['location'] ?></span>
                  </div>
                  <div class="card-row">
                    <span class="card-label">Category</span>
                    <span class="card-value"><?= $row['category'] ?></span>
                  </div>
                  <div class="card-row">
                    <span class="card-label">Status</span>
                    <span class="card-value"><span class="badge <?= $row['status'] ?>"><?= $row['status'] ?></span></span>
                  </div>
                  <div class="card-row">
                    <span class="card-label">Booked At</span>
                    <span class="card-value timestamp"><?= date('M j, h:i A', strtotime($row['created_at'])) ?></span>
                  </div>
                  <?php if (in_array($row['status'], ['Completed', 'Rejected', 'Cancelled']) && !empty($row['updated_at'])): ?>
                    <div class="card-row">
                      <span class="card-label"><?= $row['status'] ?> At</span>
                      <span class="card-value timestamp"><?= date('M j, h:i A', strtotime($row['updated_at'])) ?></span>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
          <div class="swiper-pagination"></div>
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-info text-center animate__animated animate__fadeIn">
        <i class="fas fa-info-circle me-2"></i> You don't have any booking history yet.
      </div>
    <?php endif; ?>

    <div class="text-center mt-4 animate__animated animate__fadeIn">
      <a href="dashboard.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
      </a>
    </div>
  </div>

  <!-- Footer -->
  <footer class="main-footer">
    <div class="container">
      <div class="row">
        <div class="col-md-6 text-center text-md-start">
          <h5 class="mb-3"><i class="fas fa-parking me-2"></i> Paspark</h5>
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
        &copy; <?= date('Y') ?> Paspark. All rights reserved.
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    function copyToClipboard(text) {
      navigator.clipboard.writeText(text).then(function() {
        // Show a small tooltip or change icon briefly to indicate success
        const buttons = document.querySelectorAll('.copy-btn');
        buttons.forEach(btn => {
          if (btn.innerHTML.includes(text)) {
            const originalIcon = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(() => {
              btn.innerHTML = originalIcon;
            }, 1000);
          }
        });
      }).catch(function(err) {
        console.error('Could not copy text: ', err);
      });
    }

    // Initialize Swiper for mobile
    document.addEventListener('DOMContentLoaded', function() {
      // Animation on scroll
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

      // Initialize Swiper only if mobile slider exists
      if (document.querySelector('.bookingSwiper')) {
        const swiper = new Swiper('.bookingSwiper', {
          slidesPerView: 1,
          spaceBetween: 20,
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          },
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
          },
          breakpoints: {
            576: {
              slidesPerView: 1.2,
              spaceBetween: 20,
            },
            768: {
              slidesPerView: 1.5,
              spaceBetween: 25,
            }
          }
        });
      }
    });
  </script>
</body>
</html>