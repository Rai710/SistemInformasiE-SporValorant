<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    try {
        $player_id   = (int)$_POST['player_id'];
        $ign         = trim($_POST['ign']);
        $real_name   = trim($_POST['real_name']);
        $team_id     = !empty($_POST['team_id']) ? (int)$_POST['team_id'] : NULL;
        $role        = $_POST['role'];
        $nationality = trim($_POST['nationality']);

        if (empty($ign)) throw new Exception("IGN tidak boleh kosong!");

        // --- SETUP QUERY ---
        $query_parts = "in_game_name=?, player_name=?, team_id=?, role=?, nationality=?";
        $params = [$ign, $real_name, $team_id, $role, $nationality];
        $types = "ssiss";

        // --- CEK UPLOAD FOTO ---
        if (!empty($_FILES['photo']['name'])) {
            $target_dir = "../assets/images/players/";
            
            // Buat folder otomatis kalau belum ada
            if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

            $file_ext = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($file_ext, $allowed)) {
                throw new Exception("Format file harus JPG, PNG, atau WEBP.");
            }

            // Nama file unik: player_ID_random.jpg
            $new_name = "player_" . $player_id . "_" . uniqid() . "." . $file_ext;
            $target_file = $target_dir . $new_name;
            $db_path = "assets/images/players/" . $new_name;

            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {

                $query_parts .= ", photo=?";
                $params[] = $db_path;
                $types .= "s";
            } else {
                throw new Exception("Gagal upload foto.");
            }
        }

        // --- EKSEKUSI UPDATE ---
        $sql = "UPDATE players SET $query_parts WHERE player_id=?";
        $params[] = $player_id;
        $types .= "i";

        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param($types, ...$params);

     if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Player $ign berhasil diupdate!";
            
            // LOGIKA REDIRECT PINTAR
            if (!empty($team_id)) {
                // Kalau punya tim, balikin ke Roster Tim itu
                header("Location: ../admin/manage_team_players.php?team_id=" . $team_id);
            } else {
                // Kalau Free Agent, balikin ke Daftar Semua Player
                header("Location: ../admin/manage_players.php");
            }
        } else {
            throw new Exception("Database Error: " . $stmt->error);
        }

    } catch (Exception $e) {
        $_SESSION['error_msg'] = $e->getMessage();
        header("Location: ../admin/edit_player.php?id=" . $_POST['player_id']);
    }
}
?>