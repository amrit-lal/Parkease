<?php
session_start();
include('includes/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['user_name'];
$profile_img = $_SESSION['user_img'] ?? 'default.png';

// Get all testimonials by this user
$user_testimonials = $conn->query("
    SELECT * FROM testimonials 
    WHERE user_id = $user_id
    ORDER BY 
        CASE WHEN status = 'Approved' THEN 1
             WHEN status = 'Pending' THEN 2
             ELSE 3 END,
        created_at DESC
");

// Check if editing existing testimonial
$edit_mode = false;
$testimonial_id = null;
$existing_text = '';
$current_status = 'Pending';

if (isset($_GET['edit'])) {
    $testimonial_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM testimonials WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $testimonial_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $edit_mode = true;
        $testimonial = $result->fetch_assoc();
        $existing_text = $testimonial['testimonial_text'];
        $current_status = $testimonial['status'];
    } else {
        header("Location: post_testimonial.php");
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $testimonial = $conn->real_escape_string($_POST['testimonial']);
    
    if ($edit_mode && $testimonial_id) {
        $sql = "UPDATE testimonials SET testimonial_text = ?, status = 'Pending', updated_at = NOW() WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $testimonial, $testimonial_id, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Your testimonial has been updated successfully! It will be reviewed again before publishing.";
            header("Location: post_testimonial.php?edit=".$testimonial_id);
            exit();
        } else {
            $error = "Error updating testimonial: " . $conn->error;
        }
    } else {
        $sql = "INSERT INTO testimonials (user_id, testimonial_text, status) VALUES (?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $testimonial);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Thank you for your testimonial! It will be reviewed and published soon.";
            header("Location: post_testimonial.php");
            exit();
        } else {
            $error = "Error submitting testimonial: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $edit_mode ? 'Edit' : 'Post' ?> Testimonial | Parkease</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <style>
    :root {
      --primary: #4361ee;
      --primary-light: #4895ef;
      --primary-dark: #3a0ca3;
      --secondary: #7209b7;
      --success: #4cc9f0;
      --warning: #f8961e;
      --danger: #f72585;
      --light: #f8f9fa;
      --dark: #212529;
      --gray: #e9ecef;
      --dark-gray: #6c757d;
      --body-bg: #f5f7fb;
    }

    body {
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
      line-height: 1.6;
      color: var(--dark);
      background-color: var(--body-bg);
      min-height: 100vh;
    }

    /* Header Styles */
    .main-header {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white;
      padding: 1rem 0;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      position: relative;
      z-index: 100;
    }

    .brand-logo {
      font-weight: 700;
      font-size: 1.5rem;
      color: white;
      text-decoration: none;
      display: flex;
      align-items: center;
    }

    .brand-logo i {
      margin-right: 10px;
      font-size: 1.8rem;
    }

    /* Main Container */
    .testimonial-container {
      padding: 2rem 0;
      max-width: 1200px;
      margin: 0 auto;
    }

    .page-header {
      text-align: center;
      margin-bottom: 2.5rem;
      position: relative;
      padding-bottom: 1rem;
    }

    .page-header::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      border-radius: 2px;
    }

    .page-header h2 {
      color: var(--primary-dark);
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .page-header p {
      color: var(--dark-gray);
      font-size: 1.1rem;
    }

    /* Testimonial Form */
    .testimonial-form {
      background: white;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
      padding: 2rem;
      margin-bottom: 2rem;
      border: 1px solid rgba(0, 0, 0, 0.05);
      position: relative;
      overflow: hidden;
    }

    .testimonial-form::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 8px;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
    }

    .user-profile {
      display: flex;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .user-profile img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 1rem;
      border: 4px solid white;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .user-profile h4 {
      color: var(--primary-dark);
      font-weight: 600;
      margin-bottom: 0.25rem;
    }

    .user-profile p {
      color: var(--dark-gray);
      margin-bottom: 0;
    }

    .form-label {
      font-weight: 500;
      color: var(--dark-gray);
    }

    textarea.form-control {
      min-height: 150px;
      resize: vertical;
      border-radius: 12px;
      padding: 1rem;
      border: 1px solid var(--gray);
    }

    textarea.form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.1);
    }

    .form-note {
      font-size: 0.9rem;
      color: var(--dark-gray);
      margin-top: 0.5rem;
    }

    .char-count {
      font-size: 0.85rem;
      color: var(--dark-gray);
      text-align: right;
      margin-top: 0.5rem;
    }

    /* Status Info */
    .status-info {
      margin-bottom: 1.5rem;
      padding: 1rem;
      border-radius: 12px;
      border-left: 4px solid;
      background-color: rgba(248, 249, 250, 0.8);
    }

    .status-pending {
      border-left-color: var(--warning);
      background-color: rgba(248, 150, 30, 0.1);
    }

    .status-approved {
      border-left-color: var(--success);
      background-color: rgba(76, 201, 240, 0.1);
    }

    .status-rejected {
      border-left-color: var(--danger);
      background-color: rgba(247, 37, 133, 0.1);
    }

    .status-badge {
      display: inline-block;
      padding: 0.5em 1em;
      border-radius: 50px;
      font-size: 0.85rem;
      font-weight: 500;
      margin-left: 0.5rem;
    }

    .badge-pending {
      background: linear-gradient(135deg, var(--warning), #ffb347);
      color: var(--dark);
    }

    .badge-approved {
      background: linear-gradient(135deg, var(--success), #4facfe);
      color: white;
    }

    .badge-rejected {
      background: linear-gradient(135deg, var(--danger), #ff6b6b);
      color: white;
    }

    /* Action Buttons */
    .action-buttons {
      margin-top: 1.5rem;
      display: flex;
      gap: 0.75rem;
      flex-wrap: wrap;
    }

    .btn {
      border-radius: 50px;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      border: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .btn-outline-secondary {
      border: 2px solid var(--dark-gray);
      color: var(--dark-gray);
      background: transparent;
    }

    .btn-outline-secondary:hover {
      background: var(--dark-gray);
      color: white;
    }

    .btn-outline-danger {
      border: 2px solid var(--danger);
      color: var(--danger);
      background: transparent;
    }

    .btn-outline-danger:hover {
      background: var(--danger);
      color: white;
    }

    /* Preview Card */
    .preview-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      padding: 1.5rem;
      margin-top: 1.5rem;
      border-left: 4px solid var(--primary);
    }

    .preview-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .preview-header h5 {
      color: var(--primary-dark);
      font-weight: 600;
      margin-bottom: 0;
      display: flex;
      align-items: center;
    }

    .preview-header h5 i {
      margin-right: 0.5rem;
      color: var(--primary);
    }

    .preview-content {
      background: var(--light);
      padding: 1.25rem;
      border-radius: 8px;
    }

    .preview-user {
      display: flex;
      align-items: center;
      margin-bottom: 1rem;
    }

    .preview-user img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 1rem;
      border: 3px solid white;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .preview-user h6 {
      color: var(--primary-dark);
      font-weight: 600;
      margin-bottom: 0.25rem;
    }

    .preview-user small {
      color: var(--dark-gray);
    }

    #preview-text {
      margin-bottom: 0;
      white-space: pre-line;
    }

    /* Testimonial List */
    .testimonial-list {
      margin-top: 2.5rem;
    }

    .testimonial-list h4 {
      color: var(--primary-dark);
      font-weight: 600;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
    }

    .testimonial-list h4 i {
      margin-right: 0.75rem;
      color: var(--primary);
    }

    .testimonial-item {
      background: white;
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
      padding: 1.5rem;
      margin-bottom: 1.25rem;
      border-left: 4px solid;
      transition: all 0.3s ease;
    }

    .testimonial-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    .testimonial-item.pending {
      border-left-color: var(--warning);
    }

    .testimonial-item.approved {
      border-left-color: var(--success);
    }

    .testimonial-item.rejected {
      border-left-color: var(--danger);
    }

    .testimonial-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .testimonial-text {
      margin-bottom: 1rem;
      white-space: pre-line;
    }

    .testimonial-actions {
      margin-top: 1rem;
      display: flex;
      gap: 0.5rem;
    }

    .testimonial-actions .btn {
      padding: 0.375rem 0.75rem;
      font-size: 0.85rem;
    }

    /* Alert Styles */
    .alert {
      border-radius: 12px;
      border: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    /* Footer Styles */
    .main-footer {
      background: linear-gradient(135deg, var(--primary-dark), var(--dark));
      color: white;
      padding: 2rem 0;
      margin-top: 3rem;
    }

    .footer-links a {
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      transition: all 0.3s ease;
      margin: 0 1rem;
    }

    .footer-links a:hover {
      color: white;
      text-decoration: underline;
    }

    .social-icons a {
      color: white;
      font-size: 1.5rem;
      margin: 0 0.5rem;
      transition: all 0.3s ease;
    }

    .social-icons a:hover {
      transform: translateY(-3px);
      color: var(--primary-light);
    }

    @media (max-width: 768px) {
      .testimonial-container {
        padding: 1.5rem;
      }
      
      .user-profile img {
        width: 60px;
        height: 60px;
      }
      
      .action-buttons {
        flex-direction: column;
        gap: 0.5rem;
      }
      
      .btn {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="main-header">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center">
        <a href="dashboard.php" class="brand-logo animate__animated animate__fadeInLeft">
          <i class="fas fa-parking"></i>
          <span>Parkease</span>
        </a>
        <div class="d-flex align-items-center">
          <a href="profile.php" class="text-white me-3 animate__animated animate__fadeIn">
            <i class="fas fa-user-circle me-1"></i> Profile
          </a>
          <a href="logout.php" class="text-white animate__animated animate__fadeIn">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <div class="testimonial-container container">
    <div class="page-header animate__animated animate__fadeIn">
      <h2><i class="fas fa-comment me-2"></i><?= $edit_mode ? 'Edit Your' : 'Share Your' ?> Experience</h2>
      <p><?= $edit_mode ? 'Update your testimonial below' : 'Tell us about your parking experience' ?></p>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <?php if (isset($_SESSION['success'])): ?>
          <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <?php unset($_SESSION['success']); ?>
          </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
          <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeIn">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
        
        <?php if ($edit_mode): ?>
          <div class="status-info status-<?= strtolower($current_status) ?> mb-4 animate__animated animate__fadeIn">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <strong>Current Status:</strong> 
                <span class="status-badge badge-<?= strtolower($current_status) ?>">
                  <?= $current_status ?>
                </span>
              </div>
              <a href="post_testimonial.php" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-plus me-1"></i> New Testimonial
              </a>
            </div>
            <?php if ($current_status == 'Rejected'): ?>
              <p class="mt-2 mb-0">Your testimonial was not approved. You may edit and resubmit it for review.</p>
            <?php elseif ($current_status == 'Approved'): ?>
              <p class="mt-2 mb-0"><strong>Note:</strong> Any changes will require re-approval by our team.</p>
            <?php else: ?>
              <p class="mt-2 mb-0">Your testimonial is pending review by our team.</p>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        
        <div class="testimonial-form animate__animated animate__fadeIn">
          <div class="user-profile">
            <img src="uploads/<?= $profile_img ?>" alt="<?= $name ?>" class="profile-img">
            <div>
              <h4><?= $name ?></h4>
              <p><?= $edit_mode ? 'Edit your testimonial below' : 'Share your experience with our parking services' ?></p>
            </div>
          </div>
          
          <form method="POST" action="post_testimonial.php<?= $edit_mode ? '?edit='.$testimonial_id : '' ?>">
            <div class="form-group mb-3">
              <label for="testimonial" class="form-label">Your Testimonial</label>
              <textarea class="form-control" id="testimonial" name="testimonial" required 
                placeholder="Tell us about your experience with our parking services..."><?= htmlspecialchars($existing_text) ?></textarea>
              <div class="form-note">Minimum 50 characters. Be honest and specific about your experience.</div>
              <div id="char-count" class="char-count"><?= strlen($existing_text) ?>/500 characters</div>
            </div>
            
            <?php if ($edit_mode): ?>
              <div class="alert alert-warning">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Note:</strong> Any changes will require re-approval by our team before being published.
              </div>
            <?php endif; ?>
            
            <div class="action-buttons">
              <button type="submit" class="btn btn-primary" id="submit-btn">
                <i class="fas fa-<?= $edit_mode ? 'sync' : 'paper-plane' ?> me-2"></i>
                <?= $edit_mode ? 'Update & Resubmit for Approval' : 'Submit Testimonial' ?>
              </button>
              <a href="post_testimonial.php" class="btn btn-outline-secondary">
                <i class="fas fa-times me-2"></i>Cancel
              </a>
              
              <?php if ($edit_mode): ?>
                <a href="delete_testimonial.php?id=<?= $testimonial_id ?>" 
                   class="btn btn-outline-danger" 
                   onclick="return confirm('Are you sure you want to delete this testimonial?')">
                  <i class="fas fa-trash me-2"></i>Delete
                </a>
              <?php endif; ?>
            </div>
          </form>
          
          <div class="preview-card mt-4">
            <div class="preview-header">
              <h5><i class="fas fa-eye me-2"></i>Live Preview</h5>
              <span class="badge bg-secondary">Preview</span>
            </div>
            <hr>
            <div class="preview-content">
              <div class="preview-user">
                <img src="uploads/<?= $profile_img ?>" alt="<?= $name ?>" class="user-img">
                <div>
                  <h6 class="mb-0"><?= $name ?></h6>
                  <small class="text-muted"><?= date('M d, Y') ?></small>
                </div>
              </div>
              <p id="preview-text" class="mb-0"><?= nl2br(htmlspecialchars($existing_text)) ?: 'Your testimonial will appear here as you type...' ?></p>
            </div>
          </div>
        </div>

        <!-- User's Testimonials List -->
        <div class="testimonial-list animate__animated animate__fadeIn">
          <h4><i class="fas fa-list me-2"></i>Your Testimonials</h4>
          
          <?php if ($user_testimonials->num_rows > 0): ?>
            <?php while ($testimonial = $user_testimonials->fetch_assoc()): ?>
              <div class="testimonial-item <?= strtolower($testimonial['status']) ?> animate__animated animate__fadeInUp">
                <div class="testimonial-meta">
                  <span class="badge badge-<?= strtolower($testimonial['status']) ?>">
                    <?= $testimonial['status'] ?>
                  </span>
                  <small class="text-muted">
                    <?= date('M d, Y', strtotime($testimonial['created_at'])) ?>
                  </small>
                </div>
                <p class="testimonial-text"><?= nl2br(htmlspecialchars($testimonial['testimonial_text'])) ?></p>
                <div class="testimonial-actions">
                  <a href="post_testimonial.php?edit=<?= $testimonial['id'] ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-edit me-1"></i>Edit
                  </a>
                  <?php if ($testimonial['status'] != 'Approved'): ?>
                    <a href="delete_testimonial.php?id=<?= $testimonial['id'] ?>" 
                       class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Are you sure you want to delete this testimonial?')">
                      <i class="fas fa-trash me-1"></i>Delete
                    </a>
                  <?php endif; ?>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="alert alert-info animate__animated animate__fadeIn">
              <i class="fas fa-info-circle me-2"></i> You haven't submitted any testimonials yet.
            </div>
          <?php endif; ?>
        </div>

        <div class="text-center mt-4 animate__animated animate__fadeIn">
          <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="main-footer">
    <div class="container">
      <div class="row">
        <div class="col-md-6 text-center text-md-start">
          <h5 class="mb-3"><i class="fas fa-parking me-2"></i> Parkease</h5>
          <p class="small">Smart parking solutions for modern cities.</p>
        </div>
        <div class="col-md-6 text-center text-md-end">
          <div class="footer-links mb-3">
            <a href="about.php">About Us</a>
            <a href="contact.php">Contact</a>
            <a href="privacy.php">Privacy Policy</a>
            <a href="terms.php">Terms of Service</a>
          </div>
          <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>
      </div>
      <hr class="my-3 bg-light opacity-25">
      <div class="text-center small">
        &copy; <?= date('Y') ?> Parkease. All rights reserved.
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const textarea = document.getElementById('testimonial');
    const previewText = document.getElementById('preview-text');
    const charCount = document.getElementById('char-count');
    const submitBtn = document.getElementById('submit-btn');
    const maxChars = 500;
    const minChars = 50;
    
    function updatePreview() {
      const text = textarea.value;
      const charLength = text.length;
      
      previewText.innerHTML = text ? text.replace(/\n/g, '<br>') : 'Your testimonial will appear here as you type...';
      charCount.textContent = `${charLength}/${maxChars} characters`;
      
      if (charLength > maxChars) {
        charCount.classList.add('text-danger');
        charCount.classList.remove('text-warning');
        submitBtn.disabled = true;
      } else if (charLength < minChars) {
        charCount.classList.add('text-warning');
        charCount.classList.remove('text-danger');
        submitBtn.disabled = true;
      } else {
        charCount.classList.remove('text-warning', 'text-danger');
        submitBtn.disabled = false;
      }
    }
    
    updatePreview();
    textarea.addEventListener('input', updatePreview);
    
    document.querySelector('form').addEventListener('submit', function(e) {
      const text = textarea.value;
      
      if (text.length < minChars) {
        e.preventDefault();
        alert(`Your testimonial should be at least ${minChars} characters long.`);
        textarea.focus();
      }
      
      if (text.length > maxChars) {
        e.preventDefault();
        alert(`Your testimonial exceeds the maximum of ${maxChars} characters.`);
        textarea.focus();
      }
    });

    // Add animation on scroll
    document.addEventListener('DOMContentLoaded', function() {
      const animateElements = document.querySelectorAll('.animate__animated');
      
      const animateOnScroll = function() {
        animateElements.forEach(element => {
          const elementPosition = element.getBoundingClientRect().top;
          const windowHeight = window.innerHeight;
          
          if (elementPosition < windowHeight - 100) {
            const animationClass = element.classList.item(1);
            element.classList.add(animationClass);
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