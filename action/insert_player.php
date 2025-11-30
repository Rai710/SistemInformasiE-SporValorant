<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    try {
        $ign         = trim($_POST['ign']);
        $real_name   = trim($_POST['real_name']);
        
        $team_id     = !empty($_POST['team_id']) ? (int)$_POST['team_id'] : NULL;
        
        $role        = $_POST['role'];
        $nationality = trim($_POST['nationality']);

        if (empty($ign)) throw new Exception("IGN Wajib diisi bos!");

        // --- UPLOAD FOTO ---
        $db_path = NULL;
        if (!empty($_FILES['photo']['name'])) {
            $target_dir = "../assets/images/players/";
            if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

            $file_ext = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($file_ext, $allowed)) {
                throw new Exception("Format gambar salah! Harus JPG/PNG/WEBP.");
            }

            // Nama unik
            $new_name = "player_" . uniqid() . "." . $file_ext;
            $target_file = $target_dir . $new_name;
            
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $db_path = "assets/images/players/" . $new_name;
            } else {
                throw new Exception("Gagal upload gambar ke server.");
            }
        }

        // --- INSERT DATABASE ---
        $sql = "INSERT INTO players (in_game_name, player_name, team_id, role, nationality, photo, joined_date) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $koneksi->prepare($sql);
        // types: s=string, i=int.
        $stmt->bind_param("ssisss", $ign, $real_name, $team_id, $role, $nationality, $db_path);

        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Player baru <strong>$ign</strong> berhasil direkrut!";
            
            if ($team_id) {
                // Balik ke Roster Tim tempat dia direkrut
                header("Location: ../admin/manage_team_players.php?team_id=" . $team_id);
            } else {
                // Kalau Free Agent, balik ke halaman utama Tim
                header("Location: ../admin/manage_teams.php");
            }
        } else {
            throw new Exception("Database Error: " . $stmt->error);
        }
        $stmt->close();

    } catch (Exception $e) {
        $_SESSION['error_msg'] = $e->getMessage();
        // Kalau error, balikin ke form add
        $back_url = "../admin/add_player.php";
        if(!empty($_POST['team_id'])) $back_url .= "?pre_team_id=" . $_POST['team_id'];
        
        header("Location: " . $back_url);
    }
}
?>