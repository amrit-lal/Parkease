<?php

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}
include('includes/db_connect.php');

// Dashboard data
$total_slots = $conn->query("SELECT COUNT(*) AS total FROM slots")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$total_bookings = $conn->query("SELECT COUNT(*) AS total FROM bookings")->fetch_assoc()['total'];
$pending = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE status='Pending'")->fetch_assoc()['total'];
$unread_contacts = $conn->query("SELECT COUNT(*) AS total FROM contact_form WHERE is_read=0")->fetch_assoc()['total'];
$total_guards = $conn->query("SELECT COUNT(*) AS total FROM guards")->fetch_assoc()['total'];

// Check if verifications table exists
$verifications_table_exists = $conn->query("SHOW TABLES LIKE 'verifications'")->num_rows > 0;

// Fetch recent verifications if table exists
$recent_verifications = $verifications_table_exists ? 
    $conn->query("
      SELECT v.*, g.name as guard_name, g.id as guard_id, b.booking_code
      FROM verifications v
      JOIN guards g ON v.guard_id = g.id
      JOIN bookings b ON v.booking_id = b.id
      ORDER BY v.verification_time DESC
      LIMIT 5
    ") : false;

// Fetch recent contact messages
$contact_messages = $conn->query("
  SELECT * FROM contact_form 
  ORDER BY id DESC 
  LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | parkease</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #7367f0;
      --secondary-color: #82868b;
      --success-color: #28c76f;
      --info-color: #00cfe8;
      --warning-color: #ff9f43;
      --danger-color: #ea5455;
      --dark-color: #4b4b4b;
      --light-color: #f8f8f8;
    }
    
    body {
      font-family: 'Montserrat', sans-serif;
      background-color: #f8f8f8;
      color: #6e6b7b;
      overflow-x: hidden;
      padding-top: 70px;
    }
    
    /* Header Styles */
        .main-header {
      background-color: var(--primary-color);
      color: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 999;
      height: 70px;
      transition: var(--transition);
    }
    
    .brand-logo {
      font-size: 1.8rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      color: white;
      text-decoration: none;
      transition: var(--transition);
    }
    
    .brand-logo:hover {
      transform: translateY(-2px);
    }
    
    .brand-logo i {
      margin-right: 10px;
      font-size: 1.5rem;
      transition: var(--transition);
    }
    
    .admin-info {
      display: flex;
      align-items: center;
      color: white;
      position: relative;
      cursor: pointer;
    }
    
    .admin-info:hover .admin-dropdown {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
    
    .admin-info img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 10px;
      border: 2px solid white;
      object-fit: cover;
      transition: var(--transition);
    }
    
    .admin-info:hover img {
      transform: scale(1.1);
      box-shadow: 0 0 10px rgba(255,255,255,0.5);
    }
    
    .admin-dropdown {
      position: absolute;
      top: 100%;
      right: 0;
      background: white;
      border-radius: 8px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.1);
      padding: 10px 0;
      min-width: 200px;
      opacity: 0;
      visibility: hidden;
      transform: translateY(10px);
      transition: var(--transition);
      z-index: 1000;
    }
    
    .admin-dropdown a {
      display: block;
      padding: 8px 20px;
      color: var(--dark-color);
      text-decoration: none;
      transition: var(--transition);
    }
    
    .admin-dropdown a:hover {
      background: var(--primary-light);
      color: var(--primary-color);
      padding-left: 25px;
    }
    
    .admin-dropdown a i {
      margin-right: 10px;
      width: 20px;
      text-align: center;
    }
    

    /* Footer Styles */
    .main-footer {
      background-color: white;
      padding: 20px 30px;
      text-align: center;
      border-top: 1px solid #ebe9f1;
      color: var(--secondary-color);
      font-size: 0.9rem;
      margin-left: 260px;
      transition: all 0.3s;
    }
    
    .sidebar {
      height: calc(100vh - 70px);
      background-color: white;
      padding-top: 20px;
      position: fixed;
      width: 260px;
      box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
      z-index: 1000;
      transition: transform 0.3s ease;
      left: 0;
      top: 70px;
      overflow-y: auto;
    }
    
    .sidebar.collapsed {
      transform: translateX(-260px);
    }
    
    .sidebar.show {
      transform: translateX(0);
    }
    
    .sidebar-header {
      padding: 0 20px 20px;
      border-bottom: 1px solid #ebe9f1;
      margin-bottom: 20px;
    }
    
    .sidebar .nav-item {
      margin-bottom: 5px;
      padding: 0 15px;
    }
    
    .sidebar .nav-link {
      color: #6e6b7b;
      padding: 10px 15px;
      border-radius: 5px;
      font-weight: 500;
      display: flex;
      align-items: center;
      transition: all 0.3s;
    }
    
    .sidebar .nav-link i {
      margin-right: 10px;
      width: 20px;
      text-align: center;
      font-size: 1.1rem;
    }
    
    .sidebar .nav-link:hover {
      color: var(--primary-color);
      background-color: rgba(115, 103, 240, 0.08);
    }
    
    .sidebar .nav-link.active {
      color: white;
      background-color: var(--primary-color);
      box-shadow: 0 4px 18px -4px rgba(115, 103, 240, 0.65);
    }
    
    .main-content {
      margin-left: 260px;
      padding: 30px;
      min-height: calc(100vh - 140px);
      transition: all 0.3s;
      margin-top: 70px;
    }
    
    .main-content.expanded {
      margin-left: 0;
    }
    
    .sidebar-toggle {
      display: none;
      position: fixed;
      top: 85px;
      left: 15px;
      z-index: 1050;
      background: var(--primary-color);
      color: white;
      border: none;
      border-radius: 5px;
      padding: 5px 10px;
      font-size: 1.5rem;
      cursor: pointer;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .header {
      background-color: white;
      padding: 20px 30px;
      border-radius: 10px;
      margin-bottom: 30px;
      box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .header h2 {
      color: #186329ff;
      font-weight: 600;
      margin-bottom: 0;
    }
    
    .stats-card {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 30px;
      box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
      border-left: 4px solid var(--primary-color);
    }
    
    .stats-card .icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 15px;
    }
    
    .stats-card .icon i {
      font-size: 1.5rem;
      color: white;
    }
    
    .stats-card h3 {
      font-weight: 600;
      margin-bottom: 5px;
      color: #5e5873;
    }
    
    .stats-card p {
      color: var(--secondary-color);
      margin-bottom: 0;
      font-size: 0.9rem;
    }
    
    .stats-card .trend {
      display: flex;
      align-items: center;
      margin-top: 10px;
      font-size: 0.85rem;
    }
    
    .stats-card .trend.up {
      color: var(--success-color);
    }
    
    .stats-card .trend.down {
      color: var(--danger-color);
    }
    
    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
      margin-bottom: 30px;
    }
    
    .card-header {
      background-color: white;
      border-bottom: 1px solid #ebe9f1;
      padding: 20px;
      border-radius: 10px 10px 0 0 !important;
    }
    
    .card-header h5 {
      font-weight: 600;
      color: #5e5873;
      margin-bottom: 0;
    }
    
    .card-body {
      padding: 20px;
    }
    
    .list-group-item {
      border: none;
      border-bottom: 1px solid #ebe9f1;
      padding: 15px 20px;
    }
    
    .list-group-item:last-child {
      border-bottom: none;
    }
    
    .list-group-item.unread {
      background-color: rgba(115, 103, 240, 0.05);
    }
    
    .badge {
      font-weight: 500;
      padding: 5px 10px;
    }
    
    .badge-contact {
      background-color: var(--danger-color);
    }
    
    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }
    
    .btn-outline-primary {
      color: var(--primary-color);
      border-color: var(--primary-color);
    }
    
    .btn-outline-primary:hover {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      color: white;
    }
    
    .btn-outline-success {
      color: var(--success-color);
      border-color: var(--success-color);
    }
    
    .btn-outline-success:hover {
      background-color: var(--success-color);
      border-color: var(--success-color);
      color: white;
    }
    
    @media (max-width: 992px) {
      .sidebar {
        transform: translateX(-260px);
      }
      
      .sidebar.show {
        transform: translateX(0);
      }
      
      .main-content {
        margin-left: 0;
      }
      
      .main-footer {
        margin-left: 0;
      }
      
      .sidebar-toggle {
        display: block;
      }
    }
    
    @media (max-width: 767.98px) {
      .header {
        padding: 15px;
      }
      
      .main-content {
        padding: 15px;
      }
      
      .stats-card {
        padding: 15px;
      }
      
      .stats-card .icon {
        width: 40px;
        height: 40px;
      }
      
      .stats-card h3 {
        font-size: 1.2rem;
      }
      
      .stats-card .trend {
        font-size: 0.75rem;
      }
      
      .brand-logo {
        font-size: 1.5rem;
      }
      
      .admin-info span {
        display: none;
      }
    }
    
    @media (max-width: 575.98px) {
      .stats-card .trend span {
        display: none;
      }
      
      .card-header h5 {
        font-size: 1rem;
      }
      
      .list-group-item {
        padding: 10px 15px;
      }
      
      .main-header {
        padding: 15px;
      }
    }
  </style>
