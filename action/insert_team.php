<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    try {
        $team_name   = trim($_POST['team_name']);
        $country     = trim($_POST['country']);
        $description = $_POST['description'];

        if (empty($team_name)) throw new Exception("Nama Tim wajib diisi!");

        // --- UPLOAD LOGO ---
        $db_path = NULL; // Default NULL
        
        if (!empty($_FILES['logo']['name'])) {
            $target_dir = "../assets/images/teams/";
            
            // Buat folder kalau belum ada
            if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

            $file_ext = strtolower(pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];

            if (!in_array($file_ext, $allowed)) {
                throw new Exception("Format logo harus JPG, PNG, atau WEBP!");
            }

            // Nama file unik: team_randomstring.jpg
            $new_name = uniqid("team_") . "." . $file_ext;
            $target_file = $target_dir . $new_name;
            
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                $db_path = "assets/images/teams/" . $new_name;
            } else {
                throw new Exception("Gagal upload gambar ke server.");
            }
        }

        // --- INSERT DATABASE ---
        // Kolom group_name SUDAH DIHAPUS dari query ini sesuai request
        $sql = "INSERT INTO team (team_name, country, description, logo) VALUES (?, ?, ?, ?)";
        
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("ssss", $team_name, $country, $description, $db_path);

        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Tim baru <strong>$team_name</strong> berhasil ditambahkan!";
            header("Location: ../admin/manage_teams.php");
        } else {
            throw new Exception("Database Error: " . $stmt->error);
        }
        $stmt->close();

    } catch (Exception $e) {
        $_SESSION['error_msg'] = $e->getMessage();
        header("Location: ../admin/add_team.php");
    }
}
?>