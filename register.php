<?php
require_once 'config/db_connect.php';

if (isset($_POST['register-btn'])) {
    // 1. Sanitize input data
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $contact_no = mysqli_real_escape_string($conn, trim($_POST['contact_no']));
    $owner_type = mysqli_real_escape_string($conn, $_POST['owner_type']);
    $plate_number = mysqli_real_escape_string($conn, strtoupper(trim($_POST['plate_number'])));
    $vehicle_model = mysqli_real_escape_string($conn, trim($_POST['vehicle_model']));

    // 2. Ensure no fields are empty
    if (empty($full_name) || empty($email) || empty($contact_no) || empty($owner_type) || empty($plate_number) || empty($vehicle_model)) {
        header("Location: register_vehicle.php?error=empty_fields");
        exit();
    }

    // 3. Check if the plate number already exists in vehicle storage database
    $check_plate_sql = "SELECT plate_number FROM vehicle WHERE plate_number = '$plate_number' LIMIT 1";
    $check_result = mysqli_query($conn, $check_plate_sql);

    if (mysqli_num_rows($check_result) > 0) {
        // Redirection route triggered if duplicate plate is found
        header("Location: register_vehicle.php?error=duplicate_plate");
        exit();
    }

    // 4. Quick placeholder successful execution redirect
    echo "Validation passed! Plate is unique. Proceeding to save data...";
    
} else {
    header("Location: register_vehicle.php");
    exit();
}
?>