<?php
session_start();
require_once 'config/db_connect.php'; 

if (isset($_POST['username'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username)) {
        header("Location: login.html?error=empty_user");
        exit();
    }

    // If the security guard used the numeric values (Password field is skipped)
    if (is_numeric($username)) {
        $guard_sql = "SELECT * FROM security_guard WHERE guard_id = '$username' LIMIT 1";
        $guard_result = mysqli_query($conn, $guard_sql);

        if (mysqli_num_rows($guard_result) == 1) {
            $row = mysqli_fetch_assoc($guard_result);
            
            $_SESSION['user_id'] = $row['guard_id'];
            $_SESSION['user_type'] = 'Guard';
            
            header("Location: dashboard.php");
            exit();
        }
    }

    // Student/Faculty/Staff log check
    if (empty($password)) {
        header("Location: login.html?error=empty_pass");
        exit();
    }

    $owner_sql = "SELECT * FROM owner WHERE email = '$username' AND contact_no = '$password' LIMIT 1";
    $owner_result = mysqli_query($conn, $owner_sql);

    if (mysqli_num_rows($owner_result) == 1) {
        $row = mysqli_fetch_assoc($owner_result);
        
        $_SESSION['user_id'] = $row['owner_id'];
        $_SESSION['user_type'] = $row['owner_type'];
        
        header("Location: dashboard.php");
        exit();
    }

    // Fail redirect route trigger
    header("Location: login.html?error=wrong");
    exit();

} else {
    header("Location: login.html");
    exit();
}
?>