<?php
session_start();
include "../config/koneksi.php";

// CEK ADMIN ACCESS
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$match_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($match_id == 0) { header("Location: ../admin/manage_matches.php"); exit(); }

// Ambil data POST
$final_score_t1 = (int)$_POST['final_score_t1'];
$final_score_t2 = (int)$_POST['final_score_t2'];

$map_ids = $_POST['map_id'];
$map_scores_t1 = $_POST['map_score_t1'];
$map_scores_t2 = $_POST['map_score_t2'];

// Logika Transaksi: Safety Net
$koneksi->begin_transaction();
$error_flag = false;

try {
    // ===============================================
    // 1. UPDATE SKOR UTAMA (HANYA team1_score & team2_score)
    // ===============================================
    $sql_main = "UPDATE match_esports SET 
                 team1_score = ?, 
                 team2_score = ?
                 WHERE match_id = ?";
    
    $stmt_main = $koneksi->prepare($sql_main);
    // Hanya 3 parameter integer (Skor T1, Skor T2, Match ID)
    $stmt_main->bind_param("iii", $final_score_t1, $final_score_t2, $match_id);
    if (!$stmt_main->execute()) {
        $error_flag = true;
    }
    $stmt_main->close();

    // ===============================================
    // 2. UPDATE/INSERT SKOR PER MAP (match_maps)
    // ===============================================
    
    // Hapus data map lama dulu agar bersih
    $koneksi->query("DELETE FROM match_maps WHERE match_id = $match_id");
    
    // Looping untuk setiap map yang diinput
    foreach ($map_ids as $index => $map_id) {
        $m_id = (int)$map_id;
        $m_s1 = (int)$map_scores_t1[$index];
        $m_s2 = (int)$map_scores_t2[$index];

        // Hanya insert jika map dipilih (ID > 0) dan skor valid
        if ($m_id > 0 && ($m_s1 > 0 || $m_s2 > 0)) {
            $sql_map = "INSERT INTO match_maps (match_id, map_id, score_team1, score_team2) VALUES (?, ?, ?, ?)";
            $stmt_map = $koneksi->prepare($sql_map);
            $stmt_map->bind_param("iiii", $match_id, $m_id, $m_s1, $m_s2);
            
            if (!$stmt_map->execute()) {
                $error_flag = true;
                break; // Hentikan loop jika ada error
            }
            $stmt_map->close();
        }
    }

    
    if ($error_flag) {
        $koneksi->rollback();
        header("Location: ../admin/edit_score.php?id=$match_id&pesan=error_db");
    } else {
        $koneksi->commit();
        header("Location: ../admin/manage_matches.php?pesan=success");
    }

} catch (Exception $e) {
    $koneksi->rollback();
    header("Location: ../admin/edit_score.php?id=$match_id&pesan=error_system");
}

$koneksi->close();
?>