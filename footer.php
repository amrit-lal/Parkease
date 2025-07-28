
<style>
/* Footer Styles */
.info_section {
  background-color: #155263;
  color: #155263;
  padding: 60px 0 20px;
  font-family: 'Roboto', sans-serif;
}

.info_section h4 {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 20px;
  color: #fff;
}

.footer-links {
  display: flex;
  flex-direction: column;
}

.footer-links a {
  color: #ccc;
  margin-bottom: 10px;
  text-decoration: none;
  transition: all 0.3s;
  font-size: 15px;
}

.footer-links a:hover {
  color: #fff;
  padding-left: 5px;
}

.info_section p {
  color: #aaa;
  font-size: 14px;
  line-height: 1.6;
}

.info_form input {
  width: 100%;
  padding: 10px 15px;
  margin-bottom: 15px;
  border: none;
  border-radius: 4px;
  font-size: 14px;
}

.info_form button {
  background-color: #f0b913;
  color: #222;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s;
  font-weight: 500;
  font-size: 15px;
}

.info_form button:hover {
  background-color: #e0a800;
}

.social_box {
  margin-top: 20px;
  display: flex;
}

.social_box a {
  display: flex;
  justify-content: center;
  align-items: center;
  color: #fff;
  background-color: #155263;
  width: 35px;
  height: 35px;
  border-radius: 50%;
  margin-right: 10px;
  font-size: 16px;
  transition: all 0.3s;
}

.social_box a:hover {
  background-color: #f0b913;
  color: #222;
}

.contact_nav a {
  display: flex;
  align-items: center;
  color: #ccc;
  margin-bottom: 10px;
  text-decoration: none;
  font-size: 14px;
  transition: all 0.3s;
}

.contact_nav a:hover {
  color: #fff;
}

.contact_nav a i {
  margin-right: 10px;
  font-size: 16px;
  color: #f0b913;
}

.footer_section {
  background-color: #b6d1d8ff;
  padding: 20px 0;
  text-align: center;
  font-family: 'Roboto', sans-serif;
}

.footer_section p {
  color: #155263;
  margin: 0;
  font-size: 14px;
}

.footer_section a {
  color: #f0b913;
  text-decoration: none;
  transition: all 0.3s;
}

.footer_section a:hover {
  color: #13b43bff;
  text-decoration: underline;
}

/* Responsive Styles */
@media (max-width: 991px) {
  .info_section {
    padding: 40px 0 15px;
  }
  
  .info_col {
    margin-bottom: 30px;
  }
}

@media (max-width: 767px) {
  .info_section {
    padding: 30px 0 10px;
  }
  
  .info_section h4 {
    font-size: 18px;
  }
  
  .social_box {
    justify-content: center;
  }
}
</style>

<!-- info section -->
<section class="info_section">
  <div class="container">
    <div class="info_top">
      <div class="row">
        <div class="col-md-6 col-lg-3 info_col">
          <div class="info_detail">
            <h4>ABOUT US</h4>
            <div class="footer-links">
              <a href="about.php">About Us</a>
              <a href="faq.php">FAQs</a>
              <a href="privacy.php">Privacy Policy</a>
              <a href="terms.php">Terms of Use</a>
              <a href="admin_login.php">Admin Login</a>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3 info_col">
          <div class="info_detail">
            <h4>ONLINE BOOKING</h4>
            <p>
              Book your parking space anytime, anywhere with our easy-to-use online system. Get instant confirmation and guaranteed parking spots.
            </p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3 info_col">
          <h4>
            <a href="contact.php" style="color: inherit;">CONTACT US</a>
          </h4>
          <p>Have questions? We're here to help with all your parking needs.</p>
          <div class="contact_nav">
            <a href="https://maps.google.com" target="_blank">
              <i class="fa fa-map-marker" aria-hidden="true"></i>
              <span>123 Parking St, jalandhar</span>
            </a>
            <a href="tel:+01123455678990">
              <i class="fa fa-phone" aria-hidden="true"></i>
              <span>+01 123 4556 78990</span>
            </a>
            <a href="mailto:info@parkease.com">
              <i class="fa fa-envelope" aria-hidden="true"></i>
              <span>info@parkease.com</span>
            </a>
          </div>
        </div>
        <div class="col-md-6 col-lg-3 info_col">
          <div class="info_form">
            <h4>NEWSLETTER</h4>
            <form action="#" method="post">
              <input type="email" placeholder="Enter Email Address" required />
              <button type="submit">Subscribe</button>
            </form>
            <p>Get parking tips, special offers, and updates directly to your inbox.</p>
            <div class="social_box">
              <a href="#" target="_blank">
                <i class="fa fa-facebook" aria-hidden="true"></i>
              </a>
              <a href="#" target="_blank">
                <i class="fa fa-twitter" aria-hidden="true"></i>
              </a>
              <a href="#" target="_blank">
                <i class="fa fa-linkedin" aria-hidden="true"></i>
              </a>
              <a href="#" target="_blank">
                <i class="fa fa-instagram" aria-hidden="true"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- end info_section -->

<!-- footer section -->
<footer class="footer_section">
  <div class="container">
    <p>
      &copy; <span id="displayYear"></span> All Rights Reserved By
      <a href="index.php">Parkease</a> | 
      <a href="privacy.php">Privacy Policy</a> | 
      <a href="terms.php">Terms & Conditions</a>
    </p>
  </div>
</footer>
<!-- footer section -->

<!-- jQery -->
<script src="js/jquery-3.4.1.min.js"></script>
<!-- popper js -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
</script>
<!-- nice select -->
<script src="js/jquery.nice-select.min.js"></script>
<!-- bootstrap js -->
<script src="js/bootstrap.js"></script>
<!-- owl slider -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js">
</script>
<!-- custom js -->
<script src="js/custom.js"></script>

<script>
// Update copyright year automatically
document.getElementById('displayYear').textContent = new Date().getFullYear();
</script>