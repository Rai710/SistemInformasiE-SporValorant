<?php

include "koneksi.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$selected_stage = isset($_GET['stage']) ? $_GET['stage'] : 1; 
$stage_name = ($selected_stage == 1) ? "STAGE 1" : "STAGE 2";

$matches_by_week = [
    'week1' => ['label' => 'WEEK 1', 'data' => []],
    'week2' => ['label' => 'WEEK 2', 'data' => []],
    'week3' => ['label' => 'WEEK 3', 'data' => []],
    'week4' => ['label' => 'WEEK 4', 'data' => []],
    'week5' => ['label' => 'WEEK 5', 'data' => []]
];
$playoff = []; 


$sql = "SELECT m.*, 
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
            $d = $row['match_date'];
            $key = 'week5'; 
            if ($selected_stage == 1) {
                if ($d <= '2025-03-28') $key = 'week1';
                elseif ($d <= '2025-04-04') $key = 'week2';
                elseif ($d <= '2025-04-11') $key = 'week3';
                elseif ($d <= '2025-04-18') $key = 'week4';
            } else {
                if ($d <= '2025-07-20') $key = 'week1';
                elseif ($d <= '2025-07-31') $key = 'week2';
                elseif ($d <= '2025-08-07') $key = 'week3';
                elseif ($d <= '2025-08-14') $key = 'week4';
            }
            $matches_by_week[$key]['data'][] = $row;
        }
    }
}


$sql_teams = "SELECT * FROM team ORDER BY team_name ASC";
$res_teams = $koneksi->query($sql_teams);
$team_list = [];
$standings = ['Group A' => [], 'Group B' => []];

while($t = $res_teams->fetch_assoc()){
    $team_list[] = $t['team_name'];
    $g = ($t['group_name'] == 'Group B') ? 'Group B' : 'Group A';
    $standings[$g][$t['team_id']] = [
        'name' => $t['team_name'], 'logo' => $t['logo'],
        'win' => 0, 'lose' => 0, 'rw' => 0, 'rl' => 0
    ];
}

