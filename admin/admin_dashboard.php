<?php
session_start();
include "../config/koneksi.php";

// 1. CEK AKSES ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// 2. STATISTIK DATABASE
$total_teams = $koneksi->query("SELECT COUNT(*) as total FROM team")->fetch_assoc()['total'];
$total_matches_done = $koneksi->query("SELECT COUNT(*) as total FROM match_esports WHERE team1_score > 0 OR team2_score > 0")->fetch_assoc()['total'];
$total_matches_up = $koneksi->query("SELECT COUNT(*) as total FROM match_esports WHERE team1_score = 0 AND team2_score = 0")->fetch_assoc()['total'];
$total_users = $koneksi->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'")->fetch_assoc()['total'];
$total_preds = $koneksi->query("SELECT COUNT(*) as total FROM pickem_predictions")->fetch_assoc()['total'];
$q_week = $koneksi->query("SELECT setting_value FROM system_settings WHERE setting_key = 'active_week'");
$active_week = ($q_week->num_rows > 0) ? $q_week->fetch_assoc()['setting_value'] : 'Week 1';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>VCT Admin Control</title>

    <link rel="icon" type="image/png" href="../assets/images/logoValo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>

    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

    <div class="main-content">
        
        <div class="header-bar">
            <h2 class="page-title">DASHBOARD OVERVIEW</h2>
            <div class="admin-profile">
                <div style="text-align: right;">
                    <div style="font-weight: bold;"><?php echo $_SESSION['username']; ?></div>
                    <div style="font-size: 12px; color: #ff4655;">ADMINISTRATOR</div>
                </div>
                <img src="../<?php echo $_SESSION['avatar'] ?? 'assets/images/default_agent.png'; ?>" class="admin-avatar">
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info"><h3><?php echo $total_matches_done; ?></h3><p>Matches Finished</p></div>
                <i class="fas fa-check-circle stat-icon"></i>
            </div>
            <div class="stat-card">
                <div class="stat-info"><h3><?php echo $total_matches_up; ?></h3><p>Matches Upcoming</p></div>
                <i class="fas fa-calendar-alt stat-icon"></i>
            </div>
             <div class="stat-card">
                <div class="stat-info"><h3><?php echo $total_preds; ?></h3><p>User Predictions</p></div>
                <i class="fas fa-chart-line stat-icon"></i>
            </div>
            <div class="stat-card">
                <div class="stat-info"><h3><?php echo $total_teams; ?></h3><p>Registered Teams</p></div>
                <i class="fas fa-shield-alt stat-icon"></i>
            </div>
        </div>

        <div class="action-grid">
            <div class="action-box">
                <div class="box-head"><i class="fas fa-cog"></i> SYSTEM STATUS</div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <span style="color:#ccc;">Active Prediction Week</span>
                    <span style="background:#10b981; color:black; padding:2px 10px; border-radius:4px; font-weight:bold;">
                        <?php echo $active_week; ?>
                    </span>
                </div>
            </div>

            <div class="action-box">
                <div class="box-head"><i class="fas fa-bolt"></i> QUICK ACTIONS</div>
                <div style="display: grid; gap: 10px;">
                    <a href="manage_matches.php" style="background: #263542; color: white; padding: 15px; border-radius: 4px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; transition:0.2s;">
                        <span><i class="fas fa-pen" style="margin-right:10px; color:#ff4655;"></i> Input Match Score</span>
                        <i class="fas fa-chevron-right" style="font-size:12px; color:#666;"></i>
                    </a>
                    <a href="../home.php" target="_blank" style="background: #263542; color: white; padding: 15px; border-radius: 4px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; transition:0.2s;">
                        <span><i class="fas fa-external-link-alt" style="margin-right:10px; color:#10b981;"></i> View Website</span>
                        <i class="fas fa-chevron-right" style="font-size:12px; color:#666;"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>