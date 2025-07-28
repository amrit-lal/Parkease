<?php include("admin_header.php"); ?>
<?php

// Approve booking
if (isset($_POST['approve'])) {
  $booking_id = $_POST['booking_id'];
  $slot_id = $_POST['slot_id'];
  $booking_code = "PAS" . strtoupper(bin2hex(random_bytes(4)));

  $conn->query("UPDATE bookings SET status='Booked', booking_code='$booking_code' WHERE id=$booking_id");
  $conn->query("UPDATE slots SET status='Booked' WHERE id=$slot_id");
}

// Reject booking
if (isset($_POST['reject'])) {
  $booking_id = $_POST['booking_id'];
  $slot_id = $_POST['slot_id'];
  
  // Update booking status to Rejected and clear any booking code
  $conn->query("UPDATE bookings SET status='Rejected', booking_code=NULL WHERE id=$booking_id");
  // Set the slot back to Available
  $conn->query("UPDATE slots SET status='Available' WHERE id=$slot_id");
}

// Fetch pending bookings
$pending = $conn->query("
  SELECT b.*, u.name, s.location, s.slot_date, s.start_time, s.end_time, s.category 
  FROM bookings b 
  JOIN users u ON b.user_id = u.id 
  JOIN slots s ON b.slot_id = s.id 
  WHERE b.status='Pending' 
  ORDER BY b.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Approve Bookings </title>
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
      transition: margin-left 0.3s;
    }
    
    .page-header {
      margin-bottom: 2rem;
    }
    
    .page-title {
      font-size: 1.75rem;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 0.5rem;
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
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .section-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--dark);
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
      -webkit-overflow-scrolling: touch;
    }
    
    .table {
      width: 100%;
      border-collapse: collapse;
      min-width: 600px;
    }
    
    .table th {
      background-color: var(--primary);
      color: white;
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
    
    .btn-sm {
      padding: 0.25rem 0.5rem;
      font-size: 0.875rem;
    }
    
    .badge {
      font-size: 0.75rem;
      font-weight: 600;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
    }
    
    .badge-success {
      background-color: var(--success);
    }
    
    .badge-danger {
      background-color: var(--danger);
    }

    /* Responsive improvements */
    @media (max-width: 1199.98px) {
      .main-content {
        margin-left: 250px;
        margin-right: 1rem;
      }
    }

    @media (max-width: 991.98px) {
      .main-content {
        margin-left: 0;
        padding: 0 1rem;
      }
      
      .section-header, .section-body {
        padding: 1rem;
      }
      
      .table td, .table th {
        padding: 0.5rem;
      }
    }

    @media (max-width: 767.98px) {
      .page-title {
        font-size: 1.5rem;
      }
      
      .section-title {
        font-size: 1.1rem;
      }
      
      .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
      }
    }

    @media (max-width: 575.98px) {
      body {
        padding-top: 56px;
      }
      
      .main-content {
        padding: 0 0.75rem;
      }
      
      .page-title {
        font-size: 1.3rem;
      }
      
      .section-body {
        padding: 0.75rem;
      }
      
      .table td, .table th {
        padding: 0.4rem;
        font-size: 0.85rem;
      }
      
      .d-flex.gap-2 {
        gap: 0.5rem !important;
      }
    }

    /* For very small devices */
    @media (max-width: 400px) {
      .page-title svg {
        width: 20px;
        height: 20px;
      }
      
      .section-title svg {
        width: 16px;
        height: 16px;
      }
      
      .btn-sm {
        padding: 0.15rem 0.3rem;
        font-size: 0.75rem;
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
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
        <polyline points="22 4 12 14.01 9 11.01"></polyline>
      </svg>
      Approve or Reject Bookings
    </h1>
  </div>

  <div class="content-section">
    <div class="section-header">
      <h3 class="section-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
          <polyline points="14 2 14 8 20 8"></polyline>
          <line x1="16" y1="13" x2="8" y2="13"></line>
          <line x1="16" y1="17" x2="8" y2="17"></line>
          <polyline points="10 9 9 9 8 9"></polyline>
        </svg>
        Pending Booking Requests
      </h3>
    </div>
    <div class="section-body">
      <?php if ($pending->num_rows == 0): ?>
        <div class="alert alert-info">No pending bookings to approve.</div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>User</th>
                <th>Slot</th>
                <th>Date</th>
                <th>Time</th>
                <th>Category</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $pending->fetch_assoc()): ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= $row['location'] ?></td>
                <td><?= $row['slot_date'] ?></td>
                <td><?= $row['start_time'] ?> - <?= $row['end_time'] ?></td>
                <td><?= $row['category'] ?></td>
                <td>
                  <form method="POST" class="d-flex gap-2">
                    <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="slot_id" value="<?= $row['slot_id'] ?>">
                    <button type="submit" name="approve" class="btn btn-success btn-sm">Approve</button>
                    <button type="submit" name="reject" class="btn btn-danger btn-sm">Reject</button>
                  </form>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
  
  <a href="admin_dashboard.php" class="btn btn-outline-secondary mt-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M19 12H5M12 19l-7-7 7-7"></path>
    </svg>
    Back to Dashboard
  </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Ensure sidebar toggle works properly
  document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
      const toggleBtn = document.querySelector('[data-bs-toggle="offcanvas"]');
      const mainContent = document.querySelector('.main-content');
      
      toggleBtn.addEventListener('click', function() {
        if (sidebar.classList.contains('active')) {
          mainContent.style.marginLeft = '0';
        } else {
          mainContent.style.marginLeft = '250px';
        }
      });
      
      // Handle window resize
      window.addEventListener('resize', function() {
        if (window.innerWidth < 992) {
          mainContent.style.marginLeft = '0';
        } else if (sidebar.classList.contains('active')) {
          mainContent.style.marginLeft = '250px';
        }
      });
    }
  });
</script>
<?php include("admin_footer.php"); ?>
</body>
</html>