<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { header("Location: ../login.php"); exit(); }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $koneksi->prepare("SELECT * FROM team WHERE team_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$team = $stmt->get_result()->fetch_assoc();

if (!$team) { echo "<script>alert('Tim tidak ditemukan!'); window.location='manage_teams.php';</script>"; exit(); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Team</title>
    <link rel="icon" type="image/png" href="../assets/images/logoValo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .form-box { max-width: 700px; margin: 0 auto; background: #1b2733; padding: 40px; border-radius: 8px; border: 1px solid #333; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #ccc; margin-bottom: 8px; font-weight: bold; }
        .form-control { width: 100%; background: #0f1923; border: 1px solid #555; color: white; padding: 12px; border-radius: 4px; }
        .btn-submit { background: #ff4655; color: white; border: none; padding: 12px; width: 100%; font-weight: bold; cursor: pointer; }
        .logo-preview { width: 100px; height: 100px; object-fit: contain; background: #0f1923; border: 1px solid #333; padding: 10px; border-radius: 8px; margin-top: 10px; }
    </style>
</head>
<body class="admin-body">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="header-bar"><h2 class="page-title">EDIT TEAM</h2></div>
        <div class="form-box">
            <?php if(isset($_SESSION['error_msg'])): ?>
                <div style="background:rgba(255,70,85,0.2); color:#ff4655; padding:15px; margin-bottom:20px; text-align:center;">
                    <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
                </div>
            <?php endif; ?>

            <form action="../action/update_team.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="team_id" value="<?php echo $team['team_id']; ?>">
                
                <div class="form-group">
                    <label>Team Name</label>
                    <input type="text" name="team_name" class="form-control" value="<?php echo htmlspecialchars($team['team_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Country</label>
                    <input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($team['country']); ?>">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($team['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Team Logo</label>
                    <div style="display:flex; align-items:center; gap:20px;">
                        <img src="<?php echo (strpos($team['logo'], 'http') === 0) ? $team['logo'] : '../'.$team['logo']; ?>" class="logo-preview">
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>
                </div>

                <button type="submit" class="btn-submit">UPDATE TEAM</button>
                <a href="manage_teams.php" style="display:block; text-align:center; margin-top:15px; color:#aaa; text-decoration:none;">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>