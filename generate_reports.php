<?php include("admin_header.php"); ?>
<?php


// Default filter values
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'bookings';

// Fetch data based on report type
switch($report_type) {
    case 'users':
        $data = $conn->query("
            SELECT id, name, email, profile_img, created_at 
            FROM users 
            WHERE created_at BETWEEN '$start_date' AND '$end_date 23:59:59'
            ORDER BY created_at DESC
        ");
        break;
        
    case 'guards':
        $data = $conn->query("
            SELECT id, unique_id, name, email, created_at 
            FROM guards 
            WHERE created_at BETWEEN '$start_date' AND '$end_date 23:59:59'
            ORDER BY created_at DESC
        ");
        break;
        
    case 'verifications':
        $data = $conn->query("
            SELECT v.*, g.name as guard_name, g.unique_id as guard_unique_id, 
                   b.booking_code, b.user_id, b.slot_id, b.start_time, b.end_time,
                   u.name as user_name
            FROM verifications v
            JOIN guards g ON v.guard_id = g.id
            JOIN bookings b ON v.booking_id = b.id
            JOIN users u ON b.user_id = u.id
            WHERE v.verification_time BETWEEN '$start_date' AND '$end_date 23:59:59'
            ORDER BY v.verification_time DESC
        ");
        break;
        
    default: // bookings
        $data = $conn->query("
            SELECT 
                b.*, 
                u.name AS user_name, 
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
            WHERE b.created_at BETWEEN '$start_date' AND '$end_date 23:59:59'
            ORDER BY b.created_at DESC
        ");
}

// Get statistics for all data types
$stats = [
    'users' => $conn->query("SELECT COUNT(*) as count FROM users WHERE created_at BETWEEN '$start_date' AND '$end_date 23:59:59'")->fetch_assoc(),
    'guards' => $conn->query("SELECT COUNT(*) as count FROM guards WHERE created_at BETWEEN '$start_date' AND '$end_date 23:59:59'")->fetch_assoc(),
    'verifications' => $conn->query("SELECT COUNT(*) as count FROM verifications WHERE verification_time BETWEEN '$start_date' AND '$end_date 23:59:59'")->fetch_assoc(),
    'bookings' => $conn->query("SELECT COUNT(*) as count FROM bookings WHERE created_at BETWEEN '$start_date' AND '$end_date 23:59:59'")->fetch_assoc()
];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprehensive Reports | Parkease Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Add SheetJS library for Excel export -->
    <script src="https://cdn.sheetjs.com/xlsx-0.19.3/package/dist/xlsx.full.min.js"></script>
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
            max-width: 1400px;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        
        .profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .stats-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-card .card-title {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 0.5rem;
        }
        
        .stats-card .card-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .stats-card .card-icon {
            font-size: 2rem;
            color: var(--primary-light);
            margin-bottom: 1rem;
        }
        
        .badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
        }
        
        .badge-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .badge-success {
            background-color: var(--success);
            color: white;
        }
        
        .badge-warning {
            background-color: var(--warning);
            color: white;
        }
        
        .badge-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .badge-info {
            background-color: var(--info);
            color: white;
        }
        
        .chart-container {
            height: 300px;
            margin-bottom: 2rem;
        }
        
        .btn-export {
            background-color: #28a745;
            color: white;
            border: none;
        }
        
        .btn-export:hover {
            background-color: #218838;
            color: white;
        }
        
        @media (max-width: 992px) {
            .main-content {
                padding: 0 1rem;
            }
            
            .section-header, .section-body {
                padding: 1rem;
            }
            
            .stats-card {
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
            
            .stats-card .card-value {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .main-content {
                padding: 0 0.75rem;
            }
            
            .page-title {
                font-size: 1.3rem;
            }
            
            .section-body {
                padding: 0.75rem;
            }
            
            .stats-card {
                padding: 0.75rem;
            }
            
            .stats-card .card-value {
                font-size: 1.25rem;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include('admin_sidebar.php'); ?>

<div class="main-content">
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-graph-up"></i>
            Comprehensive Reports
        </h1>
    </div>

    <!-- Report Filters -->
    <div class="content-section">
        <div class="section-header">
            <h3 class="section-title">
                <i class="bi bi-funnel"></i>
                Report Filters
            </h3>
        </div>
        <div class="section-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $start_date ?>">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $end_date ?>">
                </div>
                <div class="col-md-4">
                    <label for="report_type" class="form-label">Report Type</label>
                    <select class="form-select" id="report_type" name="report_type">
                        <option value="bookings" <?= $report_type == 'bookings' ? 'selected' : '' ?>>Bookings</option>
                        <option value="users" <?= $report_type == 'users' ? 'selected' : '' ?>>Users</option>
                        <option value="guards" <?= $report_type == 'guards' ? 'selected' : '' ?>>Guards</option>
                        <option value="verifications" <?= $report_type == 'verifications' ? 'selected' : '' ?>>Verifications</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Generate Report</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="card-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="card-title">Total Users</div>
                <div class="card-value"><?= $stats['users']['count'] ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="card-icon">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div class="card-title">Total Guards</div>
                <div class="card-value"><?= $stats['guards']['count'] ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="card-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="card-title">Total Bookings</div>
                <div class="card-value"><?= $stats['bookings']['count'] ?></div>
            </div>
        </div>
        
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="content-section">
        <div class="section-header">
            <h3 class="section-title">
                <i class="bi bi-bar-chart"></i>
                System Overview
            </h3>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="chart-container">
                        <canvas id="registrationsChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-container">
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="content-section">
        <div class="section-header">
            <h3 class="section-title">
                <i class="bi bi-table"></i>
                <?= ucfirst($report_type) ?> Report
            </h3>
            <div class="d-flex gap-2 align-items-center">
                <button class="btn btn-sm btn-light" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print
                </button>
                <button class="btn btn-sm btn-export" id="exportBtn">
                    <i class="bi bi-file-earmark-excel"></i> Export to Excel
                </button>
            </div>
        </div>
        <div class="section-body">
            <div class="table-responsive">
                <table class="table table-striped" id="reportTable">
                    <thead>
                        <?php if($report_type == 'users'): ?>
                            <tr>
                                <th>#</th>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered On</th>
                            </tr>
                        <?php elseif($report_type == 'guards'): ?>
                            <tr>
                                <th>ID</th>
                                <th>Unique ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered On</th>
                            </tr>
                        <?php elseif($report_type == 'verifications'): ?>
                            <tr>
                                <th>Booking Code</th>
                                <th>User</th>
                                <th>Slot</th>
                                <th>Time</th>
                                <th>Verified By</th>
                                <th>Verified For</th>
                                <th>Verification Time</th>
                            </tr>
                        <?php else: // bookings ?>
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
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>
                        <?php if($report_type == 'users'): ?>
                            <?php $i = 1; while ($row = $data->fetch_assoc()): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><img src="uploads/<?= $row['profile_img'] ?>" class="profile-pic" alt="profile"></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= date("d M Y", strtotime($row['created_at'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                            
                        <?php elseif($report_type == 'guards'): ?>
                            <?php while($row = $data->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['unique_id'] ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                            
                        <?php elseif($report_type == 'verifications'): ?>
                            <?php while($row = $data->fetch_assoc()): 
                                $verificationType = ucfirst($row['action']);
                                $badgeClass = $row['action'] == 'check-in' ? 'badge-primary' : 'badge-warning';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['booking_code']) ?></td>
                                <td><?= htmlspecialchars($row['user_name']) ?> (ID: <?= htmlspecialchars($row['user_id']) ?>)</td>
                                <td>Slot <?= htmlspecialchars($row['slot_id']) ?></td>
                                <td><?= date('H:i', strtotime($row['start_time'])) ?> - <?= date('H:i', strtotime($row['end_time'])) ?></td>
                                <td><?= htmlspecialchars($row['guard_name']) ?> (<?= htmlspecialchars($row['guard_unique_id']) ?>)</td>
                                <td><span class="badge <?= $badgeClass ?>"><?= $verificationType ?></span></td>
                                <td><?= date('M d, Y H:i', strtotime($row['verification_time'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                            
                        <?php else: // bookings ?>
                            <?php $i = 1; while ($row = $data->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold"><?= $i++ ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border border-primary">
                                        <?= $row['booking_code'] ?? 'N/A' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2" style="width: 32px; height: 32px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <?= strtoupper(substr($row['user_name'], 0, 1)) ?>
                                        </div>
                                        <?= htmlspecialchars($row['user_name']) ?>
                                    </div>
                                </td>
                                <td>
                                    <i class="bi bi-geo-alt-fill text-primary"></i> 
                                    <?= $row['location'] ?>
                                </td>
                                <td>
                                    <?= date('M j, Y g:i A', strtotime($row['created_at'])) ?>
                                </td>
                                <td><?= date('M j, Y', strtotime($row['slot_date'])) ?></td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= date('g:i A', strtotime($row['start_time'])) ?> - <?= date('g:i A', strtotime($row['end_time'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge 
                                        <?php 
                                        // Map status to badge classes
                                        $status = strtolower($row['status']);
                                        switch($status) {
                                            case 'completed':
                                                echo 'badge-success';
                                                break;
                                            case 'confirmed':
                                                echo 'badge-primary';
                                                break;
                                            case 'pending':
                                                echo 'badge-warning';
                                                break;
                                            case 'cancelled':
                                                echo 'badge-danger';
                                                break;
                                            default:
                                                echo 'badge-info';
                                        }
                                        ?>
                                    ">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['checkin_time']): ?>
                                        <?= date('M j g:i A', strtotime($row['checkin_time'])) ?>
                                        <?php if ($row['checkin_guard']): ?>
                                            <div class="text-muted small">by <?= $row['checkin_guard'] ?></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'Completed' && $row['checkout_time']): ?>
                                        <?= date('M j g:i A', strtotime($row['checkout_time'])) ?>
                                        <?php if ($row['checkout_guard']): ?>
                                            <div class="text-muted small">by <?= $row['checkout_guard'] ?></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prepare data for charts
        const registrationData = {
            users: <?= $stats['users']['count'] ?>,
            guards: <?= $stats['guards']['count'] ?>
        };
        
        const activityData = {
            bookings: <?= $stats['bookings']['count'] ?>,
            verifications: <?= $stats['verifications']['count'] ?>
        };
        
        // Registrations Chart (Users and Guards)
        const regCtx = document.getElementById('registrationsChart').getContext('2d');
        const regChart = new Chart(regCtx, {
            type: 'bar',
            data: {
                labels: ['Users', 'Guards'],
                datasets: [{
                    label: 'Registrations',
                    data: [registrationData.users, registrationData.guards],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(54, 162, 235, 0.7)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'User and Guard Registrations',
                        font: {
                            size: 16
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Activity Chart (Bookings and Verifications)
        const actCtx = document.getElementById('activityChart').getContext('2d');
        const actChart = new Chart(actCtx, {
            type: 'doughnut',
            data: {
                labels: ['Bookings', 'Verifications'],
                datasets: [{
                    data: [activityData.bookings, activityData.verifications],
                    backgroundColor: [
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'System Activity',
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });
        
        // Excel Export button functionality
        document.getElementById('exportBtn').addEventListener('click', function() {
            // Get the table element
            const table = document.getElementById('reportTable');
            
            // Convert table to workbook
            const wb = XLSX.utils.table_to_book(table);
            
            // Generate Excel file and download it
            XLSX.writeFile(wb, `<?= ucfirst($report_type) ?>_Report_<?= date('Y-m-d') ?>.xlsx`);
        });
    });
</script>
<?php include("admin_footer.php"); ?>
</body>
</html>