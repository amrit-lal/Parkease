<?php include("admin_header.php"); ?>
<?php

// Handle guard registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_guard'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $unique_id = 'GRD' . strtoupper(uniqid());

    $conn->query("INSERT INTO guards (unique_id, name, email, password) VALUES ('$unique_id', '$name', '$email', '$password')");
}

// Handle guard update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_guard'])) {
    $guard_id = (int)$_POST['guard_id'];
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    
    // Only update password if it's provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE guards SET name='$name', email='$email', password='$password' WHERE id=$guard_id");
    } else {
        $conn->query("UPDATE guards SET name='$name', email='$email' WHERE id=$guard_id");
    }
    
    // Redirect to prevent form resubmission
    header("Location: manage_guards.php");
    exit();
}

// Handle guard deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_guard'])) {
    $guard_id = (int)$_POST['guard_id'];
    $conn->query("DELETE FROM guards WHERE id = $guard_id");
    // Redirect to prevent form resubmission
    header("Location: manage_guards.php");
    exit();
}

// Fetch all guards
$guards = $conn->query("SELECT * FROM guards ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Guards | Parkease Admin</title>
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
      -webkit-overflow-scrolling: touch;
    }
    
    .table {
      width: 100%;
      border-collapse: collapse;
      min-width: 600px;
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
    
    .form-control {
      border-radius: 0.375rem;
      padding: 0.5rem 0.75rem;
      border: 1px solid #cbd5e0;
    }

    .btn-danger {
      background-color: var(--danger);
      border-color: var(--danger);
    }

    .btn-danger:hover {
      background-color: #e5177e;
      border-color: #e5177e;
    }

    .btn-warning {
      background-color: var(--warning);
      border-color: var(--warning);
      color: white;
    }

    .btn-warning:hover {
      background-color: #e68a19;
      border-color: #e68a19;
      color: white;
    }

    .actions-cell {
      white-space: nowrap;
    }

    .modal-content {
      border-radius: 0.75rem;
    }

    .password-note {
      font-size: 0.8rem;
      color: var(--gray);
      margin-top: 0.25rem;
    }
    
    /* Responsive improvements */
    @media (max-width: 992px) {
      .main-content {
        padding: 0 1rem;
        margin: 1rem auto;
      }
      
      .section-header, .section-body {
        padding: 1rem;
      }
    }
    
    @media (max-width: 768px) {
      body {
        padding-top: 56px;
      }
      
      .page-title {
        font-size: 1.5rem;
      }
      
      .section-title {
        font-size: 1.1rem;
      }
      
      .table td, .table th {
        padding: 0.5rem;
        font-size: 0.875rem;
      }

      .actions-cell {
        white-space: normal;
      }

      .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
      }
      
      /* Registration form responsiveness */
      .section-body .col-12 {
        margin-bottom: 1rem;
      }
      
      .section-body .d-flex.align-items-end {
        align-items: flex-start !important;
        margin-top: 0.5rem;
      }
      
      .section-body button[type="submit"] {
        width: 100%;
      }
    }
    
    @media (max-width: 576px) {
      .page-title {
        font-size: 1.3rem;
      }
      
      .section-title svg {
        width: 16px;
        height: 16px;
      }
      
      .section-body {
        padding: 0.75rem;
      }
      
      .form-control {
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
      }
      
      .password-note {
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
        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
      </svg>
      Manage Guards
    </h1>
  </div>

  <div class="content-section mb-4">
    <div class="section-header">
      <h3 class="section-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
          <circle cx="8.5" cy="7" r="4"></circle>
          <line x1="20" y1="8" x2="20" y2="14"></line>
          <line x1="23" y1="11" x2="17" y2="11"></line>
        </svg>
        Register New Guard
      </h3>
    </div>
    <div class="section-body">
      <form method="POST">
        <div class="row g-3">
          <div class="col-12 col-md-6 col-lg-4">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="col-12 col-md-6 col-lg-4">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="col-12 col-md-6 col-lg-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <div class="col-12 col-md-6 col-lg-1 d-flex align-items-end">
            <button type="submit" name="register_guard" class="btn btn-primary w-100">Register</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="content-section">
    <div class="section-header">
      <h3 class="section-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
          <circle cx="9" cy="7" r="4"></circle>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
        </svg>
        Registered Guards
      </h3>
    </div>
    <div class="section-body">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Unique ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Registered On</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while($guard = $guards->fetch_assoc()): ?>
            <tr>
              <td><?= $guard['id'] ?></td>
              <td><?= $guard['unique_id'] ?></td>
              <td><?= htmlspecialchars($guard['name']) ?></td>
              <td><?= htmlspecialchars($guard['email']) ?></td>
              <td><?= date('M d, Y', strtotime($guard['created_at'])) ?></td>
              <td class="actions-cell">
                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editGuardModal<?= $guard['id'] ?>">
                  Edit
                </button>
                <form method="POST" style="display: inline;">
                  <input type="hidden" name="guard_id" value="<?= $guard['id'] ?>">
                  <button type="submit" name="delete_guard" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this guard?')">
                    Delete
                  </button>
                </form>

                <!-- Edit Guard Modal -->
                <div class="modal fade" id="editGuardModal<?= $guard['id'] ?>" tabindex="-1" aria-labelledby="editGuardModalLabel<?= $guard['id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="editGuardModalLabel<?= $guard['id'] ?>">Edit Guard</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <form method="POST">
                        <div class="modal-body">
                          <input type="hidden" name="guard_id" value="<?= $guard['id'] ?>">
                          <div class="mb-3">
                            <label for="edit_name<?= $guard['id'] ?>" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="edit_name<?= $guard['id'] ?>" name="name" value="<?= htmlspecialchars($guard['name']) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label for="edit_email<?= $guard['id'] ?>" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email<?= $guard['id'] ?>" name="email" value="<?= htmlspecialchars($guard['email']) ?>" required>
                          </div>
                          <div class="mb-3">
                            <label for="edit_password<?= $guard['id'] ?>" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="edit_password<?= $guard['id'] ?>" name="password">
                            <div class="password-note">Leave blank to keep current password</div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          <button type="submit" name="update_guard" class="btn btn-primary">Save Changes</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include("admin_footer.php"); ?>
</body>
</html>