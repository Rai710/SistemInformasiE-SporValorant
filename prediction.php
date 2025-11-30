<?php
session_start();
include "config/koneksi.php";

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = 3; // Event VCT 2026

// AMBIL POIN
$q_user = $koneksi->query("SELECT total_pickem_points FROM users WHERE user_id = $user_id");
$my_points = $q_user->fetch_assoc()['total_pickem_points'] ?? 0;
$_SESSION['total_pickem_points'] = $my_points; 

// CEK WEEK AKTIF (Hanya untuk keperluan tampilan active tab, tidak untuk lock)
$q_set = $koneksi->query("SELECT setting_value FROM system_settings WHERE setting_key = 'active_week'");
$active_week_str = ($q_set->num_rows > 0) ? $q_set->fetch_assoc()['setting_value'] : 'Week 1';

$matches_by_week = ['Week 1' => [], 'Week 2' => [], 'Week 3' => [], 'Week 4' => [], 'Week 5' => []];

$bracket = [
    1 => [], 2 => [], 3 => [], // UB
    4 => [], 5 => [], 6 => [], 7 => [], // LB
    8 => []  // GF
];

$sql = "SELECT m.match_id, m.match_date, m.stage, m.match_week, m.team1_score, m.team2_score, m.team1_id, m.team2_id,
               t1.team_name as t1_name, t1.logo as t1_logo,
               t2.team_name as t2_name, t2.logo as t2_logo,
               p.prediction_id, p.predicted_score_t1, p.predicted_score_t2, p.predicted_winner_id
        FROM match_esports m
        JOIN team t1 ON m.team1_id = t1.team_id
        JOIN team t2 ON m.team2_id = t2.team_id
        LEFT JOIN pickem_predictions p ON m.match_id = p.match_id AND p.user_id = $user_id
        WHERE m.event_id = $event_id
        ORDER BY m.match_date ASC";

$result = $koneksi->query($sql);
$has_playoff_data = false;

