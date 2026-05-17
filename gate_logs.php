<?php
session_start();
require_once 'config/db_connect.php'; 

$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'logs';

$limit = 5; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$where_clause = "";

if ($current_tab === 'stickers') {
    if (!empty($search)) {
        $where_clause = " WHERE s.sticker_code LIKE '%$search%' OR v.plate_number LIKE '%$search%'";
    }
    $total_sql = "SELECT COUNT(*) FROM sticker s 
                  INNER JOIN registration r ON s.reg_id = r.reg_id 
                  INNER JOIN vehicle v ON r.vehicle_id = v.vehicle_id" . $where_clause;
} else {
    if (!empty($search)) {
        $where_clause = " WHERE v.plate_number LIKE '%$search%' OR g.last_name LIKE '%$search%' OR m.gate_entry LIKE '%$search%'";
    }
    $total_sql = "SELECT COUNT(*) FROM monitoring_log m 
                  INNER JOIN vehicle v ON m.vehicle_id = v.vehicle_id 
                  INNER JOIN security_guard g ON m.guard_id = g.guard_id" . $where_clause;
}

$total_result = mysqli_query($conn, $total_sql);
$total_rows = mysqli_fetch_array($total_result)[0];
$total_pages = ceil($total_rows / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUSINA - Gate Monitoring Logs</title>
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
                <a href="gate_logs.php" class="menu-item active">🚧 Gate Monitoring Logs</a>
                <a href="reported_violations.php" class="menu-item">🚨 Reported Violations</a>
                <a href="security_guards.php" class="menu-item">🛡️ Security Personnel</a>
                <div class="menu-spacer"></div>
                <a href="login.php" class="menu-item logout">🚪 Log Out</a>
            </nav>
        </aside>

        <main class="main-content">
            
            <header class="workspace-header">
                <div class="header-title">
                    <h2>Gate Access & Clearance Tracking</h2>
                    <p>Monitor security passage entries and issued vehicle clearance identifiers</p>
                </div>
            </header>

            <div class="registration-panel">
                
                <div class="tab-row">
                    <a href="gate_logs.php?tab=logs" class="tab-button <?php if($current_tab === 'logs') echo 'active'; ?>">🚧 Traffic Event Logs</a>
                    <a href="gate_logs.php?tab=stickers" class="tab-button <?php if($current_tab === 'stickers') echo 'active'; ?>">🎟️ Issued Gate Stickers Inventory</a>
                </div>

                <form method="GET" action="gate_logs.php" class="search-container">
                    <input type="hidden" name="tab" value="<?php echo htmlspecialchars($current_tab); ?>">
                    <input type="text" name="search" class="search-input" 
                           placeholder="<?php echo ($current_tab === 'stickers') ? 'Search by Sticker Barcode or Plate...' : 'Search by Plate, Gate, or Guard...'; ?>" 
                           value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn-search">Filter List</button>
                </form>

                <div class="table-responsive-container">
                    <?php if ($current_tab === 'stickers'): ?>
                        
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Sticker ID</th>
                                    <th>Sticker Code / Barcode</th>
                                    <th>Linked Plate Number</th>
                                    <th>Registration Ref ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sticker_sql = "SELECT s.sticker_id, s.sticker_code, s.reg_id, v.plate_number 
                                                FROM sticker s
                                                INNER JOIN registration r ON s.reg_id = r.reg_id
                                                INNER JOIN vehicle v ON r.vehicle_id = v.vehicle_id"
                                                . $where_clause . 
                                                " ORDER BY s.sticker_id DESC LIMIT $limit OFFSET $offset";
                                $res = mysqli_query($conn, $sticker_sql);
                                if (mysqli_num_rows($res) > 0) {
                                    while ($row = mysqli_fetch_assoc($res)) {
                                        echo "<tr>";
                                        echo "<td>#".htmlspecialchars($row['sticker_id'])."</td>";
                                        echo "<td><strong style='color:#38bdf8; font-family:monospace;'>".htmlspecialchars($row['sticker_code'])."</strong></td>";
                                        echo "<td><span class='plate-badge'>".htmlspecialchars($row['plate_number'])."</span></td>";
                                        echo "<td>Ref Reg #".htmlspecialchars($row['reg_id'])."</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' style='text-align:center; padding:20px;'>No barcode stickers logged in system data registers.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>

                    <?php else: ?>
                        
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Log ID</th>
                                    <th>Plate Number</th>
                                    <th>Vehicle Detail</th>
                                    <th>Gate Terminal</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Logging Personnel</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $log_sql = "SELECT m.log_id, m.log_date, m.time_in, m.time_out, m.gate_entry, v.plate_number, v.make, v.model, g.first_name, g.last_name, g.badge_number 
                                            FROM monitoring_log m 
                                            INNER JOIN vehicle v ON m.vehicle_id = v.vehicle_id 
                                            INNER JOIN security_guard g ON m.guard_id = g.guard_id" 
                                            . $where_clause . 
                                            " ORDER BY m.log_id DESC LIMIT $limit OFFSET $offset";
                                
                                $res = mysqli_query($conn, $log_sql);
                                if(mysqli_num_rows($res) > 0) {
                                    while($row = mysqli_fetch_assoc($res)) {
                                        echo "<tr>";
                                        echo "<td>#".$row['log_id']."</td>";
                                        echo "<td><span class='plate-badge'>".htmlspecialchars($row['plate_number'])."</span></td>";
                                        echo "<td>".htmlspecialchars($row['make']." ".$row['model'])."</td>";
                                        echo "<td><strong>".htmlspecialchars($row['gate_entry'])."</strong></td>";
                                        echo "<td style='color:#10b981;'>".date('h:i A', strtotime($row['time_in']))."</td>";
                                        echo "<td style='color:#94a3b8;'>".(!empty($row['time_out']) ? date('h:i A', strtotime($row['time_out'])) : '—')."</td>";
                                        echo "<td>".htmlspecialchars($row['first_name']." ".$row['last_name'])." <small style='color:#64748b;'>(".$row['badge_number'].")</small></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' style='text-align:center; padding:20px;'>No entry events logged matching search conditions.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>

                    <?php endif; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                    <div class="pagination-container">
                        
                        <?php if ($page > 1): ?>
                            <a href="gate_logs.php?page=<?php echo ($page - 1); ?>&tab=<?php echo $current_tab; ?>&search=<?php echo urlencode($search); ?>" class="page-link">« Prev</a>
                        <?php endif; ?>

                        <?php
                        $range = 1; 

                        if ($page > ($range + 1)) {
                            echo '<a href="gate_logs.php?page=1&tab='.$current_tab.'&search='.urlencode($search).'" class="page-link">1</a>';
                            if ($page > ($range + 2)) {
                                echo '<span class="page-dots">...</span>'; 
                            }
                        }

                        for ($i = max(1, $page - $range); $i <= min($total_pages, $page + $range); $i++) {
                            $active_class = ($page === $i) ? 'active' : '';
                            echo '<a href="gate_logs.php?page='.$i.'&tab='.$current_tab.'&search='.urlencode($search).'" class="page-link '.$active_class.'">'.$i.'</a>';
                        }

                        if ($page < ($total_pages - $range)) {
                            if ($page < ($total_pages - $range - 1)) {
                                echo '<span class="page-dots">...</span>'; 
                            }
                            echo '<a href="gate_logs.php?page='.$total_pages.'&tab='.$current_tab.'&search='.urlencode($search).'" class="page-link">'.$total_pages.'</a>';
                        }
                        ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="gate_logs.php?page=<?php echo ($page + 1); ?>&tab=<?php echo $current_tab; ?>&search=<?php echo urlencode($search); ?>" class="page-link">Next »</a>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

</body>
</html>