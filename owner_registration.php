<?php
session_start();
require_once 'config/db_connect.php'; 

$next_id = 1;

$query = "SHOW TABLE STATUS LIKE 'owner'";
$result = mysqli_query($conn, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $next_id = $row['Auto_increment'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUSINA - Vehicle Registration</title>
    <link rel="stylesheet" href="css/create.css">
</head>
<body>

    <div class="dashboard-wrapper">
        
        <aside class="sidebar">
            <div class="sidebar-logo">
                <h1><span class="orange-text">BU</span>SINA</h1>
            </div>
            <nav class="sidebar-menu">
                <a href="#" class="menu-item">📊 Dashboard</a>
                <a href="owner_registration.php" class="menu-item active">📝 Vehicle Registration</a>
                <a href="registered_vehicles.php" class="menu-item">🚗 Registered Vehicles</a>
                <a href="gate_logs.php" class="menu-item">🚧 Gate Monitoring Logs</a>   
                <a href="reported_violations.php" class="menu-item">🚨 Reported Violations</a>
                <a href="security_guards.php" class="menu-item">🛡️ Security Personnel</a> 
                <div class="menu-spacer"></div>
                <a href="login.php" class="menu-item logout">🚪 Log Out</a>
            </nav>
        </aside>

        <main class="main-content">
            
            <header class="workspace-header">
                <div class="header-title">
                    <h2>New Vehicle Registration</h2>
                    <p>Step 1: Setup Owner Profile</p>
                </div>
            </header>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'duplicate_plate'): ?>
                <div class="error-banner">
                    ❌ Error: That plate number is already registered in the system!
                </div>
            <?php endif; ?>

            <div class="registration-panel">
                <form id="ownerForm" action="vehicle_registration.php" method="POST">
                    
                    <div class="panel-section-title">
                        <h3>👤 Owner Information</h3>
                    </div>

                    <div class="form-grid">
                        
                        <div class="input-group" style="grid-column: span 2;">
                            <label>System Assigned Owner ID</label>
                            <input type="text" name="owner_id" value="<?php echo $next_id; ?>" style="background: #334155; color: #94a3b8; cursor: not-allowed;" readonly>
                        </div>

                        <div class="input-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" placeholder="Enter first name" required>
                        </div>

                        <div class="input-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" placeholder="Enter last name" required>
                        </div>

                        <div class="input-group">
                            <label>Email Address</label>
                            <input type="email" name="email" placeholder="firstname.lastname20@bicol-u.edu.ph" required>
                        </div>

                        <div class="input-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact_no" placeholder="e.g., 09123456789" pattern="09[0-9]{9}" required>
                        </div>

                        <div class="input-group">
                            <label>Owner Type</label>
                            <select name="owner_type" required>
                                <option value="">Select owner type</option>
                                <option value="Student">Student</option>
                                <option value="Faculty">Faculty</option>
                                <option value="Staff">Staff</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>Department</label>
                            <select name="department" required>
                                <option value="">Select department</option>
                                <option value="Biology">Biology</option>
                                <option value="Chemistry">Chemistry</option>
                                <option value="Computer Science">Computer Science</option>
                                <option value="Information Technology">Information Technology</option>
                                <option value="Meteorology">Meteorology</option>
                            </select>
                        </div>

                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-next">Next Step (Vehicle Specifications) ➔</button>
                    </div>

                </form>
            </div>
        </main>
    </div>

</body>
</html>