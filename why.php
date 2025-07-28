<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="parking benefits, parking advantages, smart parking" />
  <meta name="description" content="Discover why our parking system is the best choice for your parking needs" />
  <meta name="author" content="Parkease Team" />
  <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

  <title>Why Choose Us | Parkease</title>

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
    /* Custom styling for why section - with original colors */
    .why_section .box {
      height: 100%;
      display: flex;
      flex-direction: column;
      transition: all 0.3s ease;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      background: #155263; /* Original light background */
      margin-bottom: 30px;
      border: 1px solid #e0e0e0; /* Original border */
    }
    
    .why_section .box:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    
    .why_section .img-box {
      height: 120px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      background: #f9f9f9; /* Match card background */
    }
    
    .why_section .img-box img {
      max-height: 80px;
      width: auto;
      transition: all 0.3s ease;
    }
    
    .why_section .box:hover .img-box img {
      transform: scale(1.1);
    }
    
    .why_section .detail-box {
      padding: 25px;
      flex: 1;
      display: flex;
      flex-direction: column;
      background: #ffffff; /* Original white background for content */
    }
    
    .why_section .detail-box h4 {
      margin-bottom: 15px;
      color: #222831; /* Original dark color */
      font-weight: 700;
    }
    
    .why_section .detail-box p {
      color: #555555; /* Original text color */
      margin-bottom: 0;
      flex: 1;
    }
    
    /* Animation classes */
    .animate-box {
      opacity: 0;
    }
    
    .fadeInUp {
      animation-name: fadeInUp;
    }
    
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body class="sub_page">

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
              Parkease
            </span>
          </a>

          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class=""> </span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link" href="index.php">Home </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="about.php"> About</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="pricing.php">Pricing</a>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="why.php">Why Us <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="testimonial.php">Testimonial</a>
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

  <!-- why section -->
  <section class="why_section layout_padding">
    <div class="container">
      <div class="col-md-10 px-0">
        <div class="heading_container">
          <h2>
            Why Choose Our Parking System
          </h2>
          <p>
            We offer the most comprehensive, user-friendly parking solution with benefits that go beyond just finding a spot.
          </p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box animate-box" data-animate-effect="fadeInUp" data-delay="0.1s">
            <div class="img-box">
              <img src="images/w1.png" alt="Real-time availability icon">
            </div>
            <div class="detail-box">
              <h4>
                Real-Time Availability
              </h4>
              <p>
                Our network of sensors provides live updates on parking space availability across all our locations. Know exactly where to find parking before you leave, saving you time and reducing traffic congestion from drivers searching for spots.
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box animate-box" data-animate-effect="fadeInUp" data-delay="0.2s">
            <div class="img-box">
              <img src="images/w2.png" alt="Mobile payments icon">
            </div>
            <div class="detail-box">
              <h4>
                Secure Payments
              </h4>
              <p>
                Pay for parking directly when you come to park your vehicle in any mode like cash, card or UPI payment. No advance is taken from you.
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box animate-box" data-animate-effect="fadeInUp" data-delay="0.3s">
            <div class="img-box">
              <img src="images/w3.png" alt="Member benefits icon">
            </div>
            <div class="detail-box">
              <h4>
                Priority Member Benefits
              </h4>
              <p>
                Our regular users enjoy exclusive benefits including guaranteed spots during peak hours, 10% discount on monthly plans, early access to new locations, and personalized parking recommendations based on your schedule and preferences.
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box animate-box" data-animate-effect="fadeInUp" data-delay="0.4s">
            <div class="img-box">
              <img src="images/sec.jpg" alt="Safety icon">
            </div>
            <div class="detail-box">
              <h4>
                24/7 Monitored Security
              </h4>
              <p>
                All our parking facilities are equipped with surveillance cameras, emergency call boxes, and regular security patrols. Our license plate recognition system ensures only authorized vehicles enter restricted areas.
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box animate-box" data-animate-effect="fadeInUp" data-delay="0.5s">
            <div class="img-box">
              <img src="images/eco.jpg" alt="Sustainability icon">
            </div>
            <div class="detail-box">
              <h4>
                Eco-Friendly Initiatives
              </h4>
              <p>
                We're committed to sustainability with EV charging stations, solar-powered facilities, and programs that reward carpooling and low-emission vehicles. Our system reduces unnecessary driving by 30% on average.
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box animate-box" data-animate-effect="fadeInUp" data-delay="0.6s">
            <div class="img-box">
              <img src="images/support.jpg" alt="Support icon">
            </div>
            <div class="detail-box">
              <h4>
                Dedicated Support
              </h4>
              <p>
                Our customer service team is available 24/7 via phone, chat, and in-app messaging. We average under 2 minute response times and resolve 95% of issues on first contact. Premium members get a dedicated account manager.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- end why section -->

  <?php include("footer.php"); ?>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    // Animation trigger on scroll
    $(document).ready(function() {
      function animateOnScroll() {
        $('.animate-box').each(function() {
          var elementPos = $(this).offset().top;
          var scrollPos = $(window).scrollTop();
          var windowHeight = $(window).height();
          
          if (scrollPos > elementPos - windowHeight + 100) {
            var delay = $(this).data('delay') || '0s';
            $(this).css({
              'animation-delay': delay,
              'animation-duration': '1s',
              'animation-fill-mode': 'both'
            });
            $(this).addClass($(this).data('animate-effect'));
          }
        });
      }
      
      // Run once on page load
      animateOnScroll();
      
      // Run on scroll
      $(window).scroll(function() {
        animateOnScroll();
      });
    });
  </script>
</body>
</html>