while($row = $result->fetch_assoc()){
    if(empty($row['t1_logo'])) $row['t1_logo'] = 'assets/images/default.png';
    if(empty($row['t2_logo'])) $row['t2_logo'] = 'assets/images/default.png';

    if ($row['stage'] == 'Playoffs' || $row['stage'] == 'Grand Final') {
        $has_playoff_data = true;
        $w = $row['match_week'];
        if(isset($bracket[$w])) $bracket[$w][] = $row;
    } else {
        $week_num = $row['match_week']; 
        $key = 'Week ' . $week_num; 
        if(isset($matches_by_week[$key])) $matches_by_week[$key][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>VCT Pick'em Challenge</title>
    <?php include 'config/head.php'; ?>
    <link rel="stylesheet" href="assets/css/match.css"> 
    <style>
        .match-link { text-decoration: none; display: block; }
        .pred-status { font-size: 10px; font-weight: 800; float: right; padding: 2px 6px; border-radius: 4px; letter-spacing: 0.5px; }
        
        .st-done  { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; } 
        .st-open  { background: rgba(255, 255, 255, 0.1); color: #888; border: 1px solid #555; }    
        .st-close { background: rgba(255, 70, 85, 0.2); color: #ff4655; border: 1px solid #ff4655; }  
        .score-final { color: #fff !important; text-shadow: 0 0 5px rgba(255,255,255,0.5); font-weight: 900; }
        
        .m-card:hover { border-color: #ff4655; transform: translateY(-3px); cursor: pointer; transition: 0.2s; }
        
        .card-correct {
            background: linear-gradient(145deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.05)) !important;
            border: 1px solid #10b981 !important;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);
        }
        .card-wrong { opacity: 0.7; filter: grayscale(0.6); border-color: #444 !important; }

        /* LOCKED STYLE (Hanya visual jika diperlukan nanti) */
        .card-locked {
            filter: grayscale(100%) brightness(0.6);
            pointer-events: none;
            cursor: not-allowed;
            position: relative;
            border-style: dashed !important;
            border-color: #444 !important;
        }
        .locked-overlay {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            background: #000; color: #aaa; padding: 4px 8px; border-radius: 4px;
            font-size: 9px; font-weight: 800; border: 1px solid #444; letter-spacing: 1px;
            z-index: 10; display: flex; align-items: center; gap: 5px;
        }

        .user-points-area { text-align: center; margin-bottom: 30px; }
        .point-val { font-size: 32px; font-weight: 900; color: #ffd700; text-shadow: 0 0 20px rgba(255, 215, 0, 0.3); }
        .bracket-section-title { color: #ff4655; font-size: 18px; font-weight: 900; margin: 30px 0 15px; border-bottom: 2px solid #333; padding-bottom: 10px; }
    </style>
</head>
<body>

<?php include 'config/navbar.php'; ?>

<div class="container">
    
    <div class="user-points-area">
        <div style="color:#aaa; letter-spacing: 2px; font-size: 12px; font-weight: bold;">YOUR TOTAL POINTS</div>
        <div class="point-val"><?php echo $my_points; ?></div>
        <a href="leaderboard.php" style="color:#ff4655; font-size:12px; text-decoration:none; font-weight:bold; border-bottom:1px dashed #ff4655;">VIEW LEADERBOARD</a>
    </div>

    <div class="tabs">
        <button class="tab-btn active" onclick="openMainTab('regular', this)">REGULAR SEASON</button>
        <button class="tab-btn" onclick="openMainTab('playoff', this)">PLAYOFFS</button>
    </div>

    <div id="regular" class="tab-content active">
        <div class="timeline-wrapper">
            <div class="timeline-line"></div>
            <div class="week-nav">
                <?php foreach($matches_by_week as $week => $data): 
                    $isActive = ($week == $active_week_str) ? 'active' : ''; $weekId = str_replace(' ', '', $week);
                ?>
                    <button class="week-btn <?php echo $isActive; ?>" onclick="openWeek('<?php echo $weekId; ?>', this)">
                        <span><?php echo $week; ?></span><div class="week-dot"></div>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <?php foreach($matches_by_week as $week => $matches): $weekId = str_replace(' ', '', $week); $isActive = ($week == $active_week_str) ? 'active' : ''; ?>
        <div id="<?php echo $weekId; ?>" class="grid-matches week-matches <?php echo $isActive; ?>">
            <?php if(empty($matches)): ?>
                <div style="grid-column:1/-1; text-align:center; padding:50px; color:#666;"><h3>TBD</h3><p>Jadwal belum tersedia.</p></div>
            <?php else: ?>
                <?php foreach($matches as $m) renderPredCard($m, false); ?>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <div id="playoff" class="tab-content">
        <?php if(!$has_playoff_data): ?>
            <h3 style="color:#ff4655; text-align:center; padding:50px;">PLAYOFFS BELUM TERSEDIA</h3>
        <?php else: ?>
            
            <div class="bracket-container">
                <div class="bracket-main-area">
                    
                    <h3 class="bracket-section-title"><i class="fas fa-level-up-alt"></i> UPPER BRACKET</h3>
                    <div class="bracket-row">
                        <div class="round-col"> 
                            <div class="round-label">UB Quarterfinals</div> 
                            <?php foreach($bracket[1] as $m) renderPredCard($m, false); ?> 
                        </div>
                        <div class="round-col"> 
                            <div class="round-label">UB Semifinals</div> 
                            <?php foreach($bracket[2] as $m) renderPredCard($m, false); ?> 
                        </div>
                        <div class="round-col"> 
                            <div class="round-label">UB Final</div> 
                            <?php foreach($bracket[3] as $m) renderPredCard($m, false); ?> 
                        </div>
                    </div>

                    <h3 class="bracket-section-title" style="margin-top:50px; border-color:#555; color:#aaa;"><i class="fas fa-level-down-alt"></i> LOWER BRACKET</h3>
                    <div class="bracket-row">
                        <div class="round-col"> 
                            <div class="round-label">LB Round 1</div> 
                            <?php foreach($bracket[4] as $m) renderPredCard($m, false); ?> 
                        </div>
                        <div class="round-col"> 
                            <div class="round-label">LB Round 2</div> 
                            <?php foreach($bracket[5] as $m) renderPredCard($m, false); ?> 
                        </div>
                        <div class="round-col"> 
                            <div class="round-label">LB Round 3</div> 
                            <?php foreach($bracket[6] as $m) renderPredCard($m, false); ?> 
                        </div>
                        <div class="round-col"> 
                            <div class="round-label">LB Final</div> 
                            <?php foreach($bracket[7] as $m) renderPredCard($m, false); ?> 
                        </div>
                    </div>

                </div>

                <?php $gf = !empty($bracket[8]) ? reset($bracket[8]) : null; ?>
                <div class="gf-area">
                    <?php if($gf): 
                        // Logic Status GF
                        $gfFinished = ($gf['team1_score'] > 0 || $gf['team2_score'] > 0);
                        $gfHasPred  = !empty($gf['prediction_id']);
                        
                        $gfRealWinner = 0;
                        if($gf['team1_score'] > $gf['team2_score']) $gfRealWinner = $gf['team1_id'];
                        elseif($gf['team2_score'] > $gf['team1_score']) $gfRealWinner = $gf['team2_id'];

                        $gfIsCorrect = ($gfFinished && $gfHasPred && $gf['predicted_winner_id'] == $gfRealWinner);
                        
                        $gfExtraClass = "";
                        if($gfFinished) {
                            if($gfIsCorrect) { $gfExtraClass = "card-correct"; $gfLabel = "WIN (+15)"; $gfClass = "st-done"; }
                            else { $gfExtraClass = "card-wrong"; $gfLabel = "CLOSED"; $gfClass = "st-close"; }
                            $gfS1 = $gf['team1_score']; $gfS2 = $gf['team2_score'];
                            $gfScoreClass = "score-final";
                        } else {
                            if($gfHasPred) { $gfLabel = "PREDICTED"; $gfClass = "st-done"; $gfS1 = $gf['predicted_score_t1']; $gfS2 = $gf['predicted_score_t2']; }
                            else { $gfLabel = "OPEN"; $gfClass = "st-open"; $gfS1 = "-"; $gfS2 = "-"; }
                            $gfScoreClass = "";
                        }
                    ?>
                    <a href="detail_prediction.php?id=<?php echo $gf['match_id']; ?>&week=Playoff" class="match-link">
                        <div class="victory-stage">
                            <div class="m-card gf-card <?php echo $gfExtraClass; ?>">
                                <div class="gf-header">
                                    GRAND FINAL 
                                    <span class="pred-status <?php echo $gfClass; ?>"><?php echo $gfLabel; ?></span>
                                </div>
                                <div class="m-row">
                                    <span><img src="<?php echo $gf['t1_logo']; ?>" class="m-logo"><?php echo $gf['t1_name']; ?></span>
                                    <span class="m-score <?php echo $gfScoreClass; ?>"><?php echo $gfS1; ?></span>
                                </div>
                                <div class="m-row">
                                    <span><img src="<?php echo $gf['t2_logo']; ?>" class="m-logo"><?php echo $gf['t2_name']; ?></span>
                                    <span class="m-score <?php echo $gfScoreClass; ?>"><?php echo $gfS2; ?></span>
                                </div>
                            </div>
                            <div class="champion-side">
                                <img src="assets/images/pialaS1.png" class="trophy-img" alt="Trophy">
                            </div>
                        </div>
                    </a>
                    <?php else: ?>
                        <div class="champion-side"><img src="assets/images/pialaS1.png" class="trophy-img" style="opacity:0.3;"></div>
                    <?php endif; ?>
                </div>

            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'config/footer.php'; ?>

<?php 
function renderPredCard($m, $isLocked = false) {
    $hasPred = !empty($m['prediction_id']);
    $isFinished = ($m['team1_score'] > 0 || $m['team2_score'] > 0);
    $weekParam = ($m['stage'] == 'Group Stage') ? 'Week '.$m['match_week'] : 'Playoff';

    $realWinner = 0;
    if($m['team1_score'] > $m['team2_score']) $realWinner = $m['team1_id'];
    elseif($m['team2_score'] > $m['team1_score']) $realWinner = $m['team2_id'];

    $isCorrect = ($isFinished && $hasPred && $m['predicted_winner_id'] == $realWinner);

    $extraClass = "";
    if ($isFinished) {
        if ($isCorrect) { $statusLabel = "CORRECT"; $statusClass = "st-done"; $extraClass = "card-correct"; } 
        else { $statusLabel = "CLOSED"; $statusClass = "st-close"; $extraClass = "card-wrong"; }
        $s1 = $m['team1_score']; $s2 = $m['team2_score'];
        $scoreClass = "score-final";
    } else {
        if ($hasPred) { $statusLabel = "PREDICTED"; $statusClass = "st-done"; $s1 = $m['predicted_score_t1']; $s2 = $m['predicted_score_t2']; } 
        else { $statusLabel = "OPEN"; $statusClass = "st-open"; $s1 = "-"; $s2 = "-"; }
        $scoreClass = "";
    }

    $linkHref = $isLocked ? '#' : "detail_prediction.php?id={$m['match_id']}&week={$weekParam}";
    $lockedClass = $isLocked ? 'card-locked' : '';
    ?>
    
    <a href="<?php echo $linkHref; ?>" class="match-link <?php echo $lockedClass; ?>">
        <?php if($isLocked): ?>
            <div class="locked-overlay"><i class="fas fa-lock"></i> LOCKED</div>
        <?php endif; ?>

        <div class="m-card <?php echo $extraClass; ?>">
            <div class="card-header" style="justify-content:space-between; display:flex;">
                <span><?php echo date('d M', strtotime($m['match_date'])); ?></span>
                <?php if($isCorrect): ?>
                    <span class="pred-status st-done" style="border-color:#fff; color:#fff; background:#10b981;"><i class="fas fa-check"></i> WIN</span>
                <?php else: ?>
                    <span class="pred-status <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                <?php endif; ?>
            </div>
            <div class="m-row">
                <span><img src="<?php echo $m['t1_logo']; ?>" class="m-logo"><?php echo $m['t1_name']; ?></span>
                <span class="m-score <?php echo $scoreClass; ?> <?php echo ($s1 > $s2 && $s1 !== '-')?'win':''; ?>"><?php echo $s1; ?></span>
            </div>
            <div class="m-row">
                <span><img src="<?php echo $m['t2_logo']; ?>" class="m-logo"><?php echo $m['t2_name']; ?></span>
                <span class="m-score <?php echo $scoreClass; ?> <?php echo ($s2 > $s1 && $s2 !== '-')?'win':''; ?>"><?php echo $s2; ?></span>
            </div>
        </div>
    </a>
<?php } ?>

<script>
    function openMainTab(tabName, btn) {
        var contents = document.getElementsByClassName("tab-content");
        for(var i=0; i<contents.length; i++) contents[i].style.display = 'none';
        var tabs = document.getElementsByClassName("tab-btn");
        for(var i=0; i<tabs.length; i++) tabs[i].classList.remove("active");
        
        document.getElementById(tabName).style.display = 'block';
        btn.classList.add("active");
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