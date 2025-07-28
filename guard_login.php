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
include('includes/db_connect.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Both email and password are required';
    } else {
        // Check if guard exists
        $stmt = $conn->prepare("SELECT id, name, unique_id, password FROM guards WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $guard = $result->fetch_assoc();

        if ($guard && password_verify($password, $guard['password'])) {
            // Set all required session variables
            $_SESSION['guard_logged_in'] = true;
            $_SESSION['guard_id'] = $guard['id'];
            $_SESSION['guard_name'] = $guard['name'];
            $_SESSION['guard_unique_id'] = $guard['unique_id'];
            
            header("Location: scan_verification.php");
            exit();
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🛡 Guard Login | Parkease</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-container {
            max-width: 500px;
            margin: 100px auto;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: none;
        }
        .card-header {
            background-color:rgb(13, 105, 93);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            text-align: center;
            padding: 20px;
        }
        .card-body {
            padding: 30px;
        }
        .form-control {
            padding: 12px;
            border-radius: 8px;
        }
        .btn-login {
            background-color:rgb(14, 117, 91);
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
        }
        .btn-login:hover {
            background-color:rgb(26, 199, 127);
        }
    </style>
</head>
<body>
  <div class="hero_area">
  <!-- Background Image -->
  <div class="bg-box">
    <img src="images/slider-bg.jpg" alt="background">
  </div>

<div class="container">
    <div class="login-container">
        <div class="card shadow">
            <div class="card-header">
                <h4><img src="images/Sec.jpg"  width="50" height=" 50"alt=""> GUARD LOGIN</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-login w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include("footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>