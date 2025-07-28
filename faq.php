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
  <link rel="shortcut icon" href="images/fav.jpg" type="image/x-icon">

  <title>Parkease - FAQs</title>

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
    .faq-section {
      padding: 60px 0;
      background: #f9f9f9;
    }
    .heading_container h2 {
      font-size: 2.5rem;
      background: linear-gradient(135deg, #0066cc 0%, #004080 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: fadeInDown 0.8s ease-out;
    }
    .heading_container p {
      color: #666;
      max-width: 700px;
      margin: 0 auto 30px;
      font-size: 1.1rem;
      animation: fadeIn 1s ease-out 0.3s both;
    }
    .faq-item {
      margin-bottom: 30px;
      border: 1px solid #eee;
      border-radius: 8px;
      padding: 25px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
      background: #fff;
      transform: translateY(20px);
      opacity: 0;
      transition: all 0.5s ease-out;
    }
    .faq-item.animated {
      transform: translateY(0);
      opacity: 1;
    }
    .faq-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .faq-question {
      font-weight: 700;
      color: #333;
      margin-bottom: 15px;
      font-size: 1.2rem;
      position: relative;
      padding-left: 30px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .faq-question::before {
      content: 'Q';
      position: absolute;
      left: 0;
      top: 0;
      color: #f0b913;
      font-weight: bold;
      font-size: 1.3rem;
    }
    .faq-question:hover {
      color: #0066cc;
    }
    .faq-answer {
      color: #666;
      padding-left: 30px;
      position: relative;
      line-height: 1.7;
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease, padding-top 0.3s ease;
    }
    .faq-answer::before {
      content: 'A';
      position: absolute;
      left: 0;
      top: 0;
      color: #0066cc;
      font-weight: bold;
    }
    .faq-item.active .faq-answer {
      max-height: 500px;
      padding-top: 15px;
    }
    .faq-item:nth-child(1) { transition-delay: 0.1s; }
    .faq-item:nth-child(2) { transition-delay: 0.2s; }
    .faq-item:nth-child(3) { transition-delay: 0.3s; }
    .faq-item:nth-child(4) { transition-delay: 0.4s; }
    .faq-item:nth-child(5) { transition-delay: 0.5s; }
    .faq-item:nth-child(6) { transition-delay: 0.6s; }
    
    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
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
              <li class="nav-item">
                <a class="nav-link" href="about.PHP">About</a>
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
              <li class="nav-item active">
                <a class="nav-link" href="faq.PHP">FAQs <span class="sr-only">(current)</span></a>
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

  <!-- FAQ section -->
  <section class="faq-section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          Frequently Asked Questions
        </h2>
        <p>
          Find answers to common questions about our parking services and policies.
        </p>
      </div>
      <div class="row">
        <div class="col-md-8 mx-auto">
          <div class="faq-item" id="faq1">
            <div class="faq-question">1. How do I reserve a parking space?</div>
            <div class="faq-answer">
              You can reserve a parking space through our website or mobile app by selecting your desired location, date, and time. Payment can be made online for a hassle-free experience.
            </div>
          </div>
          
          <div class="faq-item" id="faq2">
            <div class="faq-question">2. What are your operating hours?</div>
            <div class="faq-answer">
              Our parking facilities are open 24/7. However, some locations may have specific operating hours which will be clearly indicated during the booking process.
            </div>
          </div>
          
          <div class="faq-item" id="faq3">
            <div class="faq-question">3. Can I extend my parking time?</div>
            <div class="faq-answer">
              Yes, you can extend your parking time through our app or website. Additional charges will apply based on the current rates at the time of extension.
            </div>
          </div>
          
          <div class="faq-item" id="faq4">
            <div class="faq-question">4. What payment methods do you accept?</div>
            <div class="faq-answer">
              We accept all major credit cards, debit cards, mobile payments, and in some locations, cash payments at the parking kiosk.
            </div>
          </div>
          
          <div class="faq-item" id="faq5">
            <div class="faq-question">5. What if I lose my ticket?</div>
            <div class="faq-answer">
              If you've booked online, you can retrieve your booking details through your account. For cash payments at the facility, please contact the parking attendant for assistance.
            </div>
          </div>
          
          <div class="faq-item" id="faq6">
            <div class="faq-question">6. Is there a cancellation policy?</div>
            <div class="faq-answer">
              Yes, cancellations made at least 2 hours before your booking time will receive a full refund. Late cancellations may incur a fee.
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end FAQ section -->

  <?php include ("footer.php"); ?>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Animate FAQ items on load
      const faqItems = document.querySelectorAll('.faq-item');
      faqItems.forEach((item, index) => {
        setTimeout(() => {
          item.classList.add('animated');
        }, 100 * (index + 1));
      });
      
      // Add click functionality to FAQ questions
      const faqQuestions = document.querySelectorAll('.faq-question');
      faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
          const faqItem = this.parentElement;
          faqItem.classList.toggle('active');
          
          // Close other open FAQs
          if (faqItem.classList.contains('active')) {
            faqItems.forEach(item => {
              if (item !== faqItem && item.classList.contains('active')) {
                item.classList.remove('active');
              }
            });
          }
        });
      });
      
      // Open first FAQ by default
      setTimeout(() => {
        document.getElementById('faq1').classList.add('active');
      }, 1500);
    });
  </script>
</body>
</html>