<?php
session_start();
include "../config/koneksi.php";

// Cek Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Add New Team</title>
    <link rel="icon" type="image/png" href="../assets/images/logoValo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .form-box { max-width: 700px; margin: 0 auto; background: #1b2733; padding: 40px; border-radius: 8px; border: 1px solid #333; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #ccc; margin-bottom: 8px; font-weight: bold; }
        .form-control { width: 100%; background: #0f1923; border: 1px solid #555; color: white; padding: 12px; border-radius: 4px; }
        .form-control:focus { border-color: #ff4655; outline: none; }
        .btn-submit { background: #10b981; color: white; border: none; padding: 12px; width: 100%; font-weight: bold; cursor: pointer; border-radius: 4px; transition: 0.2s; }
        .btn-submit:hover { background: #0e9f6e; }
        .info-text { font-size: 12px; color: #888; margin-top: 5px; }
    </style>
</head>
<body class="admin-body">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header-bar">
            <h2 class="page-title">ADD NEW TEAM</h2>
        </div>

        <div class="form-box">
            
            <?php if(isset($_SESSION['error_msg'])): ?>
                <div style="background: rgba(255, 70, 85, 0.2); color: #ff4655; padding: 15px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
                </div>
            <?php endif; ?>

            <form action="../action/insert_team.php" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label>Team Name</label>
                    <input type="text" name="team_name" class="form-control" placeholder="e.g. Sentinels" required>
                </div>

                <div class="form-group">
                    <label>Country</label>
                    <input type="text" name="country" class="form-control" placeholder="e.g. USA">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Short bio about the team..."></textarea>
                </div>

                <div class="form-group">
                    <label>Team Logo</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    <div class="info-text">*Format: JPG/PNG/WEBP. Jika kosong, akan pakai logo default.</div>
                </div>

                <button type="submit" class="btn-submit">ADD TEAM</button>
                <a href="manage_teams.php" style="display:block; text-align:center; margin-top:15px; color:#aaa; text-decoration:none;">Cancel</a>
            </form>
        </div>
    </div>

</body>
</html>