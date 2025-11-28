<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = 3; // Event VCT 2026 (Target Pick'em)

// 1. Cek Week Aktif dari Database Settings
$q_set = $koneksi->query("SELECT setting_value FROM system_settings WHERE setting_key = 'active_week'");
$active_week = ($q_set->num_rows > 0) ? $q_set->fetch_assoc()['setting_value'] : 'Week 1';

// 2. Siapkan Array Data
$matches_by_week = [
    'Week 1' => [], 'Week 2' => [], 'Week 3' => [], 'Week 4' => [], 'Week 5' => []
];
$playoff_matches = [];

// 3. Query Match + Join Prediksi User
// Pake kolom 'match_week' dari database buat grouping otomatis
$sql = "SELECT m.match_id, m.match_date, m.stage, m.match_week,
               t1.team_name as t1_name, t1.logo as t1_logo,
               t2.team_name as t2_name, t2.logo as t2_logo,
               p.prediction_id, p.predicted_score_t1, p.predicted_score_t2
        FROM match_esports m
        JOIN team t1 ON m.team1_id = t1.team_id
        JOIN team t2 ON m.team2_id = t2.team_id
        LEFT JOIN pickem_predictions p ON m.match_id = p.match_id AND p.user_id = $user_id
        WHERE m.event_id = $event_id
        ORDER BY m.match_date ASC";

$result = $koneksi->query($sql);

