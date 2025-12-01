<?php
session_start();
include "../config/koneksi.php";

// 1. CEK AKSES ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit();
}

// 2. AMBIL DATA TIM
$sql = "SELECT * FROM team ORDER BY team_name ASC";
$teams = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Manage Teams - Admin</title>
    <link rel="icon" type="image/png" href="../assets/images/logoValo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    
    <style>
        .match-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .match-table th { text-align: left; padding: 15px; background: #263542; color: #aaa; font-size: 12px; text-transform: uppercase; }
        .match-table td { padding: 15px; border-bottom: 1px solid #333; color: white; vertical-align: middle; }
        .match-table tr:hover { background: rgba(255,255,255,0.02); }
        
        .team-logo-sm { width: 40px; height: 40px; object-fit: contain; background: rgba(0,0,0,0.3); border-radius: 4px; padding: 5px; border: 1px solid #444; vertical-align: middle; margin-right: 10px; }
        
        .btn-action { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 11px; font-weight: bold; text-transform: uppercase; border: 1px solid #555; transition: 0.2s; color: #ccc; }
        .btn-edit:hover { background: #ffc107; color: black; border-color: #ffc107; }
        .btn-del:hover { background: #ff4655; color: white; border-color: #ff4655; }
        
        .btn-add { background: #10b981; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: bold; display: inline-flex; align-items: center; gap: 8px; }
        .btn-add:hover { background: #0e9f6e; }
    </style>
</head>
<body class="admin-body">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header-bar">
            <h2 class="page-title">MANAGE TEAMS</h2>
            <div class="admin-profile">
                <span><?php echo $_SESSION['username']; ?></span>
                <img src="../<?php echo $_SESSION['avatar'] ?? 'assets/images/default_agent.png'; ?>" class="admin-avatar">
            </div>
        </div>

        <?php if(isset($_SESSION['success_msg'])): ?>
            <div style="background: rgba(16, 185, 129, 0.2); color: #10b981; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #10b981;">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
            </div>
        <?php endif; ?>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div style="color:#888;">Total Teams: <strong><?php echo $teams->num_rows; ?></strong></div>
            <a href="add_teams.php" class="btn-add"><i class="fas fa-plus"></i> ADD NEW TEAM</a>
        </div>

        <div style="background: #1b2733; border-radius: 8px; border: 1px solid #333; overflow: hidden;">
            <table class="match-table">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>TEAM IDENTITY</th>
                        <th>COUNTRY</th>
                        <th width="150" style="text-align:center;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($teams->num_rows > 0): ?>
                        <?php while($t = $teams->fetch_assoc()): 
                            $logo = $t['logo'];
                            if (empty($logo)) $src = "../assets/images/default.png";
                            elseif (strpos($logo, 'http') === 0) $src = $logo;
                            else $src = "../" . $logo;
                        ?>
                        <tr>
                            <td style="color:#666;">#<?php echo $t['team_id']; ?></td>
                            <td>
                                <img src="<?php echo $src; ?>" class="team-logo-sm">
                                <span style="font-weight:bold; font-size:14px;"><?php echo $t['team_name']; ?></span>
                            </td>
                            <td style="color:#aaa;"><?php echo $t['country']; ?></td>
                            <td style="text-align:center;">
                                <a href="manage_team_players.php?team_id=<?php echo $t['team_id']; ?>" class="btn-action" style="border-color:#10b981; color:#10b981;" title="Manage Roster">
                                    <i class="fas fa-users"></i>
                                </a>

                                <a href="edit_team.php?id=<?php echo $t['team_id']; ?>" class="btn-action btn-edit" title="Edit Team Info">
                                    <i class="fas fa-pen"></i>
                                </a>

                                <a href="../action/delete_team.php?id=<?php echo $t['team_id']; ?>" class="btn-action btn-del" onclick="return confirm('Yakin hapus tim ini?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; padding:30px; color:#666;">Belum ada tim.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>