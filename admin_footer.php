</div> <!-- End of main-content -->

<!-- Simple Footer -->
<footer class="stylish-footer">
  <div class="footer-wave"></div>
  <div class="container text-center">
    <p>&copy; <?php echo date('Y'); ?> <span class="brand">ParkEase</span>. All rights reserved.</p>
  </div>
</footer>

<style>
  /* Stylish Footer */
  .stylish-footer {
    position: relative;
    background: linear-gradient(135deg, #6F42B8 0%, #6F42B8 100%);
    color: #fff;
    padding: 2rem 0;
    text-align: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    overflow: hidden;
  }
  
  .container.text-center {
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .footer-wave {
    position: absolute;
    top: -10px;
    left: 0;
    width: 100%;
    height: 20px;
    background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"><path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" fill="%23ffffff" opacity=".1"/></svg>');
    background-size: 1200px 100%;
    animation: wave 12s linear infinite;
  }
  
  @keyframes wave {
    0% { background-position-x: 0; }
    100% { background-position-x: 1200px; }
  }
  
  .stylish-footer p {
    margin: 0;
    font-size: 1rem;
    letter-spacing: 0.5px;
    position: relative;
    z-index: 1;
  }
  
  .brand {
    font-weight: 700;
    color: #fff;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    display: inline-block;
  }
</style>
</body>
</html>