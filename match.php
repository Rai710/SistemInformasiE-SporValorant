<?php
session_start();
include "koneksi.php";

// 1. Cek Login (Opsional)
// if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
//     header("Location: login.php?pesan=belum_login");
//     exit();
// }

// ==========================================
// BAGIAN 1: AMBIL DATA JADWAL (WEEK)
// ==========================================

// Siapkan wadah array biar gak error di HTML
$matches_by_week = [
    'week1' => ['label' => 'WEEK 1', 'data' => []],
    'week2' => ['label' => 'WEEK 2', 'data' => []],
    'week3' => ['label' => 'WEEK 3', 'data' => []],
    'week4' => ['label' => 'WEEK 4', 'data' => []],
    'week5' => ['label' => 'WEEK 5', 'data' => []]
];
$playoff = []; 

// Query ambil match
$sql = "SELECT m.*, 
               t1.team_name as team1_name, t1.logo as team1_logo,
               t2.team_name as team2_name, t2.logo as team2_logo
        FROM match_esports m
        JOIN team t1 ON m.team1_id = t1.team_id
        JOIN team t2 ON m.team2_id = t2.team_id
        WHERE m.event_id = 1 
        ORDER BY m.match_date ASC, m.match_id ASC";

$result = $koneksi->query($sql);

if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        if($row['stage'] == 'Playoffs' || $row['stage'] == 'Grand Final'){
            $playoff[] = $row;
        } else {
            // LOGIC TANGGAL UTAMA
            $d = $row['match_date'];
            $key = 'week5'; // Default lempar ke week 5
            
            if ($d <= '2025-03-28') { $key = 'week1'; }
            elseif ($d <= '2025-04-04') { $key = 'week2'; }
            elseif ($d <= '2025-04-11') { $key = 'week3'; }
            elseif ($d <= '2025-04-18') { $key = 'week4'; }
            
            $matches_by_week[$key]['data'][] = $row;
        }
    }
}

// ==========================================
// BAGIAN 2: HITUNG KLASEMEN OTOMATIS
// ==========================================

// A. Siapkan Array Group
$standings = [
    'Group A' => [],
    'Group B' => []
];

// B. Ambil semua Tim & Grup-nya dari Database TEAM
$sql_teams = "SELECT * FROM team";
$res_teams = $koneksi->query($sql_teams);

while($t = $res_teams->fetch_assoc()){
    // Pastikan masuk ke Group A atau B (Sesuai SQL kamu tadi)
    $g = $t['group_name']; 
    
    // Jaga-jaga kalau datanya null/salah, default ke A (atau skip)
    if($g != 'Group A' && $g != 'Group B') $g = 'Group A';

    $standings[$g][$t['team_id']] = [
        'name' => $t['team_name'],
        'logo' => $t['logo'],
        'win' => 0,
        'lose' => 0,
        'rw' => 0, // Round Won
        'rl' => 0  // Round Lost
    ];
}

// C. Hitung Poin dari Match yang SUDAH SELESAI (Group Stage)
// Kita query ulang atau filter dari data yang udah diambil di atas juga bisa.
// Biar aman kita query spesifik group stage.
$sql_calc = "SELECT * FROM match_esports WHERE stage = 'Group Stage' AND event_id = 1";
$res_calc = $koneksi->query($sql_calc);

if($res_calc){
    while($m = $res_calc->fetch_assoc()){
        $id1 = $m['team1_id'];
        $id2 = $m['team2_id'];
        $s1 = $m['team1_score'];
        $s2 = $m['team2_score'];

        // Loop check ke kedua grup (karena kita gatau ID 1 itu grup A atau B)
        foreach(['Group A', 'Group B'] as $grp) {
            // Jika Tim 1 ada di grup ini
            if(isset($standings[$grp][$id1])) {
                $standings[$grp][$id1]['rw'] += $s1;
                $standings[$grp][$id1]['rl'] += $s2;
                if($s1 > $s2) $standings[$grp][$id1]['win']++;
                elseif($s2 > $s1) $standings[$grp][$id1]['lose']++;
            }
            // Jika Tim 2 ada di grup ini
            if(isset($standings[$grp][$id2])) {
                $standings[$grp][$id2]['rw'] += $s2;
                $standings[$grp][$id2]['rl'] += $s1;
                if($s2 > $s1) $standings[$grp][$id2]['win']++;
                elseif($s1 > $s2) $standings[$grp][$id2]['lose']++;
            }
        }
    }
}

