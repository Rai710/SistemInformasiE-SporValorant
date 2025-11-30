<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];
$match_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// AMBIL DATA MATCH
$sql = "SELECT m.*, 
               t1.team_name as t1_name, t1.logo as t1_logo, t1.team_id as t1_id,
               t2.team_name as t2_name, t2.logo as t2_logo, t2.team_id as t2_id,
               p.prediction_id, p.predicted_score_t1, p.predicted_score_t2, p.predicted_winner_id
        FROM match_esports m
        JOIN team t1 ON m.team1_id = t1.team_id
        JOIN team t2 ON m.team2_id = t2.team_id
        LEFT JOIN pickem_predictions p ON m.match_id = p.match_id AND p.user_id = $user_id
        WHERE m.match_id = ?";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $match_id);
$stmt->execute();
$m = $stmt->get_result()->fetch_assoc();

if(!$m) { echo "<h3 style='color:white;text-align:center;'>Match Not Found</h3>"; exit(); }

$isBO5 = ($m['stage'] == 'Grand Final' || $m['match_week'] == 7);
$maxScore = $isBO5 ? 3 : 2; 

// --- LOGIKA LOCK (VALIDASI) ---

// 1. Cek apakah Tim masih TBD (Belum ditentukan)
// Jika nama tim mengandung "TBD" atau "Winner" atau kosong, dianggap TBD
$t1Name = strtoupper($m['t1_name']);
$t2Name = strtoupper($m['t2_name']);
$isTBD = (strpos($t1Name, 'TBD') !== false || strpos($t2Name, 'TBD') !== false || 
          strpos($t1Name, 'WINNER') !== false || strpos($t2Name, 'WINNER') !== false ||
          $m['t1_name'] == '-' || $m['t2_name'] == '-');

// 2. Cek apakah pertandingan sudah selesai (Ada skor)
$isFinished = ($m['team1_score'] > 0 || $m['team2_score'] > 0);

// 3. Cek apakah user sudah prediksi
$hasPred = !empty($m['prediction_id']); 

// KEPUTUSAN FINAL: LOCKED JIKA (Sudah Prediksi ATAU TBD ATAU Selesai)
$isLocked = ($hasPred || $isTBD || $isFinished);

// Pesan Status untuk User
$lockReason = "";
if($hasPred) $lockReason = "PREDIKSI TERKUNCI";
elseif($isFinished) $lockReason = "PERTANDINGAN SELESAI";
elseif($isTBD) $lockReason = "MENUNGGU TIM (TBD)";

