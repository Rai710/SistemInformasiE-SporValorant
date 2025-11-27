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
    
    // Ambil Data Form
    $name           = $_POST['new_username'];
    $email          = $_POST['email'];
    $riot_id        = $_POST['riot_id'];
    $rank_tier      = $_POST['rank_tier'];
    $favorite_agent = $_POST['favorite_agent'];
    $discord        = $_POST['discord'];
    $bio            = $_POST['bio'];
    
    $new_password   = $_POST['password'];
    
    $query_foto = ""; 
    
    // Cek apakah user milih file?
    if (isset($_FILES['foto_profil']['name']) && $_FILES['foto_profil']['name'] != "") {
        
        $namaFile   = $_FILES['foto_profil']['name'];
        $tmpName    = $_FILES['foto_profil']['tmp_name'];
        $error      = $_FILES['foto_profil']['error'];
        $ukuran     = $_FILES['foto_profil']['size'];

        // Cek Error Bawaan PHP
        if ($error === 4) {
            // User gak jadi upload
        } elseif ($error !== 0) {
            // Ada error upload!
            die(" ERROR UPLOAD: Kode Error " . $error . ". Coba cari di Google 'PHP Upload Error " . $error . "'");
        } elseif ($ukuran > 5000000) { // 5MB Limit
            die("ERROR: File kegedean, Bang! Maksimal 5MB.");
        } else {
            // Cek Ekstensi
            $ekstensiValid = ['jpg', 'jpeg', 'png'];
            $ekstensiGambar = explode('.', $namaFile);
            $ekstensiGambar = strtolower(end($ekstensiGambar));
            
            if (!in_array($ekstensiGambar, $ekstensiValid)) {
                die("❌ ERROR: Yang lu upload bukan gambar (jpg/png)!");
            }

            // Generate Nama Baru
            $namaBaru = uniqid() . '.' . $ekstensiGambar;

            $tujuan = "../assets/images/" . $namaBaru;
            
            // Cek tujuan folder
            if (!is_dir("../assets/images/")) {
                die(" ERROR: Folder '../assets/images/' GAK KETEMU! Coba cek nama folder.");
            }

            // Coba Pindahin
            if (move_uploaded_file($tmpName, $tujuan)) {
                // BERHASIL UPLOAD
                $path_db = "assets/images/" . $namaBaru;
                $_SESSION['avatar'] = $path_db; // Update Session
                $query_foto = ", avatar_image = '$path_db'";
            } else {
                die("❌ ERROR: Gagal mindahin file (Permission Denied). Cek folder assets/images lu.");
            }
        }
    }

    // Update Password (Optional)
    $query_pass = "";
    if (!empty($new_password)) {
        $query_pass = ", password = '$new_password'";
    }

    // Eksekusi Update ke Database
    try {
        $sql = "UPDATE users SET 
                name = ?, email = ?, riot_id = ?, rank_tier = ?, favorite_agent = ?, discord_username = ?, bio = ? 
                $query_foto $query_pass 
                WHERE user_id = ?";
                
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("sssssssi", $name, $email, $riot_id, $rank_tier, $favorite_agent, $discord, $bio, $user_id);

        if ($stmt->execute()) {
            $_SESSION['username'] = $name;
            header("Location: ../profile.php?pesan=success");
        } else {
            die("❌ ERROR DATABASE: Gagal update data text. " . $koneksi->error);
        }
        $stmt->close();

    } catch (Exception $e) {
        die("❌ ERROR SYSTEM: " . $e->getMessage());
    }

} elseif ($action == 'delete') {
} else {
    header("Location: ../profile.php");
}
$koneksi->close();
?>