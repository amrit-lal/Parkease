<?php include("admin_header.php"); ?>
<?php


$message_id = $_GET['id'] ?? 0;
$message = [];

if ($message_id) {
    // Mark as read when viewing
    $conn->query("UPDATE contact_form SET is_read=1 WHERE id=".(int)$message_id);
    
    // Get message details
    $message = $conn->query("SELECT * FROM contact_form WHERE id=".(int)$message_id)->fetch_assoc();
}

if (!$message) {
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Message | Parkease Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary: #4361ee;
      --primary-dark: #3a56d4;
      --secondary: #3f37c9;
      --success: #4cc9f0;
      --danger: #f72585;
      --warning: #f8961e;
      --info: #4895ef;
      --light: #f8f9fa;
      --dark: #212529;
      --gray: #6c757d;
      --white: #ffffff;
    }
    
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: #f5f7fb;
      color: #4a5568;
      line-height: 1.6;
      padding-top: 60px;
      transition: margin-left 0.3s ease;
    }
    
    .main-content {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1.5rem;
      transition: all 0.3s;
    }
    
    .page-header {
      margin-bottom: 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
    }
    
    .page-title {
      font-size: 1.75rem;
      font-weight: 700;
      color: var(--dark);
      margin: 0;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .content-section {
      background: var(--white);
      border-radius: 0.75rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      margin-bottom: 1.5rem;
      overflow: hidden;
    }
    
    .section-header {
      padding: 1rem 1.5rem;
      border-bottom: 1px solid #e2e8f0;
      background-color: var(--primary);
      color: white;
    }
    
    .section-title {
      font-size: 1.25rem;
      font-weight: 600;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .section-body {
      padding: 1.5rem;
    }
    
    .message-detail {
      margin-bottom: 1.5rem;
    }
    
    .message-content {
      background-color: #f8f9fa;
      padding: 1.5rem;
      border-radius: 0.5rem;
      white-space: pre-wrap;
      overflow-wrap: break-word;
    }
    
    .section-footer {
      padding: 1rem 1.5rem;
      border-top: 1px solid #e2e8f0;
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
    }
    
    /* Sidebar responsive adjustments */
    @media (max-width: 992px) {
      body {
        padding-top: 56px;
      }
      
      .main-content {
        margin-left: 0;
        width: 100%;
      }
      
      .sidebar-collapsed .main-content {
        margin-left: 0;
      }
    }
    
    @media (max-width: 768px) {
      .main-content {
        padding: 0 1rem;
      }
      
      .section-header, .section-body {
        padding: 1rem;
      }
      
      .page-title {
        font-size: 1.5rem;
      }
      
      .message-content {
        padding: 1rem;
      }
    }
    
    @media (max-width: 576px) {
      .page-title {
        font-size: 1.25rem;
      }
      
      .section-title {
        font-size: 1rem;
      }
      
      .section-footer {
        flex-direction: column;
      }
      
      .section-footer .btn {
        width: 100%;
      }
    }
  </style>
</head>
<body>

<?php include('admin_sidebar.php'); ?>

<div class="main-content" id="mainContent">
  <div class="page-header">
    <h1 class="page-title">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
      </svg>
      Message Details
    </h1>
    <a href="admin_dashboard.php" class="btn btn-outline-secondary">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M19 12H5M12 19l-7-7 7-7"></path>
      </svg>
      Back to Dashboard
    </a>
  </div>

  <div class="content-section">
    <div class="section-header">
      <h3 class="section-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <?= htmlspecialchars($message['subject']) ?>
      </h3>
    </div>
    <div class="section-body">
      <div class="message-detail">
        <strong>From:</strong> <?= htmlspecialchars($message['name']) ?> &lt;<?= htmlspecialchars($message['email']) ?>&gt;
      </div>
      <div class="message-detail">
        <strong>Received:</strong> <?= date('M d, Y H:i', strtotime($message['created_at'])) ?>
      </div>
      <div class="message-detail">
        <strong>Message:</strong>
        <div class="message-content mt-2">
          <?= nl2br(htmlspecialchars($message['message'])) ?>
        </div>
      </div>
    </div>
    <div class="section-footer">
      <a href="mailto:<?= htmlspecialchars($message['email']) ?>" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
          <polyline points="22,6 12,13 2,6"></polyline>
        </svg>
        Reply
      </a>
      <a href="admin_dashboard.php" class="btn btn-outline-secondary">Close</a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // This ensures the sidebar toggle will work properly
  document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.getElementById('mainContent');
    
    // This function will be called when the sidebar is toggled
    function adjustMainContent() {
      if (sidebar.classList.contains('active')) {
        mainContent.style.marginLeft = '250px';
      } else {
        mainContent.style.marginLeft = '0';
      }
    }
    
    // Initial adjustment
    adjustMainContent();
    
    // You might need to add an event listener for the toggle button
    // This would depend on how your admin_sidebar.php implements the toggle
    const toggleBtn = document.querySelector('.sidebar-toggle');
    if (toggleBtn) {
      toggleBtn.addEventListener('click', adjustMainContent);
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
      if (window.innerWidth < 992) {
        mainContent.style.marginLeft = '0';
      } else if (sidebar.classList.contains('active')) {
        mainContent.style.marginLeft = '250px';
      }
    });
  });
</script>
<?php include("admin_header.php"); ?>
</body>
</html>
