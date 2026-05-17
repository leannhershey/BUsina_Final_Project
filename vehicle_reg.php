<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUSINA - Vehicle Details</title>
    <link rel="stylesheet" href="css/create.css">
</head>
<body>
    <div class="create-box">
        <div class="create-header">
            <h1><span class="orange-text">BU</span>SINA</h1>
            <p>Vehicle Registration & Owner Profile Details</p>
        </div>

        <h3>🚗 Vehicle Information</h3>
            <div class="input-group">
                <label>Plate Number</label>
                <input type="text" id="plate_number" name="plate_number" placeholder="e.g., ABC1234" pattern="[A-Za-z0-9 ]+" style="text-transform: uppercase;" required>
            </div>

            <div class="input-group">
                <label>Vehicle Model / Description</label>
                <input type="text" id="vehicle_model" name="vehicle_model" placeholder="e.g., Toyota Vios Black" required>
            </div>

