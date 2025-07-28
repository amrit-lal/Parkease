
<?php include("admin_header.php"); ?>
<?php

// Get all contact messages
$messages = $conn->query("SELECT * FROM contact_form ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Messages | Parkease Admin</title>
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
    }
    
    .main-content {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1.5rem;
    }
    
    .page-header {
      margin-bottom: 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
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
    
    .table-responsive {
      overflow-x: auto;
    }
    
    .table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .table th {
      background-color: #f8f9fa;
      padding: 0.75rem;
      text-align: left;
    }
    
    .table td {
      padding: 0.75rem;
      border-top: 1px solid #e2e8f0;
      vertical-align: middle;
    }
    
    .table tr:hover {
      background-color: #f8fafc;
    }
    
    .badge {
      font-size: 0.75rem;
      font-weight: 600;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
    }
    
    .badge.bg-primary {
      background-color: var(--primary);
    }
    
    .badge.bg-secondary {
      background-color: var(--gray);
    }
    
    .btn-sm {
      padding: 0.25rem 0.5rem;
      font-size: 0.875rem;
    }
    
    @media (max-width: 768px) {
      .main-content {
        padding: 0 1rem;
      }
      
      .section-header, .section-body {
        padding: 1rem;
      }
      
      .table td, .table th {
        padding: 0.5rem;
        font-size: 0.875rem;
      }
      
      .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }
    }
  </style>
</head>
<body>

<?php include('admin_sidebar.php'); ?>

<div class="main-content">
  <div class="page-header">
    <h1 class="page-title">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
        <polyline points="22,6 12,13 2,6"></polyline>
      </svg>
      Contact Messages
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
        All Messages
      </h3>
    </div>
    <div class="section-body">
      <?php if ($messages->num_rows > 0): ?>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Subject</th>
                <th>From</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($message = $messages->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($message['subject']) ?></td>
                  <td><?= htmlspecialchars($message['name']) ?></td>
                  <td><?= date('M d, Y', strtotime($message['created_at'])) ?></td>
                  <td>
                    <span class="badge <?= $message['is_read'] ? 'bg-secondary' : 'bg-primary' ?>">
                      <?= $message['is_read'] ? 'Read' : 'New' ?>
                    </span>
                  </td>
                  <td>
                    <a href="view_contact_message.php?id=<?= $message['id'] ?>" class="btn btn-sm btn-primary">View</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-info">No contact messages found.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include("admin_footer.php"); ?>
</body>
</html>
