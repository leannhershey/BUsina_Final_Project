<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUSINA - Vehicle Registration</title>
    <link rel="stylesheet" href="css/create.css">
</head>
<body>
    <div class="create-box">
        <div class="create-header">
            <h1><span class="orange-text">BU</span>SINA</h1>
            <p>Vehicle Registration & Owner Profile Setup</p>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'duplicate_plate'): ?>
            <div class="error-banner" style="color: red; margin-bottom: 15px;">
                ❌ Error: This plate number is already registered in the system!
            </div>
        <?php endif; ?>

        <form id="createForm" action="register.php" method="POST">
            
            <h3>👤 Owner Information</h3>
            <div class="input-group">
                <label>Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            </div>

            <div class="input-group">
                <label>Contact Number</label>
                <input type="text" id="contact_no" name="contact_no" placeholder="e.g., 09123456789" pattern="09[0-9]{9}" title="Must be an 11-digit mobile number starting with 09" required>
            </div>

            <div class="input-group">
                <label>User Type</label>
                <select id="owner_type" name="owner_type" required>
                    <option value="">Select user type</option>
                    <option value="Student">Student</option>
                    <option value="Faculty">Faculty</option>
                    <option value="Staff">Staff</option>
                </select>
            </div>

            <hr style="border: 0; border-top: 1px dashed #ccc; margin: 20px 0;">

            <h3>🚗 Vehicle Information</h3>
            <div class="input-group">
                <label>Plate Number</label>
                <input type="text" id="plate_number" name="plate_number" placeholder="e.g., ABC1234" pattern="[A-Za-z0-9 ]+" style="text-transform: uppercase;" required>
            </div>

            <div class="input-group">
                <label>Vehicle Model / Description</label>
                <input type="text" id="vehicle_model" name="vehicle_model" placeholder="e.g., Toyota Vios Black" required>
            </div>

            <button type="submit" name="register-btn" class="btn-create">Submit Registration</button>
        </form>

        <p class="login-link">Already have an account? <a href="login.php">Log in here</a></p>
    </div>

    <script src="js/create_validation.js"></script>
</body>
</html>