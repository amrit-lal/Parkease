<?php  include("admin_header.php"); ?>
<?php
// Messages
$msg = "";

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Search
$search_query = "";
$search_sql = "";
if (!empty($_GET['search'])) {
  $search_query = trim($_GET['search']);
  $search_sql = "WHERE (s.location LIKE '%$search_query%' OR s.slot_date LIKE '%$search_query%')";
}

// Count slots for stats
$total_slots = $conn->query("SELECT COUNT(*) as total FROM slots")->fetch_assoc()['total'];
$available_slots = $conn->query("SELECT COUNT(*) as total FROM slots WHERE status='Available'")->fetch_assoc()['total'];
$pending_slots = $conn->query("SELECT COUNT(*) as total FROM slots WHERE status='Pending'")->fetch_assoc()['total'];
$booked_slots = $conn->query("SELECT COUNT(*) as total FROM slots WHERE status='Booked'")->fetch_assoc()['total'];
$occupied_slots = $conn->query("SELECT COUNT(*) as total FROM slots WHERE status='Occupied'")->fetch_assoc()['total'];

// Fetch pricing data
$pricing = $conn->query("SELECT * FROM pricing")->fetch_all(MYSQLI_ASSOC);

// Handle Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_slot'])) {
  $location = $_POST['location'];
  $slot_date = $_POST['slot_date'];
  $start_time = $_POST['start_time'];
  $end_time = $_POST['end_time'];
  $category = $_POST['category'];

  $conflict_check = $conn->prepare("SELECT id FROM slots WHERE location = ? AND slot_date = ? AND ((start_time < ? AND end_time > ?) OR (start_time >= ? AND end_time <= ?))");
  $conflict_check->bind_param("ssssss", $location, $slot_date, $end_time, $start_time, $start_time, $end_time);
  $conflict_check->execute();
  $result = $conflict_check->get_result();

  if ($result->num_rows > 0) {
    $msg = "<div class='alert alert-warning'>❌ A conflicting slot already exists.</div>";
  } else {
    $stmt = $conn->prepare("INSERT INTO slots (location, slot_date, start_time, end_time, category, status) VALUES (?, ?, ?, ?, ?, 'Available')");
    $stmt->bind_param("sssss", $location, $slot_date, $start_time, $end_time, $category);
    $stmt->execute();
    $msg = "<div class='alert alert-success'>✅ Slot created successfully.</div>";
  }
}

