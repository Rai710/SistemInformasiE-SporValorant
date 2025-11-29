<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit();
}

if (isset($_GET['player_id']) && isset($_GET['team_id'])) {
    $player_id = (int)$_GET['player_id'];
    $team_id   = (int)$_GET['team_id'];

    // Update team_id jadi NULL (Free Agent)
    $sql = "UPDATE players SET team_id = NULL WHERE player_id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $player_id);

    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Pemain berhasil dikeluarkan dari tim (Sekarang Free Agent).";
    } else {
        $_SESSION['success_msg'] = "Gagal kick pemain.";
    }
    
    // Balik ke halaman roster tim tadi
    header("Location: ../admin/manage_team_players.php?team_id=" . $team_id);
    exit();
}

header("Location: ../admin/manage_teams.php");
?>