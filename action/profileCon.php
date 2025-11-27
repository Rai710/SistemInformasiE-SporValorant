<?php
session_start();
include "../config/koneksi.php"; 

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$action  = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'update') {
    
    $name           = $_POST['new_username'];
    $email          = $_POST['email'];
    $riot_id        = $_POST['riot_id'];
    $rank_tier      = $_POST['rank_tier'];
    $favorite_agent = $_POST['favorite_agent'];
    $discord        = $_POST['discord'];
    $bio            = $_POST['bio'];

    $fav_team_id    = !empty($_POST['favorite_team_id']) ? $_POST['favorite_team_id'] : NULL;

    $new_password   = $_POST['password'];
    

    $query_foto = ""; 
    
    if (isset($_FILES['foto_profil']['name']) && $_FILES['foto_profil']['name'] != "") {
        $namaFile   = $_FILES['foto_profil']['name'];
        $tmpName    = $_FILES['foto_profil']['tmp_name'];
        $error      = $_FILES['foto_profil']['error'];
        
        if ($error === 0) {
            $ekstensiValid = ['jpg', 'jpeg', 'png'];
            $ekstensiGambar = explode('.', $namaFile);
            $ekstensiGambar = strtolower(end($ekstensiGambar));
            
            if (in_array($ekstensiGambar, $ekstensiValid)) {
                $namaBaru = uniqid() . '.' . $ekstensiGambar;
                $tujuan = "../assets/images/" . $namaBaru;
                
                if (move_uploaded_file($tmpName, $tujuan)) {
                    $path_db = "assets/images/" . $namaBaru;
                    $_SESSION['avatar'] = $path_db; 
                    $query_foto = ", avatar_image = '$path_db'";
                }
            }
        }
    }

    // Logic Password
    $query_pass = "";
    if (!empty($new_password)) {
        $query_pass = ", password = '$new_password'";
    }


    try {
        $sql = "UPDATE users SET 
                name = ?, 
                email = ?, 
                riot_id = ?, 
                rank_tier = ?, 
                favorite_agent = ?, 
                discord_username = ?, 
                bio = ?,
                favorite_team_id = ? 
                $query_foto $query_pass 
                WHERE user_id = ?";
                
        $stmt = $koneksi->prepare($sql);

        $stmt->bind_param("sssssssii", $name, $email, $riot_id, $rank_tier, $favorite_agent, $discord, $bio, $fav_team_id, $user_id);

        if ($stmt->execute()) {
            $_SESSION['username'] = $name;
            $_SESSION['fav_team_id'] = $fav_team_id;

            header("Location: ../profile.php?pesan=success");
        } else {
            // Kalau gagal
            header("Location: ../edit_profile.php?pesan=error_update");
        }
        $stmt->close();

    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }

} else {
    // Kalau gak ada action, balik ke profil
    header("Location: ../profile.php");
}
$koneksi->close();
?>