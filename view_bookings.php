<?php include("admin_header.php"); ?>
<?php

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

// Auto-complete bookings when slots become available
$conn->query("
    UPDATE bookings b
    JOIN slots s ON b.slot_id = s.id
    SET b.status = 'Completed', 
        b.checkout_time = NOW(),
        b.updated_at = NOW()
    WHERE s.status = 'Available' 
    AND b.status IN ('Booked', 'Occupied')
");

// Fetch all bookings with related data
$bookings = $conn->query("
    SELECT 
        b.*, 
        u.name AS user_name, 
        u.email AS user_email,
        s.location, 
        s.status AS slot_status,
        p.price AS slot_price,
        v_in.verification_time AS checkin_time,
        v_out.verification_time AS checkout_time,
        guard_in.name AS checkin_guard,
        guard_out.name AS checkout_guard
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN slots s ON b.slot_id = s.id
    JOIN pricing p ON s.category = p.category
    LEFT JOIN verifications v_in ON v_in.booking_id = b.id AND v_in.action = 'check-in'
    LEFT JOIN verifications v_out ON v_out.booking_id = b.id AND v_out.action = 'check-out'
    LEFT JOIN guards guard_in ON v_in.guard_id = guard_in.id
    LEFT JOIN guards guard_out ON v_out.guard_id = guard_out.id
    ORDER BY b.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings | Parking Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6c5ce7;
            --primary-light: #a29bfe;
            --secondary: #fd79a8;
            --success: #00b894;
            --danger: #d63031;
            --warning: #fdcb6e;
            --info: #0984e3;
            --light: #f8f9fa;
            --dark: #2d3436;
            --gray: #636e72;
            --white: #ffffff;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9ff;
            color: #4a4a6a;
            padding-top: 70px;
        }
        
        .main-content {
            max-width: 1800px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1.5rem;
            border-radius: 12px;
            color: white;
            margin-bottom: 2rem;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.2);
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .content-section {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .section-header {
            padding: 1.25rem 1.75rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .section-body {
            padding: 1.75rem;
        }
        
        .table-responsive {
            overflow-x: auto;
            border-radius: 12px;
            -webkit-overflow-scrolling: touch;
        }
        
        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.95rem;
            min-width: 900px;
        }
        
        .table th {
            background: #f8f9fa;
            padding: 1rem;
            text-align: left;
            position: sticky;
            top: 0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }
        
        .table td {
            padding: 1rem;
            border-top: 1px solid rgba(0,0,0,0.05);
            vertical-align: middle;
        }
        
        .table tr:hover {
            background-color: rgba(108, 92, 231, 0.05);
        }
        
        .badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
        }
        
        .badge.Pending { background: #ff9a9e; color: #000; }
        .badge.Booked { background: var(--success); color: #fff; }
        .badge.Occupied { background: var(--primary); color: #fff; }
        .badge.Completed { background: var(--dark); color: #fff; }
        .badge.Rejected { background: var(--danger); color: #fff; }
        .badge.Cancelled { 
            background: var(--warning); 
            color: #000;
            border: 1px solid rgba(0,0,0,0.1);
            display: inline-block;
    min-width: 80px;
    text-align: center;
    white-space: nowrap;
        }
        
        .search-container {
            position: relative;
            width: 300px;
        }
        
        .search-container i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
        }
        
        #bookingSearch {
            padding-left: 40px;
            border-radius: 50px;
            border: 2px solid rgba(108, 92, 231, 0.2);
        }
        
        .action-btns {
            white-space: nowrap;
        }
        
        .action-btns form {
            display: inline-block;
            margin-right: 5px;
        }
        
        .action-btns .btn {
            padding: 0.35rem 0.65rem;
            font-size: 0.85rem;
            border-radius: 8px;
        }

        /* Responsive styles */
        @media (max-width: 1199.98px) {
            .main-content {
                padding: 0 1rem;
            }
            
            .page-header {
                padding: 1.25rem;
            }
            
            .section-header {
                padding: 1rem;
            }
            
            .section-body {
                padding: 1rem;
            }
            
            .table th, .table td {
                padding: 0.75rem;
            }
        }

        @media (max-width: 991.98px) {
            .page-title {
                font-size: 1.75rem;
            }
            
            .section-title {
                font-size: 1.25rem;
            }
            
            .search-container {
                width: 250px;
            }
        }

        @media (max-width: 767.98px) {
            body {
                padding-top: 60px;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
            
            .search-container {
                width: 100%;
            }
            
            .action-btns {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .action-btns .btn {
                padding: 0.3rem 0.5rem;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 575.98px) {
            .main-content {
                margin: 1rem auto;
            }
            
            .page-header {
                padding: 1rem;
                border-radius: 10px;
            }
            
            .page-title {
                font-size: 1.3rem;
            }
            
            .section-title {
                font-size: 1.1rem;
            }
            
            .badge {
                padding: 0.25rem 0.5rem;
                font-size: 0.7rem;
            }
        }

        /* Print styles */
        @media print {
            body {
                padding-top: 0;
                background: white;
                color: black;
            }
            
            .page-header, .section-header {
                background: white !important;
                color: black !important;
                box-shadow: none;
            }
            
            .table th {
                background: #f1f1f1 !important;
            }
            
            .badge {
                border: 1px solid #ddd;
            }
            
            .btn, .search-container {
                display: none !important;
            }
        }
    </style>
</head>
<body>
  
<?php include('admin_sidebar.php'); ?>

<div class="main-content">
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-calendar-check"></i>
            Booking Management
        </h1>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show mx-3">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mx-3">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="content-section">
        <div class="section-header">
            <h3 class="section-title">
                <i class="bi bi-clock-history"></i>
                Booking History
            </h3>
            <div class="d-flex gap-2 align-items-center">
                <div class="search-container">
                    <i class="bi bi-search"></i>
                    <input type="text" id="bookingSearch" class="form-control form-control-sm" placeholder="Search bookings...">
                </div>
                <button class="btn btn-sm btn-light" onclick="window.print()">
                    <i class="bi bi-printer-fill"></i> Print
                </button>
                <button class="btn btn-sm btn-success" id="exportBtn">
                    <i class="bi bi-file-earmark-excel-fill"></i> Export
                </button>
            </div>
        </div>
        <div class="section-body">
            <div class="table-responsive">
                <table class="table" id="bookingsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Booking Code</th>
                            <th>User</th>
                            <th>Location</th>
                            <th>Request Time</th>
                            <th>Date</th>
                            <th>Time Slot</th>
                            <th>Status</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; while ($row = $bookings->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold"><?= $i++ ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border border-primary">
                                        <?= htmlspecialchars($row['booking_code'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2" style="width: 32px; height: 32px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <?= strtoupper(substr($row['user_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <?= htmlspecialchars($row['user_name']) ?>
                                            <div class="text-muted small"><?= htmlspecialchars($row['user_email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="bi bi-geo-alt-fill text-primary"></i> 
                                    <?= htmlspecialchars($row['location']) ?>
                                </td>
                                <td>
                                    <?= date('M j, Y g:i A', strtotime($row['created_at'])) ?>
                                    <div class="text-muted small">
                                        <?= time_elapsed_string($row['created_at']) ?>
                                    </div>
                                </td>
                                <td><?= date('M j, Y', strtotime($row['slot_date'])) ?></td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= date('g:i A', strtotime($row['start_time'])) ?> - <?= date('g:i A', strtotime($row['end_time'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $row['status'] ?>">
                                        <?= $row['status'] ?>
                                        <?php if ($row['status'] == 'Cancelled' && !empty($row['cancelled_at'])): ?>
                                            <div class="text-muted small mt-1">
                                                <?= date('M j, g:i A', strtotime($row['cancelled_at'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['checkin_time']): ?>
                                        <?= date('M j g:i A', strtotime($row['checkin_time'])) ?>
                                        <?php if ($row['checkin_guard']): ?>
                                            <div class="text-muted small">by <?= htmlspecialchars($row['checkin_guard']) ?></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'Completed' && $row['checkout_time']): ?>
                                        <?= date('M j g:i A', strtotime($row['checkout_time'])) ?>
                                        <?php if ($row['checkout_guard']): ?>
                                            <div class="text-muted small">by <?= htmlspecialchars($row['checkout_guard']) ?></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-btns">
                                    <?php if ($row['status'] == 'Booked'): ?>
                                        <form method="POST" action="update_booking_status.php" class="d-inline">
                                            <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                                            <input type="hidden" name="new_status" value="Cancelled">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                <i class="bi bi-x-circle"></i> Cancel
                                            </button>
                                        </form>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Search functionality
    document.getElementById('bookingSearch').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#bookingsTable tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    // Export button functionality
    document.getElementById('exportBtn').addEventListener('click', function() {
        alert('Export functionality will be implemented here');
        // In a real implementation, this would generate a CSV or Excel file
    });
</script>
<?php include("admin_footer.php"); ?>
</body>
</html>