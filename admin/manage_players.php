<?php
session_start();
include "../config/koneksi.php";

// 1. CEK AKSES ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit();
}

// 2. AMBIL DATA PLAYERS
$sql = "SELECT p.*, t.team_name, t.logo as team_logo 
        FROM players p 
        LEFT JOIN team t ON p.team_id = t.team_id 
        ORDER BY p.in_game_name ASC";
$players = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Manage Players - Admin</title>
    <link rel="icon" type="image/png" href="../assets/images/logoValo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    
    <style>
        .player-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .player-table th { text-align: left; padding: 15px; background: #263542; color: #aaa; font-size: 12px; text-transform: uppercase; }
        .player-table td { padding: 12px 15px; border-bottom: 1px solid #333; color: white; vertical-align: middle; }
        .player-table tr:hover { background: rgba(255,255,255,0.02); }
        
        .player-img { width: 35px; height: 35px; object-fit: cover; border-radius: 4px; border: 1px solid #555; margin-right: 10px; vertical-align: middle; }
        .team-logo-mini { width: 20px; height: 20px; object-fit: contain; vertical-align: middle; margin-right: 5px; }
        
        .role-badge { font-size: 10px; padding: 3px 8px; border-radius: 4px; background: #0f1923; border: 1px solid #555; color: #ccc; }
        
        .btn-action { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 4px; text-decoration: none; border: 1px solid #555; color: #ccc; transition: 0.2s; }
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
            <h2 class="page-title">MANAGE PLAYERS</h2>
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
            <div style="color:#888;">Total Players: <strong><?php echo $players->num_rows; ?></strong></div>
            <a href="add_player.php" class="btn-add"><i class="fas fa-plus"></i> ADD PLAYER</a>
        </div>

        <div style="background: #1b2733; border-radius: 8px; border: 1px solid #333; overflow: hidden;">
            <table class="player-table">
                <thead>
                    <tr>
                        <th>PLAYER</th>
                        <th>ROLE</th>
                        <th>TEAM</th>
                        <th>NATIONALITY</th>
                        <th width="100" style="text-align:center;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($players->num_rows > 0): ?>
                        <?php while($p = $players->fetch_assoc()): 
                            // Cek Foto Player
                            $foto = !empty($p['photo']) ? (strpos($p['photo'], 'http')===0 ? $p['photo'] : '../'.$p['photo']) : '../assets/images/default_player.png';
                            // Cek Logo Tim
                            $t_logo = !empty($p['team_logo']) ? (strpos($p['team_logo'], 'http')===0 ? $p['team_logo'] : '../'.$p['team_logo']) : '../assets/images/default.png';
                        ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center;">
                                    <img src="<?php echo $foto; ?>" class="player-img">
                                    <div>
                                        <div style="font-weight:bold; font-size:14px;"><?php echo $p['in_game_name']; ?></div>
                                        <div style="font-size:11px; color:#888;"><?php echo $p['player_name']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="role-badge"><?php echo $p['role']; ?></span></td>
                            <td>
                                <?php if($p['team_id']): ?>
                                    <img src="<?php echo $t_logo; ?>" class="team-logo-mini"> <?php echo $p['team_name']; ?>
                                <?php else: ?>
                                    <span style="color:#555; font-style:italic;">Free Agent</span>
                                <?php endif; ?>
                            </td>
                            <td style="color:#aaa;"><?php echo $p['nationality']; ?></td>
                            <td style="text-align:center;">
                                <a href="edit_player.php?id=<?php echo $p['player_id']; ?>" class="btn-action btn-edit" title="Edit">
                                    <i class="fas fa-pen" style="font-size:12px;"></i>
                                </a>
                                <a href="../action/delete_players.php?id=<?php echo $p['player_id']; ?>" class="btn-action btn-del" title="Delete" onclick="return confirm('Yakin hapus player ini?');">
                                    <i class="fas fa-trash" style="font-size:12px;"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; padding:30px; color:#666;">Belum ada data pemain.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>