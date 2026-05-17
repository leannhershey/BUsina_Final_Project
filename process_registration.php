<?php
session_start();
require_once 'config/db_connect.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register-btn'])) {

    $first_name   = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $last_name    = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $email        = mysqli_real_escape_string($conn, trim($_POST['email']));
    $contact_no   = mysqli_real_escape_string($conn, trim($_POST['contact_no']));
    $owner_type   = mysqli_real_escape_string($conn, $_POST['owner_type']);
    $department   = mysqli_real_escape_string($conn, trim($_POST['department']));

    $plate_number = mysqli_real_escape_string($conn, strtoupper(trim($_POST['plate_number'])));
    $vehicle_type = mysqli_real_escape_string($conn, $_POST['vehicle_type']);
    $make         = mysqli_real_escape_string($conn, trim($_POST['make']));
    $model        = mysqli_real_escape_string($conn, trim($_POST['model']));
    $color        = mysqli_real_escape_string($conn, trim($_POST['color']));
    $year_model   = mysqli_real_escape_string($conn, trim($_POST['year_model']));

    $check_plate_sql = "SELECT plate_number FROM vehicle WHERE plate_number = '$plate_number' LIMIT 1";
    $check_result = mysqli_query($conn, $check_plate_sql);

    if (mysqli_num_rows($check_result) > 0) {
        header("Location: owner_registration.php?error=duplicate_plate");
        exit();
    }

    $owner_sql = "INSERT INTO owner (first_name, last_name, owner_type, contact_no, email, department) 
                  VALUES ('$first_name', '$last_name', '$owner_type', '$contact_no', '$email', '$department')";
    
    if (mysqli_query($conn, $owner_sql)) {
        
        $new_owner_id = mysqli_insert_id($conn);

        $vehicle_sql = "INSERT INTO vehicle (owner_id, plate_number, vehicle_type, make, model, color, year_model) 
                        VALUES ('$new_owner_id', '$plate_number', '$vehicle_type', '$make', '$model', '$color', '$year_model')";
        
        if (mysqli_query($conn, $vehicle_sql)) {
            
            $new_vehicle_id = mysqli_insert_id($conn);

            $current_acad_year = "2025-2026"; 
            $reg_date = date('Y-m-d');
            $expiry_date = date('Y-m-d', strtotime('+1 year')); 

            $registration_sql = "INSERT INTO registration (vehicle_id, reg_date, expiry_date, acad_year, status) 
                                 VALUES ('$new_vehicle_id', '$reg_date', '$expiry_date', '$current_acad_year', 'Active')";
            
            if (mysqli_query($conn, $registration_sql)) {
                
                header("Location: registered_vehicles.php?success=registered");
                exit();
                
            } else {
                echo "Database Error: Could not compile registration record.";
            }
        } else {
            echo "Database Error: Could not save vehicle specifications.";
        }
    } else {
        echo "Database Error: Could not establish owner profile entry.";
    }

} else {
    header("Location: owner_registration.php");
    exit();
}
?>