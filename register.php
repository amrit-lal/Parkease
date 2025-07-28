<?php include 'header.php'; ?>
<?php
include('includes/db_connect.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Handle profile image upload
    $profile_img = '';
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == UPLOAD_ERR_OK) {
        $allowed_image_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['profile_img']['type'];
        
        if (in_array($file_type, $allowed_image_types)) {
            $original_name = $_FILES['profile_img']['name'];
            $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
            $random_number = mt_rand(100000, 999999);
            $profile_img = "profile_" . $random_number . "_" . time() . "." . $file_extension;
            $upload_dir = 'uploads/';
            
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            move_uploaded_file($_FILES['profile_img']['tmp_name'], $upload_dir . $profile_img);
        }
    }

    // Handle license file upload (can be image or PDF)
    $license_file = '';
    if (isset($_FILES['license_file']) && $_FILES['license_file']['error'] == UPLOAD_ERR_OK) {
        $allowed_license_types = [
            'image/jpeg', 
            'image/png', 
            'application/pdf'
        ];
        $file_type = $_FILES['license_file']['type'];
        
        if (in_array($file_type, $allowed_license_types)) {
            $original_name = $_FILES['license_file']['name'];
            $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
            $random_number = mt_rand(100000, 999999);
            $license_file = "license_" . $random_number . "_" . time() . "." . $file_extension;
            $upload_dir = 'uploads/licenses/';
            
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            move_uploaded_file($_FILES['license_file']['tmp_name'], $upload_dir . $license_file);
        }
    }

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, profile_img, license_file) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $password, $profile_img, $license_file);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Registration successful!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error in registration: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>User Registration | Parkease</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
  <link rel="stylesheet" href="css/nice-select.min.css" />
  <link rel="stylesheet" href="css/font-awesome.min.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/responsive.css" />
</head>

<body>
<div class="hero_area">
  <!-- Background Image -->
  <div class="bg-box">
    <img src="images/slider-bg.jpg" alt="background">
  </div>

  <!-- Header -->
  <header class="header_section">
    <div class="container">
      <nav class="navbar navbar-expand-lg custom_nav-container">
        
      </nav>
    </div>
  </header>

  <!-- Registration Form -->
  <section class="slider_section mt-5 pt-5">
    <div class="container">
      <div class="card p-4 mx-auto shadow-lg" style="max-width: 600px; background-color: rgba(21, 82, 99, 1);">
        <h3 class="mb-4 text-center" style="max-width: 600px; background-color: #ff6f3c; color:rgb(233, 240, 241);">REGISTER TO Parkease</h3>

        <form method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label><h6 style="color: rgb(222, 230, 233);">Name</h6></label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label><h6 style="color: rgb(233, 240, 241);">Email</h6></label>
            <input type="email" name="email" class="form-control" required>
          </div>

          <div class="mb-3">
            <label><h6 style="color: rgb(226, 241, 245);">Password</h6></label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <div class="mb-3">
            <label><h6 style="color: rgb(222, 237, 241);">Profile Image (JPG/PNG)</h6></label>
            <input type="file" name="profile_img" class="form-control" accept="image/jpeg,image/png" required>
          </div>

          <div class="mb-3">
            <label><h6 style="color: rgb(222, 237, 241);">License File (JPG/PNG/PDF)</h6></label>
            <input type="file" name="license_file" class="form-control" accept="image/jpeg,image/png,application/pdf" required>
            <small class="text-muted">Upload your driver's license as image or PDF</small>
          </div>

          <button type="submit" class="btn btn-success w-100">Register</button>

          <p class="mt-3 text-center" style="color: rgba(22, 145, 216, 0.95);">
            Already have an account? <a href="login.php" style="color: rgba(22, 145, 216, 0.95);">Login here</a>
          </p>
        </form>
      </div>
    </div>
  </section>
</div>

<script src="js/bootstrap.js"></script>
</body>
</html>

<?php include 'footer.php'; ?>