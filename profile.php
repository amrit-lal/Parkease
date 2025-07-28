<?php
include('includes/db_connect.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['user_name'];
$email = $_SESSION['user_email'] ?? '';
$profile_img = $_SESSION['user_img'] ?? 'default.png';

// Handle profile updates
$update_success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['name'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate inputs
    if (empty($new_name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($new_email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Handle password change if any password field is filled
    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        if (empty($current_password)) {
            $errors[] = "Current password is required to change password";
        } else {
            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if (!password_verify($current_password, $user['password'])) {
                $errors[] = "Current password is incorrect";
            } elseif (empty($new_password)) {
                $errors[] = "New password is required";
            } elseif (strlen($new_password) < 8) {
                $errors[] = "New password must be at least 8 characters";
            } elseif ($new_password !== $confirm_password) {
                $errors[] = "New passwords do not match";
            }
        }
    }
    
    // Handle profile picture upload
    $profile_picture_changed = false;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['profile_picture']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Only JPG, PNG, and GIF images are allowed";
        } else {
            $upload_dir = 'uploads/';
            $file_ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $new_filename = 'user_' . $user_id . '_' . time() . '.' . $file_ext;
            $destination = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {
                // Delete old profile picture if it's not the default
                if ($profile_img !== 'default.png' && file_exists($upload_dir . $profile_img)) {
                    unlink($upload_dir . $profile_img);
                }
                
                $profile_img = $new_filename;
                $profile_picture_changed = true;
            } else {
                $errors[] = "Failed to upload profile picture";
            }
        }
    }
    
    // Update profile if no errors
    if (empty($errors)) {
        // Update user data
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, profile_img = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $new_name, $new_email, $hashed_password, $profile_img, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, profile_img = ? WHERE id = ?");
            $stmt->bind_param("sssi", $new_name, $new_email, $profile_img, $user_id);
        }
        
        if ($stmt->execute()) {
            // Update session variables
            $_SESSION['user_name'] = $new_name;
            $_SESSION['user_email'] = $new_email;
            $_SESSION['user_img'] = $profile_img;
            
            $update_success = true;
            $name = $new_name;
            $email = $new_email;
        } else {
            $errors[] = "Failed to update profile. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile | Parkease</title>
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

    /* Profile Container */
    .profile-container {
      padding: 2rem 0;
      max-width: 1000px;
      margin: 0 auto;
      flex: 1;
    }

    /* Profile Card */
    .profile-card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
      padding: 2rem;
      margin-bottom: 2rem;
      transition: all 0.3s ease;
      border: 1px solid rgba(0, 0, 0, 0.05);
      position: relative;
      overflow: hidden;
    }

    .profile-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 8px;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      border-radius: 8px 8px 0 0;
    }

    .profile-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .profile-picture {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      border: 5px solid white;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
      margin: 0 auto 1rem;
      display: block;
      transition: all 0.3s ease;
    }

    .profile-picture:hover {
      transform: scale(1.05);
    }

    .profile-title {
      color: var(--primary-dark);
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    /* Form Styles */
    .form-label {
      font-weight: 500;
      color: var(--dark-gray);
    }

    .form-control {
      border-radius: 8px;
      padding: 0.75rem 1rem;
      border: 1px solid var(--gray);
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      border: none;
      border-radius: 50px;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    /* File Upload */
    .file-upload {
      position: relative;
      display: inline-block;
      width: 100%;
    }

    .file-upload-btn {
      width: 100%;
      color: var(--dark);
      background-color: var(--light);
      padding: 0.75rem 1rem;
      border-radius: 8px;
      border: 1px dashed var(--dark-gray);
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .file-upload-btn:hover {
      border-color: var(--primary);
      background-color: rgba(67, 97, 238, 0.05);
    }

    .file-upload-input {
      position: absolute;
      left: 0;
      top: 0;
      opacity: 0;
      width: 100%;
      height: 100%;
      cursor: pointer;
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

    /* Responsive Adjustments */
    @media (max-width: 768px) {
      .profile-picture {
        width: 120px;
        height: 120px;
      }
      
      .profile-container {
        padding: 1.5rem;
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
          <a href="dashboard.php" class="text-white me-3 animate__animated animate__fadeIn">
            <i class="fas fa-home me-1"></i> Dashboard
          </a>
          <a href="logout.php" class="text-white animate__animated animate__fadeIn">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <div class="profile-container container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="profile-card animate__animated animate__fadeIn">
          <div class="profile-header">
            <img src="uploads/<?= $profile_img ?>" alt="Profile Picture" class="profile-picture">
            <h3 class="profile-title"><?= $name ?></h3>
            <p class="text-muted">Manage your account settings</p>
          </div>

          <?php if ($update_success): ?>
            <div class="alert alert-success animate__animated animate__fadeIn">
              <i class="fas fa-check-circle me-2"></i> Your profile has been updated successfully!
            </div>
          <?php endif; ?>

          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger animate__animated animate__fadeIn">
              <h5><i class="fas fa-exclamation-triangle me-2"></i> Error</h5>
              <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                  <li><?= $error ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form method="POST" enctype="multipart/form-data">
            <div class="mb-4">
              <label for="profile_picture" class="form-label">Profile Picture</label>
              <div class="file-upload">
                <div class="file-upload-btn">
                  <i class="fas fa-cloud-upload-alt me-2"></i>
                  <span>Click to upload new profile picture</span>
                </div>
                <input type="file" id="profile_picture" name="profile_picture" class="file-upload-input" accept="image/*">
              </div>
              <small class="text-muted">Max size: 2MB (JPG, PNG, GIF)</small>
            </div>

            <div class="mb-3">
              <label for="name" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="mb-4">
              <h5 class="mb-3">Change Password</h5>
              <div class="mb-3">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" class="form-control" id="current_password" name="current_password">
                <small class="text-muted">Leave blank if you don't want to change</small>
              </div>

              <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password">
              </div>

              <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
              </div>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save me-2"></i> Save Changes
              </button>
            </div>
          </form>
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
    // Display file name when selected
    document.getElementById('profile_picture').addEventListener('change', function(e) {
      const fileName = e.target.files[0]?.name || 'No file selected';
      const uploadBtn = document.querySelector('.file-upload-btn span');
      uploadBtn.textContent = fileName;
    });

    // Add animation on load
    document.addEventListener('DOMContentLoaded', function() {
      const elements = document.querySelectorAll('.animate__animated');
      elements.forEach(el => {
        const animation = el.classList.item(1);
        el.classList.add(animation);
      });
    });
  </script>
</body>
</html>