// D. Sorting (Menang Terbanyak -> Selisih Round Terbaik)
function sortTable($a, $b) {
    if ($a['win'] == $b['win']) {
        $diffA = $a['rw'] - $a['rl'];
        $diffB = $b['rw'] - $b['rl'];
        return $diffB - $diffA; // Descending
    }
    return $b['win'] - $a['win']; // Descending
}
usort($standings['Group A'], 'sortTable');
usort($standings['Group B'], 'sortTable');


// Helper Playoff
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
  /* BASIC SETUP - DARK MODE */
  * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
  
  body { 
    margin: 0; 
    background-color: #0f1923; /* Dark Valorant Blue */
    color: #ece8e1; 
    padding-bottom: 80px; 
    overflow-x: hidden; 
  }

  /* HEADER */
  header {
    background: #000;
    padding: 15px 40px;
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 2px solid #ff4655;
  }
  header nav a { color: white; margin-left: 20px; text-decoration: none; font-weight: bold; font-size: 14px; text-transform: uppercase;}
  header nav a:hover { color: #ff4655; }

  .container { max-width: 1400px; margin: 30px auto; padding: 20px; }

  /* TABS */
  .tabs { display: flex; justify-content: center; gap: 40px; margin: 40px 0; border-bottom: 1px solid #333; position: relative; }
  .tab-btn { background: none; border: none; padding: 15px 10px; font-size: 18px; font-weight: bold; color: #888; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; }
  .tab-btn.active { color: #ff4655; }
  .tab-btn.active::after { content: ''; position: absolute; bottom: -1px; left: 0; width: 100%; height: 3px; background: #ff4655; }
  .tab-content { display: none; }
  .tab-content.active { display: block; animation: fadeIn 0.5s; }
  @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

  /* TIMELINE WEEK */
  .timeline-wrapper { position: relative; margin: 40px auto 50px; max-width: 900px; text-align: center; }
  .timeline-line { position: absolute; top: 35px; left: 0; right: 0; height: 2px; background: #333; z-index: 1; }
  .week-nav { display: flex; justify-content: space-between; position: relative; z-index: 2; }
  .week-btn { background: none; border: none; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 10px; color: #666; transition: 0.3s; }
  .week-btn span { font-size: 14px; font-weight: 600; text-transform: uppercase; }
  .week-dot { width: 12px; height: 12px; background: #333; border-radius: 50%; border: 2px solid #0f1923; box-shadow: 0 0 0 2px #333; transition: 0.3s; }
  .week-btn.active span { color: #ff4655; font-weight: 800; }
  .week-btn.active .week-dot { background: #ff4655; box-shadow: 0 0 0 3px #ff4655; transform: scale(1.2); }
  .week-btn:hover .week-dot { background: #888; }

  /* MATCH CARD */
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

  /* === STYLE KLASEMEN === */
  .standings-section { margin-top: 80px; }
  .standings-title { text-align:center; font-size:28px; font-weight:900; margin-bottom:30px; letter-spacing:2px; color: #fff; }
  
  .standings-wrapper { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
  @media(max-width: 900px) { .standings-wrapper { grid-template-columns: 1fr; } }

  .standings-box { background: #1b2733; border-radius: 8px; padding: 20px; border: 1px solid #333; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
  .st-title { color: #fff; font-size: 18px; font-weight: 800; margin-bottom: 15px; text-transform: uppercase; display: flex; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 10px; }
  .st-group { color: #ff4655; }
  .st-group.b { color: #10b981; }

  .velo-table { width: 100%; border-collapse: collapse; font-size: 13px; }
  .velo-table th { text-align: left; padding: 10px; background: #263542; color: #aaa; text-transform: uppercase; font-size: 11px; }
  .velo-table td { padding: 12px 10px; border-bottom: 1px solid #2c3b4e; font-weight: 600; color: #fff; }
  .rank-1 { color: #ffd700 !important; } 
  .rank-2 { color: #c0c0c0 !important; }
  .rank-3 { color: #cd7f32 !important; }
  .diff-plus { color: #10b981; } .diff-min { color: #ff4655; }

  /* BRACKET */
  .bracket-wrapper { display: flex; flex-direction: column; gap: 50px; overflow-x: auto; padding: 20px 0; }
  .bracket-row { display: flex; gap: 60px; }
  .round-column { display: flex; flex-direction: column; justify-content: space-around; width: 260px; flex-shrink: 0; position: relative; }
  .round-header { text-align: center; font-size: 12px; font-weight: 800; color: #888; margin-bottom: 15px; letter-spacing: 1px; background: #263542; padding: 5px; border-radius: 4px; }
  .match-box { background: #1b2733; border: 1px solid #444; border-radius: 4px; padding: 0; position: relative; height: 86px; display:flex; flex-direction:column; justify-content:center; }
  .line-straight::before { content: ''; position: absolute; left: -62px; top: 50%; width: 62px; height: 2px; background: #555; z-index: -1; }
  .line-fork::before { content: ''; position: absolute; left: -30px; top: 50%; width: 30px; height: 2px; background: #555; z-index: -1; }
  .line-fork::after { content: ''; position: absolute; left: -32px; top: 50%; transform: translateY(-50%); height: 108px; width: 2px; background: #555; z-index: -1; }

</style>
</head>
<body>

<header>
  <div class="logos">
    <img src="image/logoValo.png" width="80">
    <img src="image/logoVCT.png" width="80">
  </div>
  <nav>
    <a href="home.php">Home</a>
    <a href="tim.php">Tim</a>
    <a href="match.php" style="color:#ff4655;">Jadwal</a> <a href="#">Tiket</a>
    <a href="#">Statistik</a>
  </nav>
</header>

<div class="container">
    
    <div class="tabs">
        <button class="tab-btn active" onclick="openTab('group')">Regular Season</button>
        <button class="tab-btn" onclick="openTab('playoff')">Playoffs</button>
    </div>

    <div id="group" class="tab-content active">
        
        <div class="timeline-wrapper">
            <div class="timeline-line"></div>
            <div class="week-nav">
                <?php 
                $i = 0; 
                foreach($matches_by_week as $key => $val): 
                    $activeClass = ($i == 0) ? 'active' : ''; 
                ?>
                    <button class="week-btn <?php echo $activeClass; ?>" onclick="openWeek('<?php echo $key; ?>', this)">
                        <span><?php echo $val['label']; ?></span>
                        <div class="week-dot"></div>
                    </button>
                <?php $i++; endforeach; ?>
            </div>
        </div>

        <?php 
        $j = 0;
        foreach($matches_by_week as $key => $val): 
            $activeClass = ($j == 0) ? 'active' : '';
            $matches = $val['data'];
        ?>
            <div id="<?php echo $key; ?>" class="grid-matches week-matches <?php echo $activeClass; ?>">
                <?php if(empty($matches)): ?>
                    <div style="grid-column: 1/-1; text-align:center; padding:30px; color:#666;">
                        Tidak ada pertandingan di minggu ini.
                    </div>
                <?php else: ?>
                    <?php foreach($matches as $m): ?>
                        <div class="match-card">
                            <div class="card-header">
                                <span><?php echo date('l, d F', strtotime($m['match_date'])); ?></span>
                                <span style="color:#10b981;">FINISHED</span>
                            </div>
                            <div class="team-row">
                                <div class="t-info">
                                    <img src="<?php echo $m['team1_logo'] ?: 'image/default.png'; ?>" class="t-logo">
                                    <?php echo $m['team1_name']; ?>
                                </div>
                                <span class="t-score <?php echo $m['team1_score'] > $m['team2_score'] ? 'win' : ''; ?>">
                                    <?php echo $m['team1_score']; ?>
                                </span>
                            </div>
                            <div class="team-row">
                                <div class="t-info">
                                    <img src="<?php echo $m['team2_logo'] ?: 'image/default.png'; ?>" class="t-logo">
                                    <?php echo $m['team2_name']; ?>
                                </div>
                                <span class="t-score <?php echo $m['team2_score'] > $m['team1_score'] ? 'win' : ''; ?>">
                                    <?php echo $m['team2_score']; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php $j++; endforeach; ?>

        <div class="standings-section">
            <div class="standings-title">STANDINGS</div>
            <div class="standings-wrapper">
                <div class="standings-box">
                    <div class="st-title">GROUP <span class="st-group">A</span></div>
                    <table class="velo-table">
                        <thead><tr><th width="30">#</th> <th>TEAM</th> <th width="60" style="text-align:center">W-L</th> <th width="50" style="text-align:center">DIFF</th></tr></thead>
                        <tbody>
                            <?php $rank=1; foreach($standings['Group A'] as $team): $diff = $team['rw'] - $team['rl']; ?>
                            <tr>
                                <td class="<?php echo ($rank<=3)?'rank-'.$rank:''; ?>"><?php echo $rank; ?></td>
                                <td style="display:flex; align-items:center; gap:10px;"><img src="<?php echo $team['logo']; ?>" style="width:24px;"> <?php echo $team['name']; ?></td>
                                <td style="text-align:center"><?php echo $team['win']."-".$team['lose']; ?></td>
                                <td style="text-align:center" class="<?php echo $diff>=0?'diff-plus':'diff-min'; ?>"><?php echo ($diff>0?'+':'').$diff; ?></td>
                            </tr>
                            <?php $rank++; endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="standings-box">
                    <div class="st-title">GROUP <span class="st-group b">B</span></div>
                    <table class="velo-table">
                        <thead><tr><th width="30">#</th> <th>TEAM</th> <th width="60" style="text-align:center">W-L</th> <th width="50" style="text-align:center">DIFF</th></tr></thead>
                        <tbody>
                            <?php $rank=1; foreach($standings['Group B'] as $team): $diff = $team['rw'] - $team['rl']; ?>
                            <tr>
                                <td class="<?php echo ($rank<=3)?'rank-'.$rank:''; ?>"><?php echo $rank; ?></td>
                                <td style="display:flex; align-items:center; gap:10px;"><img src="<?php echo $team['logo']; ?>" style="width:24px;"> <?php echo $team['name']; ?></td>
                                <td style="text-align:center"><?php echo $team['win']."-".$team['lose']; ?></td>
                                <td style="text-align:center" class="<?php echo $diff>=0?'diff-plus':'diff-min'; ?>"><?php echo ($diff>0?'+':'').$diff; ?></td>
                            </tr>
                            <?php $rank++; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div id="playoff" class="tab-content">
        <div class="bracket-wrapper">
            <h3 style="color:#ff4655; margin:0;">UPPER BRACKET</h3>
            <div class="bracket-row">
                <div class="round-column" style="gap:20px;">
                    <div class="round-header">UB ROUND 1</div>
                    <?php for($i=0; $i<2; $i++): $m = getMatch($playoff, $i); ?>
                    <div class="match-box">
                        <div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score win"><?php echo $m['team1_score']; ?></span></div>
                        <div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div>
                    </div>
                    <?php endfor; ?>
                </div>
                <div class="round-column" style="gap:20px;">
                    <div class="round-header">UB SEMIS</div>
                    <?php for($i=2; $i<4; $i++): $m = getMatch($playoff, $i); ?>
                    <div class="match-box line-straight">
                        <div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score <?php echo $m['team1_score']>$m['team2_score']?'win':''; ?>"><?php echo $m['team1_score']; ?></span></div>
                        <div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score <?php echo $m['team2_score']>$m['team1_score']?'win':''; ?>"><?php echo $m['team2_score']; ?></span></div>
                    </div>
                    <?php endfor; ?>
                </div>
                <div class="round-column" style="justify-content:center;">
                    <div class="round-header">UB FINAL</div>
                    <?php $m = getMatch($playoff, 8); ?>
                    <div class="match-box line-fork">
                        <div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score"><?php echo $m['team1_score']; ?></span></div>
                        <div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div>
                    </div>
                </div>
            </div>

            <h3 style="color:#ff4655; margin:30px 0 0;">LOWER BRACKET</h3>
            <div class="bracket-row">
                <div class="round-column" style="gap:20px;">
                    <div class="round-header">LB ROUND 1</div>
                    <?php for($i=4; $i<6; $i++): $m = getMatch($playoff, $i); ?>
                    <div class="match-box">
                        <div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score"><?php echo $m['team1_score']; ?></span></div>
                        <div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div>
                    </div>
                    <?php endfor; ?>
                </div>
                <div class="round-column" style="gap:20px;">
                    <div class="round-header">LB ROUND 2</div>
                    <?php for($i=6; $i<8; $i++): $m = getMatch($playoff, $i); ?>
                    <div class="match-box line-straight">
                        <div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score"><?php echo $m['team1_score']; ?></span></div>
                        <div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div>
                    </div>
                    <?php endfor; ?>
                </div>
                <div class="round-column" style="justify-content:center;">
                    <div class="round-header">LB SEMIS</div>
                    <?php $m = getMatch($playoff, 9); ?>
                    <div class="match-box line-fork">
                        <div class="team-row"><div class="t-info"><img src="<?php echo $m['team1_logo']; ?>" class="t-logo"><?php echo $m['team1_name']; ?></div><span class="t-score"><?php echo $m['team1_score']; ?></span></div>
                        <div class="team-row"><div class="t-info"><img src="<?php echo $m['team2_logo']; ?>" class="t-logo"><?php echo $m['team2_name']; ?></div><span class="t-score"><?php echo $m['team2_score']; ?></span></div>
                    </div>
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