<?php
session_start();
require_once 'includes/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Initialize variables
$admin_id = $_SESSION['admin_id'];
$admin = [];
$profile_img = 'images/admin_default.jpg';
$update_success = false;
$errors = [];

// Fetch admin data
try {
    $stmt = $conn->prepare("SELECT id, username, full_name, email, password_hash, profile_img FROM admins WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    
    if ($admin) {
        $profile_img = !empty($admin['profile_img']) ? 'uploads/admin/' . $admin['profile_img'] : 'images/admin_default.jpg';
    } else {
        throw new Exception("Admin profile not found");
    }
} catch (Exception $e) {
    $errors[] = "Database error: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $full_name = trim(filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate inputs
    if (empty($username)) {
        $errors[] = "Username is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Handle password change
    $password_changed = false;
    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        if (empty($current_password)) {
            $errors[] = "Current password is required";
        } elseif (!password_verify($current_password, $admin['password_hash'])) {
            $errors[] = "Current password is incorrect";
        } elseif (empty($new_password)) {
            $errors[] = "New password is required";
        } elseif (strlen($new_password) < 8) {
            $errors[] = "Password must be at least 8 characters";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "Passwords do not match";
        } else {
            $password_changed = true;
        }
    }
    
    // Handle profile picture upload
    $profile_img = $admin['profile_img'] ?? 'admin_default.jpg';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        $file_type = $_FILES['profile_picture']['type'];
        $file_size = $_FILES['profile_picture']['size'];
        
        if (!array_key_exists($file_type, $allowed_types)) {
            $errors[] = "Only JPG, PNG, and GIF images are allowed";
        } elseif ($file_size > $max_size) {
            $errors[] = "Image size must be less than 2MB";
        } else {
            $upload_dir = 'uploads/admin/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Delete old image if not default
            if (!empty($admin['profile_img']) && $admin['profile_img'] !== 'admin_default.jpg') {
                $old_image = $upload_dir . $admin['profile_img'];
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
            }
            
            // Generate unique filename
            $file_ext = $allowed_types[$file_type];
            $new_filename = 'admin_' . $admin_id . '_' . time() . '.' . $file_ext;
            $destination = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {
                $profile_img = $new_filename;
            } else {
                $errors[] = "Failed to upload profile picture";
            }
        }
    }
    
    // Update database if no errors
    if (empty($errors)) {
        try {
            $conn->begin_transaction();
            
            if ($password_changed) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE admins SET 
                    username = ?, 
                    full_name = ?, 
                    email = ?, 
                    password_hash = ?, 
                    profile_img = ?,
                    updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?");
                $stmt->bind_param("sssssi", $username, $full_name, $email, $hashed_password, $profile_img, $admin_id);
            } else {
                $stmt = $conn->prepare("UPDATE admins SET 
                    username = ?, 
                    full_name = ?, 
                    email = ?, 
                    profile_img = ?,
                    updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?");
                $stmt->bind_param("ssssi", $username, $full_name, $email, $profile_img, $admin_id);
            }
            
            if ($stmt->execute()) {
                // Update session
                $_SESSION['admin_username'] = $username;
                $_SESSION['admin_email'] = $email;
                $_SESSION['admin_full_name'] = $full_name;
                $_SESSION['admin_profile_img'] = $profile_img;
                
                $update_success = true;
                $conn->commit();
                
                // Refresh admin data
                $admin['username'] = $username;
                $admin['full_name'] = $full_name;
                $admin['email'] = $email;
                $admin['profile_img'] = $profile_img;
                
                // Refresh profile image path
                $profile_img = 'uploads/admin/' . $profile_img;
            } else {
                throw new Exception("Database update failed");
            }
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Error updating profile: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile | Parkease</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a0ca3;
            --secondary: #7209b7;
            --success: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #e9ecef;
            --dark-gray: #6c757d;
            --body-bg: #f5f7fb;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--body-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-header {
            background: linear-gradient(135deg, var(--primary-dark), var(--secondary));
            color: white;
            padding: 1rem 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .profile-container {
            padding: 2rem 0;
            max-width: 1000px;
            margin: 0 auto;
            flex: 1;
        }

        .profile-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .profile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .admin-badge {
            background: linear-gradient(135deg, var(--secondary), var(--primary-dark));
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
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
        }

        @media (max-width: 768px) {
            .profile-picture {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="admin_dashboard.php" class="text-white text-decoration-none fs-4 fw-bold">
                    <i class="fas fa-parking me-2"></i>Parkease Admin
                </a>
                <div class="d-flex align-items-center gap-3">
                    <a href="admin_dashboard.php" class="text-white text-decoration-none">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                    <a href="admin_logout.php" class="text-white text-decoration-none">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="profile-container container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="profile-card">
                    <div class="text-center mb-4">
                        <img src="<?= htmlspecialchars($profile_img) ?>" alt="Admin Profile" class="profile-picture mb-3">
                        <h3 class="mb-1"><?= htmlspecialchars($admin['full_name'] ?? 'Administrator') ?></h3>
                        <span class="admin-badge">Super Admin</span>
                    </div>

                    <?php if ($update_success): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i> Profile updated successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> Errors</h5>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" novalidate>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Profile Picture</label>
                            <div class="file-upload position-relative">
                                <div class="file-upload-btn">
                                    <i class="fas fa-cloud-upload-alt me-2"></i>
                                    <span class="file-name">Click to upload new profile picture</span>
                                </div>
                                <input type="file" name="profile_picture" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" 
                                       accept="image/jpeg, image/png, image/gif">
                            </div>
                            <small class="text-muted">Max 2MB (JPG, PNG, GIF)</small>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label fw-bold">Username</label>
                            <input type="text" class="form-control py-2" id="username" name="username" 
                                   value="<?= htmlspecialchars($admin['username'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="full_name" class="form-label fw-bold">Full Name</label>
                            <input type="text" class="form-control py-2" id="full_name" name="full_name" 
                                   value="<?= htmlspecialchars($admin['full_name'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control py-2" id="email" name="email" 
                                   value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required>
                        </div>

                        <div class="mb-4">
                            <h5 class="mb-3">Change Password</h5>
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control py-2" id="current_password" name="current_password">
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control py-2" id="new_password" name="new_password">
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control py-2" id="confirm_password" name="confirm_password">
                            </div>
                            <small class="text-muted">Leave blank to keep current password</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload display
        document.querySelector('input[name="profile_picture"]').addEventListener('change', function(e) {
            const fileInput = e.target;
            const fileNameDisplay = document.querySelector('.file-name');
            const profileImage = document.querySelector('.profile-picture');
            
            if (fileInput.files.length > 0) {
                const fileName = fileInput.files[0].name;
                fileNameDisplay.textContent = fileName;
                
                // Preview image
                const reader = new FileReader();
                reader.onload = function(event) {
                    profileImage.src = event.target.result;
                };
                reader.readAsDataURL(fileInput.files[0]);
            } else {
                fileNameDisplay.textContent = 'Click to upload new profile picture';
            }
        });
    </script>
</body>
</html>