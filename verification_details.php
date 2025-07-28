<?php include("admin_header.php"); ?>
<?php

// Fetch all verifications with guard and booking details (updated to include slot location)
$verifications = $conn->query("
    SELECT v.*, g.name as guard_name, g.unique_id as guard_unique_id, 
           b.booking_code, b.user_id, b.slot_id, b.start_time, b.end_time,
           u.name as user_name,
           s.location as slot_location
    FROM verifications v
    JOIN guards g ON v.guard_id = g.id
    JOIN bookings b ON v.booking_id = b.id
    JOIN users u ON b.user_id = u.id
    JOIN slots s ON b.slot_id = s.id
    ORDER BY v.verification_time DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Details | Parkease Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* All original styles remain exactly the same */
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
            transition: margin-left 0.3s ease;
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
            white-space: nowrap;
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
        
        .badge-success {
            background-color: var(--success);
        }
        
        .badge-primary {
            background-color: var(--primary);
        }
        
        .badge-warning {
            background-color: var(--warning);
        }

        /* Sidebar toggler styles */
        .sidebar-toggled .main-content {
            margin-left: 0;
        }

        /* Responsive table text */
        .table-responsive .table td, 
        .table-responsive .table th {
            white-space: normal;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 0 1rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .section-header, .section-body {
                padding: 1rem;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 0 0.75rem;
            }
            
            .page-title {
                font-size: 1.25rem;
            }
            
            .section-title {
                font-size: 1.1rem;
            }
            
            .table td, .table th {
                padding: 0.5rem;
                font-size: 0.875rem;
            }
        }
        
        @media (max-width: 576px) {
            body {
                padding-top: 56px;
            }
            
            .main-content {
                padding: 0 0.5rem;
            }
            
            .page-header {
                margin-bottom: 1rem;
            }
            
            .section-body {
                padding: 0.75rem;
            }
            
            .table td, .table th {
                padding: 0.5rem 0.25rem;
                font-size: 0.8125rem;
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
            Verification Details
        </h1>
    </div>

    <div class="content-section">
        <div class="section-header">
            <h3 class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                All Verifications
            </h3>
        </div>
        <div class="section-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Booking Code</th>
                            <th>User</th>
                            <th>Slot Location</th>
                            <th>Time</th>
                            <th>Verified By</th>
                            <th>Verified For</th>
                            <th>Verification Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($verification = $verifications->fetch_assoc()): 
                            $verificationType = ucfirst($verification['action']);
                            $badgeClass = $verification['action'] == 'check-in' ? 'badge-primary' : 'badge-warning';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($verification['booking_code']) ?></td>
                            <td><?= htmlspecialchars($verification['user_name']) ?> (ID: <?= htmlspecialchars($verification['user_id']) ?>)</td>
                            <td><?= htmlspecialchars($verification['slot_location']) ?></td>
                            <td><?= date('H:i', strtotime($verification['start_time'])) ?> - <?= date('H:i', strtotime($verification['end_time'])) ?></td>
                            <td><?= htmlspecialchars($verification['guard_name']) ?> (<?= htmlspecialchars($verification['guard_unique_id']) ?>)</td>
                            <td><span class="badge <?= $badgeClass ?>"><?= $verificationType ?></span></td>
                            <td><?= date('M d, Y H:i', strtotime($verification['verification_time'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggler = document.querySelector('.sidebar-toggler');
        if (sidebarToggler) {
            sidebarToggler.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-toggled');
            });
        }
    });
</script>
<?php include("admin_footer.php"); ?>
</body>
</html>