// Values
$s1 = $hasPred ? $m['predicted_score_t1'] : 0;
$s2 = $hasPred ? $m['predicted_score_t2'] : 0;
$winId = $hasPred ? $m['predicted_winner_id'] : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Predict Match</title>
    <?php include 'config/head.php'; ?>
    <style>
        .match-link { text-decoration: none; color: inherit; display: block; transition: transform 0.2s; }
        .match-link:hover .match-card { border-color: #ff4655; transform: translateY(-5px); box-shadow: 0 10px 20px rgba(255, 70, 85, 0.15); }

        .pred-badge { font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 4px 8px; border-radius: 4px; letter-spacing: 1px; }
        .pb-done { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; }
        .pb-open { background: rgba(255, 255, 255, 0.1); color: #aaa; border: 1px solid #555; }

        .detail-pred-container { max-width: 600px; margin: 40px auto; padding: 20px; }
        .pred-box { background: #1b2733; border: 1px solid #333; border-radius: 8px; padding: 30px; position: relative; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .vs-divider { text-align: center; font-size: 24px; font-weight: 900; color: #555; margin: 20px 0; }

        .input-row { display: flex; justify-content: space-between; align-items: center; background: #0f1923; padding: 15px; border-radius: 6px; border: 1px solid #333; transition: 0.3s; cursor: pointer; }
        .score-big-input { width: 60px; height: 50px; text-align: center; background: #263542; border: 2px solid #444; color: white; font-size: 24px; font-weight: 900; border-radius: 6px; }
        .score-big-input:focus { border-color: #ff4655; outline: none; }

        .pred-radio { display: none; }
        .input-row:has(.pred-radio:checked) { border-color: #10b981; background: rgba(16, 185, 129, 0.1); }
        .input-row:has(.pred-radio:checked) .score-big-input { border-color: #10b981; color: #10b981; }

        .lock-msg { text-align: center; padding: 20px; background: rgba(0,0,0,0.3); border-radius: 6px; color: #888; margin-top: 20px; border: 1px dashed #555; }
    </style>
</head>
<body>

<?php include 'config/navbar.php'; ?>

<div class="detail-pred-container">
    
    <a href="prediction.php" style="color:#aaa; text-decoration:none; font-weight:bold;">
        <i class="fas fa-arrow-left"></i> BACK
    </a>

    <div class="pred-box" style="margin-top:20px;">
        
        <div style="text-align:center; margin-bottom:20px;">
            <span class="pred-badge <?php echo $isBO5 ? 'pb-done' : 'pb-open'; ?>">
                <?php echo $isBO5 ? 'BO5 SERIES' : 'BO3 SERIES'; ?>
            </span>
            <div style="margin-top:10px; color:#888; font-size:12px;">
                <?php echo date('l, d F Y', strtotime($m['match_date'])); ?>
            </div>
        </div>

        <form action="action/submit_prediction.php" method="POST">
            <input type="hidden" name="match_id" value="<?php echo $match_id; ?>">

            <label class="input-row" for="rad_t1">
                <div style="display:flex; align-items:center; gap:15px;">
                    <img src="<?php echo $m['t1_logo']; ?>" style="width:50px;">
                    <span style="font-size:20px; font-weight:900; color:white; text-transform:uppercase;">
                        <?php echo $m['t1_name']; ?>
                    </span>
                </div>
                
                <input type="radio" id="rad_t1" name="predicted_winner_id" value="<?php echo $m['t1_id']; ?>" 
                       class="pred-radio" <?php echo ($winId == $m['t1_id'])?'checked':''; ?> 
                       <?php echo $isLocked?'disabled':''; ?> required>

                <input type="number" id="sc_t1" name="score_t1" class="score-big-input" 
                       min="0" max="<?php echo $maxScore; ?>" value="<?php echo $s1; ?>"
                       oninput="autoWin('t1', <?php echo $maxScore; ?>)"
                       <?php echo $isLocked?'disabled':''; ?> required>
            </label>

            <div class="vs-divider">VS</div>

            <label class="input-row" for="rad_t2">
                <div style="display:flex; align-items:center; gap:15px;">
                    <img src="<?php echo $m['t2_logo']; ?>" style="width:50px;">
                    <span style="font-size:20px; font-weight:900; color:white; text-transform:uppercase;">
                        <?php echo $m['t2_name']; ?>
                    </span>
                </div>
                
                <input type="radio" id="rad_t2" name="predicted_winner_id" value="<?php echo $m['t2_id']; ?>" 
                       class="pred-radio" <?php echo ($winId == $m['t2_id'])?'checked':''; ?> 
                       <?php echo $isLocked?'disabled':''; ?> required>

                <input type="number" id="sc_t2" name="score_t2" class="score-big-input" 
                       min="0" max="<?php echo $maxScore; ?>" value="<?php echo $s2; ?>"
                       oninput="autoWin('t2', <?php echo $maxScore; ?>)"
                       <?php echo $isLocked?'disabled':''; ?> required>
            </label>

            <?php if(!$isLocked): ?>
                <button type="submit" class="btn-red" style="width:100%; padding:15px; margin-top:30px; font-size:16px;">
                    LOCK PREDICTION
                </button>
                <div style="text-align:center; color:#ff4655; font-size:11px; margin-top:10px;">
                    *Hati-hati! Prediksi tidak bisa diubah setelah dikunci.
                </div>
            <?php else: ?>
                <div class="lock-msg">
                    <?php if($hasPred): ?>
                        <i class="fas fa-check-circle" style="color:#10b981; font-size:30px; margin-bottom:10px;"></i><br>
                        <span style="color:white; font-weight:bold;"><?php echo $lockReason; ?></span>
                    <?php else: ?>
                        <i class="fas fa-lock" style="font-size:24px; margin-bottom:10px;"></i><br>
                        <span style="color:#aaa; font-weight:bold;"><?php echo $lockReason; ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </form>

    </div>
</div>

<?php include 'config/footer.php'; ?>

<script>
    function autoWin(team, maxScore) {
        let opponent = (team === 't1') ? 't2' : 't1';
        let myInput = document.getElementById('sc_' + team);
        let opInput = document.getElementById('sc_' + opponent);
        let myRad   = document.getElementById('rad_' + team);

        let val = parseInt(myInput.value);

        if (val < 0) { myInput.value = 0; val = 0; }
        if (val > maxScore) { myInput.value = maxScore; val = maxScore; }

        if (val === maxScore) {
            if(myRad) myRad.checked = true; 
            if (parseInt(opInput.value) >= maxScore) {
                opInput.value = maxScore - 1;
            }
            opInput.setAttribute('max', maxScore - 1);
        } else {
            opInput.setAttribute('max', maxScore);
        }
    }
</script>

</body>
</html>