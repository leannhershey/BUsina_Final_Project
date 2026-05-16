<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUSINA - Login Form</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

    <div class="login-box">
        <div class="login-header">
            <h1><span class="orange-text">BU</span>SINA</h1>
            <p>Vehicle Registration and Violation Management Portal</p>
        </div>

        <div class="error-banner" id="error-alert" style="display: none;"></div>

        <form id="loginForm" action="authenticate.php" method="POST">
            <div class="input-group">
                <label>ID Number or Email</label>
                <input type="text" id="username" name="username" placeholder="Enter ID Number or Email">
            </div>

            <div class="input-group">
                <label>Password</label> <!-- Mobile Number for Student/Faculty/Staff -->
                <input type="password" id="password" name="password" placeholder="••••••••">
            </div>

            <button type="submit" name="login-btn" class="btn-login">Log In</button>
        </form>
    </div>

    <script src="js/auth_validation.js"></script>
</body>
</html>