<?php

include "config/koneksi.php";

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// LOGIKA PILIH STAGE
$selected_stage = isset($_GET['stage']) ? (int)$_GET['stage'] : 1; 

// AMBIL NAMA EVENT
$q_event_name = $koneksi->prepare("SELECT event_name FROM events WHERE event_id = ?");
$q_event_name->bind_param("i", $selected_stage);
$q_event_name->execute();
$stage_data = $q_event_name->get_result()->fetch_assoc();
$stage_name = $stage_data['event_name'] ?? "VCT PACIFIC";
$q_event_name->close();


$matches_by_week = [
    'week1' => ['label' => 'WEEK 1', 'data' => []],
    'week2' => ['label' => 'WEEK 2', 'data' => []],
    'week3' => ['label' => 'WEEK 3', 'data' => []],
    'week4' => ['label' => 'WEEK 4', 'data' => []],
    'week5' => ['label' => 'WEEK 5', 'data' => []]
];
$playoff = []; 

// QUERY MATCH UTAMA
$sql = "SELECT m.*, m.match_week,  
               t1.team_name as team1_name, t1.logo as team1_logo,
               t2.team_name as team2_name, t2.logo as team2_logo
        FROM match_esports m
        JOIN team t1 ON m.team1_id = t1.team_id
        JOIN team t2 ON m.team2_id = t2.team_id
        WHERE m.event_id = $selected_stage 
        ORDER BY m.match_date ASC, m.match_id ASC";

$result = $koneksi->query($sql);

if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        if($row['stage'] == 'Playoffs' || $row['stage'] == 'Grand Final'){
            $playoff[] = $row;
        } else {
            $week_num = $row['match_week']; 
            $key = 'week' . $week_num; 
            
            if(isset($matches_by_week[$key])) {
                $matches_by_week[$key]['data'][] = $row;
            }
        }
    }
}

// QUERY TEAM & KLASEMEN
$sql_teams = "SELECT t.*, et.group_name 
              FROM team t
              JOIN event_teams et ON t.team_id = et.team_id
              WHERE et.event_id = $selected_stage
              ORDER BY t.team_name ASC";

$res_teams = $koneksi->query($sql_teams);
$standings = ['Group A' => [], 'Group B' => []];

while($t = $res_teams->fetch_assoc()){
    $g = $t['group_name']; 
    if(!isset($standings[$g])) $standings[$g] = [];

    $standings[$g][$t['team_id']] = [
        'name' => $t['team_name'], 
        'logo' => $t['logo'],
        'win' => 0, 'lose' => 0, 'rw' => 0, 'rl' => 0
    ];
}

// QUERY KALKULASI STATS
$sql_calc = "SELECT * FROM match_esports WHERE stage = 'Group Stage' AND event_id = $selected_stage";
$res_calc = $koneksi->query($sql_calc);
if($res_calc){
    while($m = $res_calc->fetch_assoc()){
        if ($m['team1_score'] == 0 && $m['team2_score'] == 0) { continue; }

        $id1 = $m['team1_id']; $id2 = $m['team2_id']; $s1 = $m['team1_score']; $s2 = $m['team2_score'];
        
        foreach(['Group A', 'Group B'] as $grp) {
            if(isset($standings[$grp][$id1])) {
                $standings[$grp][$id1]['rw'] += $s1; $standings[$grp][$id1]['rl'] += $s2;
                if($s1 > $s2) $standings[$grp][$id1]['win']++; else $standings[$grp][$id1]['lose']++;
            }
            if(isset($standings[$grp][$id2])) {
                $standings[$grp][$id2]['rw'] += $s2; $standings[$grp][$id2]['rl'] += $s1;
                if($s2 > $s1) $standings[$grp][$id2]['win']++; else $standings[$grp][$id2]['lose']++;
            }
        }
    }
}
function sortTable($a, $b) {
    if ($a['win'] == $b['win']) return ($b['rw']-$b['rl']) - ($a['rw']-$a['rl']);
    return $b['win'] - $a['win'];
}
usort($standings['Group A'], 'sortTable');
usort($standings['Group B'], 'sortTable');

