<?php
session_start();
require_once 'config/db_connect.php'; 

$limit = 5; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

$where_clause = "";
if (!empty($search)) {
    $where_clause = " WHERE v.plate_number LIKE '%$search%' 
                      OR o.first_name LIKE '%$search%' 
                      OR o.last_name LIKE '%$search%' 
                      OR vio.violation_type LIKE '%$search%'";
}

$total_sql = "SELECT COUNT(*) FROM violation vio
              INNER JOIN registration r ON vio.reg_id = r.reg_id
              INNER JOIN vehicle v ON r.vehicle_id = v.vehicle_id
              INNER JOIN owner o ON v.owner_id = o.owner_id" . $where_clause;

$total_result = mysqli_query($conn, $total_sql);
$total_rows = mysqli_fetch_array($total_result)[0];
$total_pages = ceil($total_rows / $limit);
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;
?>
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUSINA - Reported Violations</title>
    <link rel="stylesheet" href="css/read.css">
</head>
<body>

    <div class="dashboard-wrapper">
        
        <aside class="sidebar">
            <div class="sidebar-logo">
                <h1><span class="orange-text">BU</span>SINA</h1>
            </div>
            <nav class="sidebar-menu">
                <a href="#" class="menu-item">📊 Dashboard</a>
                <a href="owner_registration.php" class="menu-item">📝 Vehicle Registration</a>
                <a href="registered_vehicles.php" class="menu-item">🚗 Registered Vehicles</a>
                <a href="reported_violations.php" class="menu-item active">🚨 Reported Violations</a>
                <div class="menu-spacer"></div>
                <a href="login.php" class="menu-item logout">🚪 Log Out</a>
            </nav>
        </aside>

        <main class="main-content">
            
            <header class="workspace-header">
                <div class="header-title">
                    <h2>Traffic Infractions & Violations</h2>
                    <p>Track reported vehicle non-compliance and fine collection logs</p>
                </div>
                <div class="header-status-icons">
                    <span>🔔</span>
                    <span>👤</span>
                </div>
            </header>

            <div class="registration-panel">
                <div class="panel-section-title">
                    <h3>📋 Violation Tracking Ledger</h3>
                </div>

                <form method="GET" action="reported_violations.php" class="search-container">
                    <input type="text" name="search" class="search-input" placeholder="Search by Plate, Driver Name, or Violation Type..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn-search">Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="reported_violations.php" class="btn-clear">Clear Filter</a>
                    <?php endif; ?>
                </form>

                <div style="width: 100%; overflow-x: auto; display: block;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Driver / Owner Name</th>
                                <th>Plate Number</th>
                                <th>Violation Details</th>
                                <th>Incident Date</th>
                                <th>Fine Amount</th>
                                <th>Payment Status</th>
                                <th>Settlement Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $read_sql = "SELECT 
                                            vio.violation_id,
                                            o.first_name, 
                                            o.last_name, 
                                            v.plate_number, 
                                            vio.violation_type, 
                                            vio.violation_date, 
                                            vio.remarks,
                                            p.amount, 
                                            p.paid_status, 
                                            p.payment_date
                                         FROM violation vio
                                         INNER JOIN penalty p ON vio.violation_id = p.violation_id
                                         INNER JOIN registration r ON vio.reg_id = r.reg_id
                                         INNER JOIN vehicle v ON r.vehicle_id = v.vehicle_id
                                         INNER JOIN owner o ON v.owner_id = o.owner_id"
                                         . $where_clause . 
                                         " ORDER BY vio.violation_id DESC 
                                         LIMIT $limit OFFSET $offset";

                            $table_result = mysqli_query($conn, $read_sql);

                            if (mysqli_num_rows($table_result) > 0) {
                                while ($row = mysqli_fetch_assoc($table_result)) {
                                    $driver_name = $row['first_name'] . ' ' . $row['last_name'];
                                    
                                    $fine_formatted = '₱' . number_format($row['amount'], 2);
                                    
                                    $violation_date = date('M d, Y', strtotime($row['violation_date']));
                                    $payment_date = !empty($row['payment_date']) ? date('M d, Y', strtotime($row['payment_date'])) : '—';

                                    $status = htmlspecialchars($row['paid_status']);
                                    $status_class = 'unpaid-tag';
                                    if ($status === 'Paid') $status_class = 'paid-tag';
                                    if ($status === 'Waived') $status_class = 'waived-tag';
                                    
                                    echo "<tr>";
                                    echo "<td style='font-weight: bold; color: white;'>" . htmlspecialchars($driver_name) . "</td>";
                                    echo "<td><span class='plate-badge'>" . htmlspecialchars($row['plate_number']) . "</span></td>";
                                    echo "<td>";
                                    echo "<span style='display:block; font-weight:600; color:#38bdf8;'>" . htmlspecialchars($row['violation_type']) . "</span>";
                                    if (!empty($row['remarks'])) {
                                        echo "<small style='color:#94a3b8; display:block; margin-top:2px; max-width:250px; white-space:normal;'>" . htmlspecialchars($row['remarks']) . "</small>";
                                    }
                                    echo "</td>";
                                    echo "<td>" . $violation_date . "</td>";
                                    echo "<td style='font-weight: bold; color:#f43f5e;'>" . $fine_formatted . "</td>";
                                    echo "<td><span class='status-tag $status_class'>" . $status . "</span></td>";
                                    echo "<td>" . $payment_date . "</td>";
                                    echo "<td>";
                                    echo "  <a href='update_violation.php?id=" . $row['violation_id'] . "' title='Update' style='text-decoration:none; margin-right:10px;'>📝</a>";
                                    echo "  <a href='delete_handler.php?type=violation&id=" . $row['violation_id'] . "' title='Delete' onclick='return confirm(\"Are you sure you want to completely delete this violation record?\")' style='text-decoration:none;'>🗑️</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' style='text-align: center; color: #94a3b8; padding: 30px;'>No reported violations found in ledger files.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                    <div class="pagination-container">
                        <?php if ($page > 1): ?>
                            <a href="reported_violations.php?page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search); ?>" class="page-link">« Prev</a>
                        <?php endif; ?>

                        <?php
                        $range = 1; 
                        if ($page > ($range + 1)) {
                            echo '<a href="reported_violations.php?page=1&search='.urlencode($search).'" class="page-link">1</a>';
                            if ($page > ($range + 2)) echo '<span class="page-dots">...</span>';
                        }

                        for ($i = max(1, $page - $range); $i <= min($total_pages, $page + $range); $i++) {
                            $active_class = ($page === $i) ? 'active' : '';
                            echo '<a href="reported_violations.php?page='.$i.'&search='.urlencode($search).'" class="page-link '.$active_class.'">'.$i.'</a>';
                        }

                        if ($page < ($total_pages - $range)) {
                            if ($page < ($total_pages - $range - 1)) echo '<span class="page-dots">...</span>';
                            echo '<a href="reported_violations.php?page='.$total_pages.'&search='.urlencode($search).'" class="page-link">'.$total_pages.'</a>';
                        }
                        ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="reported_violations.php?page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search); ?>" class="page-link">Next »</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

</body>
</html>