<?php
session_start();
include "../config/koneksi.php";

// Cek Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {


        $stmt = $koneksi->prepare("DELETE FROM match_esports WHERE match_id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Match ID #$id berhasil dihapus permanen.";
        } else {
            throw new Exception("Gagal menghapus match: " . $stmt->error);
        }
        $stmt->close();

    } catch (Exception $e) {

        header("Location: ../admin/manage_matches.php?msg=error_db");
        exit();
    }
}

header("Location: ../admin/manage_matches.php");
?>