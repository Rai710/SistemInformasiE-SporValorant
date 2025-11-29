<?php
session_start();
include "../config/koneksi.php";

// Cek Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit();
}

// Cek apakah ada titipan ID Tim? (Misal dari halaman Roster)
$pre_selected_team = isset($_GET['pre_team_id']) ? (int)$_GET['pre_team_id'] : 0;

// Ambil Daftar Tim buat Dropdown
$teams = $koneksi->query("SELECT team_id, team_name FROM team ORDER BY team_name ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Recruit New Player</title>
    <link rel="icon" type="image/png" href="../assets/images/logoValo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .form-box { max-width: 700px; margin: 0 auto; background: #1b2733; padding: 40px; border-radius: 8px; border: 1px solid #333; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #ccc; margin-bottom: 8px; font-weight: bold; }
        .form-control { width: 100%; background: #0f1923; border: 1px solid #555; color: white; padding: 12px; border-radius: 4px; }
        .form-control:focus { border-color: #ff4655; outline: none; }
        .btn-submit { background: #10b981; color: white; border: none; padding: 12px; width: 100%; font-weight: bold; cursor: pointer; border-radius: 4px; transition:0.2s;}
        .btn-submit:hover { background: #0e9f6e; }
        .info-text { font-size: 12px; color: #888; margin-top: 5px; }
    </style>
</head>
<body class="admin-body">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header-bar">
            <h2 class="page-title">RECRUIT NEW PLAYER</h2>
        </div>

        <div class="form-box">
            
            <?php if(isset($_SESSION['error_msg'])): ?>
                <div style="background:rgba(255,70,85,0.2); color:#ff4655; padding:15px; margin-bottom:20px; text-align:center; border-radius:4px;">
                    <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
                </div>
            <?php endif; ?>

            <form action="../action/insert_player.php" method="POST" enctype="multipart/form-data">
                
                <div class="form-group" style="display:flex; gap:20px;">
                    <div style="flex:1;">
                        <label>In-Game Name (IGN)</label>
                        <input type="text" name="ign" class="form-control" placeholder="e.g. f0rsakeN" required>
                    </div>
                    <div style="flex:1;">
                        <label>Full Name</label>
                        <input type="text" name="real_name" class="form-control" placeholder="e.g. Jason Susanto">
                    </div>
                </div>

                <div class="form-group" style="display:flex; gap:20px;">
                    <div style="flex:1;">
                        <label>Team</label>
                        <select name="team_id" class="form-control">
                            <option value="">-- Free Agent --</option>
                            <?php while($t = $teams->fetch_assoc()): 
                                // Kalau ID sama dengan pre_selected, otomatis SELECTED
                                $is_selected = ($t['team_id'] == $pre_selected_team) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $t['team_id']; ?>" <?php echo $is_selected; ?>>
                                    <?php echo $t['team_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div style="flex:1;">
                        <label>Role</label>
                        <select name="role" class="form-control">
                            <option value="Duelist">Duelist</option>
                            <option value="Controller">Controller</option>
                            <option value="Sentinel">Sentinel</option>
                            <option value="Initiator">Initiator</option>
                            <option value="Flex">Flex</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nationality</label>
                    <input type="text" name="nationality" class="form-control" placeholder="e.g. Indonesia">
                </div>

                <div class="form-group">
                    <label>Player Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    <div class="info-text">*Format: JPG/PNG/WEBP.</div>
                </div>

                <button type="submit" class="btn-submit">ADD PLAYER</button>
                
                <?php 
                    // Kalau datang dari tim tertentu, balik ke tim itu. Kalau gak, ke Manage Teams.
                    $cancel_link = ($pre_selected_team > 0) ? "manage_team_players.php?team_id=$pre_selected_team" : "manage_teams.php";
                ?>
                <a href="<?php echo $cancel_link; ?>" style="display:block; text-align:center; margin-top:15px; color:#aaa; text-decoration:none;">Cancel</a>
            
            </form>
        </div>
    </div>

</body>
</html>