while($row = $result->fetch_assoc()){
    if ($row['stage'] == 'Playoffs' || $row['stage'] == 'Grand Final') {
        $playoff_matches[] = $row;
    } else {
        $week_num = $row['match_week']; 
        $key = 'Week ' . $week_num; 
        
        if(isset($matches_by_week[$key])) {
            $matches_by_week[$key][] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>VCT Pick'em - Schedule</title>
    <?php include 'config/head.php'; ?>
    <link rel="stylesheet" href="assets/css/match.css"> <style>
        .match-link { text-decoration: none; color: inherit; display: block; transition: transform 0.2s; }
        .match-link:hover .match-card { border-color: #ff4655; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(255, 70, 85, 0.1); }
        
        /* Badge Status */
        .pred-badge { font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 4px 8px; border-radius: 4px; letter-spacing: 1px; float: right; }
        .pb-done { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; }
        .pb-open { background: rgba(255, 255, 255, 0.1); color: #aaa; border: 1px solid #555; }
    </style>
</head>
<body>

<?php include 'config/navbar.php'; ?>


<div class="container">
    
    <h1 class="page-title">PICK'EM CHALLENGE</h1>
    <div class="leaderboard-link">
        <p style="color:#aaa; margin-top:20px;">
            Total Poin Kamu: <span style="color:#ffd700; font-weight:bold;"><?php echo $_SESSION['total_pickem_points'] ?? 0; ?></span>
        </p>
        <a href="leaderboard.php" style="color:#ff4655; font-weight:bold; text-decoration:none; border:1px solid #ff4655; padding:8px 20px; border-radius:4px; display:inline-block; margin-top:10px;">
            LIHAT GLOBAL LEADERBOARD
        </a>
    </div>
    <div class="tabs">
        <button class="tab-btn active" onclick="openMainTab('regular')">REGULAR SEASON</button>
        <button class="tab-btn" onclick="openMainTab('playoff')">PLAYOFFS</button>
    </div>

    <div id="regular" class="main-content active">
        
        <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'success_predict'): ?>
            <div class="alert alert-success text-center fw-bold mb-4" style="background:rgba(16, 185, 129, 0.2); color:#10b981; border:1px solid #10b981;">
                TEBAKAN TERSIMPAN!
            </div>
        <?php endif; ?>

        <div class="timeline-wrapper">
            <div class="timeline-line"></div>
            <div class="week-nav">
                <?php 
                $i = 0; 
                foreach($matches_by_week as $week => $data): 
                    $isActive = ($week == $active_week) ? 'active' : '';
                    $weekId = str_replace(' ', '', $week);
                ?>
                    <button class="week-btn <?php echo $isActive; ?>" onclick="openWeek('<?php echo $weekId; ?>', this)">
                        <span><?php echo $week; ?></span><div class="week-dot"></div>
                    </button>
                <?php $i++; endforeach; ?>
            </div>
        </div>

        <?php foreach($matches_by_week as $week => $matches): 
            $weekId = str_replace(' ', '', $week);
            $isActive = ($week == $active_week) ? 'active' : '';
        ?>
        <div id="<?php echo $weekId; ?>" class="grid-matches week-matches <?php echo $isActive; ?>">
            
            <?php if(empty($matches)): ?>
                <div style="grid-column:1/-1; text-align:center; padding:50px; color:#666;">
                    <i class="fas fa-calendar-times" style="font-size:40px; margin-bottom:10px;"></i><br>
                    <h3>TBD / TO BE ANNOUNCED</h3>
                    <p>Jadwal pertandingan belum tersedia.</p>
                </div>
            <?php else: ?>
                
                <?php foreach($matches as $m): $hasPred = !empty($m['prediction_id']); ?>
                <a href="detail_prediction.php?id=<?php echo $m['match_id']; ?>&week=<?php echo $week; ?>" class="match-link">
                    <div class="match-card">
                        <div class="card-header">
                            <span><?php echo date('D, d M', strtotime($m['match_date'])); ?></span>
                            <?php if($hasPred): ?>
                                <span class="pred-badge pb-done"><i class="fas fa-check"></i> PREDICTED</span>
                            <?php else: ?>
                                <span class="pred-badge pb-open">OPEN</span>
                            <?php endif; ?>
                        </div>
                        <div class="team-row">
                            <div class="t-info"><img src="<?php echo $m['t1_logo']; ?>" class="t-logo"><?php echo $m['t1_name']; ?></div>
                            <span class="t-score <?php echo ($hasPred && $m['predicted_score_t1'] > $m['predicted_score_t2'])?'win':''; ?>">
                                <?php echo $hasPred ? $m['predicted_score_t1'] : '-'; ?>
                            </span>
                        </div>
                        <div class="team-row">
                            <div class="t-info"><img src="<?php echo $m['t2_logo']; ?>" class="t-logo"><?php echo $m['t2_name']; ?></div>
                            <span class="t-score <?php echo ($hasPred && $m['predicted_score_t2'] > $m['predicted_score_t1'])?'win':''; ?>">
                                <?php echo $hasPred ? $m['predicted_score_t2'] : '-'; ?>
                            </span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <div id="playoff" class="main-content" style="display:none;">
        <div class="grid-matches">
            <?php if(empty($playoff_matches)): ?>
                <div style="grid-column:1/-1; text-align:center; padding:50px; color:#666;">
                    <i class="fas fa-trophy" style="font-size:40px; margin-bottom:10px;"></i><br>
                    <h3>PLAYOFFS TBD</h3>
                    <p>Menunggu babak Group Stage selesai.</p>
                </div>
            <?php else: ?>
                <?php endif; ?>
        </div>
    </div>

    
</div>
<?php include 'config/footer.php'; ?>

<script>
    function openMainTab(tabName) {
        document.getElementById('regular').style.display = 'none';
        document.getElementById('playoff').style.display = 'none';
        document.getElementById(tabName).style.display = 'block';
        
        var tabs = document.getElementsByClassName("tab-btn");
        for(var i=0; i<tabs.length; i++) tabs[i].classList.remove("active");
        event.currentTarget.classList.add("active");
    }

    function openWeek(weekId, btn) {
        var weeks = document.getElementsByClassName("week-matches");
        for(var i=0; i<weeks.length; i++) weeks[i].classList.remove("active");
        
        var btns = document.getElementsByClassName("week-btn");
        for(var i=0; i<btns.length; i++) btns[i].classList.remove("active");
        
        document.getElementById(weekId).classList.add("active");
        btn.classList.add("active");
    }
</script>

</body>
</html>