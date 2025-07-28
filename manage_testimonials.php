<?php include("admin_header.php"); ?>
<?php
// Handle testimonial actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve'])) {
        $testimonial_id = intval($_POST['testimonial_id']);
        $stmt = $conn->prepare("UPDATE testimonials SET status = 'Approved' WHERE id = ?");
        $stmt->bind_param("i", $testimonial_id);
        $stmt->execute();
        $_SESSION['message'] = "Testimonial approved successfully!";
    } 
    elseif (isset($_POST['reject'])) {
        $testimonial_id = intval($_POST['testimonial_id']);
        $stmt = $conn->prepare("UPDATE testimonials SET status = 'Rejected' WHERE id = ?");
        $stmt->bind_param("i", $testimonial_id);
        $stmt->execute();
        $_SESSION['message'] = "Testimonial rejected successfully!";
    }
    elseif (isset($_POST['delete'])) {
        $testimonial_id = intval($_POST['testimonial_id']);
        $stmt = $conn->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->bind_param("i", $testimonial_id);
        $stmt->execute();
        $_SESSION['message'] = "Testimonial deleted successfully!";
    }
    
    header("Location: manage_testimonials.php");
    exit();
}

// Get all testimonials with user info
$testimonials = $conn->query("
    SELECT t.*, u.name as user_name, u.profile_img 
    FROM testimonials t
    JOIN users u ON t.user_id = u.id
    ORDER BY 
        CASE WHEN t.status = 'Pending' THEN 0
             WHEN t.status = 'Approved' THEN 1
             ELSE 2 END,
        t.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Testimonials |  Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-gray: #f8f9fa;
            --medium-gray: #eaeaea;
            --dark-gray: #495057;
            --text-color: #2c3e50;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Roboto, sans-serif;
            color: var(--text-color);
        }
        
        .main-content {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header h2 {
            color: var(--text-color);
            font-weight: 600;
            margin: 0;
        }
        
        .alert {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--medium-gray);
            padding: 15px 20px;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .card-header h5 {
            font-weight: 600;
            color: var(--text-color);
            margin: 0;
        }
        
        .testimonial-card {
            border-left: 4px solid;
            transition: all 0.3s ease;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .status-pending { 
            border-left-color: var(--warning-color);
            background-color: rgba(255, 193, 7, 0.03);
        }
        .status-approved { 
            border-left-color: var(--success-color);
            background-color: rgba(40, 167, 69, 0.03);
        }
        .status-rejected { 
            border-left-color: var(--danger-color);
            background-color: rgba(220, 53, 69, 0.03);
        }
        
        .badge {
            font-weight: 500;
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-pending { 
            background-color: var(--warning-color); 
            color: #212529; 
        }
        .badge-approved { 
            background-color: var(--success-color); 
            color: white;
        }
        .badge-rejected { 
            background-color: var(--danger-color); 
            color: white;
        }
        
        .user-img-container {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 2px solid var(--medium-gray);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            flex-shrink: 0;
        }
        
        .user-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .user-icon {
            font-size: 24px;
            color: #95a5a6;
        }
        
        .testimonial-text {
            color: var(--dark-gray);
            line-height: 1.6;
            padding: 15px;
            background-color: var(--light-gray);
            border-radius: 8px;
            position: relative;
            flex-grow: 1;
        }
        
        .testimonial-text:before {
            content: '"';
            font-size: 60px;
            color: rgba(0,0,0,0.1);
            position: absolute;
            left: 5px;
            top: -15px;
            font-family: Georgia, serif;
            line-height: 1;
        }
        
        .text-muted {
            font-size: 0.85rem;
            color: #7f8c8d !important;
        }
        
        .action-btns {
            border-top: 1px solid var(--medium-gray);
            padding-top: 15px;
            margin-top: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .action-btns .btn {
            font-size: 0.8rem;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .action-btns .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: #212529;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .user-info-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            gap: 15px;
        }
        
        .user-details {
            flex: 1;
        }
        
        .user-name {
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 3px;
            font-size: 1.1rem;
        }
        
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        
        .status-indicator.pending { background-color: var(--warning-color); }
        .status-indicator.approved { background-color: var(--success-color); }
        .status-indicator.rejected { background-color: var(--danger-color); }
        
        .no-testimonials {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            background-color: var(--light-gray);
            border-radius: 10px;
        }
        
        .no-testimonials i {
            font-size: 50px;
            margin-bottom: 15px;
            color: #bdc3c7;
        }
        
        .testimonial-date {
            font-size: 0.8rem;
            color: #95a5a6;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .user-img-container {
                width: 50px;
                height: 50px;
            }
            
            .testimonial-card {
                margin-bottom: 15px;
            }
            
            .action-btns .btn {
                padding: 0.3rem 0.6rem;
                font-size: 0.75rem;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <?php include("admin_sidebar.php"); ?>
    <div class="main-content" id="mainContent">
        <div class="header">
            <h2><i class="fas fa-comment-alt me-2"></i>Manage Testimonials</h2>
            <a href="admin_dashboard.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= htmlspecialchars($_SESSION['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-comments me-2" style="color: var(--primary-color)"></i> Customer Testimonials</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if ($testimonials->num_rows > 0): ?>
                        <?php while ($testimonial = $testimonials->fetch_assoc()): ?>
                            <div class="col-md-6 mb-4">
                                <div class="testimonial-card status-<?= strtolower($testimonial['status']) ?>">
                                    <div class="card-body">
                                        <div class="user-info-container">
                                            <div class="user-img-container">
                                                <?php if (!empty($testimonial['profile_img'])): ?>
                                                    <img src="../uploads/<?= htmlspecialchars($testimonial['profile_img']) ?>" 
                                                         alt="<?= htmlspecialchars($testimonial['user_name']) ?>" 
                                                         class="user-img"
                                                         onerror="this.style.display='none'; this.parentNode.innerHTML='<i class=\'fas fa-user user-icon\'></i>'">
                                                <?php else: ?>
                                                    <i class="fas fa-user user-icon"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="user-details">
                                                <div class="user-name"><?= htmlspecialchars($testimonial['user_name']) ?></div>
                                                <div class="d-flex align-items-center">
                                                    <span class="status-indicator <?= strtolower($testimonial['status']) ?>"></span>
                                                    <span class="badge badge-<?= strtolower($testimonial['status']) ?>">
                                                        <?= htmlspecialchars($testimonial['status']) ?>
                                                    </span>
                                                </div>
                                                <small class="testimonial-date">
                                                    <i class="far fa-clock"></i>
                                                    <?= date('M d, Y H:i', strtotime($testimonial['created_at'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="testimonial-text">
                                            <?= nl2br(htmlspecialchars($testimonial['testimonial_text'])) ?>
                                        </div>
                                        
                                        <div class="action-btns">
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="testimonial_id" value="<?= $testimonial['id'] ?>">
                                                
                                                <?php if ($testimonial['status'] != 'Approved'): ?>
                                                    <button type="submit" name="approve" class="btn btn-success">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <?php if ($testimonial['status'] != 'Rejected'): ?>
                                                    <button type="submit" name="reject" class="btn btn-warning">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <button type="submit" name="delete" class="btn btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this testimonial?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="no-testimonials">
                                <i class="far fa-comment-dots"></i>
                                <h4 class="mt-3">No testimonials yet</h4>
                                <p class="mb-0">When customers submit testimonials, they'll appear here</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize Bootstrap tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Add animation to cards when they come into view
            const cards = document.querySelectorAll('.testimonial-card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });
            
            cards.forEach(card => {
                card.style.opacity = 0;
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.5s ease';
                observer.observe(card);
            });
        });
    </script>
    <?php include("admin_footer.php"); ?>
</body>
</html>+