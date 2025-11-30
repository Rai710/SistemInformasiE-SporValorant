<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { header("Location: ../login.php"); exit(); }

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // 1. AMBIL DATA DULU SEBELUM HAPUS 
    $q = $koneksi->query("SELECT photo, in_game_name, team_id FROM players WHERE player_id = $id");
    $data = $q->fetch_assoc();

    if ($data) {
        $team_id_lama = $data['team_id'];
        // Hapus Foto
        if (!empty($data['photo']) && strpos($data['photo'], 'http') === false) {
            $file_path = "../" . $data['photo'];
            if (file_exists($file_path)) unlink($file_path);
        }

        // Hapus Data
        $stmt = $koneksi->prepare("DELETE FROM players WHERE player_id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Player " . $data['in_game_name'] . " berhasil dihapus.";
        } else {
            $_SESSION['success_msg'] = "Gagal hapus: " . $koneksi->error;
        }

        if (!empty($team_id_lama)) {
            // Punya tim, balik ke halaman timnya
            header("Location: ../admin/manage_team_players.php?team_id=" . $team_id_lama);
        } else {
            header("Location: ../admin/manage_players.php");
        }
        exit();
    }
}

header("Location: ../admin/manage_players.php");
?>