<?php include("admin_header.php"); ?>
<?php


// Handle block/unblock action
if (isset($_GET['action']) && isset($_GET['user_id'])) {
  $user_id = intval($_GET['user_id']);
  $action = $_GET['action'];
  
  if ($action === 'block' || $action === 'unblock') {
    $status = $action === 'block' ? 'blocked' : 'active';
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $user_id);
    $stmt->execute();
    
    // Redirect back to avoid resubmission on refresh
    header("Location: view_users.php");
    exit();
  }
}

// Fetch users with license files
$users = $conn->query("SELECT id, name, email, profile_img, license_file, created_at, status FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Users | Parkease Admin</title>
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
    
    .profile-pic {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #e2e8f0;
      cursor: pointer;
      transition: transform 0.2s;
    }
    
    .profile-pic:hover {
      transform: scale(1.1);
    }
    
    .badge {
      font-weight: 500;
      padding: 0.5em 0.75em;
    }
    
    .action-btn {
      padding: 0.25rem 0.5rem;
      font-size: 0.875rem;
      border-radius: 0.25rem;
    }
    
    .file-icon {
      font-size: 1.5rem;
      cursor: pointer;
    }
    
    .pdf-icon { color: #e74c3c; }
    .image-icon { color: #3498db; }
    
    .modal-img {
      max-width: 100%;
      max-height: 80vh;
    }
    
    .license-container {
      height: 80vh;
      width: 100%;
    }
    
    .license-iframe {
      width: 100%;
      height: 100%;
      border: none;
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
      
      .profile-pic {
        width: 35px;
        height: 35px;
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
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
        <circle cx="9" cy="7" r="4"></circle>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
      </svg>
      Registered Users
    </h1>
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
        User List
      </h3>
    </div>
    <div class="section-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>Profile</th>
              <th>Name</th>
              <th>Email</th>
              <th>License</th>
              <th>Status</th>
              <th>Registered On</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $i = 1; while ($row = $users->fetch_assoc()): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td>
                <?php if (!empty($row['profile_img'])): ?>
                  <img src="uploads/<?= $row['profile_img'] ?>" 
                       class="profile-pic" 
                       alt="profile"
                       data-bs-toggle="modal" 
                       data-bs-target="#imageModal"
                       data-img-src="uploads/<?= $row['profile_img'] ?>">
                <?php else: ?>
                  <div class="profile-pic" style="background-color: #e2e8f0; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 18px; color: #6c757d;"><?= strtoupper(substr($row['name'], 0, 1)) ?></span>
                  </div>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td>
                <?php if (!empty($row['license_file'])): ?>
                  <?php 
                  $license_path = 'uploads/licenses/' . $row['license_file'];
                  $file_extension = pathinfo($row['license_file'], PATHINFO_EXTENSION);
                  ?>
                  <span class="file-icon <?= $file_extension === 'pdf' ? 'pdf-icon' : 'image-icon' ?>"
                        data-bs-toggle="modal" 
                        data-bs-target="#licenseModal"
                        data-license-src="<?= $license_path ?>"
                        data-license-type="<?= $file_extension ?>">
                    <i class="bi bi-file-earmark-<?= $file_extension === 'pdf' ? 'pdf' : 'image' ?>"></i>
                  </span>
                <?php else: ?>
                  <span class="text-muted">No license</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge <?= $row['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>">
                  <?= ucfirst($row['status']) ?>
                </span>
              </td>
              <td><?= date("d M Y", strtotime($row['created_at'])) ?></td>
              <td>
                <?php if ($row['status'] === 'active'): ?>
                  <a href="view_users.php?action=block&user_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger">Block</a>
                <?php else: ?>
                  <a href="view_users.php?action=unblock&user_id=<?= $row['id'] ?>" class="btn btn-sm btn-success">Unblock</a>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Profile Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel">Profile Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img src="" class="modal-img" id="modalImage" alt="Profile Image">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- License Modal -->
<div class="modal fade" id="licenseModal" tabindex="-1" aria-labelledby="licenseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="licenseModalLabel">License Document</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="license-container" id="licenseContainer">
          <!-- Content will be loaded dynamically -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Profile Image Modal
  const imageModal = document.getElementById('imageModal');
  if (imageModal) {
    imageModal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const imgSrc = button.getAttribute('data-img-src');
      const modalImage = imageModal.querySelector('.modal-img');
      modalImage.src = imgSrc;
    });
  }

  // License Modal
  const licenseModal = document.getElementById('licenseModal');
  if (licenseModal) {
    licenseModal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const licenseSrc = button.getAttribute('data-license-src');
      const licenseType = button.getAttribute('data-license-type');
      const licenseContainer = licenseModal.querySelector('#licenseContainer');
      
      if (licenseType === 'pdf') {
        licenseContainer.innerHTML = `
          <iframe src="${licenseSrc}" class="license-iframe"></iframe>
        `;
      } else {
        licenseContainer.innerHTML = `
          <img src="${licenseSrc}" class="modal-img" alt="License Image">
        `;
      }
    });

    licenseModal.addEventListener('hidden.bs.modal', function() {
      const licenseContainer = licenseModal.querySelector('#licenseContainer');
      licenseContainer.innerHTML = '';
    });
  }
</script>
<?php include("admin_footer.php"); ?>
</body>
</html>