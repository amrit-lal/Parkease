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
                <a class="nav-link" href="index.PHP">Home</a>
              </li>
              <li class="nav-item ">
                <a class="nav-link" href="about.PHP">About <span class="sr-only">(current)</span> </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="pricing.PHP">Pricing</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="why.PHP">Why Us</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="testimonial.PHP">Testimonial</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="register.PHP">REGISTER</a>
              </li>
              
              <li class="nav-item">
                <a class="nav-link" href="admin_login.PHP">ADMIN</a>
              </li>
              <li class="nav-item">
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

    <!-- Contact Section -->
<section id="contact" class="contact section py-5">
  <div class="container" data-aos="fade-up">

    <!-- Google Map -->
    <div class="mb-5">
      <iframe style="width: 100%; height: 400px;" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d12097.433213460943!2d-74.0062269!3d40.7101282!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xb89d1fe6bc499443!2sDowntown+Conference+Center!5e0!3m2!1smk!2sbg!4v1539943755621" frameborder="0" allowfullscreen=""></iframe>
    </div>

    <!-- Contact Info and Form in Cards -->
    <div class="row gy-4 gx-lg-5">

      <!-- Contact Info Card -->
      <div class="col-lg-4" > 
        <div class="card shadow-sm p-4 h-100" style=" background-color: rgb(20, 96, 119);"> 
          <h3 style=" color: rgb(233, 238, 240);">Get in touch</h3>
          <p style=" color: rgb(233, 238, 240);">Feel free to reach out for parking assistance, inquiries, or feedback.</p>

          <div class="mb-3">
            <i class="bi bi-geo-alt-fill me-2 text-primary"></i>
            <strong style=" color: rgb(233, 238, 240);">Location:</strong><br>
          <h6 style=" color: rgb(233, 238, 240);"> A108 Adam Street, New York, NY 535022</h6>
          </div>

          <div class="mb-3">
            <i class="bi bi-envelope-fill me-2 text-primary"></i>
            <strong style=" color: rgb(233, 238, 240);">Email:</strong><br>
            <h6 style=" color: rgb(233, 238, 240);">info@example.com</h6>
          </div>

          <div class="mb-3">
            <i class="bi bi-phone-fill me-2 text-primary"></i>
            <strong style=" color: rgb(233, 238, 240);">Call:</strong><br>
            <h6 style=" color: rgb(233, 238, 240);">+1 5589 55488 55</h6>
          </div>
        </div>
      </div>

         <div class="col-lg-8">
<?php


// DB connection (to Parking Management System DB)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "parking_db"; // Connect to your main Parking DB

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die('<div class="alert alert-danger">Connection failed: ' . $conn->connect_error . '</div>');
}

$success = $error = "";

// Handle POST form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST["name"] ?? ''));
    $email = htmlspecialchars(trim($_POST["email"] ?? ''));
    $subject = htmlspecialchars(trim($_POST["subject"] ?? ''));
    $message = htmlspecialchars(trim($_POST["message"] ?? ''));

    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // Insert into contact_form
        $stmt = $conn->prepare("INSERT INTO contact_form (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        if ($stmt->execute()) {
            $success = "Thank you! Your message has been sent.";
        } else {
            $error = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!-- Success/Error Alert -->
 <?php if ($_SERVER["REQUEST_METHOD"] == "POST") ?>
<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $success ?>
        
    </div>
<?php elseif ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Contact Form -->
<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="needs-validation" novalidate>
  <H1>CONTACT US</H1><BR>
  <div class="row">
    <div class="col-md-6 mb-3">
      <input type="text" name="name" class="form-control" placeholder="Your Name" required>
      <div class="invalid-feedback">Name is required</div>
    </div>
    <div class="col-md-6 mb-3">
      <input type="email" name="email" class="form-control" placeholder="Your Email" required>
      <div class="invalid-feedback">Valid email is required</div>
    </div>
  </div>
  <div class="mb-3">
    <input type="text" name="subject" class="form-control" placeholder="Subject" required>
    <div class="invalid-feedback">Subject is required</div>
  </div>
  <div class="mb-3">
    <textarea name="message" class="form-control" rows="6" placeholder="Message" required></textarea>
    <div class="invalid-feedback">Message is required</div>
  </div>
  <div class="text-center">
    <button type="submit" class="btn btn-success" style=" background-color: rgba(21, 82, 99, 1);">Send Message</button>
  </div>
</form>

<!-- Enable Bootstrap JS Validation -->
<script>
// Bootstrap validation (client-side)
(() => {
  'use strict';
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>
</div>

<!-- End Contact Form -->

        </div>

      </div>

    </section><!-- /Contact Section -->

  </main>

  <?php include("footer.php");
?>