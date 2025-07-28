<?php
// Start session and include database connection
include('includes/db_connect.php');

// Fetch approved testimonials with user info
$testimonials = $conn->query("
    SELECT t.*, u.name as user_name, u.profile_img 
    FROM testimonials t
    JOIN users u ON t.user_id = u.id
    WHERE t.status = 'Approved'
    ORDER BY t.created_at DESC
    LIMIT 4
");

// Fetch pricing information
$pricing = $conn->query("SELECT * FROM pricing");
$pricing_data = [];
if ($pricing && $pricing->num_rows > 0) {
    while($row = $pricing->fetch_assoc()) {
        $pricing_data[$row['category']] = $row;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="parking, parking system, find parking, reserve parking" />
  <meta name="description" content="Smart parking solutions for modern cities - find and reserve parking spots in real-time" />
  <meta name="author" content="Parkease Team" />
  <link rel="shortcut icon" href="images/fav.jpg" type="image/x-icon">

  <title>Parkease - Smart Parking Solutions</title>

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

  <!-- animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />

  <style>
    /* Enhanced Slider Section */
    .slider_section {
      position: relative;
      padding: 120px 0;
      color: #fff;
      overflow: hidden;
    }
    
    .slider_section .bg-box {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
    }
    
    .slider_section .bg-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      filter: brightness(0.6);
    }
    
    .slider_section .detail-box {
      text-align: center;
      position: relative;
      z-index: 2;
      animation: fadeInUp 1s ease-out;
    }
    
    .slider_section h1 {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 25px;
      text-shadow: 0 2px 4px rgba(0,0,0,0.3);
      line-height: 1.2;
    }
    
    .slider_section p {
      font-size: 1.2rem;
      margin-bottom: 40px;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
      text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    }
    
    /* Call-to-action button */
    .slider_section .btn-primary {
      background-color: #2c7be5;
      border-color: #2c7be5;
      padding: 12px 30px;
      font-size: 1.1rem;
      font-weight: 500;
      border-radius: 30px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(44, 123, 229, 0.4);
    }
    
    .slider_section .btn-primary:hover {
      background-color: #1a68d1;
      border-color: #1a68d1;
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(44, 123, 229, 0.5);
    }
    
    /* Animation for sections */
    .about_section, .why_section, .pricing_section, .client_section {
      opacity: 0;
      transform: translateY(30px);
      transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }
    
    .section-visible {
      opacity: 1;
      transform: translateY(0);
    }
    
    /* Enhanced Testimonial Cards */
    .client_section .box {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border-radius: 10px;
      overflow: hidden;
    }
    
    .client_section .box:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    /* Pricing cards hover effect */
    .pricing_section .box {
      transition: all 0.3s ease;
    }
    
    .pricing_section .box:hover {
      transform: scale(1.03);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    /* Why Us icons animation */
    .why_section .img-box img {
      transition: transform 0.5s ease;
    }
    
    .why_section .box:hover .img-box img {
      transform: rotateY(180deg);
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
      .slider_section h1 {
        font-size: 2.8rem;
      }
      
      .slider_section p {
        font-size: 1.1rem;
      }
    }
    
    @media (max-width: 768px) {
      .slider_section {
        padding: 80px 0;
      }
      
      .slider_section h1 {
        font-size: 2.2rem;
      }
    }
  </style>
</head>

<body>

  <div class="hero_area">
    <div class="bg-box">
      <img src="images/slider-bg.jpg" alt="Parking lot image">
    </div>
    <!-- header section strats -->
    <header class="header_section">
      <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container ">
          <a class="navbar-brand" href="index.php">
            <span>
              ParkEase
            </span>
          </a>

          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class=""> </span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav">
              <li class="nav-item active">
                <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="about.php"> About</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="pricing.php">Pricing</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="why.php">Why Us</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="testimonial.php">Testimonial</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="register.php">REGISTER</a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  LOGIN
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="admin_login.php">Admin Login</a>
                  <a class="dropdown-item" href="login.php">User Login</a>
                  <a class="dropdown-item" href="guard_login.php">Guard Login</a>
                </div>
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
    <!-- slider section -->
    <section class="slider_section ">
      <div class="container">
        <div class="detail-box col-md-9 mx-auto px-0">
          <h1 class="animate__animated animate__fadeInDown">
            Smart Parking Solutions for Modern Cities
          </h1>
          <p class="animate__animated animate__fadeIn animate__delay-1s">
            Say goodbye to circling the block! Our advanced parking management system helps you find and reserve parking spots in real-time. Whether you're commuting to work or running errands, we make parking hassle-free.
          </p>
          <div class="animate__animated animate__fadeInUp animate__delay-2s">
            <a href="register.php" class="btn btn-primary">
              Get Started Today <i class="fa fa-arrow-right ml-2" aria-hidden="true"></i>
            </a>
          </div>
        </div>
      </div>
    </section>
    <!-- end slider section -->
  </div>

  <!-- about section -->
  <section class="about_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          About Our Parking System
        </h2>
        <p>
          Pioneering smart parking technology since 2025, serving thousands of drivers daily.
        </p>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="img-box">
            <img src="images/about-img.jpg" alt="Parking system about image" class="img-fluid animate__animated animate__fadeInLeft">
          </div>
        </div>
        <div class="col-lg-6">
          <div class="detail-box animate__animated animate__fadeInRight">
            <h3>
              Your Parking Problems Solved
            </h3>
            <p>
              Our system connects drivers with available parking spots through real-time monitoring and reservation technology. With over 500 parking locations across the city, we provide convenient options wherever you need them.
            </p>
            <div class="mt-4">
              <a href="about.php" class="btn btn-outline-primary">
                Read More <i class="fa fa-arrow-right ml-2" aria-hidden="true"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- end about section -->

  <!-- why section -->
  <section class="why_section layout_padding-bottom">
    <div class="container">
      <div class="col-md-10 px-0">
        <div class="heading_container">
          <h2>
            Why Choose Us
          </h2>
          <p>
            We offer the most comprehensive, user-friendly parking solution with benefits that go beyond just finding a spot.
          </p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box animate__animated animate__fadeInUp">
            <div class="img-box">
              <img src="images/w1.png" alt="No fees icon">
            </div>
            <div class="detail-box">
              <h4>
                Real-Time Availability
              </h4>
              <p>
                Our sensors provide live updates on parking space availability, so you'll know exactly where to go before you arrive. No more guessing or wasted time driving around full lots.
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box animate__animated animate__fadeInUp animate__delay-1s">
            <div class="img-box">
              <img src="images/w2.png" alt="Online payments icon">
            </div>
            <div class="detail-box">
              <h4>
                Secure  Payments
              </h4>
              <p>
                Pay for parking directly  when you come to park your vehicle in  any  mode  like cash,  card  or UPI payment. No advance is taken from you.
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box animate__animated animate__fadeInUp animate__delay-2s">
            <div class="img-box">
              <img src="images/w3.png" alt="Simple booking icon">
            </div>
            <div class="detail-box">
              <h4>
                Priority Member Benefits
              </h4>
              <p>
                Regular users enjoy premium features like guaranteed spots, discounted rates, and personalized parking recommendations based on your habits and schedule.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- end why section -->

  <!-- pricing section -->
  <!-- pricing section -->
<section class="pricing_section layout_padding">
    <div class="bg-box">
        <img src="images/pricing-bg.jpg" alt="Pricing background">
    </div>
    <div class="container">
        <div class="heading_container heading_center">
            <h2>
                Our Parking Rates
            </h2>
        </div>
        <div class="col-xl-10 px-0 mx-auto">
            <div class="row">
                <div class="col-md-6 col-lg-6 mx-auto">
                    <div class="box animate__animated animate__flipInY">
                        <div class="text-center mb-3">
                            <i class="fa fa-car fa-3x" aria-hidden="true"></i>
                        </div>
                        <h4 class="price" style="color: #2c7be5; font-size: 2.5rem; margin: 15px 0;">
                            <?php 
                            // Default value if database fetch fails
                            $four_wheeler_price = '100';
                            
                            // Check if pricing data exists and has 4W category
                            if (isset($pricing_data['4W']) && isset($pricing_data['4W']['price'])) {
                                $four_wheeler_price = $pricing_data['4W']['price'];
                            }
                            echo '₹'.$four_wheeler_price.'<small style="font-size: 1rem; color: #666;">/hour</small>';
                            ?>
                        </h4>
                        <h5 class="name">
                            4 Wheeler Parking
                        </h5>
                        <p>
                            Secure parking for cars and SUVs with 24/7 surveillance and easy access. 
                        </p>
                        <a href="register.php" style="display: inline-block; margin-top: 20px;">
                            Get Started <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 mx-auto">
                    <div class="box animate__animated animate__flipInY animate__delay-1s">
                        <div class="text-center mb-3">
                            <i class="fa fa-motorcycle fa-3x" aria-hidden="true"></i>
                        </div>
                        <h4 class="price" style="color: #2c7be5; font-size: 2.5rem; margin: 15px 0;">
                            <?php 
                            // Default value if database fetch fails
                            $two_wheeler_price = '50';
                            
                            // Check if pricing data exists and has 2W category
                            if (isset($pricing_data['2W']) && isset($pricing_data['2W']['price'])) {
                                $two_wheeler_price = $pricing_data['2W']['price'];
                            }
                            echo '₹'.$two_wheeler_price.'<small style="font-size: 1rem; color: #666;">/hour</small>';
                            ?>
                        </h4>
                        <h5 class="name">
                            2 Wheeler Parking
                        </h5>
                        <p>
                            Covered parking for bikes and scooters with dedicated security.
                        </p>
                        <a href="register.php" style="display: inline-block; margin-top: 20px;">
                            Get Started <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end pricing section -->

  <!-- end pricing section -->

  <!-- testimonials section -->
  <section class="client_section layout_padding">
    <div class="container">
      <div class="heading_container col">
        <h2>
          What Says Our <span>Clients</span>
        </h2>
      </div>
      <div class="client_container">
        <div class="carousel-wrap">
          <div class="owl-carousel client_owl-carousel">
            <?php 
            if ($testimonials && $testimonials->num_rows > 0) {
                while ($testimonial = $testimonials->fetch_assoc()): 
                    $profile_img = !empty($testimonial['profile_img']) ? 
                        'uploads/' . htmlspecialchars($testimonial['profile_img']) : 
                        'images/default-user.jpg';
            ?>
            <div class="item">
              <div class="box animate__animated animate__fadeIn">
                <div class="detail-box">
                  <p>
                    <?= nl2br(htmlspecialchars($testimonial['testimonial_text'])) ?>
                  </p>
                </div>
                <div class="client_id">
                  <div class="img-box">
                    <img src="<?= $profile_img ?>" 
                         alt="<?= htmlspecialchars($testimonial['user_name']) ?>" 
                         class="img-1">
                  </div>
                  <div class="name">
                    <h6>
                      <?= htmlspecialchars($testimonial['user_name']) ?>
                    </h6>
                    <p>
                      <?= date('M Y', strtotime($testimonial['created_at'])) ?>
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <?php 
                endwhile;
            } else {
                // Fallback content if no testimonials found
            ?>
            <div class="item">
              <div class="box animate__animated animate__fadeIn">
                <div class="detail-box">
                  <p>
                    "The parking service was excellent! I found a spot quickly right near my office and the mobile payment was seamless. Saved me 15 minutes every morning."
                  </p>
                </div>
                <div class="client_id">
                  <div class="img-box">
                    <img src="images/default-user.jpg" alt="Default User" class="img-1">
                  </div>
                  <div class="name">
                    <h6>
                      Sarah Johnson
                    </h6>
                    <p>
                      <?= date('M Y') ?>
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <?php } ?>
          </div>
        </div>
        <div class="btn-box text-center mt-4">
          <a href="testimonial.php" class="btn btn-primary animate__animated animate__pulse animate__infinite animate__slower">
            View All Testimonials
          </a>
        </div>
      </div>
    </div>
  </section>
  <!-- end testimonials section -->

<?php include("footer.php"); ?>

<script>
  // Intersection Observer for scroll animations
  document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.about_section, .why_section, .pricing_section, .client_section');
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('section-visible');
        }
      });
    }, {
      threshold: 0.1
    });
    
    sections.forEach(section => {
      observer.observe(section);
    });
  });
</script>