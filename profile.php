<?php
session_start();
// Sesuaikan path koneksi ini kalau folder lu beda
include "config/koneksi.php";

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil Data User Terbaru
$query = $koneksi->prepare("SELECT * FROM users WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$user = $query->get_result()->fetch_assoc();

// Default Values kalau kosong
$avatar = !empty($user['avatar_image']) ? $user['avatar_image'] : 'assets/images/default_agent.png';
$fav_agent = !empty($user['favorite_agent']) ? $user['favorite_agent'] : 'Jett';
$rank = !empty($user['rank_tier']) ? $user['rank_tier'] : 'Unranked';

// Banner dinamis
$banner_bg = "assets/images/bg.jpg"; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Profile - <?php echo $user['name']; ?></title>
    
    <link rel="stylesheet" href="assets/css/body.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        /* PROFILE SPECIFIC CSS */
        .profile-wrapper {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* 1. HEADER BANNER */
        .profile-banner {
            height: 200px;
            background: url('<?php echo $banner_bg; ?>') no-repeat center center/cover;
            border-radius: 8px 8px 0 0;
            position: relative;
            border-bottom: 4px solid #ff4655;
        }
        
        .profile-banner::after {
            content: ''; position: absolute; top:0; left:0; width:100%; height:100%;
            background: linear-gradient(to bottom, transparent 0%, rgba(15, 25, 35, 0.9) 100%);
        }

        /* 2. MAIN CARD LAYOUT */
        .profile-card {
            background: #1b2733;
            border-radius: 0 0 8px 8px;
            border: 1px solid #333;
            border-top: none;
            display: grid;
            grid-template-columns: 300px 1fr;
            
            
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        /* --- SIDEBAR KIRI (IDENTITY) --- */
        .profile-sidebar {
            padding: 0 30px 40px;
            text-align: center;
            border-right: 1px solid #333;
            background: #141e26;
            position: relative;
        }

        .avatar-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: -75px auto 20px; 
            z-index: 10;
        }

        .profile-avatar {
            width: 100%; height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #1b2733;
            background: #000;
            transition: 0.3s;
        }
        
        .avatar-container:hover .profile-avatar { border-color: #ff4655; transform: scale(1.05); }

        .rank-badge {
            background: #263542;
            color: #ffd700;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 14px;
            border: 1px solid #ffd700;
            display: inline-block;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .user-fullname { font-size: 24px; font-weight: 900; text-transform: uppercase; color: white; margin-bottom: 5px; }
        .user-email { color: #888; font-size: 14px; margin-bottom: 30px; }

        .btn-upload {
            background: #333; color: white; border: none; padding: 10px 20px; width: 100%;
            border-radius: 4px; font-weight: bold; cursor: pointer; transition: 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-upload:hover { background: #ff4655; }

        /* --- CONTENT KANAN (FORM) --- */
        .profile-content {
            padding: 40px;
        }

        .section-title {
            font-size: 18px; font-weight: 800; text-transform: uppercase;
            color: #ff4655; margin-bottom: 25px; border-bottom: 1px solid #333; padding-bottom: 10px;
        }

        .form-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
        }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #aaa; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 8px; }
        
        .form-input, .form-select {
            width: 100%; background: #0f1923; border: 1px solid #444; color: white;
            padding: 12px; border-radius: 4px; font-size: 14px; transition: 0.3s;
        }
        .form-input:focus, .form-select:focus { border-color: #ff4655; outline: none; }

        .btn-save {
            background: #ff4655; color: white; border: none; padding: 12px 30px;
            font-weight: bold; text-transform: uppercase; letter-spacing: 1px;
            border-radius: 4px; cursor: pointer; float: right; margin-top: 20px;
            transition: 0.3s;
        }
        .btn-save:hover { background: #d93c48; box-shadow: 0 0 15px rgba(255, 70, 85, 0.4); }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .profile-card { grid-template-columns: 1fr; }
            .profile-sidebar { border-right: none; border-bottom: 1px solid #333; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <?php include 'config/navbar.php'; ?>

    <div class="profile-wrapper">
        
        <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'success'): ?>
            <div style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #10b981; padding: 15px; margin-bottom: 20px; border-radius: 4px; text-align: center; font-weight: bold;">
                DATA AGEN BERHASIL DIPERBARUI!
            </div>
        <?php endif; ?>

        <div class="profile-banner">
        </div>

         <form action="action/profileCon.php?action=update" method="POST" enctype="multipart/form-data">
            <div class="profile-card">
                
                <div class="profile-sidebar">
                    <div class="avatar-container">
                        <img src="<?php echo $avatar; ?>" class="profile-avatar" id="avatarPreview">
                    </div>
                    
                    <div class="rank-badge">
                        <i class="fas fa-medal"></i> <?php echo $rank; ?>
                    </div>

                    <h2 class="user-fullname"><?php echo $user['name']; ?></h2>
                    <p class="user-email"><?php echo $user['email']; ?></p>

                    <input type="file" name="foto_profil" id="fotoInput" style="display: none;" accept="image/*" onchange="previewImage(this)">
                    <label for="fotoInput" class="btn-upload">
                        <i class="fas fa-camera"></i> Ganti Foto
                    </label>
                </div>

                <div class="profile-content">
                    
                    <div class="section-title">Informasi Gamer</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Riot ID (Game Name #Tag)</label>
                            <input type="text" name="riot_id" class="form-input" value="<?php echo $user['riot_id']; ?>" placeholder="Contoh: f0rsakeN #PRX">
                        </div>

                    <div class="form-group">
                        <label>Rank Competitive</label>
                        <select name="rank_tier" class="form-select">
                            <?php 
                            // List Rank Baru yang Simpel
                            $list_rank = [
                                'Unranked', 'Iron', 'Bronze', 'Silver', 'Gold', 
                                'Platinum', 'Diamond', 'Ascendant', 'Immortal', 'Radiant'
                            ];

                            foreach($list_rank as $r){
                                $cek = ($rank == $r) ? 'selected' : '';
                                echo "<option value='$r' $cek>$r</option>";
                            } 
                            ?>
                        </select>
                    </div>

                        <div class="form-group">
                            <label>Agent Andalan</label>
                            <select name="favorite_agent" class="form-select">
                                <?php 
                                $agents = ['Jett', 'Reyna', 'Raze', 'Omen', 'Sova', 'Sage', 'Chamber', 'Viper', 'Iso', 'Clove', 'Vyse']; 
                                foreach($agents as $ag){
                                    $sel = ($fav_agent == $ag) ? 'selected' : '';
                                    echo "<option value='$ag' $sel>$ag</option>";
                                } 
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Discord Username</label>
                            <input type="text" name="discord" class="form-input" value="<?php echo $user['discord_username']; ?>" placeholder="user#1234">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Bio Singkat (Taunting Message)</label>
                        <input type="text" name="bio" class="form-input" value="<?php echo $user['bio']; ?>" placeholder="Tulis sesuatu...">
                    </div>

                    <div class="section-title" style="margin-top: 30px;">Keamanan Akun</div>
                    <div class="form-grid">
                        
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="new_username" class="form-input" value="<?php echo $user['name']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Email Login</label>
                            <input type="email" name="email" class="form-input" value="<?php echo $user['email']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Password Baru (Opsional)</label>
                            <input type="password" name="password" class="form-input" placeholder="Isi jika ingin ganti password">
                        </div>
                    </div>

                    <button type="submit" class="btn-save">SIMPAN PERUBAHAN</button>

                </div>
            </div>
        </form>
    </div>

    <?php include 'config/footer.php'; ?>

    <script>
        // Script Preview Foto
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

</body>
</html>