// Handle Update
if (isset($_POST['update_slot'])) {
    $id = $_POST['slot_id'];
    $location = $_POST['location'];
    $slot_date = $_POST['slot_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $category = $_POST['category'];
    $new_status = $_POST['status'];

    // Check if there's an active booking for this slot
    $booking_check = $conn->query("SELECT id FROM bookings WHERE slot_id=$id AND status IN ('Pending', 'Booked', 'Occupied')");
    
    if ($booking_check->num_rows > 0 && $new_status !== 'Pending' && $new_status !== 'Booked' && $new_status !== 'Occupied') {
        $msg = "<div class='alert alert-warning'>❌ Cannot change status - this slot has an active booking.</div>";
    } else {
        // First check for conflicts
        $conflict_check = $conn->prepare("SELECT id FROM slots WHERE id != ? AND location = ? AND slot_date = ? AND ((start_time < ? AND end_time > ?) OR (start_time >= ? AND end_time <= ?))");
        $conflict_check->bind_param("issssss", $id, $location, $slot_date, $end_time, $start_time, $start_time, $end_time);
        $conflict_check->execute();
        $result = $conflict_check->get_result();

        if ($result->num_rows > 0) {
            $msg = "<div class='alert alert-warning'>❌ A conflicting slot already exists. Update not performed.</div>";
        } else {
            $stmt = $conn->prepare("UPDATE slots SET location=?, slot_date=?, start_time=?, end_time=?, category=?, status=? WHERE id=?");
            $stmt->bind_param("ssssssi", $location, $slot_date, $start_time, $end_time, $category, $new_status, $id);
            $stmt->execute();
            $msg = "<div class='alert alert-success'>✅ Slot updated successfully.</div>";
        }
    }
}

// Handle Delete
if (isset($_POST['delete_slot'])) {
  $id = intval($_POST['slot_id']);
  // Also delete any bookings for this slot
  $conn->query("DELETE FROM bookings WHERE slot_id=$id");
  $conn->query("DELETE FROM slots WHERE id=$id");
  $msg = "<div class='alert alert-danger'>🗑️ Slot and associated bookings deleted.</div>";
}

// Handle Pricing Update
if (isset($_POST['update_pricing'])) {
    foreach ($_POST['prices'] as $category => $price) {
        $price = floatval($price);
        $stmt = $conn->prepare("UPDATE pricing SET price=? WHERE category=?");
        $stmt->bind_param("ds", $price, $category);
        $stmt->execute();
    }
    $msg = "<div class='alert alert-success'>✅ Pricing updated successfully.</div>";
}

// Fetch data with pagination and calculated status
$slot_query = "SELECT s.*, 
    CASE 
        WHEN EXISTS (SELECT 1 FROM bookings b WHERE b.slot_id = s.id AND b.status = 'Pending') THEN 'Pending'
        WHEN EXISTS (SELECT 1 FROM bookings b WHERE b.slot_id = s.id AND b.status = 'Booked') THEN 'Booked'
        WHEN EXISTS (SELECT 1 FROM bookings b WHERE b.slot_id = s.id AND b.status = 'Occupied') THEN 'Occupied'
        ELSE s.status
    END as calculated_status
    FROM slots s $search_sql 
    ORDER BY slot_date DESC, start_time ASC 
    LIMIT $offset, $limit";
$slots = $conn->query($slot_query);

// Total for pagination
$total_filtered = $conn->query("SELECT COUNT(*) as total FROM slots s $search_sql")->fetch_assoc()['total'];
$total_pages = ceil($total_filtered / $limit);

$edit_id = $_POST['edit_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Slots | Parkease</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #7367f0;
      --secondary-color: #82868b;
      --success-color: #28c76f;
      --info-color: #00cfe8;
      --warning-color: #ff9f43;
      --danger-color: #ea5455;
      --dark-color: #4b4b4b;
      --light-color: #f8f8f8;
    }
    
    body {
      font-family: 'Montserrat', sans-serif;
      background-color: #f8f8f8;
      color: #6e6b7b;
      overflow-x: hidden;
    }
    
    .sidebar {
      height: 100vh;
      background-color: white;
      padding-top: 20px;
      position: fixed;
      width: 260px;
      box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
      z-index: 1000;
      transition: transform 0.3s ease;
    }
    
    .sidebar.collapsed {
      transform: translateX(-260px);
    }
    
    .sidebar-header {
      padding: 0 20px 20px;
      border-bottom: 1px solid #ebe9f1;
      margin-bottom: 20px;
    }
    
    .sidebar .nav-item {
      margin-bottom: 5px;
    }
    
    .sidebar .nav-link {
      color: #6e6b7b;
      padding: 10px 20px;
      border-radius: 5px;
      font-weight: 500;
      display: flex;
      align-items: center;
    }
    
    .sidebar .nav-link i {
      margin-right: 10px;
      width: 20px;
      text-align: center;
    }
    
    .sidebar .nav-link:hover {
      color: var(--primary-color);
      background-color: rgba(115, 103, 240, 0.08);
    }
    
    .sidebar .nav-link.active {
      color: white;
      background-color: var(--primary-color);
      box-shadow: 0 4px 18px -4px rgba(115, 103, 240, 0.65);
    }
    
    .main-content {
      margin-left: 260px;
      padding: 30px;
      min-height: 100vh;
      transition: margin 0.3s ease;
    }
    
    .main-content.expanded {
      margin-left: 0;
    }
    
    .header {
      background-color: white;
      padding: 20px 30px;
      border-radius: 10px;
      margin-bottom: 30px;
      box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
    }
    
    .header h2 {
      color: #5e5873;
      font-weight: 600;
      margin-bottom: 0;
    }
    
    .stats-card {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 30px;
      box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
      border-left: 4px solid var(--primary-color);
    }
    
    .stats-box {
      background: white;
      border-radius: 10px;
      padding: 15px;
      box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
      margin-bottom: 25px;
      text-align: center;
    }
    
    .stats-box h4 {
      font-weight: 600;
      margin-top: 5px;
    }
    
    .form-section, .table-wrapper {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
      margin-bottom: 30px;
    }
    
    .table td input, .table td select {
      width: 100%;
      font-size: 0.9em;
    }
    
    .status-available { color: var(--success-color); font-weight: bold; }
    .status-pending { color: var(--warning-color); font-weight: bold; }
    .status-booked { color: var(--info-color); font-weight: bold; }
    .status-occupied { color: var(--danger-color); font-weight: bold; }
    
    .pricing-header { 
      border-bottom: 2px solid var(--primary-color); 
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    
    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }
    
    .btn-outline-primary {
      color: var(--primary-color);
      border-color: var(--primary-color);
    }
    
    .btn-outline-primary:hover {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      color: white;
    }
    
    .alert {
      border-radius: 8px;
    }
    
    .sidebar-toggle {
      display: none;
      position: fixed;
      top: 15px;
      left: 15px;
      z-index: 1050;
      background: var(--primary-color);
      color: white;
      border: none;
      border-radius: 5px;
      padding: 5px 10px;
      font-size: 1.5rem;
    }
    
    .table-responsive {
      overflow-x: auto;
    }
    
    @media (max-width: 992px) {
      .sidebar {
        transform: translateX(-260px);
      }
      
      .sidebar.show {
        transform: translateX(0);
      }
      
      .main-content {
        margin-left: 0;
      }
      
      .sidebar-toggle {
        display: block;
      }
      
      .stats-box {
        margin-bottom: 15px;
      }
    }
    
    @media (max-width: 768px) {
      .form-section .row > div {
        margin-bottom: 15px;
      }
      
      .form-section .d-flex.align-items-end {
        align-items: flex-start !important;
      }
      
      .table td, .table th {
        white-space: nowrap;
      }
      
      .pagination {
        flex-wrap: wrap;
      }
    }
    
    @media (max-width: 576px) {
      .header {
        padding: 15px;
      }
      
      .main-content {
        padding: 15px;
      }
      
      .form-section, .table-wrapper {
        padding: 15px;
      }
    }
  </style>
</head>
<body>

<!-- Sidebar Toggle -->
<button class="sidebar-toggle" id="sidebarToggle">
  <i class="fas fa-bars"></i>
</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <h4 class="text-primary">🅿️ Admin</h4>
  </div>
  <ul class="nav flex-column">
    <li class="nav-item">
      <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" href="manage_slots.php"><i class="fas fa-parking"></i> Manage Slots</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="view_users.php"><i class="fas fa-users"></i> View Users</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="view_bookings.php"><i class="fas fa-list"></i> View Bookings</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="approve_bookings.php"><i class="fas fa-check-circle"></i> Approve Bookings</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="manage_guards.php"><i class="fas fa-user-shield"></i> Manage Guards</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="verification_details.php"><i class="fas fa-check-double"></i> Verification Details</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="generate_reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </li>
  </ul>
</div>

<!-- Main Content -->
<div class="main-content" id="mainContent">
  <div class="header">
    <h2>Manage Slots <span class="text-primary">🗂️</span></h2>
  </div>

  <?= $msg ?>

  <!-- Stats -->
  <div class="row">
    <div class="col-md-3 col-sm-6">
      <div class="stats-box">
        <h6>Total Slots</h6>
        <h4><?= $total_slots ?></h4>
      </div>
    </div>
    <div class="col-md-2 col-sm-6">
      <div class="stats-box">
        <h6>Available</h6>
        <h4 class="text-success"><?= $available_slots ?></h4>
      </div>
    </div>
    <div class="col-md-2 col-sm-6">
      <div class="stats-box">
        <h6>Pending</h6>
        <h4 class="text-warning"><?= $pending_slots ?></h4>
      </div>
    </div>
    <div class="col-md-2 col-sm-6">
      <div class="stats-box">
        <h6>Booked</h6>
        <h4 class="text-info"><?= $booked_slots ?></h4>
      </div>
    </div>
    <div class="col-md-3 col-sm-6">
      <div class="stats-box">
        <h6>Occupied</h6>
        <h4 class="text-danger"><?= $occupied_slots ?></h4>
      </div>
    </div>
  </div>

  <!-- Search -->
  <div class="form-section">
    <form method="GET" class="row g-3">
      <div class="col-md-10 col-sm-9">
        <input type="text" name="search" class="form-control" placeholder="Search by location or date..." value="<?= htmlspecialchars($search_query) ?>">
      </div>
      <div class="col-md-2 col-sm-3">
        <button class="btn btn-primary w-100">Search</button>
      </div>
    </form>
  </div>

  <!-- Create Slot -->
  <div class="form-section">
    <form method="POST" class="row g-3">
      <input type="hidden" name="create_slot" value="1">
      <div class="col-md-3 col-sm-6"><label>Location</label><input type="text" name="location" class="form-control" required></div>
      <div class="col-md-2 col-sm-6"><label>Date</label><input type="date" name="slot_date" class="form-control" required></div>
      <div class="col-md-2 col-sm-6"><label>Start Time</label><input type="time" name="start_time" class="form-control" required></div>
      <div class="col-md-2 col-sm-6"><label>End Time</label><input type="time" name="end_time" class="form-control" required></div>
      <div class="col-md-2 col-sm-6">
        <label>Category</label>
        <select name="category" class="form-select" required>
          <option value="2W">2-Wheeler</option>
          <option value="4W">4-Wheeler</option>
        </select>
      </div>
      <div class="col-md-1 col-sm-6 d-flex align-items-end">
        <button class="btn btn-primary">➕ Add</button>
      </div>
    </form>
  </div>

  <!-- Pricing Management -->
  <div class="form-section">
    <form method="POST">
      <h5 class="pricing-header">💰 Pricing Management</h5>
      <div class="row g-3">
        <?php foreach ($pricing as $price): ?>
        <div class="col-md-3 col-sm-6">
          <label><?= $price['category'] ?> Price (₹)</label>
          <input type="number" name="prices[<?= $price['category'] ?>]" 
                 value="<?= $price['price'] ?>" class="form-control" step="0.01" min="0" required>
        </div>
        <?php endforeach; ?>
        <div class="col-md-3 col-sm-6 d-flex align-items-end">
          <button name="update_pricing" class="btn btn-primary">Update Pricing</button>
        </div>
      </div>
    </form>
  </div>

  <!-- Table -->
  <div class="table-wrapper">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-primary">
          <tr>
            <th>ID</th><th>Location</th><th>Date</th><th>Time</th><th>Category</th><th>Status</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($slot = $slots->fetch_assoc()): ?>
          <tr>
            <?php if ($edit_id == $slot['id']): ?>
            <form method="POST">
              <input type="hidden" name="slot_id" value="<?= $slot['id'] ?>">
              <td><?= $slot['id'] ?></td>
              <td><input name="location" value="<?= $slot['location'] ?>" class="form-control"></td>
              <td><input type="date" name="slot_date" value="<?= $slot['slot_date'] ?>" class="form-control"></td>
              <td>
                <div class="d-flex gap-2">
                  <input type="time" name="start_time" value="<?= $slot['start_time'] ?>" class="form-control">
                  <input type="time" name="end_time" value="<?= $slot['end_time'] ?>" class="form-control">
                </div>
              </td>
              <td>
                <select name="category" class="form-select">
                  <option value="2W" <?= $slot['category'] == '2W' ? 'selected' : '' ?>>2W</option>
                  <option value="4W" <?= $slot['category'] == '4W' ? 'selected' : '' ?>>4W</option>
                </select>
              </td>
              <td>
                <select name="status" class="form-select">
                  <option value="Available" <?= $slot['calculated_status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                  <option value="Pending" <?= $slot['calculated_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                  <option value="Booked" <?= $slot['calculated_status'] == 'Booked' ? 'selected' : '' ?>>Booked</option>
                  <option value="Occupied" <?= $slot['calculated_status'] == 'Occupied' ? 'selected' : '' ?>>Occupied</option>
                </select>
              </td>
              <td><button class="btn btn-success btn-sm" name="update_slot">💾 Save</button></td>
            </form>
            <?php else: ?>
            <td><?= $slot['id'] ?></td>
            <td><?= $slot['location'] ?></td>
            <td><?= $slot['slot_date'] ?></td>
            <td><?= $slot['start_time'] ?> - <?= $slot['end_time'] ?></td>
            <td><?= $slot['category'] ?></td>
            <td class="status-<?= strtolower($slot['calculated_status']) ?>"><?= $slot['calculated_status'] ?></td>
            <td>
              <form method="POST" class="d-inline"><input type="hidden" name="edit_id" value="<?= $slot['id'] ?>"><button class="btn btn-warning btn-sm">✏️ Edit</button></form>
              <form method="POST" onsubmit="return confirm('Delete slot and all associated bookings?')" class="d-inline"><input type="hidden" name="slot_id" value="<?= $slot['id'] ?>"><button name="delete_slot" class="btn btn-danger btn-sm">🗑️</button></form>
            </td>
            <?php endif; ?>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  <nav class="mt-4">
    <ul class="pagination">
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search_query) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Sidebar toggle functionality
  document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('show');
    document.getElementById('mainContent').classList.toggle('expanded');
  });

  // Close sidebar when clicking outside on mobile
  document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const isClickInsideSidebar = sidebar.contains(event.target);
    const isClickOnToggleBtn = event.target === toggleBtn || toggleBtn.contains(event.target);
    
    if (window.innerWidth <= 992 && !isClickInsideSidebar && !isClickOnToggleBtn && sidebar.classList.contains('show')) {
      sidebar.classList.remove('show');
      document.getElementById('mainContent').classList.add('expanded');
    }
  });
</script>
<?php include("admin_footer.php"); ?>
</body>
</html>