function getMatch($data, $index) {
    if(isset($data[$index])) return $data[$index];
    return ['team1_name' => 'TBD', 'team1_logo' => '', 'team1_score' => '-', 'team2_name' => 'TBD', 'team2_logo' => '', 'team2_score' => '-', 'match_date' => 'Upcoming'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $stage_name; ?> Match Center</title>
<?php include 'config/head.php'; ?>
<link rel="stylesheet" href="assets/css/match.css" />
<style>
    .page-title { text-align: center; font-size: 42px; font-weight: 900; color: white; margin: 40px 0 20px; letter-spacing: 2px; text-transform: uppercase; }
</style>
</head>
<body>
<?php include 'config/navbar.php'; ?>

<div class="container">
    <h1 class="page-title"><?php echo $stage_name; ?></h1>
    <div class="tabs">
        <button class="tab-btn active" onclick="openTab('group', this)">Regular Season</button>
        <button class="tab-btn" onclick="openTab('playoff', this)">Playoffs</button>
    </div>

    <div id="group" class="tab-content active">
        <div class="timeline-wrapper">
            <div class="timeline-line"></div>
            <div class="week-nav">
                <?php $i = 0; foreach($matches_by_week as $key => $val): 
                    if(empty($val['data']) && $i > 0) continue; 
                    $activeClass = ($i == 0) ? 'active' : '';
                    $val['label'] = strtoupper(str_replace('week', 'WEEK ', $key));
                ?>
                    <button class="week-btn <?php echo $activeClass; ?>" onclick="openWeek('<?php echo $key; ?>', this)">
                        <span><?php echo $val['label']; ?></span><div class="week-dot"></div>
                    </button>
                <?php $i++; endforeach; ?>
            </div>
        </div>
        
        <?php $j = 0; foreach($matches_by_week as $key => $val): 
            $isActive = ($j == 0) ? 'active' : ''; $matches = $val['data']; 
        ?>
            <div id="<?php echo $key; ?>" class="grid-matches week-matches <?php echo $isActive; ?>">
                <?php if(empty($matches)): ?>
                    <div style="grid-column: 1/-1; text-align:center; padding:30px; color:#666;">Tidak ada pertandingan di minggu ini.</div>
                <?php else: ?>
                    <?php foreach($matches as $m): ?>
                       <div class="match-card">
                            <div class="card-header">
                                <span><?php echo date('l, d F', strtotime($m['match_date'])); ?></span>
                                <span style="color:<?php echo ($m['team1_score']!=0 || $m['team2_score']!=0) ? '#10b981' : '#ff4655'; ?>;">
                                    <?php echo ($m['team1_score']!=0 || $m['team2_score']!=0) ? 'FINISHED' : 'UPCOMING'; ?>
                                </span>
                            </div>
                            <div class="team-row">
                                <div class="t-info"><img src="<?php echo $m['team1_logo']?:'image/default.png'; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div>
                                <span class="t-score <?php echo $m['team1_score']>$m['team2_score']?'win':''; ?>"><?php echo $m['team1_score']; ?></span>
                            </div>
                            <div class="team-row">
                                <div class="t-info"><img src="<?php echo $m['team2_logo']?:'image/default.png'; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div>
                                <span class="t-score <?php echo $m['team2_score']>$m['team1_score']?'win':''; ?>"><?php echo $m['team2_score']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php $j++; endforeach; ?>

        <div class="standings-section">
            <div class="standings-title">STANDINGS</div>
            <div class="standings-wrapper">
                <?php foreach(['Group A', 'Group B'] as $grpName): ?>
                <div class="standings-box">
                    <div class="st-title"><?php echo strtoupper($grpName); ?></div>
                    <table class="velo-table">
                        <thead><tr><th width="30">#</th> <th>TEAM</th> <th width="60" style="text-align:center">W-L</th> <th width="50" style="text-align:center">DIFF</th></tr></thead>
                        <tbody><?php $rank=1; foreach($standings[$grpName] as $team): $diff = $team['rw'] - $team['rl']; ?>
                            <tr><td class="<?php echo ($rank<=3)?'rank-'.$rank:''; ?>"><?php echo $rank; ?></td><td style="display:flex; align-items:center; gap:10px;"><img src="<?php echo $team['logo']; ?>" style="width:24px;"> <?php echo $team['name']; ?></td><td style="text-align:center"><?php echo $team['win']."-".$team['lose']; ?></td><td style="text-align:center" class="<?php echo $diff>=0?'diff-plus':'diff-min'; ?>"><?php echo ($diff>0?'+':'').$diff; ?></td></tr>
                        <?php $rank++; endforeach; ?></tbody>
                    </table>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

   <div id="playoff" class="tab-content">
        <div class="bracket-container">
            
            <div class="bracket-main-area">
                
                <h3 style="color:var(--red); margin-bottom:10px; font-size:16px;">UPPER BRACKET</h3>
                <div class="bracket-row">
                    <div class="round-col">
                        <div class="round-label">UB Quarterfinals</div>
                        <?php $m = getMatch($playoff, 0); ?>
                        <div class="m-card">
                            <div class="m-row"><span><img src="<?php echo $m['team1_logo']; ?>" class="m-logo"><?php echo $m['team1_name']; ?></span><span class="m-score win"><?php echo $m['team1_score']; ?></span></div>
                            <div class="m-row"><span><img src="<?php echo $m['team2_logo']; ?>" class="m-logo"><?php echo $m['team2_name']; ?></span><span class="m-score"><?php echo $m['team2_score']; ?></span></div>
                        </div>
                        <?php $m = getMatch($playoff, 1); ?>
                        <div class="m-card">
                            <div class="m-row"><span><img src="<?php echo $m['team1_logo']; ?>" class="m-logo"><?php echo $m['team1_name']; ?></span><span class="m-score win"><?php echo $m['team1_score']; ?></span></div>
                            <div class="m-row"><span><img src="<?php echo $m['team2_logo']; ?>" class="m-logo"><?php echo $m['team2_name']; ?></span><span class="m-score"><?php echo $m['team2_score']; ?></span></div>
                        </div>
                    </div>
                    <div class="round-col">
                        <div class="round-label">UB Semifinals</div>
                        <?php $m = getMatch($playoff, 2); ?>
                        <div class="m-card">
                            <div class="m-row"><span><img src="<?php echo $m['team1_logo']; ?>" class="m-logo"><?php echo $m['team1_name']; ?></span><span class="m-score"><?php echo $m['team1_score']; ?></span></div>
                            <div class="m-row"><span><img src="<?php echo $m['team2_logo']; ?>" class="m-logo"><?php echo $m['team2_name']; ?></span><span class="m-score"><?php echo $m['team2_score']; ?></span></div>
                        </div>
                        <?php $m = getMatch($playoff, 3); ?>
                        <div class="m-card">
                            <div class="m-row"><span><img src="<?php echo $m['team1_logo']; ?>" class="m-logo"><?php echo $m['team1_name']; ?></span><span class="m-score"><?php echo $m['team1_score']; ?></span></div>
                            <div class="m-row"><span><img src="<?php echo $m['team2_logo']; ?>" class="m-logo"><?php echo $m['team2_name']; ?></span><span class="m-score"><?php echo $m['team2_score']; ?></span></div>
                        </div>
                    </div>
                    <div class="round-col">
                        <div class="round-label">UB Final</div>
                        <?php $m = getMatch($playoff, 8); ?>
                        <div class="m-card">
                            <div class="m-row"><span><img src="<?php echo $m['team1_logo']; ?>" class="m-logo"><?php echo $m['team1_name']; ?></span><span class="m-score"><?php echo $m['team1_score']; ?></span></div>
                            <div class="m-row"><span><img src="<?php echo $m['team2_logo']; ?>" class="m-logo"><?php echo $m['team2_name']; ?></span><span class="m-score"><?php echo $m['team2_score']; ?></span></div>
                        </div>
                    </div>
                </div>

                <h3 style="color:var(--red); margin-top:20px; margin-bottom:10px; font-size:16px;">LOWER BRACKET</h3>
                <div class="bracket-row">
                    <div class="round-col">
                        <div class="round-label">LB Round 1</div>
                        <?php $m = getMatch($playoff, 4); ?>
                        <div class="m-card">
                            <div class="m-row"><span><img src="<?php echo $m['team1_logo']; ?>" class="m-logo"><?php echo $m['team1_name']; ?></span><span class="m-score"><?php echo $m['team1_score']; ?></span></div>
                            <div class="m-row"><span><img src="<?php echo $m['team2_logo']; ?>" class="m-logo"><?php echo $m['team2_name']; ?></span><span class="m-score"><?php echo $m['team2_score']; ?></span></div>
                        </div>
                        <?php $m = getMatch($playoff, 5); ?>
                        <div class="m-card">
                            <div class="m-row"><span><img src="<?php echo $m['team1_logo']; ?>" class="m-logo"><?php echo $m['team1_name']; ?></span><span class="m-score"><?php echo $m['team1_score']; ?></span></div>
                            <div class="m-row"><span><img src="<?php echo $m['team2_logo']; ?>" class="m-logo"><?php echo $m['team2_name']; ?></span><span class="m-score"><?php echo $m['team2_score']; ?></span></div>
                        </div>
                    </div>
                    <div class="round-col">
                        <div class="round-label">LB Round 2</div>
                        <?php $m = getMatch($playoff, 6); ?>
                        <div class="m-card">
                            <div class="m-row"><span><img src="<?php echo $m['team1_logo']; ?>" class="m-logo"><?php echo $m['team1_name']; ?></span><span class="m-score"><?php echo $m['team1_score']; ?></span></div>
                            <div class="m-row"><span><img src="<?php echo $m['team2_logo']; ?>" class="m-logo"><?php echo $m['team2_name']; ?></span><span class="m-score"><?php echo $m['team2_score']; ?></span></div>
                        </div>
                        <?php $m = getMatch($playoff, 7); ?>
                        <div class="m-card">
                            <div class="m-row"><span><img src="<?php echo $m['team1_logo']; ?>" class="m-logo"><?php echo $m['team1_name']; ?></span><span class="m-score"><?php echo $m['team1_score']; ?></span></div>
                            <div class="m-row"><span><img src="<?php echo $m['team2_logo']; ?>" class="m-logo"><?php echo $m['team2_name']; ?></span><span class="m-score"><?php echo $m['team2_score']; ?></span></div>
                        </div>
                    </div>
                    <div class="round-col">
                        <div class="round-label">LB Final</div>
                        <?php $m = getMatch($playoff, 10); ?>
                        <div class="m-card">
                            <div class="m-row"><span><img src="<?php echo $m['team1_logo']; ?>" class="m-logo"><?php echo $m['team1_name']; ?></span><span class="m-score"><?php echo $m['team1_score']; ?></span></div>
                            <div class="m-row"><span><img src="<?php echo $m['team2_logo']; ?>" class="m-logo"><?php echo $m['team2_name']; ?></span><span class="m-score"><?php echo $m['team2_score']; ?></span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="gf-area">
                
                <h3 style="color:var(--gold); text-align:center; margin-bottom:20px; font-size:18px; letter-spacing:3px; text-shadow:0 0 15px rgba(255,215,0,0.3);">
                    <i class="fas fa-trophy"></i> GRAND FINAL
                </h3>

                <?php 
                // Logic Piala & Winner (Tetap Sama)
                $piala_img = ($selected_stage == 1) ? 'assets/images/pialaS1.png' : 'assets/images/pialaS2.png';
                $m = getMatch($playoff, 11); 
                $winner_logo = '';
                if($m['team1_name'] != 'TBD' && is_numeric($m['team1_score'])) {
                    if($m['team1_score'] > $m['team2_score']) $winner_logo = $m['team1_logo'];
                    elseif ($m['team2_score'] > $m['team1_score']) $winner_logo = $m['team2_logo'];
                }
                ?>

                <div class="victory-stage">
                    
                    <div class="m-card gf-card">
                        <div class="gf-header">BO5 SERIES</div>
                        <div class="m-row <?php echo ($m['team1_score'] > $m['team2_score']) ? 'winner-row' : ''; ?>">
                            <span><img src="<?php echo $m['team1_logo']; ?>" class="m-logo gf-logo"><?php echo $m['team1_name']; ?></span>
                            <span class="m-score <?php echo ($m['team1_score'] > $m['team2_score']) ? 'win' : ''; ?>" style="color:var(--gold); font-size:18px;"><?php echo $m['team1_score']; ?></span>
                        </div>
                        <div class="m-row <?php echo ($m['team2_score'] > $m['team1_score']) ? 'winner-row' : ''; ?>">
                            <span><img src="<?php echo $m['team2_logo']; ?>" class="m-logo gf-logo"><?php echo $m['team2_name']; ?></span>
                            <span class="m-score <?php echo ($m['team2_score'] > $m['team1_score']) ? 'win' : ''; ?>" style="color:var(--gold); font-size:18px;"><?php echo $m['team2_score']; ?></span>
                        </div>
                    </div>

                    <div class="champion-side">
                        <img src="<?php echo $piala_img; ?>" class="trophy-img" alt="Trophy">
                        
                        <?php if(!empty($winner_logo)): ?>
                            <div class="winner-badge">
                                <img src="<?php echo $winner_logo; ?>" class="winner-logo">
                                <span class="winner-label">CHAMPION</span>
                            </div>
                        <?php else: ?>
                            <div class="winner-label" style="background:transparent; border:1px solid #555; color:#888; box-shadow:none;">
                                ???
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'config/footer.php'; ?>
<script>
    function openTab(tabName, btnElement) {
        var contents = document.getElementsByClassName("tab-content");
        for(var i=0; i<contents.length; i++) contents[i].classList.remove("active");
        var tabs = document.getElementsByClassName("tab-btn");
        for(var i=0; i<tabs.length; i++) tabs[i].classList.remove("active");
        document.getElementById(tabName).classList.add("active");
        btnElement.classList.add("active");
    }
    function openWeek(weekId, btnElement) {
        var weeks = document.getElementsByClassName("week-matches");
        for(var i=0; i<weeks.length; i++) weeks[i].classList.remove("active");
        var btns = document.getElementsByClassName("week-btn");
        for(var i=0; i<btns.length; i++) btns[i].classList.remove("active");
        document.getElementById(weekId).classList.add("active");
        btnElement.classList.add("active");
    }
</script>
</body>
</html>