</head>
<body>

<!-- Main Header -->
<header class="main-header animate__animated animate__fadeInDown">
  <a href="admin_dashboard.php" class="brand-logo">
    <i class="fas fa-parking"></i>
    <span>parkease</span>
  </a>
  <div class="admin-info">
    <img src="images/admin.jpg" alt="Admin Avatar" class="animate__animated animate__pulse">
    <span>Admin</span>
    <div class="admin-dropdown">
      <a href="admin_profile.php"><i class="fas fa-user"></i> Profile</a>
      
      <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>
</header>

<!-- Sidebar Toggle Button -->
<button class="sidebar-toggle" id="sidebarToggle">
  <i class="fas fa-bars"></i>
</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <h4 class="text-primary">🅿️ Admin Panel</h4>
  </div>
  <ul class="nav flex-column">
    <li class="nav-item">
      <a class="nav-link active" href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="manage_slots.php"><i class="fas fa-parking"></i> Manage Slots</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="view_users.php"><i class="fas fa-users"></i> View Users</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="view_bookings.php"><i class="fas fa-list"></i> View Bookings</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="approve_bookings.php"><i class="fas fa-check-circle"></i> Approve Bookings</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="manage_guards.php"><i class="fas fa-user-shield"></i> Manage Guards</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="verification_details.php"><i class="fas fa-check-double"></i> Verification Details</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="generate_reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </li>
  </ul>
