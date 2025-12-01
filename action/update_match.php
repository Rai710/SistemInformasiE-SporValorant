<?php
// action/update_match.php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { header("Location: ../login.php"); exit(); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $match_id   = (int)$_POST['match_id'];
    $event_id   = (int)$_POST['event_id'];
    $team1_id   = (int)$_POST['team1_id'];
    $team2_id   = (int)$_POST['team2_id'];
    $score1     = (int)$_POST['score1'];
    $score2     = (int)$_POST['score2'];
    $match_date = $_POST['match_date'];
    $stage      = $_POST['stage'];
    $match_week = (int)$_POST['match_week'];

    if ($team1_id == $team2_id) { header("Location: ../admin/edit_match.php?id=$match_id&msg=same_team"); exit(); }

    try {
        $koneksi->begin_transaction();

        // 1. UPDATE MATCH
        $stmt = $koneksi->prepare("UPDATE match_esports SET event_id=?, team1_id=?, team2_id=?, team1_score=?, team2_score=?, match_date=?, stage=?, match_week=? WHERE match_id=?");
        $stmt->bind_param("iiiiissii", $event_id, $team1_id, $team2_id, $score1, $score2, $match_date, $stage, $match_week, $match_id);
        $stmt->execute();

        if ($score1 == $score2) { 
            $koneksi->commit();
            header("Location: ../admin/manage_matches.php?msg=updated");
            exit(); 
        }

        $winner = ($score1 > $score2) ? $team1_id : $team2_id;
        $loser  = ($score1 > $score2) ? $team2_id : $team1_id;

        // 2. AUTO ADVANCE BRACKET
        if ($stage != 'Group Stage') {
            
            // --- Helper: Cek Posisi ---
            // Tambahan: AND stage != 'Group Stage' biar match Group Stage gak ikut kehitung
            function getMatchPosition($conn, $ev, $wk, $my_id) {
                $sql = "SELECT COUNT(*) as antrian FROM match_esports 
                        WHERE event_id=$ev AND match_week=$wk 
                        AND match_id < $my_id 
                        AND stage != 'Group Stage'"; 
                $res = $conn->query($sql)->fetch_assoc();
                return (int)$res['antrian']; 
            }

            // --- Helper: Update Target (HANYA UPDATE MATCH PLAYOFF) ---
            function updateTarget($conn, $ev, $wk, $idx_target, $col, $team_id) {
                $sql = "SELECT match_id FROM match_esports 
                        WHERE event_id=$ev AND match_week=$wk 
                        AND stage != 'Group Stage' 
                        ORDER BY match_id ASC LIMIT 1 OFFSET $idx_target";
                $res = $conn->query($sql)->fetch_assoc();
                
                if ($res) {
                    $tid = $res['match_id'];
                    $conn->query("UPDATE match_esports SET $col=$team_id WHERE match_id=$tid");
                }
            }

            // Cek Posisi Saya (Sekarang pasti 0 atau 1)
            $posisi_saya = getMatchPosition($koneksi, $event_id, $match_week, $match_id);

            switch ($match_week) {
                // UB QF
                case 1:
                    // Menang -> Week 2 (Slot 2)
                    updateTarget($koneksi, $event_id, 2, $posisi_saya, 'team2_id', $winner);
                    // Kalah -> Week 4 (Slot 1)
                    updateTarget($koneksi, $event_id, 4, $posisi_saya, 'team1_id', $loser);
                    break;

                // UB SEMI
                case 2:
                    // Menang -> Week 3 (UB Final)
                    $slot = ($posisi_saya == 0) ? 'team1_id' : 'team2_id';
                    updateTarget($koneksi, $event_id, 3, 0, $slot, $winner);
                    
                    // Kalah -> Week 5 (LB R2)
                    updateTarget($koneksi, $event_id, 5, $posisi_saya, 'team1_id', $loser);
                    break;

                // LB R1
                case 4:
                    // Menang -> Week 5 (LB R2) Slot 2
                    updateTarget($koneksi, $event_id, 5, $posisi_saya, 'team2_id', $winner);
                    break;

                // LB R2
                case 5:
                    // Menang -> Week 6 (LB Semi)
                    $slot = ($posisi_saya == 0) ? 'team1_id' : 'team2_id';
                    updateTarget($koneksi, $event_id, 6, 0, $slot, $winner);
                    break;

                // UB FINAL
                case 3:
                    // Menang -> Grand Final (Week 8) Slot 1
                    updateTarget($koneksi, $event_id, 8, 0, 'team1_id', $winner);
                    // Kalah -> LB Final (Week 7) Slot 1
                    updateTarget($koneksi, $event_id, 7, 0, 'team1_id', $loser);
                    break;

                // LB SEMI
                case 6:
                    // Menang -> LB Final (Week 7) Slot 2
                    updateTarget($koneksi, $event_id, 7, 0, 'team2_id', $winner);
                    break;

                // LB FINAL
                case 7:
                    // Menang -> Grand Final (Week 8) Slot 2
                    updateTarget($koneksi, $event_id, 8, 0, 'team2_id', $winner);
                    break;
            }
        }

        // 3. HITUNG POIN
        if ($winner) {
            $qp = $koneksi->query("SELECT * FROM pickem_predictions WHERE match_id=$match_id");
            while($p = $qp->fetch_assoc()){
                $pts = 0;
                if($p['predicted_winner_id'] == $winner) {
                    $pts += 5;
                    if($p['predicted_score_t1'] == $score1 && $p['predicted_score_t2'] == $score2) $pts += 10;
                }
                $koneksi->query("UPDATE pickem_predictions SET is_graded=1, score_awarded=$pts WHERE prediction_id={$p['prediction_id']}");
                $koneksi->query("UPDATE users u SET total_pickem_points = (SELECT IFNULL(SUM(score_awarded),0) FROM pickem_predictions WHERE user_id=u.user_id AND is_graded=1) WHERE user_id={$p['user_id']}");
            }
        }

        $koneksi->commit();
        header("Location: ../admin/manage_matches.php?msg=updated");

    } catch (Exception $e) {
        $koneksi->rollback();
        die("System Error: " . $e->getMessage());
    }
}
?>