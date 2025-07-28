<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}
include('includes/db_connect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel | parkease</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <style>
    :root {
      --primary-color: #7367f0;
      --primary-light: rgba(115, 103, 240, 0.1);
      --secondary-color: #82868b;
      --success-color: #28c76f;
      --info-color: #00cfe8;
      --warning-color: #ff9f43;
      --danger-color: #ea5455;
      --dark-color: #4b4b4b;
      --light-color: #f8f8f8;
      --transition: all 0.3s ease;
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
