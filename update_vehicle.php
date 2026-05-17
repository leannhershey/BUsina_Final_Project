<?php
session_start();
require_once 'config/db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: registered_vehicles.php");
    exit();
}

$reg_id = (int)$_GET['id'];

$fetch_sql = "SELECT r.*, v.*, o.* FROM registration r
              INNER JOIN vehicle v ON r.vehicle_id = v.vehicle_id
              INNER JOIN owner o ON v.owner_id = o.owner_id
              WHERE r.reg_id = $reg_id LIMIT 1";
$result = mysqli_query($conn, $fetch_sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: registered_vehicles.php");
    exit();
}

$data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-btn'])) {
    $first_name   = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $last_name    = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $email        = mysqli_real_escape_string($conn, trim($_POST['email']));
    $contact_no   = mysqli_real_escape_string($conn, trim($_POST['contact_no']));
    $owner_type   = mysqli_real_escape_string($conn, $_POST['owner_type']);
    $department   = mysqli_real_escape_string($conn, $_POST['department']);

    $plate_number = mysqli_real_escape_string($conn, strtoupper(trim($_POST['plate_number'])));
    $vehicle_type = mysqli_real_escape_string($conn, $_POST['vehicle_type']);
    $make         = mysqli_real_escape_string($conn, trim($_POST['make']));
    $model        = mysqli_real_escape_string($conn, trim($_POST['model']));
    $color        = mysqli_real_escape_string($conn, trim($_POST['color']));
    $year_model   = mysqli_real_escape_string($conn, trim($_POST['year_model']));
    $status       = mysqli_real_escape_string($conn, $_POST['status']);
    $acad_year    = mysqli_real_escape_string($conn, trim($_POST['acad_year']));

    $owner_id   = $data['owner_id'];
    $vehicle_id = $data['vehicle_id'];

    $update_owner = "UPDATE owner SET first_name='$first_name', last_name='$last_name', owner_type='$owner_type', contact_no='$contact_no', email='$email', department='$department' WHERE owner_id=$owner_id";

    $update_vehicle = "UPDATE vehicle SET plate_number='$plate_number', vehicle_type='$vehicle_type', make='$make', model='$model', color='$color', year_model='$year_model' WHERE vehicle_id=$vehicle_id";
    
    $update_reg = "UPDATE registration SET acad_year='$acad_year', status='$status' WHERE reg_id=$reg_id";

    if (mysqli_query($conn, $update_owner) && mysqli_query($conn, $update_vehicle) && mysqli_query($conn, $update_reg)) {
        header("Location: registered_vehicles.php?update=success");
        exit();
    } else {
        $error_msg = "Database Error: Failed to execute record updates.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUSINA - Update Vehicle Record</title>
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
                    <h2>Modify Vehicle Registry Entry</h2>
                    <p>Update technical specifications and access parameters</p>
                </div>
            </header>

            <?php if (isset($error_msg)): ?>
                <div class="error-banner" style="background: #ef4444; color: white; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                    ❌ <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <div class="registration-panel">
                <form method="POST" action="update_vehicle.php?id=<?php echo $reg_id; ?>">
                    
                    <div class="panel-section-title">
                        <h3>👤 Owner Profile Information</h3>
                    </div>
                    <div class="form-grid">
                        <div class="input-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" value="<?php echo htmlspecialchars($data['first_name']); ?>" required>
                        </div>
                        <div class="input-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" value="<?php echo htmlspecialchars($data['last_name']); ?>" required>
                        </div>
                        <div class="input-group">
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required>
                        </div>
                        <div class="input-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact_no" value="<?php echo htmlspecialchars($data['contact_no']); ?>" pattern="09[0-9]{9}" required>
                        </div>
                        <div class="input-group">
                            <label>Owner Type</label>
                            <select name="owner_type" required>
                                <option value="Student" <?php if($data['owner_type'] == 'Student') echo 'selected'; ?>>Student</option>
                                <option value="Faculty" <?php if($data['owner_type'] == 'Faculty') echo 'selected'; ?>>Faculty</option>
                                <option value="Staff" <?php if($data['owner_type'] == 'Staff') echo 'selected'; ?>>Staff</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Department</label>
                            <select name="department" required>
                                <option value="Biology" <?php if($data['department'] == 'Biology') echo 'selected'; ?>>Biology</option>
                                <option value="Chemistry" <?php if($data['department'] == 'Chemistry') echo 'selected'; ?>>Chemistry</option>
                                <option value="Computer Science" <?php if($data['department'] == 'Computer Science') echo 'selected'; ?>>Computer Science</option>
                                <option value="Information Technology" <?php if($data['department'] == 'Information Technology') echo 'selected'; ?>>Information Technology</option>
                                <option value="Meteorology" <?php if($data['department'] == 'Meteorology') echo 'selected'; ?>>Meteorology</option>
                            </select>
                        </div>
                    </div>

                    <div class="panel-section-title" style="margin-top: 30px;">
                        <h3>🚗 Vehicle Specs & Campus Status</h3>
                    </div>
                    <div class="form-grid">
                        <div class="input-group">
                            <label>Plate Number</label>
                            <input type="text" name="plate_number" value="<?php echo htmlspecialchars($data['plate_number']); ?>" pattern="[A-Za-z]{3}-[0-9]{4}" style="text-transform: uppercase;" required>
                        </div>
                        <div class="input-group">
                            <label>Vehicle Type</label>
                            <select name="vehicle_type" required>
                                <option value="Car" <?php if($data['vehicle_type'] == 'Car') echo 'selected'; ?>>Car</option>
                                <option value="Motorcycle" <?php if($data['vehicle_type'] == 'Motorcycle') echo 'selected'; ?>>Motorcycle</option>
                                <option value="Van" <?php if($data['vehicle_type'] == 'Van') echo 'selected'; ?>>Van</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Brand / Make</label>
                            <input type="text" name="make" value="<?php echo htmlspecialchars($data['make']); ?>" required>
                        </div>
                        <div class="input-group">
                            <label>Model Variant</label>
                            <input type="text" name="model" value="<?php echo htmlspecialchars($data['model']); ?>" required>
                        </div>
                        <div class="input-group">
                            <label>Vehicle Color</label>
                            <input type="text" name="color" value="<?php echo htmlspecialchars($data['color']); ?>" required>
                        </div>
                        <div class="input-group">
                            <label>Year Model</label>
                            <input type="number" name="year_model" min="1950" max="2027" value="<?php echo htmlspecialchars($data['year_model']); ?>" required>
                        </div>
                        <div class="input-group">
                            <label>Academic Year</label>
                            <input type="text" name="acad_year" value="<?php echo htmlspecialchars($data['acad_year']); ?>" required>
                        </div>
                        <div class="input-group">
                            <label>Clearance Status</label>
                            <select name="status" required>
                                <option value="Active" <?php if($data['status'] == 'Active') echo 'selected'; ?>>Active</option>
                                <option value="Expired" <?php if($data['status'] == 'Expired') echo 'selected'; ?>>Expired</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="registered_vehicles.php" class="btn-next" style="background: #475569; max-width: 150px;">⬅ Cancel</a>
                        <button type="submit" name="update-btn" class="btn-create">Save Changes ➔</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

</body>
</html>