<?php
session_start();
include "../config/koneksi.php";

// Cek Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { header("Location: ../login.php"); exit(); }

$team_id = isset($_GET['team_id']) ? (int)$_GET['team_id'] : 0;

// 1. AMBIL INFO TIM
$q_team = $koneksi->query("SELECT * FROM team WHERE team_id = $team_id");
$team = $q_team->fetch_assoc();

if (!$team) { echo "<script>alert('Tim Ghoib!'); window.location='manage_teams.php';</script>"; exit(); }

// 2. AMBIL PEMAIN DARI TIM INI AJA
$sql_players = "SELECT * FROM players WHERE team_id = $team_id ORDER BY role ASC";
$players = $koneksi->query($sql_players);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Roster: <?php echo $team['team_name']; ?></title>
    <link rel="icon" type="image/png" href="../assets/images/logoValo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .team-header-card {
            background: #1b2733; padding: 30px; border-radius: 8px; border: 1px solid #333;
            display: flex; align-items: center; gap: 20px; margin-bottom: 30px;
        }
        .th-logo { width: 80px; height: 80px; object-fit: contain; }
        .th-info h2 { margin: 0; color: white; font-size: 24px; text-transform: uppercase; letter-spacing: 1px; }
        .th-info p { margin: 5px 0 0; color: #888; }

        .player-table { width: 100%; border-collapse: collapse; }
        .player-table th { text-align: left; padding: 15px; background: #0f1923; color: #aaa; font-size: 12px; }
        .player-table td { padding: 15px; border-bottom: 1px solid #333; color: white; vertical-align: middle; }
        
        .p-photo { width: 40px; height: 40px; border-radius: 4px; object-fit: cover; background: #000; border: 1px solid #444; margin-right: 15px; vertical-align: middle; }
        .role-badge { font-size: 10px; padding: 4px 8px; border-radius: 4px; background: #263542; color: #ccc; text-transform: uppercase; font-weight: bold; }

        .btn-kick { border-color: #ff4655; color: #ff4655; }
        .btn-kick:hover { background: #ff4655; color: white; }
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
        
        <div class="team-header-card">
            <?php 
                $logo = !empty($team['logo']) ? (strpos($team['logo'], 'http')===0 ? $team['logo'] : '../'.$team['logo']) : '../assets/images/default.png';
            ?>
            <img src="<?php echo $logo; ?>" class="th-logo">
            <div class="th-info">
                <h2><?php echo $team['team_name']; ?> ROSTER</h2>
                <p><?php echo $team['country']; ?> • <?php echo $players->num_rows; ?> Active Players</p>
            </div>
            
            <div style="margin-left: auto; display:flex; gap:10px;">
                <a href="manage_teams.php" style="color:#aaa; text-decoration:none; padding:10px; font-weight:bold;">
                    <i class="fas fa-arrow-left"></i> BACK
                </a>
                <a href="add_player.php?pre_team_id=<?php echo $team_id; ?>" class="btn-add" style="background:#10b981; padding:10px 20px; border-radius:4px; color:white; text-decoration:none; font-weight:bold;">
                    <i class="fas fa-plus"></i> RECRUIT PLAYER
                </a>
            </div>
        </div>

        <?php if(isset($_SESSION['success_msg'])): ?>
            <div style="background: rgba(16, 185, 129, 0.2); color: #10b981; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #10b981;">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
            </div>
        <?php endif; ?>

        <div style="background: #1b2733; border-radius: 8px; border: 1px solid #333; overflow: hidden;">
            <table class="player-table">
                <thead>
                    <tr>
                        <th>PLAYER IDENTITY</th>
                        <th>ROLE</th>
                        <th>NATIONALITY</th>
                        <th style="text-align:center;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($players->num_rows > 0): ?>
                        <?php while($p = $players->fetch_assoc()): 
                             $photo = !empty($p['photo']) ? (strpos($p['photo'], 'http')===0 ? $p['photo'] : '../'.$p['photo']) : '../assets/images/default_player.png';
                        ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center;">
                                    <img src="<?php echo $photo; ?>" class="p-photo">
                                    <div>
                                        <div style="font-weight:bold; font-size:16px;"><?php echo $p['in_game_name']; ?></div>
                                        <div style="font-size:12px; color:#666;"><?php echo $p['player_name']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="role-badge"><?php echo $p['role']; ?></span></td>
                            <td style="color:#aaa;"><?php echo $p['nationality']; ?></td>
                            
                            <td style="text-align:center;">
                                <a href="edit_player.php?id=<?php echo $p['player_id']; ?>" class="btn-action" style="border:1px solid #aaa; padding:5px 10px; border-radius:4px; color:#aaa; text-decoration:none;">
                                    <i class="fas fa-pen"></i>
                                </a>
                                
                                <a href="../action/kick_player.php?player_id=<?php echo $p['player_id']; ?>&team_id=<?php echo $team_id; ?>" 
                                   class="btn-action btn-kick" 
                                   style="border:1px solid #ff4655; padding:5px 10px; border-radius:4px; text-decoration:none; margin-left:5px;"
                                   onclick="return confirm('Yakin mau kick <?php echo $p['in_game_name']; ?> dari tim? Dia akan jadi Free Agent.');">
                                    <i class="fas fa-user-times"></i> Kick
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; padding:40px; color:#666;">Tim ini belum punya pemain. Klik Recruit Player!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>