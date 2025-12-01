<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { header("Location: ../login.php"); exit(); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $team_id     = (int)$_POST['team_id'];
        $team_name   = trim($_POST['team_name']);
        $country     = trim($_POST['country']);
        $description = $_POST['description'];

        // --- UPLOAD LOGO ---
        $logo_query_part = "";
        $params = [$team_name, $country, $description];
        $types = "sss";

        if (!empty($_FILES['logo']['name'])) {
            $target_dir = "../assets/images/teams/";
            if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

            $file_ext = strtolower(pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION));
            $new_name = uniqid("team_") . "." . $file_ext;
            $target_file = $target_dir . $new_name;
            
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                $logo_query_part = ", logo = ?";
                $params[] = "assets/images/teams/" . $new_name;
                $types .= "s";
            }
        }

        // --- QUERY (Tanpa group_name) ---
        $sql = "UPDATE team SET team_name=?, country=?, description=? $logo_query_part WHERE team_id=?";
        
        $params[] = $team_id; 
        $types .= "i";

        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Tim berhasil diupdate!";
            header("Location: ../admin/manage_teams.php");
        } else {
            throw new Exception("Error: " . $stmt->error);
        }
        $stmt->close();

    } catch (Exception $e) {
        $_SESSION['error_msg'] = $e->getMessage();
        header("Location: ../admin/edit_team.php?id=" . $_POST['team_id']);
    }
}
?>