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
                <label>First Name</label>
                <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
            </div>

            <div class="input-group">
                <label>Last Name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
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
                <label>Owner Type</label>
                <select id="owner_type" name="owner_type" required>
                    <option value="">Select owner type</option>
                    <option value="Student">Student</option>
                    <option value="Faculty">Faculty</option>
                    <option value="Staff">Staff</option>
                </select>
            </div>

            <div class="input-group">
                <label>Department</label>
                <select id="department" name="department" required>
                    <option value="">Select department</option>
                    <option value="Biology">Biology</option>
                    <option value="Chemistry">Chemistry</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Information Technology">Information Technology</option>
                    <option value="Meteorology">Meteorology</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" name="register-btn" class="btn-create">Save Owner Information</button>
                <a href="vehicle_reg.php" class="btn-next">Next</a>
            </div>
        </form>
    </div>

    <script src="js/create_validation.js"></script>
</body>
</html>