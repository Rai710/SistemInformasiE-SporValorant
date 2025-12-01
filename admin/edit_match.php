<?php
session_start();
include "../config/koneksi.php";


// Cek Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil Data Match
$sql_match = "SELECT * FROM match_esports WHERE match_id = $id";
$match = $koneksi->query($sql_match)->fetch_assoc();

if (!$match) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='manage_matches.php';</script>";
    exit();
}

// Ambil Data Tim & Event buat Dropdown
$q_teams = $koneksi->query("SELECT * FROM team ORDER BY team_name ASC");
$teams = $q_teams->fetch_all(MYSQLI_ASSOC);

$q_events = $koneksi->query("SELECT * FROM events ORDER BY event_date DESC");

// Tentukan apakah ini BO3 atau BO5
$is_bo5 = ($match['stage'] == 'Grand Final' || $match['match_week'] == 7);

$maxScore = $is_bo5 ? 3 : 2;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Match #<?php echo $id; ?></title>
    <link rel="icon" type="image/png" href="../assets/images/logoValo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .form-box { max-width: 700px; margin: 0 auto; background: #1b2733; padding: 40px; border-radius: 8px; border: 1px solid #333; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #ccc; margin-bottom: 8px; font-weight: bold; }
        .form-control { width: 100%; background: #0f1923; border: 1px solid #555; color: white; padding: 12px; border-radius: 4px; }
        .btn-submit { background: #ff4655; color: white; border: none; padding: 12px 30px; border-radius: 4px; font-weight: bold; cursor: pointer; width: 100%; }
        .btn-submit:hover { background: #d93c48; }
        .vs-row { display: flex; gap: 20px; align-items: center; }
        .score-input { width: 80px; text-align: center; font-size: 18px; font-weight: bold; }
        .badge-bo { background: #ffd700; color: black; font-size: 10px; padding: 3px 8px; border-radius: 4px; font-weight: 900; vertical-align: top; margin-left: 5px; }
        .btn-delete {
            display: block; width: 100%; text-align: center;
            background: transparent; color: #ff4655; 
            border: 2px solid #ff4655; padding: 12px; 
            border-radius: 4px; font-weight: bold; 
            text-decoration: none; margin-top: 10px;
            transition: 0.2s;
        }
        .btn-delete:hover {
            background: #ff4655; color: white;
        }
    </style>
</head>
<body class="admin-body">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header-bar">
            <h2 class="page-title">EDIT MATCH DETAILS</h2>
        </div>

        <div class="form-box">
            
            <?php if(isset($_GET['msg'])): ?>
                <div style="padding:15px; margin-bottom:20px; border-radius:4px; text-align:center; font-weight:bold; background:rgba(255,70,85,0.2); color:#ff4655; border:1px solid #ff4655;">
                    <?php 
                    if($_GET['msg'] == 'same_team') echo "Tim 1 dan Tim 2 tidak boleh sama!";
                    elseif($_GET['msg'] == 'error_db') echo "Gagal update database.";
                    ?>
                </div>
            <?php endif; ?>

            <form action="../action/update_match.php" method="POST">
                <input type="hidden" name="match_id" value="<?php echo $match['match_id']; ?>">
                
                <div class="form-group">
                    <div class="vs-row">
                        <div style="flex:1;">
                            <label>Event</label>
                            <select name="event_id" class="form-control">
                                <?php while($ev = $q_events->fetch_assoc()): ?>
                                    <option value="<?php echo $ev['event_id']; ?>" <?php echo ($match['event_id'] == $ev['event_id']) ? 'selected' : ''; ?>>
                                        <?php echo $ev['event_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div style="flex:1;">
                            <label>Date</label>
                            <input type="date" name="match_date" class="form-control" value="<?php echo $match['match_date']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Teams & Scores <span class="badge-bo">BO<?php echo ($maxScore==3) ? '5' : '3'; ?></span></label>
                    <div class="vs-row">
                        <div style="flex:2;">
                            <select name="team1_id" class="form-control">
                                <?php foreach($teams as $t): ?>
                                    <option value="<?php echo $t['team_id']; ?>" <?php echo ($match['team1_id'] == $t['team_id']) ? 'selected' : ''; ?>>
                                        <?php echo $t['team_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <input type="number" id="score1" name="score1" class="form-control score-input" 
                               value="<?php echo $match['team1_score']; ?>" 
                               min="0" max="<?php echo $maxScore; ?>" 
                               oninput="autoWin(1, <?php echo $maxScore; ?>)">
                        
                        <span style="font-weight:bold; color:#555;">:</span>
                        
                        <input type="number" id="score2" name="score2" class="form-control score-input" 
                               value="<?php echo $match['team2_score']; ?>" 
                               min="0" max="<?php echo $maxScore; ?>" 
                               oninput="autoWin(2, <?php echo $maxScore; ?>)">

                        <div style="flex:2;">
                            <select name="team2_id" class="form-control">
                                <?php foreach($teams as $t): ?>
                                    <option value="<?php echo $t['team_id']; ?>" <?php echo ($match['team2_id'] == $t['team_id']) ? 'selected' : ''; ?>>
                                        <?php echo $t['team_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="vs-row">
                        <div style="flex:1;">
                            <label>Stage</label>
                            <select name="stage" class="form-control" id="stageSelect" onchange="updateFormat()">
                                <option value="Group Stage" <?php echo ($match['stage'] == 'Group Stage') ? 'selected' : ''; ?>>Group Stage</option>
                                <option value="Playoffs" <?php echo ($match['stage'] == 'Playoffs') ? 'selected' : ''; ?>>Playoffs</option>
                                <option value="Grand Final" <?php echo ($match['stage'] == 'Grand Final') ? 'selected' : ''; ?>>Grand Final</option>
                            </select>
                        </div>
                        <div style="flex:1;">
                            <label>Week</label>
                            <input type="number" name="match_week" class="form-control" value="<?php echo $match['match_week']; ?>">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">SAVE CHANGES</button>
                
                <a href="manage_matches.php" style="display:block; text-align:center; margin-top:15px; color:#aaa; text-decoration:none;">Cancel</a>

                <hr style="border: 0; border-top: 1px solid #333; margin: 20px 0;">

                <a href="../action/delete_match.php?id=<?php echo $match['match_id']; ?>" 
                   class="btn-delete"
                   onclick="return confirm('⚠️ YAKIN HAPUS MATCH INI?\n\nData skor dan semua prediksi user di match ini bakal ILANG PERMANEN!');">
                    <i class="fas fa-trash"></i> DELETE MATCH PERMANENTLY
                </a>

            </form>
        </div>
    </div>
<script>
        // === LOGIKA SKOR PINTAR (BO3 & BO5) ===
        function autoWin(teamIdx, max) {
            let myInput = document.getElementById('score' + teamIdx);
            // Kalau teamIdx 1, lawannya 2. Kalau 2, lawannya 1.
            let opInput = document.getElementById('score' + (teamIdx === 1 ? 2 : 1));
            
            let val = parseInt(myInput.value);
            
            // 1. Batasi Input: Gak boleh lebih dari Max Score (2 atau 3)
            if (val > max) { 
                myInput.value = max; 
                val = max; 
            }

            if (val === max) {
                if (parseInt(opInput.value) >= max) {
                    opInput.value = max - 1;
                }
                opInput.setAttribute('max', max - 1);
            } else {

                opInput.setAttribute('max', max);
            }
        }

                // === UPDATE FORMAT SAAT GANTI STAGE ===
        function updateFormat() {
            let stage = document.getElementById('stageSelect').value;

            let week  = parseInt(document.querySelector('input[name="match_week"]').value);
            
            // Jika Grand Final ATAU Week 7, maka Max 3 (BO5)
            let isBO5 = (stage === 'Grand Final' || week === 7);
            let max = isBO5 ? 3 : 2;
            
            let s1 = document.getElementById('score1');
            let s2 = document.getElementById('score2');

            s1.setAttribute('max', max);
            s2.setAttribute('max', max);
            
            if(parseInt(s1.value) > max) s1.value = max;
            if(parseInt(s2.value) > max) s2.value = max;
            
            let badge = document.querySelector('.badge-bo');
            if(badge) badge.innerText = 'BO' + (max === 3 ? '5' : '3');
            
            autoWin(1, max);
            autoWin(2, max);
        }

        document.querySelector('input[name="match_week"]').addEventListener('input', updateFormat);
    </script>
</body>
</html>