<?php
session_start();
include "../config/koneksi.php";

// Cek Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Ambil Week yang Sedang Aktif
$q_week = $koneksi->query("SELECT setting_value FROM system_settings WHERE setting_key = 'active_week'");
$current_week = ($q_week->num_rows > 0) ? $q_week->fetch_assoc()['setting_value'] : 'Week 1';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>System Settings - VCT Admin</title>
    <link rel="icon" type="image/png" href="../assets/images/logoValo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .setting-box { 
            max-width: 500px; margin: 50px auto; background: #1b2733; padding: 40px; 
            border-radius: 8px; border: 1px solid #333; text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }
        .current-status {
            background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 10px; 
            border-radius: 4px; margin-bottom: 30px; border: 1px solid #10b981; font-weight: bold;
        }
        .form-select {
            width: 100%; background: #0f1923; border: 1px solid #555; color: white; 
            padding: 15px; border-radius: 4px; font-size: 16px; font-weight: bold; margin-bottom: 20px;
        }
        .btn-save {
            background: #ff4655; color: white; width: 100%; padding: 15px; border: none; 
            border-radius: 4px; font-weight: bold; font-size: 16px; cursor: pointer; transition: 0.3s;
        }
        .btn-save:hover { background: #d93c48; }
    </style>
</head>
<body class="admin-body">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header-bar">
            <h2 class="page-title">EDIT MATCH DETAILS</h2>
        </div>

        <div class="setting-box">
            <h3 style="color:white; margin-bottom:10px;">ACTIVE PREDICTION WEEK</h3>
            <p style="color:#aaa; margin-bottom:20px;">Tentukan minggu mana yang terbuka untuk prediksi user.</p>

            <div class="current-status">
                STATUS SAAT INI: <?php echo strtoupper($current_week); ?>
            </div>

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
                <div style="color:#10b981; margin-bottom:20px; font-weight:bold;">✅ Berhasil diupdate!</div>
            <?php endif; ?>

            <form action="../action/update_week.php" method="POST">
                <select name="active_week" class="form-select">
                    <option value="Week 1" <?php echo ($current_week == 'Week 1') ? 'selected' : ''; ?>>WEEK 1</option>
                    <option value="Week 2" <?php echo ($current_week == 'Week 2') ? 'selected' : ''; ?>>WEEK 2</option>
                    <option value="Week 3" <?php echo ($current_week == 'Week 3') ? 'selected' : ''; ?>>WEEK 3</option>
                    <option value="Week 4" <?php echo ($current_week == 'Week 4') ? 'selected' : ''; ?>>WEEK 4</option>
                    <option value="Week 5" <?php echo ($current_week == 'Week 5') ? 'selected' : ''; ?>>WEEK 5</option>
                    <option value="Playoff" <?php echo ($current_week == 'Playoff') ? 'selected' : ''; ?>>PLAYOFFS</option>
                </select>
                
                <button type="submit" class="btn-save">UPDATE SYSTEM</button>
            </form>
        </div>
    </div>

</body>
</html>