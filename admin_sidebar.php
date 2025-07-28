<?php
// admin_sidebar.php
?>
<!-- Sidebar -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Mobile Toggle Button -->
<button class="sidebar-toggle d-lg-none">
  <i class="fas fa-bars"></i>
</button>

<div class="sidebar">
  <div class="sidebar-header">
    <h4 class="text-primary">🅿️  Admin</h4>
    <button class="sidebar-close d-lg-none">
      <i class="fas fa-times"></i>
    </button>
  </div>
  <ul class="nav flex-column">
    <li class="nav-item">
      <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : '' ?>" href="admin_dashboard.php">
        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manage_slots.php' ? 'active' : '' ?>" href="manage_slots.php">
        <i class="fas fa-parking me-2"></i> Manage Slots
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'view_users.php' ? 'active' : '' ?>" href="view_users.php">
        <i class="fas fa-users me-2"></i> View Users
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'view_bookings.php' ? 'active' : '' ?>" href="view_bookings.php">
        <i class="fas fa-list me-2"></i> View Bookings
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'approve_bookings.php' ? 'active' : '' ?>" href="approve_bookings.php">
        <i class="fas fa-check-circle me-2"></i> Approve Bookings
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manage_guards.php' ? 'active' : '' ?>" href="manage_guards.php">
        <i class="fas fa-user-shield me-2"></i> Manage Guards
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'verification_details.php' ? 'active' : '' ?>" href="verification_details.php">
        <i class="fas fa-check-double me-2"></i> Verification Details
      </a>
    </li>
    <li class="nav-item">
    <a class="nav-link" href="manage_testimonials.php"><i class="fas fa-comment-alt"></i> Manage Testimonials</a>
</li>
    <li class="nav-item">
      <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'generate_reports.php' ? 'active' : '' ?>" href="generate_reports.php">
        <i class="fas fa-chart-bar me-2"></i> Reports
      </a>
    </li>
    <li class="nav-item mt-3">
      <a class="nav-link text-danger" href="admin_logout.php">
        <i class="fas fa-sign-out-alt me-2"></i> Logout
      </a>
    </li>
  </ul>
</div>

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
  
  /* Mobile Toggle Button */
  .sidebar-toggle {
    display: none;
    position: fixed;
    top: 15px;
    left: 15px;
    z-index: 999;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 5px;
    width: 40px;
    height: 40px;
    font-size: 1.2rem;
    cursor: pointer;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }
  
  .sidebar {
    height: 100vh;
    background-color: white;
    padding: 20px 0;
    position: fixed;
    width: 260px;
    left: 0;
    top: 0;
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
    z-index: 1000;
    transition: all 0.3s ease;
    overflow-y: auto;
  }
  
  .sidebar-header {
    padding: 0 20px 20px;
    border-bottom: 1px solid #ebe9f1;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .sidebar-header h4 {
    font-weight: 600;
    font-size: 1.25rem;
    margin: 0;
  }
  
  .sidebar-close {
    display: none;
    background: transparent;
    border: none;
    color: var(--secondary-color);
    font-size: 1.2rem;
    cursor: pointer;
  }
  
  .nav-item {
    margin-bottom: 5px;
    padding: 0 15px;
  }
  
  .nav-link {
    color: #6e6b7b;
    padding: 10px 15px;
    border-radius: 5px;
    font-weight: 500;
    display: flex;
    align-items: center;
    transition: all 0.2s ease;
  }
  
  .nav-link i {
    width: 20px;
    text-align: center;
    font-size: 1.1rem;
  }
  
  .nav-link:hover {
    color: #7367f0;
    background-color: rgba(115, 103, 240, 0.08);
    transform: translateX(3px);
  }
  
  .nav-link.active {
    color: white;
    background-color: #7367f0;
    box-shadow: 0 4px 18px -4px rgba(115, 103, 240, 0.65);
  }
  
  .nav-link.active:hover {
    transform: none;
  }
  
  .nav-link.text-danger {
    color: #ea5455 !important;
  }
  
  .nav-link.text-danger:hover {
    color: white !important;
    background-color: #ea5455;
  }

  /* Ensure content doesn't overlap sidebar */
  body {
    padding-left: 260px;
    transition: padding 0.3s ease;
  }

  @media (max-width: 992px) {
    .sidebar {
      transform: translateX(-100%);
    }
    
    .sidebar.active {
      transform: translateX(0);
    }
    
    body {
      padding-left: 0;
    }
    
    .sidebar-toggle {
      display: block;
    }
    
    .sidebar-close {
      display: block;
    }
  }
</style>

<script>
  // Toggle sidebar on mobile
  document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.querySelector('.sidebar-toggle');
    const closeBtn = document.querySelector('.sidebar-close');
    
    toggleBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      sidebar.classList.toggle('active');
    });
    
    closeBtn.addEventListener('click', function() {
      sidebar.classList.remove('active');
    });
    
    // Close sidebar when clicking outside
    document.addEventListener('click', function(e) {
      if (!sidebar.contains(e.target) && e.target !== toggleBtn) {
        sidebar.classList.remove('active');
      }
    });
    
    // Prevent clicks inside sidebar from closing it
    sidebar.addEventListener('click', function(e) {
      e.stopPropagation();
    });
  });
</script>