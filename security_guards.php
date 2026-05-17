<?php
session_start();
require_once 'config/db_connect.php'; 

$limit = 5; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$where_clause = "";
if (!empty($search)) {
    $where_clause = " WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR badge_number LIKE '%$search%'";
}

$total_rows = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) FROM security_guard" . $where_clause))[0];
$total_pages = ceil($total_rows / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUSINA - Security Personnel Directory</title>
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
                <a href="gate_logs.php" class="menu-item">🚧 Gate Monitoring Logs</a>
                <a href="reported_violations.php" class="menu-item">🚨 Reported Violations</a>
                <a href="security_guards.php" class="menu-item active">🛡️ Security Personnel</a>
                <div class="menu-spacer"></div>
                <a href="login.php" class="menu-item logout">🚪 Log Out</a>
            </nav>
        </aside>

        <main class="main-content">
            
            <header class="workspace-header">
                <div class="header-title">
                    <h2>Security Guards Directory</h2>
                    <p>View profiles, active shifts, and terminal gate post assignments of deployed personnel</p>
                </div>
            </header>

            <div class="registration-panel">
                <div class="panel-section-title">
                    <h3>📋 On-Duty Roster Personnel</h3>
                </div>

                <form method="GET" action="security_guards.php" class="search-container">
                    <input type="text" name="search" class="search-input" placeholder="Search by name, badge identifier..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn-search">Search Personnel</button>
                </form>

                <div class="table-responsive-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Guard ID</th>
                                <th>Guard Full Name</th>
                                <th>Badge Code Number</th>
                                <th>Assigned Shift Schedule</th>
                                <th>Active Station Post</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $guard_sql = "SELECT guard_id, first_name, last_name, badge_number, shift, gate_assigned 
                                          FROM security_guard"
                                          . $where_clause . 
                                          " ORDER BY guard_id ASC LIMIT $limit OFFSET $offset";
                            $res = mysqli_query($conn, $guard_sql);
                            if (mysqli_num_rows($res) > 0) {
                                while ($row = mysqli_fetch_assoc($res)) {
                                    echo "<tr>";
                                    echo "<td>#".htmlspecialchars($row['guard_id'])."</td>";
                                    echo "<td style='font-weight:bold; color:white;'>".htmlspecialchars($row['first_name'] . ' ' . $row['last_name'])."</td>";
                                    echo "<td><strong style='color:#f97316;'>".htmlspecialchars($row['badge_number'])."</strong></td>";
                                    echo "<td>".htmlspecialchars($row['shift'])."</td>";
                                    echo "<td>🚧 ".htmlspecialchars($row['gate_assigned'])."</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' style='text-align:center; padding:20px;'>No security officers found matching requirements.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($total_pages > 1): ?>
                    <div class="pagination-container">
                        
                        <?php if ($page > 1): ?>
                            <a href="security_guards.php?page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search); ?>" class="page-link">« Prev</a>
                        <?php endif; ?>

                        <?php
                        $range = 1; 

                        if ($page > ($range + 1)) {
                            echo '<a href="security_guards.php?page=1&search='.urlencode($search).'" class="page-link">1</a>';
                            if ($page > ($range + 2)) {
                                echo '<span class="page-dots">...</span>'; 
                            }
                        }

                        for ($i = max(1, $page - $range); $i <= min($total_pages, $page + $range); $i++) {
                            $active_class = ($page === $i) ? 'active' : '';
                            echo '<a href="security_guards.php?page='.$i.'&search='.urlencode($search).'" class="page-link '.$active_class.'">'.$i.'</a>';
                        }

                        if ($page < ($total_pages - $range)) {
                            if ($page < ($total_pages - $range - 1)) {
                                echo '<span class="page-dots">...</span>'; 
                            }
                            echo '<a href="security_guards.php?page='.$total_pages.'&search='.urlencode($search).'" class="page-link">'.$total_pages.'</a>';
                        }
                        ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="security_guards.php?page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search); ?>" class="page-link">Next »</a>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

</body>
</html>