<?php
session_start();
include "../config/koneksi.php";

// Cek Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 1. AMBIL DATA PLAYER
$stmt = $koneksi->prepare("SELECT * FROM players WHERE player_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$player = $stmt->get_result()->fetch_assoc();

if (!$player) {
    echo "<script>alert('Player tidak ditemukan!'); window.location='manage_players.php';</script>";
    exit();
}

// 2. AMBIL DATA TEAM BUAT DROPDOWN
$teams = $koneksi->query("SELECT team_id, team_name FROM team ORDER BY team_name ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Player - <?php echo $player['in_game_name']; ?></title>
    <link rel="icon" type="image/png" href="../assets/images/logoValo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .form-box { max-width: 700px; margin: 0 auto; background: #1b2733; padding: 40px; border-radius: 8px; border: 1px solid #333; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #ccc; margin-bottom: 8px; font-weight: bold; }
        .form-control { width: 100%; background: #0f1923; border: 1px solid #555; color: white; padding: 12px; border-radius: 4px; }
        .form-control:focus { border-color: #ff4655; outline: none; }
        .btn-submit { background: #ff4655; color: white; border: none; padding: 12px; width: 100%; font-weight: bold; cursor: pointer; border-radius: 4px; }
        .btn-submit:hover { background: #d93c48; }
        
        .photo-preview { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #555; background: #000; }
        .info-text { font-size: 12px; color: #888; margin-top: 5px; }
    </style>
</head>
<body class="admin-body">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header-bar">
            <h2 class="page-title">EDIT PLAYER</h2>
        </div>

        <div class="form-box">
            
            <?php if(isset($_SESSION['error_msg'])): ?>
                <div style="background:rgba(255,70,85,0.2); color:#ff4655; padding:15px; margin-bottom:20px; text-align:center; border-radius:4px;">
                    <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
                </div>
            <?php endif; ?>

            <form action="../action/update_players.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="player_id" value="<?php echo $player['player_id']; ?>">

                <div class="form-group" style="display:flex; gap:20px;">
                    <div style="flex:1;">
                        <label>In-Game Name (IGN)</label>
                        <input type="text" name="ign" class="form-control" value="<?php echo htmlspecialchars($player['in_game_name']); ?>" required>
                    </div>
                    <div style="flex:1;">
                        <label>Full Name</label>
                        <input type="text" name="real_name" class="form-control" value="<?php echo htmlspecialchars($player['player_name']); ?>">
                    </div>
                </div>

                <div class="form-group" style="display:flex; gap:20px;">
                    <div style="flex:1;">
                        <label>Team</label>
                        <select name="team_id" class="form-control">
                            <option value="">-- Free Agent --</option>
                            <?php while($t = $teams->fetch_assoc()): ?>
                                <option value="<?php echo $t['team_id']; ?>" <?php echo ($player['team_id'] == $t['team_id']) ? 'selected' : ''; ?>>
                                    <?php echo $t['team_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div style="flex:1;">
                        <label>Role</label>
                        <select name="role" class="form-control">
                            <?php 
                            $roles = ['Duelist', 'Controller', 'Sentinel', 'Initiator', 'Flex'];
                            foreach($roles as $r) {
                                $sel = ($player['role'] == $r) ? 'selected' : '';
                                echo "<option value='$r' $sel>$r</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nationality</label>
                    <input type="text" name="nationality" class="form-control" value="<?php echo htmlspecialchars($player['nationality']); ?>">
                </div>

                <div class="form-group">
                    <label>Player Photo</label>
                    <div style="display:flex; gap:20px; align-items:center;">
                        <div>
                            <?php 
                                $photo = $player['photo'];
                                if(empty($photo)) $src = "../assets/images/default_player.png";
                                elseif(strpos($photo, 'http') === 0) $src = $photo;
                                else $src = "../" . $photo;
                            ?>
                            <img src="<?php echo $src; ?>" class="photo-preview">
                        </div>
                        <div style="flex:1;">
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <div class="info-text">*Format: JPG/PNG. Kosongkan jika tidak ingin mengganti foto.</div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">UPDATE PLAYER DATA</button>

                <?php 
                    // Kalau player punya tim, balik ke Roster Tim tersebut
                    if (!empty($player['team_id'])) {
                        $cancel_link = "manage_team_players.php?team_id=" . $player['team_id'];
                    } else {
                        // Kalau Free Agent, balik ke Manage Teams aja
                        $cancel_link = "manage_teams.php";
                    }
                ?>
                <a href="<?php echo $cancel_link; ?>" style="display:block; text-align:center; margin-top:15px; color:#aaa; text-decoration:none;">Cancel</a>

                </form>
                
                <a href="manage_players.php" style="display:block; text-align:center; margin-top:15px; color:#aaa; text-decoration:none;">Cancel</a>
            </form>
        </div>
    </div>

</body>
</html>