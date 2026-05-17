<?php
session_start();
require_once 'config/db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: reported_violations.php");
    exit();
}

$violation_id = (int)$_GET['id'];


$fetch_sql = "SELECT vio.*, p.amount, p.paid_status, p.payment_date, o.first_name, o.last_name, v.plate_number
              FROM violation vio
              INNER JOIN penalty p ON vio.violation_id = p.violation_id
              INNER JOIN registration r ON vio.reg_id = r.reg_id
              INNER JOIN vehicle v ON r.vehicle_id = v.vehicle_id
              INNER JOIN owner o ON v.owner_id = o.owner_id
              WHERE vio.violation_id = $violation_id LIMIT 1";

$result = mysqli_query($conn, $fetch_sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: reported_violations.php");
    exit();
}

$data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-vio-btn'])) {
    $violation_type = mysqli_real_escape_string($conn, trim($_POST['violation_type']));
    $remarks        = mysqli_real_escape_string($conn, trim($_POST['remarks']));
    $violation_date = mysqli_real_escape_string($conn, $_POST['violation_date']);
    $amount         = mysqli_real_escape_string($conn, $_POST['amount']);
    $paid_status    = mysqli_real_escape_string($conn, $_POST['paid_status']);
    

    $payment_date = ($paid_status === 'Paid') ? date('Y-m-d') : "NULL";

    $update_vio_sql = "UPDATE violation SET violation_type='$violation_type', remarks='$remarks', violation_date='$violation_date' WHERE violation_id=$violation_id";
    
    if($payment_date === "NULL") {
        $update_p_sql = "UPDATE penalty SET amount='$amount', paid_status='$paid_status', payment_date=NULL WHERE violation_id=$violation_id";
    } else {
        $update_p_sql = "UPDATE penalty SET amount='$amount', paid_status='$paid_status', payment_date='$payment_date' WHERE violation_id=$violation_id";
    }

    if (mysqli_query($conn, $update_vio_sql) && mysqli_query($conn, $update_p_sql)) {
        header("Location: reported_violations.php?update=success");
        exit();
    } else {
        $error_msg = "Database Error: Could not adjust violation ledger elements.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUSINA - Update Traffic Violation</title>
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
                <a href="registered_vehicles.php" class="menu-item">🚗 Registered Vehicles</a>
                <a href="gate_logs.php" class="menu-item">🚧 Gate Monitoring Logs</a>   
                <a href="reported_violations.php" class="menu-item active">🚨 Reported Violations</a>
                <a href="security_guards.php" class="menu-item">🛡️ Security Personnel</a> 
                <div class="menu-spacer"></div>
                <a href="login.php" class="menu-item logout">🚪 Log Out</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="workspace-header">
                <div class="header-title">
                    <h2>Update Infraction File</h2>
                    <p>Log penalty terms, adjustments, and settlement statuses</p>
                </div>
            </header>

            <?php if (isset($error_msg)): ?>
                <div class="error-banner" style="background: #ef4444; color: white; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                    ❌ <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <div class="registration-panel">
                <form method="POST" action="update_violation.php?id=<?php echo $violation_id; ?>">
                    
                    <div class="panel-section-title">
                        <h3>📋 Core Incident Details</h3>
                    </div>
                    <div class="form-grid">
                        <div class="input-group">
                            <label>Driver / Owner</label>
                            <input type="text" value="<?php echo htmlspecialchars($data['first_name'] . ' ' . $data['last_name']); ?>" style="background: #334155; color: #94a3b8; cursor: not-allowed;" readonly>
                        </div>
                        <div class="input-group">
                            <label>Plate Number Involved</label>
                            <input type="text" value="<?php echo htmlspecialchars($data['plate_number']); ?>" style="background: #334155; color: #38bdf8; font-weight: bold; cursor: not-allowed;" readonly>
                        </div>
                        <div class="input-group">
                            <label>Violation Classification</label>
                            <input type="text" name="violation_type" value="<?php echo htmlspecialchars($data['violation_type']); ?>" required>
                        </div>
                        <div class="input-group">
                            <label>Incident Date</label>
                            <input type="date" name="violation_date" value="<?php echo $data['violation_date']; ?>" required>
                        </div>
                        <div class="input-group" style="grid-column: span 2;">
                            <label>Officer Remarks / Context</label>
                            <textarea name="remarks" rows="3" style="width:100%; background:#1e293b; border:1px solid #475569; border-radius:6px; padding:10px; color:white; resize:none;"><?php echo htmlspecialchars($data['remarks']); ?></textarea>
                        </div>
                    </div>

                    <div class="panel-section-title" style="margin-top: 30px;">
                        <h3>💰 Assessment and Billing</h3>
                    </div>
                    <div class="form-grid">
                        <div class="input-group">
                            <label>Fine Amount (PHP)</label>
                            <input type="number" step="0.01" name="amount" value="<?php echo $data['amount']; ?>" required>
                        </div>
                        <div class="input-group">
                            <label>Payment Ledger Status</label>
                            <select name="paid_status" required>
                                <option value="Unpaid" <?php if($data['paid_status'] == 'Unpaid') echo 'selected'; ?>>Unpaid</option>
                                <option value="Paid" <?php if($data['paid_status'] == 'Paid') echo 'selected'; ?>>Paid</option>
                                <option value="Waived" <?php if($data['paid_status'] == 'Waived') echo 'selected'; ?>>Waived</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="reported_violations.php" class="btn-next" style="background: #475569; max-width: 150px;">⬅ Cancel</a>
                        <button type="submit" name="update-vio-btn" class="btn-create">Commit Adjustments ➔</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

</body>
</html>