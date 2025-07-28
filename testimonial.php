<?php
session_start();
include('includes/db_connect.php');

$testimonials = $conn->query("
    SELECT t.id, t.testimonial_text, t.created_at, 
           u.name as user_name, u.profile_img 
    FROM testimonials t
    JOIN users u ON t.user_id = u.id
    WHERE t.status = 'Approved'
    GROUP BY t.id
    ORDER BY t.created_at DESC
    LIMIT 6
");

$testimonial_data = [];
if ($testimonials && $testimonials->num_rows > 0) {
    while ($row = $testimonials->fetch_assoc()) {
        $testimonial_data[] = $row;
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
  <meta name="keywords" content="car parking, parking solutions, smart parking" />
  <meta name="description" content="Parkease - Smart Parking Solutions for Modern Cities" />
  <meta name="author" content="Parkese Team" />
  <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

  <title>Parkese - Testimonials</title>

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
  
  <style>
    /* Enhanced Testimonial Styles */
    .client_section {
      background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
      padding: 80px 0;
    }
    
    .client_container .box {
      background: white;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      transition: transform 0.3s, box-shadow 0.3s;
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    
    .client_container .box:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.12);
    }
    
    .client_container .detail-box {
      padding: 30px;
      flex-grow: 1;
    }
    
    .client_container p {
      color: #555;
      font-size: 16px;
      line-height: 1.7;
      margin-bottom: 25px;
    }
    
    .client_id {
      padding: 0 30px 30px;
      display: flex;
      align-items: center;
    }
    
    .img-box {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      overflow: hidden;
      margin-right: 20px;
      border: 3px solid #f0f0f0;
    }
    
    .img-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .name h6 {
      font-weight: 700;
      color: #333;
      margin-bottom: 5px;
    }
    
    .name p {
      color: #888;
      font-size: 14px;
      margin-bottom: 0;
    }
    
    .heading_container h2 {
      position: relative;
      display: inline-block;
    }
    
    .heading_container h2 span {
      color: #4e9cff;
    }
    
    .heading_container h2:after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 50px;
      height: 3px;
      background: #4e9cff;
    }
    
    .owl-carousel .owl-nav button.owl-prev,
    .owl-carousel .owl-nav button.owl-next {
      background: rgba(78, 156, 255, 0.2);
      width: 40px;
      height: 40px;
      border-radius: 50%;
      color: #4e9cff;
      font-size: 20px;
      transition: all 0.3s;
    }
    
    .owl-carousel .owl-nav button.owl-prev:hover,
    .owl-carousel .owl-nav button.owl-next:hover {
      background: #4e9cff;
      color: white;
    }
  </style>
</head>

<body class="sub_page">
  <div class="hero_area">
    <div class="bg-box">
      <img src="images/slider-bg.jpg" alt="">
    </div>
    <!-- header section -->
    <header class="header_section">
      <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container">
          <a class="navbar-brand" href="index.html">
            <span>Parkese</span>
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
            <span class=""></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav">
              <li class="nav-item"><a class="nav-link" href="index.PHP">Home</a></li>
              <li class="nav-item"><a class="nav-link" href="about.PHP">About</a></li>
              <li class="nav-item"><a class="nav-link" href="pricing.PHP">Pricing</a></li>
              <li class="nav-item"><a class="nav-link" href="why.PHP">Why Us</a></li>
              <li class="nav-item active"><a class="nav-link" href="testimonial.PHP">Testimonial</a></li>
            </ul>
            <form class="form-inline">
              <button class="btn nav_search-btn" type="submit">
                <i class="fa fa-search" aria-hidden="true"></i>
              </button>
            </form>
          </div>
        </nav>
      </div>
    </header>
    <!-- end header section -->
  </div>

  <!-- client section -->
  <section class="client_section layout_padding">
    <div class="container">
      <div class="heading_container col">
        <h2>What Our <span>Customers</span> Say</h2>
      </div>
      <div class="client_container">
        <div class="carousel-wrap">
          <div class="owl-carousel client_owl-carousel">
            <?php if (!empty($testimonial_data)): 
              foreach ($testimonial_data as $testimonial): 
                $profile_img = !empty($testimonial['profile_img']) 
                  ? 'uploads/' . htmlspecialchars($testimonial['profile_img']) 
                  : 'images/default-user.jpg';
            ?>
              <div class="item">
                <div class="box">
                  <div class="detail-box">
                    <p><?= nl2br(htmlspecialchars($testimonial['testimonial_text'])) ?></p>
                  </div>
                  <div class="client_id">
                    <div class="img-box">
                      <img src="<?= $profile_img ?>" 
                           alt="<?= htmlspecialchars($testimonial['user_name']) ?>" 
                           class="img-1">
                    </div>
                    <div class="name">
                      <h6><?= htmlspecialchars($testimonial['user_name']) ?></h6>
                      <p><?= date('M Y', strtotime($testimonial['created_at'])) ?></p>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; 
            else: ?>
              <div class="item">
                <div class="box">
                  <div class="detail-box">
                    <p>We value our customers' feedback. Be the first to share your experience with Parkease!</p>
                  </div>
                  <div class="client_id">
                    <div class="img-box">
                      <img src="images/default-user.jpg" alt="Default User">
                    </div>
                    <div class="name">
                      <h6>No Reviews Yet</h6>
                      <p>Share your thoughts</p>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>


  
  <?php include("footer.php"); ?>
