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
                      OR o.owner_type LIKE '%$search%'";
}

$total_sql = "SELECT COUNT(*) FROM registration r
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
    <title>BUSINA - Registered Vehicles</title>
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
                <a href="registered_vehicles.php" class="menu-item active">🚗 Registered Vehicles</a>
                <a href="reported_violations.php" class="menu-item">🚨 Reported Violations</a>
                <div class="menu-spacer"></div>
                <a href="login.php" class="menu-item logout">🚪 Log Out</a>
            </nav>
        </aside>

        <main class="main-content">
            
            <header class="workspace-header">
                <div class="header-title">
                    <h2>Registered Vehicles Database</h2>
                    <p>Live registry of active campus vehicle access clearances</p>
                </div>
                <div class="header-status-icons">
                    <span>🔔</span>
                    <span>👤</span>
                </div>
            </header>

            <?php if (isset($_GET['success']) && $_GET['success'] == 'registered'): ?>
                <div class="success-banner" style="background: #10b981; color: white; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-size: 14px;">
                    🎉 Success! New vehicle record has been successfully committed to the registry.
                </div>
            <?php endif; ?>

            <div class="registration-panel">
                <div class="panel-section-title">
                    <h3>📋 Registered Vehicles Master List</h3>
                </div>

                <form method="GET" action="registered_vehicles.php" class="search-container">
                    <input type="text" name="search" class="search-input" placeholder="Search by Plate Number, Name, or Owner Type..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn-search">Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="registered_vehicles.php" class="btn-clear">Clear Filter</a>
                    <?php endif; ?>
                </form>

                <div style="width: 100%; overflow-x: auto; display: block;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Owner Name</th>
                                <th>Type</th>
                                <th>Department</th>
                                <th>Plate Number</th>
                                <th>Vehicle Specification</th>
                                <th>Academic Year</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $read_sql = "SELECT 
                                            r.reg_id,
                                            o.first_name, 
                                            o.last_name, 
                                            o.owner_type, 
                                            o.department,
                                            v.plate_number, 
                                            v.vehicle_type,
                                            v.make, 
                                            v.model, 
                                            v.color,
                                            v.year_model,
                                            r.acad_year, 
                                            r.status
                                         FROM registration r
                                         INNER JOIN vehicle v ON r.vehicle_id = v.vehicle_id
                                         INNER JOIN owner o ON v.owner_id = o.owner_id"
                                         . $where_clause . 
                                         " ORDER BY r.reg_id DESC 
                                         LIMIT $limit OFFSET $offset";

                            $table_result = mysqli_query($conn, $read_sql);

                            if (mysqli_num_rows($table_result) > 0) {
                                while ($row = mysqli_fetch_assoc($table_result)) {
                                    $full_name = $row['first_name'] . ' ' . $row['last_name'];
                                    $vehicle_desc = $row['year_model'] . ' ' . $row['color'] . ' ' . $row['make'] . ' ' . $row['model'] . ' (' . $row['vehicle_type'] . ')';
                                    
                                    echo "<tr>";
                                    echo "<td style='font-weight: bold; color: white;'>" . htmlspecialchars($full_name) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['owner_type']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['department'] ?? 'N/A') . "</td>";
                                    echo "<td><span class='plate-badge'>" . htmlspecialchars($row['plate_number']) . "</span></td>";
                                    echo "<td>" . htmlspecialchars($vehicle_desc) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['acad_year']) . "</td>";
                                    
                                    $status = htmlspecialchars($row['status']);
                                    $status_class = ($status === 'Active') ? 'active-tag' : 'expired-tag';
                                    
                                    echo "<td><span class='status-tag $status_class'>" . $status . "</span></td>";
                                    echo "<td>";
                                    echo "  <a href='update_vehicle.php?id=" . $row['reg_id'] . "' title='Update' style='text-decoration:none; margin-right:10px;'>📝</a>";
                                    echo "  <a href='delete_handler.php?type=vehicle&id=" . $row['reg_id'] . "' title='Delete' onclick='return confirm(\"Are you sure you want to completely delete this vehicle registration record?\")' style='text-decoration:none;'>🗑️</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' style='text-align: center; color: #94a3b8; padding: 30px;'>No matching records found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                    <div class="pagination-container">
                        
                        <?php if ($page > 1): ?>
                            <a href="registered_vehicles.php?page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search); ?>" class="page-link">« Prev</a>
                        <?php endif; ?>

                        <?php
                        $range = 1; 

                        if ($page > ($range + 1)) {
                            echo '<a href="registered_vehicles.php?page=1&search='.urlencode($search).'" class="page-link">1</a>';
                            if ($page > ($range + 2)) {
                                echo '<span class="page-dots">...</span>'; 
                            }
                        }

                        for ($i = max(1, $page - $range); $i <= min($total_pages, $page + $range); $i++) {
                            $active_class = ($page === $i) ? 'active' : '';
                            echo '<a href="registered_vehicles.php?page='.$i.'&search='.urlencode($search).'" class="page-link '.$active_class.'">'.$i.'</a>';
                        }

                        if ($page < ($total_pages - $range)) {
                            if ($page < ($total_pages - $range - 1)) {
                                echo '<span class="page-dots">...</span>'; 
                            }
                            echo '<a href="registered_vehicles.php?page='.$total_pages.'&search='.urlencode($search).'" class="page-link">'.$total_pages.'</a>';
                        }
                        ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="registered_vehicles.php?page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search); ?>" class="page-link">Next »</a>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

</body>
</html>