<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="parking about, parking company, parking technology" />
  <meta name="description" content="Learn about our smart parking solutions and how we're transforming urban mobility" />
  <meta name="author" content=" Team" />
  <link rel="shortcut icon" href="images/fav.jpg" type="image/x-icon">

  <title>About Us </title>

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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />

  <style>
    /* Content Section Styling */
    .content-section {
      padding: 80px 0;
      background: #f8f9fa;
    }
    
    .section-title {
      font-size: 2.5rem;
      font-weight: 700;
      color: #2c3e50;
      margin-bottom: 30px;
      position: relative;
      text-align: center;
    }
    
    .section-title:after {
      content: '';
      position: absolute;
      bottom: -15px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 3px;
      background: linear-gradient(90deg, #3498db, #2ecc71);
    }
    
    .section-subtitle {
      color: #555;
      text-align: center;
      max-width: 700px;
      margin: 0 auto 50px;
    }
    
    /* Feature Cards */
    .feature-card {
      background: white;
      border-radius: 8px;
      padding: 30px;
      margin-bottom: 30px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      transition: all 0.4s ease;
      opacity: 0;
      transform: translateY(30px);
    }
    
    .feature-card.animated {
      opacity: 1;
      transform: translateY(0);
    }
    
    .feature-card:hover {
      transform: translateY(-5px) !important;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .feature-icon {
      font-size: 2.5rem;
      color: #3498db;
      margin-bottom: 20px;
    }
    
    .feature-title {
      font-size: 1.4rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 15px;
    }
    
    .feature-text {
      color: #555;
      line-height: 1.7;
    }
    
    /* Stats Section */
    .stats-section {
      padding: 60px 0;
      background: linear-gradient(135deg, #3498db, #2ecc71);
      color: white;
    }
    
    .stat-item {
      text-align: center;
      margin-bottom: 30px;
      opacity: 0;
      transform: translateY(30px);
      transition: all 0.4s ease;
    }
    
    .stat-item.animated {
      opacity: 1;
      transform: translateY(0);
    }
    
    .stat-number {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 10px;
    }
    
    .stat-label {
      font-size: 1.1rem;
      opacity: 0.9;
    }
    
    /* Team Section */
    .team-member {
      background: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      margin-bottom: 30px;
      transition: all 0.4s ease;
      opacity: 0;
      transform: translateY(30px);
    }
    
    .team-member.animated {
      opacity: 1;
      transform: translateY(0);
    }
    
    .team-member:hover {
      transform: translateY(-5px) !important;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .team-img {
      width: 100%;
      height: 250px;
      object-fit: cover;
    }
    
    .team-info {
      padding: 20px;
    }
    
    .team-name {
      font-size: 1.3rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 5px;
    }
    
    .team-position {
      color: #3498db;
      font-weight: 500;
      margin-bottom: 10px;
    }
    
    .team-bio {
      color: #555;
      font-size: 0.95rem;
    }
  </style>
</head>

<body class="sub_page">

  <!-- Original Header Section (Completely Unchanged) -->
  <div class="hero_area">
    <div class="bg-box">
      <img src="images/slider-bg.jpg" alt="">
    </div>
    <header class="header_section">
      <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container ">
          <a class="navbar-brand" href="index.php">
            <span>
              parkease
            </span>
          </a>

          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class=""> </span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="about.php">About <span class="sr-only">(current)</span></a>
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
  </div>

  <!-- Enhanced Content Sections -->
  <section class="content-section">
    <div class="container" >
      <div class="bg-box">
      <img src="images/slider-bg.jpg" alt="">
    </div>
      
      <h2 class="section-title">Smart Parking Solutions</h2>
      <p class="section-subtitle">Innovative technology transforming urban mobility through efficient parking management</p>
      
      <div class="row">
        <div class="col-md-6">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa fa-sensor"></i>
            </div>
            <h3 class="feature-title">Real-Time Monitoring</h3>
            <p class="feature-text">
              Our IoT sensors provide live updates on parking space availability across all locations, reducing search time by an average of 72%.
            </p>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa fa-mobile"></i>
            </div>
            <h3 class="feature-title">Mobile Integration</h3>
            <p class="feature-text">
              Reserve and pay for parking directly through our app, with digital receipts and remote time extension capabilities.
            </p>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa fa-shield"></i>
            </div>
            <h3 class="feature-title">Security Systems</h3>
            <p class="feature-text">
              24/7 monitored facilities with license plate recognition and emergency call boxes for complete peace of mind.
            </p>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa fa-chart-line"></i>
            </div>
            <h3 class="feature-title">Data Analytics</h3>
            <p class="feature-text">
              Smart algorithms optimize space utilization and provide insights for urban planning and traffic management.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="stats-section">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <div class="stat-item">
            <div class="stat-number">500+</div>
            <div class="stat-label">Parking Locations</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-item">
            <div class="stat-number">95%</div>
            <div class="stat-label">Customer Satisfaction</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-item">
            <div class="stat-number">24/7</div>
            <div class="stat-label">Support Availability</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="content-section">
    <div class="container">
      <h2 class="section-title">Our Leadership Team</h2>
      <p class="section-subtitle">The experienced professionals driving our parking innovation</p>
      
      <div class="row">
        <div class="col-md-4">
          <div class="team-member">
            <img src="images/team1.jpeg" alt="AMRIT" class="team-img">
            <div class="team-info">
              <h4 class="team-name">AMRIT </h4>
              <div class="team-position">CEO & Founder</div>
              <p class="team-bio">15 years experience in urban mobility solutions and parking infrastructure.</p>
            </div>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="team-member">
            <img src="images/team2.jpeg" alt="AMRIT LAL" class="team-img">
            <div class="team-info">
              <h4 class="team-name">AMRIT LAL</h4>
              <div class="team-position">Chief Technology Officer</div>
              <p class="team-bio">Leads our technology development with expertise in IoT and smart city solutions.</p>
            </div>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="team-member">
            <img src="images/team3.jpeg" alt="RAM" class="team-img">
            <div class="team-info">
              <h4 class="team-name"> RAM</h4>
              <div class="team-position">Chief Operations Officer</div>
              <p class="team-bio">Oversees nationwide operations and facility management.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Original Footer Section (Completely Unchanged) -->
  <?php include("footer.php"); ?>

  <script>
    // Animation trigger on scroll
    document.addEventListener('DOMContentLoaded', function() {
      const animateOnScroll = function() {
        const elements = document.querySelectorAll('.feature-card, .stat-item, .team-member');
        
        elements.forEach(element => {
          const elementPosition = element.getBoundingClientRect().top;
          const windowHeight = window.innerHeight;
          
          if (elementPosition < windowHeight - 100) {
            element.classList.add('animated');
          }
        });
      };
      
      // Initial check
      animateOnScroll();
      
      // Check on scroll
      window.addEventListener('scroll', animateOnScroll);
    });
  </script>
</body>
</html>