<?php
session_start();
include "../config/koneksi.php";

// 1. CEK AKSES
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Mulai Transaksi biar aman (Kalau gagal satu, batal semua)
    $koneksi->begin_transaction();

    try {
        // A. AMBIL INFO TIM (Buat hapus logo nanti)
        $q = $koneksi->query("SELECT team_name, logo FROM team WHERE team_id = $id");
        $data = $q->fetch_assoc();

        if (!$data) {
            throw new Exception("Tim tidak ditemukan!");
        }

        // --- B. BERSIH-BERSIH RELASI (PENTING!) ---

        // 1. Lepas Pemain (Set jadi Free Agent)
        // Jangan dihapus playernya, kasian. Cuma copot seragam aja.
        $koneksi->query("UPDATE players SET team_id = NULL WHERE team_id = $id");

        // 2. Hapus Data Partisipasi Event
        $koneksi->query("DELETE FROM event_teams WHERE team_id = $id");

        // 3. Hapus History Match (Jadwal & Skor)
        // Hati-hati: Ini akan menghapus SEMUA match yang melibatkan tim ini!
        $koneksi->query("DELETE FROM match_esports WHERE team1_id = $id OR team2_id = $id");

        // 4. Hapus Statistik Tim (Kalau ada tabel team_stats)
        $koneksi->query("DELETE FROM team_stats WHERE team_id = $id");

        // --- C. HAPUS INDUK TIM ---
        $stmt = $koneksi->prepare("DELETE FROM team WHERE team_id = ?");
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Gagal menghapus tim utama: " . $stmt->error);
        }

        // --- D. HAPUS FILE LOGO FISIK ---
        if (!empty($data['logo']) && strpos($data['logo'], 'http') === false) {
            $file_path = "../" . $data['logo'];
            if (file_exists($file_path)) unlink($file_path);
        }

        // SUKSES SEMUA? KOMIT!
        $koneksi->commit();
        $_SESSION['success_msg'] = "Tim <strong>" . htmlspecialchars($data['team_name']) . "</strong> dan semua history match-nya berhasil dihapus.";

    } catch (Exception $e) {
        // ADA ERROR? BATALIN SEMUA (ROLLBACK)
        $koneksi->rollback();
        $_SESSION['error_msg'] = "Gagal Hapus: " . $e->getMessage();
    }
}

header("Location: ../admin/manage_teams.php");
exit();
?>