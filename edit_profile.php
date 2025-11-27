<?php
session_start();
include "config/koneksi.php";

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Ambil Data User buat diisi ke Form
$query = $koneksi->prepare("SELECT * FROM users WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$user = $query->get_result()->fetch_assoc();

// Default values
$avatar = !empty($user['avatar_image']) ? $user['avatar_image'] : 'assets/images/default_agent.png';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - <?php echo $user['name']; ?></title>
    
    <?php include 'config/head.php'; ?>

    <style>
        .edit-wrapper { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        
        .edit-card {
            background: #1b2733; border: 1px solid #333; border-radius: 8px;
            overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        .edit-header {
            background: linear-gradient(to right, #ff4655, #d93c48);
            padding: 20px; color: white; font-weight: 900;
            text-transform: uppercase; letter-spacing: 1px;
            display: flex; justify-content: space-between; align-items: center;
        }

        .edit-body { padding: 40px; display: grid; grid-template-columns: 250px 1fr; gap: 40px; }

        /* Kiri: Foto */
        .photo-section { text-align: center; }
        .preview-box {
            width: 200px; height: 200px; margin: 0 auto 20px;
            border-radius: 50%; border: 5px solid #0f1923;
            overflow: hidden; position: relative;
        }
        .img-preview { width: 100%; height: 100%; object-fit: cover; }
        
        .btn-upload-custom {
            display: inline-block; background: #333; color: white;
            padding: 10px 20px; border-radius: 4px; cursor: pointer;
            font-size: 13px; font-weight: bold; transition: 0.3s;
            border: 1px solid #555;
        }
        .btn-upload-custom:hover { background: #ff4655; border-color: #ff4655; }

        /* Kanan: Form */
        .form-section { border-left: 1px solid #333; padding-left: 40px; }
        .form-title { color: #ff4655; font-weight: 800; text-transform: uppercase; margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 10px; }

        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; color: #aaa; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 8px; }
        .form-control {
            width: 100%; background: #0f1923; border: 1px solid #444; color: white;
            padding: 12px; border-radius: 4px; font-size: 14px;
        }
        .form-control:focus { outline: none; border-color: #ff4655; }

        .btn-save {
            background: #ff4655; color: white; border: none; padding: 15px 40px;
            font-weight: 900; text-transform: uppercase; letter-spacing: 1px;
            border-radius: 4px; cursor: pointer; transition: 0.3s; width: 100%;
        }
        .btn-save:hover { background: #b02a36; }
        
        .btn-back { color: white; text-decoration: none; font-size: 14px; font-weight: bold; display: flex; align-items: center; gap: 5px; }
        .btn-back:hover { color: #ccc; }

        /* Alert Box */
        .alert-box {
            padding: 15px; margin-bottom: 25px; border-radius: 6px; 
            text-align: center; font-weight: bold; font-size: 14px;
            border: 1px solid transparent;
        }
        .alert-success { background: rgba(16, 185, 129, 0.2); color: #10b981; border-color: #10b981; }
        .alert-danger { background: rgba(255, 70, 85, 0.2); color: #ff4655; border-color: #ff4655; }

        @media (max-width: 768px) {
            .edit-body { grid-template-columns: 1fr; }
            .form-section { border-left: none; padding-left: 0; border-top: 1px solid #333; padding-top: 30px; }
        }
    </style>
</head>
<body>

<?php include 'config/navbar.php'; ?>

<div class="edit-wrapper">
    <div style="margin-bottom: 20px;">
        <a href="profile.php" class="btn-back"><i class="fas fa-arrow-left"></i> KEMBALI KE PROFIL</a>
    </div>

    <?php 
    if(isset($_GET['pesan'])){
        if($_GET['pesan'] == 'success') {
            echo "<div class='alert-box alert-success'><i class='fas fa-check-circle'></i> Data Agen Berhasil Diperbarui!</div>";
        } 
        else if($_GET['pesan'] == 'format_salah') {
            echo "<div class='alert-box alert-danger'><i class='fas fa-exclamation-triangle'></i> Format file salah! Hanya boleh JPG, JPEG, PNG.</div>";
        }
        else if($_GET['pesan'] == 'error_upload') {
            echo "<div class='alert-box alert-danger'><i class='fas fa-times-circle'></i> Gagal upload gambar ke server. Cek folder permission.</div>";
        }
        else if($_GET['pesan'] == 'error_update') {
            echo "<div class='alert-box alert-danger'><i class='fas fa-database'></i> Database Error: Gagal menyimpan data.</div>";
        }
    }
    ?>

    <form action="action/profileCon.php?action=update" method="POST" enctype="multipart/form-data">
        <div class="edit-card">
            <div class="edit-header">
                <span>Edit Data Agent</span>
                <i class="fas fa-cog"></i>
            </div>
            
            <div class="edit-body">
                
                <div class="photo-section">
                    <div class="preview-box">
                        <img src="<?php echo $avatar; ?>" class="img-preview" id="avatarPreview">
                    </div>
                    <input type="file" name="foto_profil" id="fotoInput" style="display: none;" accept="image/*" onchange="previewImage(this)">
                    <label for="fotoInput" class="btn-upload-custom">
                        <i class="fas fa-camera"></i> Ganti Foto Profil
                    </label>
                    <p style="font-size: 11px; color: #666; margin-top: 10px;">Max 2MB (JPG, PNG)</p>
                </div>

                <div class="form-section">
                    
                    <div class="form-title">Identitas Game</div>
                    
                    <div class="input-group">
                        <label>Riot ID (Contoh: f0rsakeN #PRX)</label>
                        <input type="text" name="riot_id" class="form-control" value="<?php echo $user['riot_id']; ?>">
                    </div>

                    <div class="input-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label>Rank Competitive</label>
                            <select name="rank_tier" class="form-control">
                                <?php 
                                $ranks = ['Unranked', 'Iron', 'Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Ascendant', 'Immortal', 'Radiant'];
                                foreach($ranks as $r) {
                                    $sel = ($user['rank_tier'] == $r) ? 'selected' : '';
                                    echo "<option value='$r' $sel>$r</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label>Agent Andalan</label>
                            <select name="favorite_agent" class="form-control">
                                <?php 
                                $agents = ['Jett', 'Reyna', 'Raze', 'Omen', 'Sova', 'Sage', 'Chamber', 'Viper', 'Iso', 'Clove', 'Vyse']; 
                                foreach($agents as $ag) {
                                    $sel = ($user['favorite_agent'] == $ag) ? 'selected' : '';
                                    echo "<option value='$ag' $sel>$ag</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label style="color:#ff4655;">❤️ Tim Favorit (My Team)</label>
                        <select name="favorite_team_id" class="form-control">
                            <option value="">-- Pilih Tim Favoritmu --</option>
                            <?php
                            // Ambil semua tim dari database
                            $q_team = $koneksi->query("SELECT team_id, team_name FROM team ORDER BY team_name ASC");
                            while($t = $q_team->fetch_assoc()) {
                                $sel_team = ($user['favorite_team_id'] == $t['team_id']) ? 'selected' : '';
                                echo "<option value='".$t['team_id']."' $sel_team>".$t['team_name']."</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Bio / Status</label>
                        <input type="text" name="bio" class="form-control" value="<?php echo $user['bio']; ?>">
                    </div>

                    <div class="form-title" style="margin-top: 30px;">Data Akun</div>

                    <div class="input-group">
                        <label>Username (Nama Login)</label>
                        <input type="text" name="new_username" class="form-control" value="<?php echo $user['name']; ?>" required>
                    </div>

                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
                    </div>

                    <div class="input-group">
                        <label>Discord Username</label>
                        <input type="text" name="discord" class="form-control" value="<?php echo $user['discord_username']; ?>">
                    </div>

                    <div class="input-group">
                        <label>Password Baru (Kosongkan jika tidak diganti)</label>
                        <input type="password" name="password" class="form-control" placeholder="***">
                    </div>

                    <button type="submit" class="btn-save">SIMPAN PERUBAHAN</button>

                </div>
            </div>
        </div>
    </form>
</div>

<?php include 'config/footer.php'; ?>

<script>
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