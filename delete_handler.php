<?php
session_start();
require_once 'config/db_connect.php';

if (isset($_GET['type']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $type = $_GET['type'];
    $id = (int)$_GET['id'];

    if ($type === 'vehicle') {

        $get_ids_sql = "SELECT vehicle_id FROM registration WHERE reg_id = $id LIMIT 1";
        $get_ids_res = mysqli_query($conn, $get_ids_sql);
        
        if (mysqli_num_rows($get_ids_res) > 0) {
            $row = mysqli_fetch_assoc($get_ids_res);
            $vehicle_id = $row['vehicle_id'];
            

            $get_owner_sql = "SELECT owner_id FROM vehicle WHERE vehicle_id = $vehicle_id LIMIT 1";
            $get_owner_res = mysqli_query($conn, $get_owner_sql);
            $owner_row = mysqli_fetch_assoc($get_owner_res);
            $owner_id = $owner_row['owner_id'];

            $delete_reg = mysqli_query($conn, "DELETE FROM registration WHERE reg_id = $id");
            
            $delete_veh = mysqli_query($conn, "DELETE FROM vehicle WHERE vehicle_id = $vehicle_id");
        
            $delete_own = mysqli_query($conn, "DELETE FROM owner WHERE owner_id = $owner_id");
        }
        
        header("Location: registered_vehicles.php?delete=success");
        exit();

    } elseif ($type === 'violation') {
       
        $delete_penalty = mysqli_query($conn, "DELETE FROM penalty WHERE violation_id = $id");

        $delete_violation = mysqli_query($conn, "DELETE FROM violation WHERE violation_id = $id");
        
        header("Location: reported_violations.php?delete=success");
        exit();
    }
}


header("Location: dashboard.php");
exit();
?>