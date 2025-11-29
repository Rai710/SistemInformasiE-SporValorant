<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $match_id = (int)$_POST['match_id'];
    $winner_id = (int)$_POST['predicted_winner_id'];
    $score_t1 = (int)$_POST['score_t1'];
    $score_t2 = (int)$_POST['score_t2'];

    // Cek apakah match sudah selesai/dimulai?
    $q_check = $koneksi->query("SELECT team1_score, team2_score, match_date FROM match_esports WHERE match_id = $match_id");
    $match_data = $q_check->fetch_assoc();

    if ($match_data['team1_score'] > 0 || $match_data['team2_score'] > 0) {
        header("Location: ../prediction.php?pesan=match_started");
        exit();
    }


    if ($score_t1 > 3 || $score_t2 > 3 || ($score_t1 + $score_t2 > 5) || $score_t1 == $score_t2) {
        header("Location: ../prediction.php?pesan=invalid_score");
        exit();
    }
    
    try {
        $sql = "INSERT INTO pickem_predictions 
                (user_id, match_id, predicted_winner_id, predicted_score_t1, predicted_score_t2) 
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                predicted_winner_id = VALUES(predicted_winner_id),
                predicted_score_t1 = VALUES(predicted_score_t1),
                predicted_score_t2 = VALUES(predicted_score_t2),
                prediction_date = NOW()";

        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("iiiii", $user_id, $match_id, $winner_id, $score_t1, $score_t2);

        if ($stmt->execute()) {
            header("Location: ../prediction.php?pesan=success_predict");
        } else {
            header("Location: ../prediction.php?pesan=error_db");
        }
        $stmt->close();

    } catch (Exception $e) {
        header("Location: ../prediction.php?pesan=error_system");
    }
}
$koneksi->close();
?>