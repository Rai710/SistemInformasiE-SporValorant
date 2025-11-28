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
    
    // Asumsi BO3: Pastikan skor max 2 dan total max 3
    if ($score_t1 > 2 || $score_t2 > 2 || ($score_t1 + $score_t2 > 3) || $score_t1 == $score_t2) {
        header("Location: ../prediction.php?pesan=invalid_score");
        exit();
    }
    
    try {
        // Query INSERT / UPDATE (ON DUPLICATE KEY UPDATE mencegah double prediction)
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