</div>

<!-- Main Content -->
<div class="main-content" id="mainContent">
  <div class="header">
    <h2>Welcome Admin <span class="text-primary">👋</span></h2>
  </div>

  <div class="row">
    <!-- Stats Cards -->
    <div class="col-6 col-md-4 col-lg-2">
      <div class="stats-card">
        <div class="icon" style="background-color: var(--primary-color);">
          <i class="fas fa-parking"></i>
        </div>
        <h3><?= $total_slots ?></h3>
        <p>Total Slots</p>
        <div class="trend up"></div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="stats-card">
        <div class="icon" style="background-color: var(--info-color);">
          <i class="fas fa-users"></i>
        </div>
        <h3><?= $total_users ?></h3>
        <p>Total Users</p>
        <div class="trend up"></div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="stats-card">
        <div class="icon" style="background-color: var(--warning-color);">
          <i class="fas fa-list"></i>
        </div>
        <h3><?= $total_bookings ?></h3>
        <p>Total Bookings</p>
        <div class="trend up"></div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="stats-card">
        <div class="icon" style="background-color: var(--danger-color);">
          <i class="fas fa-clock"></i>
        </div>
        <h3><?= $pending ?></h3>
        <p>Pending Approvals</p>
        <div class="trend down"></div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="stats-card">
        <div class="icon" style="background-color: var(--success-color);">
          <i class="fas fa-user-shield"></i>
        </div>
        <h3><?= $total_guards ?></h3>
        <p>Registered Guards</p>
        <div class="trend up"></div>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="stats-card">
        <div class="icon" style="background-color: #7367f0;">
          <i class="fas fa-envelope"></i>
        </div>
        <h3><?= $unread_contacts ?></h3>
        <p>Unread Messages</p>
        <div class="trend up"></div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Contact Messages Section -->
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5><i class="fas fa-envelope me-2"></i> Contact Messages</h5>
          <?php if ($unread_contacts > 0): ?>
            <span class="badge badge-contact"><?= $unread_contacts ?> New</span>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <?php if ($contact_messages->num_rows > 0): ?>
            <div class="list-group">
              <?php while ($message = $contact_messages->fetch_assoc()): ?>
                <a href="#" class="list-group-item list-group-item-action <?= isset($message['is_read']) && !$message['is_read'] ? 'unread' : '' ?>">
                  <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1"><?= htmlspecialchars($message['subject']) ?></h6>
                    <small><?= isset($message['created_at']) ? date('M d, H:i', strtotime($message['created_at'])) : 'N/A' ?></small>
                  </div>
                  <p class="mb-1">From: <?= htmlspecialchars($message['name']) ?> &lt;<?= htmlspecialchars($message['email']) ?>&gt;</p>
                  <small class="text-muted"><?= substr(htmlspecialchars($message['message']), 0, 100) ?>...</small>
                </a>
              <?php endwhile; ?>
            </div>
            <div class="text-end mt-3">
              <a href="view_contacts.php" class="btn btn-outline-primary">View All Messages</a>
            </div>
          <?php else: ?>
            <div class="alert alert-info">No contact messages yet.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    
    <!-- Recent Verifications Section -->
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5><i class="fas fa-check-double me-2"></i> Recent Verifications</h5>
          <?php if ($verifications_table_exists && $recent_verifications && $recent_verifications->num_rows > 0): ?>
            <span class="badge bg-success"><?= $recent_verifications->num_rows ?> New</span>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <?php if (!$verifications_table_exists): ?>
            <div class="alert alert-warning">Verifications tracking not set up yet.</div>
          <?php elseif ($recent_verifications && $recent_verifications->num_rows > 0): ?>
            <div class="list-group">
              <?php while ($verification = $recent_verifications->fetch_assoc()): ?>
                <div class="list-group-item">
                  <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">Booking: <?= $verification['booking_code'] ?></h6>
                    <small><?= date('M d, H:i', strtotime($verification['verification_time'])) ?></small>
                  </div>
                  <p class="mb-1">Verified by: Guard #<?= $verification['guard_id'] ?></p>
                  <div class="d-flex justify-content-between">
                    <small class="text-muted">Click for details</small>
                    <span class="badge bg-success">Verified</span>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
            <div class="text-end mt-3">
              <a href="verification_details.php" class="btn btn-outline-success">View All Verifications</a>
            </div>
          <?php else: ?>
            <div class="alert alert-info">No recent verifications found.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="main-footer">
  <div class="container">
    <p>&copy; <?php echo date('Y'); ?> parkease Admin Dashboard. All rights reserved. | 
      <a href="#">Privacy Policy</a> | 
      <a href="#">Terms of Service</a>
    </p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Sidebar toggle functionality
  document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('show');
    document.getElementById('mainContent').classList.toggle('expanded');
  });

  // Close sidebar when clicking outside on mobile
  document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const isClickInsideSidebar = sidebar.contains(event.target);
    const isClickOnToggleBtn = event.target === toggleBtn || toggleBtn.contains(event.target);
    
    if (window.innerWidth <= 992 && !isClickInsideSidebar && !isClickOnToggleBtn && sidebar.classList.contains('show')) {
      sidebar.classList.remove('show');
      document.getElementById('mainContent').classList.add('expanded');
    }
  });

  // Close sidebar when window is resized above 992px
  window.addEventListener('resize', function() {
    if (window.innerWidth > 992) {
      document.getElementById('sidebar').classList.remove('show');
      document.getElementById('mainContent').classList.remove('expanded');
    }
  });
</script>
</body>
</html>