$sql_calc = "SELECT * FROM match_esports WHERE stage = 'Group Stage' AND event_id = $selected_stage";
$res_calc = $koneksi->query($sql_calc);
if($res_calc){
    while($m = $res_calc->fetch_assoc()){
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
<title>VCT Match Center</title>
<style>
  /* === GLOBAL VARS === */
  :root { --conn-color: #555; --conn-width: 2px; }

  * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
  body { margin: 0; background-color: #0f1923; color: #ece8e1; padding-bottom: 80px; overflow-x: hidden; } 

  /* HEADER */
  header { background: #000; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ff4655; }
  header nav { display: flex; align-items: center; }
  header nav a { color: white; margin-left: 20px; text-decoration: none; font-weight: bold; font-size: 14px; text-transform: uppercase; transition: 0.3s; }
  header nav a:hover { color: #ff4655; }

  /* DROPDOWN */
  .dropdown { position: relative; display: inline-block; margin-left: 20px; }
  .dropbtn { color: white; font-weight: bold; font-size: 14px; text-transform: uppercase; text-decoration: none; cursor: pointer; padding: 10px 0; }
  .dropbtn:hover { color: #ff4655; }
  .dropdown-content { display: none; position: absolute; background-color: #1b2733; min-width: 140px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.5); z-index: 100; border: 1px solid #ff4655; border-radius: 4px; top: 100%; left: 0; }
  .dropdown-content a { color: white; padding: 12px 16px; text-decoration: none; display: block; margin: 0; font-size: 13px; text-align: left; border-bottom: 1px solid #333; }
  .dropdown-content a:last-child { border-bottom: none; }
  .dropdown-content a:hover { background-color: #ff4655; color: white; }
  .dropdown:hover .dropdown-content { display: block; }

  .container { max-width: 1400px; margin: 30px auto; padding: 20px; }
  .page-title { text-align: center; font-size: 42px; font-weight: 900; margin-bottom: 20px; letter-spacing: 2px; text-transform: uppercase; color: #fff; text-shadow: 0 0 20px rgba(255, 70, 85, 0.5); }

  /* TABS */
  .tabs { display: flex; justify-content: center; gap: 40px; margin: 20px 0 40px; border-bottom: 1px solid #333; position: relative; }
  .tab-btn { background: none; border: none; padding: 15px 10px; font-size: 18px; font-weight: bold; color: #888; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; }
  .tab-btn.active { color: #ff4655; }
  .tab-btn.active::after { content: ''; position: absolute; bottom: -1px; left: 0; width: 100%; height: 3px; background: #ff4655; }
  .tab-content { display: none; }
  .tab-content.active { display: block; animation: fadeIn 0.5s; }
  @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

  /* GROUP STAGE STYLES */
  .timeline-wrapper { position: relative; margin: 40px auto 50px; max-width: 900px; text-align: center; }
  .timeline-line { position: absolute; top: 35px; left: 0; right: 0; height: 2px; background: #333; z-index: 1; }
  .week-nav { display: flex; justify-content: space-between; position: relative; z-index: 2; }
  .week-btn { background: none; border: none; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 10px; color: #666; transition: 0.3s; }
  .week-btn span { font-size: 14px; font-weight: 600; text-transform: uppercase; }
  .week-dot { width: 12px; height: 12px; background: #333; border-radius: 50%; border: 2px solid #0f1923; box-shadow: 0 0 0 2px #333; transition: 0.3s; }
  .week-btn.active span { color: #ff4655; font-weight: 800; }
  .week-btn.active .week-dot { background: #ff4655; box-shadow: 0 0 0 3px #ff4655; transform: scale(1.2); }
  .week-btn:hover .week-dot { background: #888; }

  .grid-matches { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; margin-top: 20px; }
  .week-matches { display: none; } 
  .week-matches.active { display: grid; animation: fadeIn 0.4s; }

  .match-card { background: #1b2733; border-radius: 6px; overflow: hidden; transition: 0.2s; border: 1px solid #333; }
  .match-card:hover { transform: translateY(-5px); border-color: #ff4655; }
  .card-header { background: #263542; padding: 10px 15px; border-bottom: 1px solid #333; display: flex; justify-content: space-between; font-size: 12px; font-weight: bold; color: #aaa; }
  .team-row { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #263542; }
  .team-row:last-child { border-bottom: none; }
  .t-info { display: flex; align-items: center; gap: 12px; font-weight: 700; font-size: 15px; color: #fff; }
  .t-logo { width: 32px; height: 32px; object-fit: contain; }
  .t-score { font-size: 18px; font-weight: bold; color: #666; }
  .t-score.win { color: #10b981; }

  .standings-section { margin-top: 80px; }
  .standings-title { text-align:center; font-size:28px; font-weight:900; margin-bottom:30px; letter-spacing:2px; color: #fff; }
  .standings-wrapper { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
  .standings-box { background: #1b2733; border-radius: 8px; padding: 20px; border: 1px solid #333; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
  .st-title { color: #fff; font-size: 18px; font-weight: 800; margin-bottom: 15px; text-transform: uppercase; display: flex; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 10px; }
  .st-group { color: #ff4655; } .st-group.b { color: #10b981; }
  .velo-table { width: 100%; border-collapse: collapse; font-size: 13px; }
  .velo-table th { text-align: left; padding: 10px; background: #263542; color: #aaa; text-transform: uppercase; font-size: 11px; }
  .velo-table td { padding: 12px 10px; border-bottom: 1px solid #2c3b4e; font-weight: 600; color: #fff; }
  .rank-1 { color: #ffd700 !important; } .rank-2 { color: #c0c0c0 !important; } .rank-3 { color: #cd7f32 !important; }
  .diff-plus { color: #10b981; } .diff-min { color: #ff4655; }

  /* === PLAYOFF BRACKET FIX === */
  .playoff-container { display: flex; gap: 80px; overflow-x: auto; padding: 40px 20px; min-height: 600px; align-items: stretch; }
  .bracket-tree { display: flex; flex-direction: column; justify-content: center; gap: 80px; } /* Pemisah Upper & Lower */
  .bracket-row { display: flex; gap: 80px; align-items: center; } /* Baris Upper/Lower */

  .round-column { 
      display: flex; flex-direction: column; 
      gap: 40px; /* Jarak antar match dalam satu kolom */
      width: 240px; flex-shrink: 0; position: relative; justify-content: center;
  }
  
  .round-header { text-align: center; font-size: 11px; font-weight: 800; color: #888; margin-bottom: 15px; letter-spacing: 1px; background: #263542; padding: 5px; border-radius: 4px; }
  
  .match-box { background: #1b2733; border: 1px solid #444; border-radius: 4px; padding: 0; position: relative; height: 88px; display:flex; flex-direction:column; justify-content:center; z-index: 2; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
  .match-box .team-row { padding: 0 12px; height: 50%; border-bottom: 1px solid #2c3b4e; display:flex; justify-content:space-between; align-items:center; }
  .match-box .team-row:last-child { border-bottom: none; }
  .match-box .t-info { width: 100%; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
  .match-box .t-logo { width: 24px; height: 24px; flex-shrink: 0; margin-right: 10px; }

  .pair-wrapper { display: flex; flex-direction: column; gap: 20px; position: relative; justify-content: center; }

  .connector-right::after {
      content: ''; position: absolute; right: -40px; top: 50%; width: 40px; height: 2px; background: #555; z-index: 0;
  }

  .connector-left::before {
      content: ''; position: absolute; left: -40px; top: 50%; width: 40px; height: 2px; background: #555; z-index: 0;
  }
  
  .pair-wrapper::after {
      content: ''; position: absolute; right: -40px; top: 24%; bottom: 24%; width: 2px; background: #555;
  }
  
  .pair-wrapper::before {
      content: ''; position: absolute; right: -80px; top: 50%; width: 40px; height: 2px; background: #555;
  }

  .grand-final-column { width: 300px; flex-shrink: 0; display: flex; flex-direction: column; justify-content: center; position: relative; margin-left: 40px; }
  .gf-card { border: 2px solid #ffd700; box-shadow: 0 0 30px rgba(255, 215, 0, 0.15); height: 110px; }
  .gf-header { color: #ffd700 !important; background: rgba(255, 215, 0, 0.1) !important; text-align:center; display:block !important; font-size:11px; padding: 5px 0;}
  
  .gf-pole { position: absolute; left: -60px; top: 50%; transform: translateY(-50%); height: 500px; width: 2px; background: #555; }
  .gf-connector { position: absolute; left: -60px; top: 50%; width: 60px; height: 2px; background: #ffd700; }

</style>
</head>
<body>

<header>
  <div class="logos"><img src="image/logoValo.png" width="80"><img src="image/logoVCT.png" width="80"></div>
  <nav>
    <a href="home.php" class="nav-link">Home</a>
    <a href="tim.php" class="nav-link">Tim</a>
    <div class="dropdown">
        <a href="#" class="dropbtn">Jadwal ▾</a>
        <div class="dropdown-content">
            <a href="match.php?stage=1">STAGE 1</a>
            <a href="match.php?stage=2">STAGE 2</a>
        </div>
    </div>
    <a href="berita.php" class="nav-link">Berita</a>
    <a href="logout.php" class="nav-link">Logout</a>
  </nav>
</header>

<div class="container">
    <h1 class="page-title">VCT PACIFIC <?php echo $stage_name; ?></h1>
    <div class="tabs">
        <button class="tab-btn active" onclick="openTab('group')">Regular Season</button>
        <button class="tab-btn" onclick="openTab('playoff')">Playoffs</button>
    </div>

    <div id="group" class="tab-content active">
        <div class="timeline-wrapper">
            <div class="timeline-line"></div>
            <div class="week-nav">
                <?php $i = 0; foreach($matches_by_week as $key => $val): $activeClass = ($i == 0) ? 'active' : ''; ?>
                    <button class="week-btn <?php echo $activeClass; ?>" onclick="openWeek('<?php echo $key; ?>', this)">
                        <span><?php echo $val['label']; ?></span><div class="week-dot"></div>
                    </button>
                <?php $i++; endforeach; ?>
            </div>
        </div>
        <?php $j = 0; foreach($matches_by_week as $key => $val): $activeClass = ($j == 0) ? 'active' : ''; $matches = $val['data']; ?>
            <div id="<?php echo $key; ?>" class="grid-matches week-matches <?php echo $activeClass; ?>">
                <?php if(empty($matches)): ?>
                    <div style="grid-column: 1/-1; text-align:center; padding:30px; color:#666;">Tidak ada pertandingan.</div>
                <?php else: ?>
                    <?php foreach($matches as $m): ?>
                        <div class="match-card">
                            <div class="card-header"><span><?php echo date('l, d F', strtotime($m['match_date'])); ?></span><span style="color:#10b981;">FINISHED</span></div>
                            <div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']?:'image/default.png'; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score <?php echo $m['team1_score']>$m['team2_score']?'win':''; ?>"><?php echo $m['team1_score']; ?></span></div>
                            <div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']?:'image/default.png'; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score <?php echo $m['team2_score']>$m['team1_score']?'win':''; ?>"><?php echo $m['team2_score']; ?></span></div>
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
        <div class="playoff-container">
            
            <div class="bracket-tree">
                <h3 style="color:#ff4655; margin:0;">UPPER BRACKET</h3>
                <div class="bracket-row">
                    <div class="round-column">
                        <div class="round-header">UB ROUND 1</div>
                        <div class="pair-wrapper">
                            <?php $m = getMatch($playoff, 0); ?>
                            <div class="match-box connector-right"><div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score win"><?php echo $m['team1_score']; ?></span></div><div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div></div>
                            <?php $m = getMatch($playoff, 1); ?>
                            <div class="match-box connector-right"><div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score win"><?php echo $m['team1_score']; ?></span></div><div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div></div>
                        </div>
                    </div>
                    <div class="round-column">
                        <div class="round-header">UB SEMIS</div>
                         <div class="pair-wrapper" style="gap: 80px;">
                            <?php $m = getMatch($playoff, 2); ?>
                            <div class="match-box connector-left connector-right"><div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score"><?php echo $m['team1_score']; ?></span></div><div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div></div>
                            <?php $m = getMatch($playoff, 3); ?>
                            <div class="match-box connector-left connector-right"><div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score"><?php echo $m['team1_score']; ?></span></div><div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div></div>
                        </div>
                    </div>
                    <div class="round-column" style="justify-content: center;">
                        <div class="round-header">UB FINAL</div>
                        <?php $m = getMatch($playoff, 8); ?>
                        <div class="match-box connector-left connector-right"><div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score"><?php echo $m['team1_score']; ?></span></div><div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div></div>
                    </div>
                </div>

                <h3 style="color:#ff4655; margin:30px 0 10px;">LOWER BRACKET</h3>
                <div class="bracket-row">
                    <div class="round-column">
                        <div class="round-header">LB ROUND 1</div>
                         <div class="pair-wrapper">
                            <?php $m = getMatch($playoff, 4); ?>
                            <div class="match-box connector-right"><div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score"><?php echo $m['team1_score']; ?></span></div><div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div></div>
                            <?php $m = getMatch($playoff, 5); ?>
                            <div class="match-box connector-right"><div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score"><?php echo $m['team1_score']; ?></span></div><div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div></div>
                        </div>
                    </div>
                    <div class="round-column">
                        <div class="round-header">LB ROUND 2</div>
                        <div class="pair-wrapper">
                            <?php $m = getMatch($playoff, 6); ?>
                            <div class="match-box connector-left connector-right"><div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score"><?php echo $m['team1_score']; ?></span></div><div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div></div>
                            <?php $m = getMatch($playoff, 7); ?>
                            <div class="match-box connector-left connector-right"><div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score"><?php echo $m['team1_score']; ?></span></div><div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div></div>
                        </div>
                    </div>
                    <div class="round-column" style="justify-content: center;">
                        <div class="round-header">LB SEMIS</div>
                        <?php $m = getMatch($playoff, 9); ?>
                        <div class="match-box connector-left connector-right"><div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score"><?php echo $m['team1_score']; ?></span></div><div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div></div>
                    </div>
                     <div class="round-column" style="justify-content: center;">
                        <div class="round-header">LB FINAL</div>
                        <?php $m = getMatch($playoff, 10); ?>
                        <div class="match-box connector-left connector-right"><div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score"><?php echo $m['team1_score']; ?></span></div><div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div></div>
                    </div>
                </div>
            </div>

            <div class="grand-final-column">
                <div class="gf-pole"></div> <div class="gf-connector"></div>
                <div class="round-header" style="color:#ffd700;">GRAND FINAL</div>
                <?php $m = getMatch($playoff, 11); ?>
                <div class="match-box gf-card">
                    <div class="card-header gf-header">BO5 MATCH</div>
                    <div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score win" style="color:#d4af37;"><?php echo $m['team1_score']; ?></span></div>
                    <div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function openTab(tabName) {
        var contents = document.getElementsByClassName("tab-content");
        for(var i=0; i<contents.length; i++) contents[i].classList.remove("active");
        var tabs = document.getElementsByClassName("tab-btn");
        for(var i=0; i<tabs.length; i++) tabs[i].classList.remove("active");
        document.getElementById(tabName).classList.add("active");
        event.currentTarget.classList.add("active");
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