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

  <title>Parkeaase</title>

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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />

  <style>
    :root {
      --primary-color: #4361ee;
      --secondary-color: #3f37c9;
      --accent-color: #4895ef;
      --dark-color: #1b263b;
      --light-color: #f8f9fa;
    }

    .pricing_section .bg-box {
      position: absolute;
      width: 100%;
      height: 100%;
      z-index: -1;
    }

    .pricing-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 40px 30px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      margin: 20px;
      transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      overflow: hidden;
      position: relative;
      z-index: 1;
      border: none;
    }

    .pricing-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
      transition: all 0.3s ease;
    }

    .pricing-card:hover {
      transform: translateY(-10px) scale(1.02);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    }

    .pricing-card:hover::before {
      height: 10px;
    }

    .pricing-icon {
      font-size: 3.5rem;
      margin-bottom: 20px;
      color: var(--primary-color);
      transition: all 0.3s ease;
    }

    .pricing-card:hover .pricing-icon {
      transform: scale(1.1);
      color: var(--accent-color);
    }

    .price-value {
      font-size: 2.8rem;
      font-weight: 700;
      background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin: 15px 0;
      position: relative;
      display: inline-block;
    }

    .price-value small {
      font-size: 1rem;
      font-weight: 400;
      background: none;
      -webkit-text-fill-color: #666;
    }

    .book-now-btn {
      display: inline-block;
      margin-top: 25px;
      color: white;
      background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
      padding: 12px 30px;
      border-radius: 50px;
      text-decoration: none;
      transition: all 0.4s ease;
      border: none;
      font-weight: 600;
      letter-spacing: 0.5px;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    .book-now-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 0;
      height: 100%;
      background: linear-gradient(135deg, var(--accent-color), var(--secondary-color));
      transition: all 0.4s ease;
      z-index: -1;
    }

    .book-now-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3);
    }

    .book-now-btn:hover::before {
      width: 100%;
    }

    .pricing-features {
      list-style: none;
      padding: 0;
      margin: 20px 0;
    }

    .pricing-features li {
      padding: 8px 0;
      position: relative;
      padding-left: 25px;
    }

    .pricing-features li::before {
      content: '\f00c';
      font-family: 'FontAwesome';
      position: absolute;
      left: 0;
      color: var(--accent-color);
    }

    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0px); }
    }

    .floating {
      animation: float 3s ease-in-out infinite;
    }

    .heading_container.heading_center h2 {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 50px;
      position: relative;
      display: inline-block;
    }

    .heading_container.heading_center h2::after {
      content: '';
      position: absolute;
      bottom: -15px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
      border-radius: 2px;
    }

    /* Pulse animation for attention */
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }

    .pulse {
      animation: pulse 2s infinite;
    }

    /* Ribbon for popular plan */
    .popular-badge {
      position: absolute;
      top: 15px;
      right: -30px;
      background: var(--accent-color);
      color: white;
      padding: 5px 30px;
      transform: rotate(45deg);
      font-size: 0.8rem;
      font-weight: bold;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
  </style>
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
              Parkeaase
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
              <li class="nav-item active">
                <a class="nav-link" href="pricing.PHP">Pricing <span class="sr-only">(current)</span></a>
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

  <!-- pricing section -->
  <section class="pricing_section layout_padding">
    <div class="bg-box">
      <img src="images/pricing-bg.jpg" alt="">
    </div>
    <div class="container">
      <div class="heading_container heading_center animate__animated animate__fadeInDown">
        <h2>
          Our <span class="text-primary">Parking</span> Rates
        </h2>
      </div>
      <div class="row justify-content-center">
        <?php
        // Include database connection
        include('includes/db_connect.php');

        
        // Fetch pricing data from database
        $sql = "SELECT * FROM pricing";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Determine details based on category
                if ($row['category'] == '2W') {
                    $icon = 'fa-motorcycle';
                    $title = '2 Wheeler Parking';
                    $description = 'Covered parking for bikes and scooters with dedicated security.';
                    $features = [
                        '24/7 Security Monitoring',
                        'Covered Parking Space',
                        'Easy Access Location',
                        'Instant Booking'
                    ];
                    $popular = false;
                } else {
                    $icon = 'fa-car';
                    $title = '4 Wheeler Parking';
                    $description = 'Secure parking for cars and SUVs with 24/7 surveillance and easy access.';
                    $features = [
                        '24/7 Video Surveillance',
                        'Well-lit Parking Area',
                        'EV Charging Available',
                        'Valet Service Option'
                    ];
                    $popular = true;
                }
                
                echo '<div class="col-lg-5 col-md-6 mb-4 animate__animated animate__fadeInUp">
                        <div class="pricing-card h-100 '.($popular ? 'pulse' : '').'">
                          '.($popular ? '<div class="popular-badge">MOST POPULAR</div>' : '').'
                          <div class="text-center">
                            <i class="fa '.$icon.' pricing-icon floating"></i>
                            <h3 class="my-3">'.$title.'</h3>
                            <div class="price-value">₹'.$row['price'].' <small>per hour</small></div>
                            <p class="mb-4">'.$description.'</p>
                            <ul class="pricing-features">';
                              
                foreach ($features as $feature) {
                    echo '<li>'.$feature.'</li>';
                }
                
                echo '</ul>
                            <a href="register.php" class="book-now-btn">
                              Book Now <i class="fa fa-arrow-right ml-2"></i>
                            </a>
                          </div>
                        </div>
                      </div>';
            }
        } else {
            // Fallback to default pricing if no data found
            echo '<div class="col-lg-5 col-md-6 mb-4 animate__animated animate__fadeInUp">
                    <div class="pricing-card h-100">
                      <div class="text-center">
                        <i class="fa fa-car pricing-icon floating"></i>
                        <h3 class="my-3">4 Wheeler Parking</h3>
                        <div class="price-value">₹150 <small>per hour</small></div>
                        <p class="mb-4">Secure parking for cars and SUVs with 24/7 surveillance and easy access.</p>
                        <ul class="pricing-features">
                          <li>24/7 Video Surveillance</li>
                          <li>Well-lit Parking Area</li>
                          <li>EV Charging Available</li>
                          <li>Valet Service Option</li>
                        </ul>
                        <a href="register.php" class="book-now-btn">
                          Book Now <i class="fa fa-arrow-right ml-2"></i>
                        </a>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-5 col-md-6 mb-4 animate__animated animate__fadeInUp" data-wow-delay="0.2s">
                    <div class="pricing-card h-100 pulse">
                      <div class="popular-badge">MOST POPULAR</div>
                      <div class="text-center">
                        <i class="fa fa-motorcycle pricing-icon floating"></i>
                        <h3 class="my-3">2 Wheeler Parking</h3>
                        <div class="price-value">₹100 <small>per hour</small></div>
                        <p class="mb-4">Covered parking for bikes and scooters with dedicated security.</p>
                        <ul class="pricing-features">
                          <li>24/7 Security Monitoring</li>
                          <li>Covered Parking Space</li>
                          <li>Easy Access Location</li>
                          <li>Instant Booking</li>
                        </ul>
                        <a href="register.php" class="book-now-btn">
                          Book Now <i class="fa fa-arrow-right ml-2"></i>
                        </a>
                      </div>
                    </div>
                  </div>';
        }
        $conn->close();
        ?>
      </div>
    </div>
  </section>
  <!-- end pricing section -->

  <?php include("footer.php"); ?>

  <!-- jQuery -->
  <script src="js/jquery-3.4.1.min.js"></script>
  <!-- bootstrap js -->
  <script src="js/bootstrap.js"></script>
  <!-- custom js -->
  <script src="js/custom.js"></script>
  
  <script>
    // Add animation on scroll
    $(document).ready(function() {
      // Animate elements when they come into view
      $(window).scroll(function() {
        $('.pricing-card').each(function() {
          var cardPosition = $(this).offset().top;
          var scrollPosition = $(window).scrollTop() + $(window).height();
          
          if (cardPosition < scrollPosition) {
            $(this).addClass('animate__animated animate__fadeInUp');
          }
        });
      });
      
      // Trigger scroll event on page load
      $(window).trigger('scroll');
      
      // Add hover effect to pricing cards
      $('.pricing-card').hover(
        function() {
          $(this).addClass('animate__pulse');
        },
        function() {
          $(this).removeClass('animate__pulse');
        }
      );
    });
  </script>
</body>
</html>