<?php
session_start();
require_once 'config/db_connect.php'; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['first_name'])) {
    header("Location: owner_registration.php");
    exit();
}

$owner_id   = $_POST['owner_id'];
$first_name = $_POST['first_name'];
$last_name  = $_POST['last_name'];
$email      = $_POST['email'];
$contact_no = $_POST['contact_no'];
$owner_type = $_POST['owner_type'];
$department = $_POST['department'];
?>
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUSINA - Vehicle Specifications</title>
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
                <a href="reported_violations.php" class="menu-item">🚨 Reported Violations</a>
                <div class="menu-spacer"></div>
                <a href="login.php" class="menu-item logout">🚪 Log Out</a>
            </nav>
        </aside>

        <main class="main-content">
            
            <header class="workspace-header">
                <div class="header-title">
                    <h2>New Vehicle Registration</h2>
                    <p>Step 2: Input Vehicle Specifications</p>
                </div>
            </header>

            <div class="registration-panel">
                <form id="vehicleForm" action="process_registration.php" method="POST">
                    
                    <input type="hidden" name="owner_id" value="<?php echo htmlspecialchars($owner_id); ?>">
                    <input type="hidden" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>">
                    <input type="hidden" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <input type="hidden" name="contact_no" value="<?php echo htmlspecialchars($contact_no); ?>">
                    <input type="hidden" name="owner_type" value="<?php echo htmlspecialchars($owner_type); ?>">
                    <input type="hidden" name="department" value="<?php echo htmlspecialchars($department); ?>">

                    <div class="panel-section-title">
                        <h3>🚗 Vehicle Information</h3>
                    </div>

                    <div class="form-grid">
                        
                        <div class="input-group">
                            <label>Plate Number</label>
                            <input type="text" 
                                name="plate_number" 
                                placeholder="e.g., ABC-1234" 
                                pattern="[A-Za-z]{3}-[0-9]{4}" 
                                title="Plate format must follow exactly: 3 letters, a hyphen, and 4 numbers (e.g., ABC-1234)" 
                                style="text-transform: uppercase;" 
                                required>
                        </div>

                        <div class="input-group">
                            <label>Vehicle Type</label>
                            <select name="vehicle_type" required>
                                <option value="">Select vehicle type</option>
                                <option value="Car">Car</option>
                                <option value="Motorcycle">Motorcycle</option>
                                <option value="Van">Van</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>Brand / Make</label>
                            <input type="text" name="make" placeholder="e.g., Toyota, Honda, Suzuki" required>
                        </div>

                        <div class="input-group">
                            <label>Model Variant</label>
                            <input type="text" name="model" placeholder="e.g., Vios, Civic, Click 125i" required>
                        </div>

                        <div class="input-group">
                            <label>Vehicle Color</label>
                            <input type="text" name="color" placeholder="e.g., Matte Black" required>
                        </div>

                        <div class="input-group">
                            <label>Year Model</label>
                            <input type="number" name="year_model" min="1950" max="2027" placeholder="e.g., 2024" required>
                        </div>

                    </div>

                    <div class="form-actions">
                        <a href="owner_reg.php" class="btn-next" style="background: #475569; max-width: 150px;">⬅ Back</a>
                        <button type="submit" name="register-btn" class="btn-create">Submit Registration Application ➔</button>
                    </div>

                </form>
            </div>
        </main>
    </div>

</body>
</html>