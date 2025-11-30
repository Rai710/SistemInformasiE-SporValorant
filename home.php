<?php 
include 'config/koneksi.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// --- 1. DATA MVP ---
$mvp1_id = 17; 
$q_mvp1 = $koneksi->query("SELECT p.*, t.team_name, t.logo as team_logo FROM players p JOIN team t ON p.team_id = t.team_id WHERE p.player_id = $mvp1_id");
$mvp1 = $q_mvp1->fetch_assoc();

$mvp2_id = 2;
$q_mvp2 = $koneksi->query("SELECT p.*, t.team_name, t.logo as team_logo FROM players p JOIN team t ON p.team_id = t.team_id WHERE p.player_id = $mvp2_id");
$mvp2 = $q_mvp2->fetch_assoc();

// --- 2. DATA CHAMPIONS (TOP 4) ---
$q_champs = $koneksi->query("SELECT * FROM team WHERE team_id IN (1, 2, 3, 6) ORDER BY team_name ASC");
$q_recent = $koneksi->query("SELECT m.*, 
                             t1.team_name as t1_name, t1.logo as t1_logo, 
                             t2.team_name as t2_name, t2.logo as t2_logo 
                             FROM match_esports m
                             JOIN team t1 ON m.team1_id = t1.team_id
                             JOIN team t2 ON m.team2_id = t2.team_id
                             WHERE (m.team1_score > 0 OR m.team2_score > 0)
                             ORDER BY m.match_date DESC, m.match_id DESC 
                             LIMIT 3");
$q_next = $koneksi->query("SELECT m.*, t1.team_name as t1_name, t1.logo as t1_logo, t2.team_name as t2_name, t2.logo as t2_logo 
                           FROM match_esports m
                           JOIN team t1 ON m.team1_id = t1.team_id
                           JOIN team t2 ON m.team2_id = t2.team_id
                           WHERE (m.team1_score = 0 AND m.team2_score = 0)
                           ORDER BY m.match_date ASC, m.match_id ASC LIMIT 1");
$next_match = $q_next->fetch_assoc();

// --- [FITUR BARU] REAL STATS ---
$total_teams = $koneksi->query("SELECT COUNT(*) as total FROM team")->fetch_assoc()['total'];
$matches_played = $koneksi->query("SELECT COUNT(*) as total FROM match_esports WHERE team1_score > 0 OR team2_score > 0")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
    <head>
<meta charset="UTF-8" />

<title>VCT Pacific - Home</title>
<?php include 'config/head.php'; ?>
<style>
    
 
:root {
        --vct-red: #ff4655;
        --vct-dark: #0f1923;
        --vct-light: #ece8e1;
        --vct-black: #000000;
    }

    .section-spacing { margin: 120px auto; max-width: 1400px; padding: 0 40px; }
    .glitch-text { font-size: 80px; font-weight: 900; line-height: 0.8; text-transform: uppercase; color: transparent; -webkit-text-stroke: 1px rgba(255,255,255,0.3); position: absolute; z-index: -1; opacity: 0.5; top: -40px; left: -20px; pointer-events: none; }
    
    /* === 1. HERO SLIDER (PERTAHANKAN STRUKTUR LAMA, MODERNISASI CSS) === */
    .hero-section { position: relative; width: 100%; height: 750px; overflow: hidden; border-bottom: 4px solid var(--vct-red); }
    .slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 0.8s ease-in-out; transform: scale(1.05); }
    .slide.active { opacity: 1; transform: scale(1); transition: opacity 0.8s ease-in-out, transform 6s linear; }
    .slide img { width: 100%; height: 100%; object-fit: cover; object-position: top; filter: brightness(0.7); }
    
    /* Overlay Baru yg Lebih "Tech" */
    .slide-overlay { 
        position: absolute; bottom: 0; left: 0; width: 100%; height: 100%; 
        background: linear-gradient(90deg, rgba(15,25,35,0.9) 0%, rgba(15,25,35,0.6) 40%, transparent 100%);
        display: flex; flex-direction: column; justify-content: center; padding-left: 100px;
    }
    .slide-tag { 
        background: transparent; color: var(--vct-red); border: 1px solid var(--vct-red); 
        padding: 8px 16px; font-weight: 800; letter-spacing: 3px; width: fit-content; 
        margin-bottom: 20px; font-size: 14px; 
    }
    .slide-title { 
        font-size: 90px; font-weight: 900; text-transform: uppercase; color: #fff; 
        line-height: 0.9; margin-bottom: 20px; letter-spacing: -2px;
    }
    .slide-desc { 
        font-size: 18px; color: #aaa; max-width: 600px; margin-bottom: 40px; 
        border-left: 3px solid var(--vct-red); padding-left: 20px; line-height: 1.5;
    }
    .cta-btn { 
        padding: 20px 50px; background: var(--vct-red); color: white; text-decoration: none; 
        font-weight: 900; text-transform: uppercase; letter-spacing: 2px;
        clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px);
        transition: 0.3s; width: fit-content;
    }
    .cta-btn:hover { background: white; color: var(--vct-black); transform: translateX(10px); }
    
    /* Navigasi Slider */
    .slider-nav { position: absolute; bottom: 50px; right: 100px; display: flex; gap: 10px; z-index: 20; }
    .nav-arrow { 
        width: 60px; height: 60px; border: 1px solid rgba(255,255,255,0.3); 
        display: flex; align-items: center; justify-content: center; color: white; 
        cursor: pointer; transition: 0.3s; background: rgba(0,0,0,0.5);
    }
    .nav-arrow:hover { background: var(--vct-red); border-color: var(--vct-red); }
  /* === TICKER === */
  .ticker-wrap { width: 100%; background: #ff4655; overflow: hidden; height: 40px; display: flex; align-items: center; }
  .ticker { display: inline-block; white-space: nowrap; animation: marquee 20s linear infinite; padding-left: 100%; }
  .ticker-item { display: inline-block; padding: 0 30px; font-size: 14px; font-weight: bold; color: white; text-transform: uppercase; }
  @keyframes marquee { 0% { transform: translate(0, 0); } 100% { transform: translate(-100%, 0); } }

  /* CONTAINER & TITLES  */
    .content-container { max-width: 1300px; margin: 80px auto; padding: 0 20px; }
    .section-head { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; border-bottom: 1px solid #333; padding-bottom: 15px; }
    .sec-title { font-size: 48px; font-weight: 900; text-transform: uppercase; line-height: 1; color: white; }
    .sec-subtitle { color: #888; font-size: 14px; text-transform: uppercase; letter-spacing: 2px; font-weight: bold; }

  /* NEW: PACIFIC FACTS */
    .feat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-bottom: 100px; }
    .feat-card {
        background: #1b2733; padding: 40px 30px; border: 1px solid #333; transition: 0.3s;
        position: relative; overflow: hidden;
    }
    .feat-card:hover { transform: translateY(-10px); border-color: #ff4655; }
    .feat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: #ff4655; opacity: 0; transition: 0.3s; }
    .feat-card:hover::before { opacity: 1; }
    .feat-icon { font-size: 40px; margin-bottom: 20px; color: #ff4655; }
    .feat-h { font-size: 24px; font-weight: 900; color: white; margin-bottom: 10px; text-transform: uppercase; }
    .feat-p { color: #aaa; font-size: 14px; line-height: 1.6; }

    /* === RECENT MATCHES SECTION === */
    .rec-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-bottom: 100px; }

    .rec-card {
        background: #1b2733; border: 1px solid #333; border-radius: 4px;
        padding: 25px; position: relative; transition: 0.3s;
        display: flex; flex-direction: column; justify-content: center;
    }
    .rec-card:hover { transform: translateY(-5px); border-color: #ff4655; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }

    /* Header Kartu (Tanggal & Stage) */
    .rec-meta { 
        display: flex; justify-content: space-between; font-size: 11px; 
        color: #888; text-transform: uppercase; font-weight: bold; 
        margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); 
        padding-bottom: 10px; letter-spacing: 1px;
    }

    /* Baris Tim */
    .rec-row { 
        display: flex; justify-content: space-between; align-items: center; 
        margin-bottom: 15px; 
    }
    .rec-row:last-child { margin-bottom: 0; }

    .rec-team { display: flex; align-items: center; gap: 15px; font-weight: 800; font-size: 18px; color: white; text-transform: uppercase; }
    .rec-logo { width: 30px; height: 30px; object-fit: contain; }

    /* Skor */
    .rec-score { 
        font-size: 24px; font-weight: 900; color: #555; 
        width: 30px; text-align: center;
    }
    .rec-score.win { color: #10b981; text-shadow: 0 0 10px rgba(16, 185, 129, 0.3); }
    .rec-score.lose { opacity: 0.6; }

    /* Responsive */
    @media (max-width: 900px) { .rec-grid { grid-template-columns: 1fr; } }
  /* 1. CHAMPIONS QUALIFIED  */
    .paris-section { position: relative; }
    .paris-header { display: flex; justify-content: space-between; align-items: end; margin-bottom: 50px; border-bottom: 2px solid #333; padding-bottom: 20px; }
    .ph-title { font-size: 42px; font-weight: 900; text-transform: uppercase; color: white; }
    .ph-sub { font-size: 14px; color: var(--vct-red); font-weight: bold; letter-spacing: 2px; }
    
    .team-showcase { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
    .ts-card { 
        height: 350px; background: #1b2733; position: relative; overflow: hidden; transition: 0.4s;
        border: 1px solid #333; display: flex; flex-direction: column; justify-content: flex-end;
    }
    .ts-card:hover { transform: translateY(-10px); border-color: var(--vct-red); }
    .ts-bg { 
        position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.5; 
        background-size: contain; background-position: center; background-repeat: no-repeat;
        transition: 0.4s; filter: grayscale(100%);
    }
    .ts-card:hover .ts-bg { opacity: 0.2; transform: scale(1.2); }
    .ts-content { padding: 30px; position: relative; z-index: 2; background: linear-gradient(to top, #0f1923, transparent); }
    .ts-logo { width: 60px; margin-bottom: 15px; filter: drop-shadow(0 0 10px rgba(255,255,255,0.5)); }
    .ts-name { font-size: 24px; font-weight: 900; text-transform: uppercase; line-height: 1; }
    .ts-seed { font-size: 12px; color: var(--vct-red); font-weight: bold; letter-spacing: 1px; margin-top: 5px; display: block; }
  /* 2. MVP SECTION  */
  .mvp-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 100px; }
  .mvp-card {
      background: #1b2733; border-radius: 12px; overflow: hidden; display: flex;
      border: 1px solid #333; box-shadow: 0 10px 30px rgba(0,0,0,0.3); transition: 0.3s; position: relative;
  }
  .mvp-card:hover { transform: translateY(-10px); border-color: #ff4655; }
  .stage-badge {
      position: absolute; top: 0; left: 0; background: #ff4655; color: white;
      padding: 8px 20px; font-weight: 900; font-size: 14px; border-bottom-right-radius: 12px; z-index: 10;
  }
  .mvp-img-box { width: 40%; position: relative; overflow: hidden; }
  .mvp-img { width: 100%; height: 100%; object-fit: cover; object-position: top; transition: 0.5s; }
  .mvp-card:hover .mvp-img { transform: scale(1.1); }
  .mvp-info { width: 60%; padding: 30px; display: flex; flex-direction: column; justify-content: center; }
  .mvp-role { font-size: 12px; color: #aaa; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
  .mvp-ign { font-size: 42px; font-weight: 900; text-transform: uppercase; line-height: 1; margin-bottom: 10px; color: #fff; }
  .mvp-team-row { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
  .mvp-team-logo { width: 30px; }
  .mvp-team-name { font-size: 16px; font-weight: bold; color: #ccc; }
  .mvp-stat { display: flex; justify-content: space-between; }
  .stat-box { text-align: center; }
  .stat-val { font-size: 24px; font-weight: 800; color: #ff4655; display: block; }
  .stat-lbl { font-size: 10px; color: #888; text-transform: uppercase; }
  
  /* 3. META WATCH */
  .meta-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-bottom: 100px; }
  .meta-card { 
      position: relative; height: 560px; background: #1b2733; border-radius: 8px; overflow: hidden; 
      border: 1px solid #333; transition: 0.3s;
  }
  .meta-card:hover { border-color: #ff4655; transform: scale(1.02); }
  /* Ubah .meta-img agar bisa dipakai oleh tag VIDEO juga */
  .meta-img { width: 100%; height: 100%; object-fit: cover; object-position: center top; transition: 0.3s; display: block; }
  
  .meta-overlay {
      position: absolute; bottom: 0; left: 0; width: 100%; padding: 20px;
      background: linear-gradient(to top, #0f1923 10%, transparent 100%);
  }
  .meta-name { font-size: 32px; font-weight: 900; text-transform: uppercase; color: #fff; line-height: 1; }
  .meta-role { color: #ff4655; font-weight: bold; text-transform: uppercase; font-size: 12px; letter-spacing: 2px; }
  .meta-pick { position: absolute; top: 20px; right: 20px; background: #fff; color: #000; font-weight: 800; padding: 5px 10px; border-radius: 4px; font-size: 12px; z-index: 5; }

  /* 5. TOURNAMENT STATS */
  .stats-banner {
      background: rgba(255,255,255,0.03);
      padding: 40px; border-radius: 12px; border: 1px solid #333;
      display: flex; justify-content: space-around; align-items: center; text-align: center;
  }
  .stat-item h3 { font-size: 48px; color: #fff; margin: 0; font-weight: 900; }
  .stat-item p { color: #ff4655; font-weight: bold; text-transform: uppercase; font-size: 14px; letter-spacing: 1px; margin-top: 5px; }
  .stat-divider { width: 1px; height: 60px; background: #444; }

  @media (max-width: 900px) { 
      .mvp-grid, .meta-grid, .facts-grid { grid-template-columns: 1fr; } 
      .stats-banner { flex-direction: column; gap: 30px; }
      .stat-divider { width: 100px; height: 1px; }
  }


</style>
</head>

<body>



<?php include 'config/navbar.php'; ?>

<div class="hero-section">
    <div class="slide active">
        <img src="https://cdn.oneesports.gg/cdn-data/2025/05/Valorant_RRQ_VCTPacificStage1_Trophy-1024x576.jpg">
        <div class="slide-overlay">
            <div class="slide-tag">STAGE 1 CHAMPION</div>
            <div class="slide-title">THE KINGS<br>OF PACIFIC</div>
            <div class="slide-desc">Rex Regum Qeon mencetak sejarah baru dengan menjuarai VCT Pacific Stage 1, membawa trofi pertama ke tanah air.</div>
            <a href="match.php?stage=1" class="cta-btn">VIEW STAGE 1</a>
        </div>
    </div>
    <div class="slide">
        <img src="https://valo2asia.com/wp-content/uploads/2025/08/54721215025_694f9dd835_k-1170x780.jpg">
        <div class="slide-overlay">
            <div class="slide-tag">STAGE 2 CHAMPION</div>
            <div class="slide-title">PAPER REX<br>REDEMPTION</div>
            <div class="slide-desc">Dominasi total dari W-Gaming. Paper Rex mengamankan slot utama menuju Champions Paris dengan gaya.</div>
            <a href="match.php?stage=2" class="cta-btn">VIEW STAGE 2</a>
        </div>
    </div>

    <div class="slider-nav">
        <div class="nav-arrow" onclick="prevSlide()"><i class="fas fa-chevron-left"></i></div>
        <div class="nav-arrow" onclick="nextSlide()"><i class="fas fa-chevron-right"></i></div>
    </div>
</div>

<div class="ticker-wrap">
    <div class="ticker">
        <span class="ticker-item">CHAMPIONS PARIS 2025 COMING SOON</span><span class="ticker-item"> • </span>
        <span class="ticker-item">STAGE 2 WINNER: PAPER REX</span><span class="ticker-item"> • </span>
        <span class="ticker-item">STAGE 1 WINNER: REX REGUM QEON</span><span class="ticker-item"> • </span>
        <span class="ticker-item">PLAYER OF THE YEAR: F0RSAKEN</span>
    </div>
</div>

<div class="content-container">

    <div class="section-head">
        <div>
            <div class="sec-subtitle">League Insights</div>
            <div class="sec-title">PACIFIC TERRITORY</div>
        </div>
    </div>
    <div class="feat-grid">
        <div class="feat-card">
            <div class="feat-icon"><i class="fas fa-globe-asia"></i></div>
            <div class="feat-h">ONE REGION</div>
            <div class="feat-p">Menyatukan kekuatan terbaik dari Korea, Jepang, Asia Tenggara, hingga Asia Selatan dalam satu liga kompetitif.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon"><i class="fas fa-fire"></i></div>
            <div class="feat-h">AGGRESSIVE STYLE</div>
            <div class="feat-p">Dikenal dengan gaya main "W-Gaming" yang eksplosif dan tidak terduga, dipimpin oleh tim seperti PRX dan RRQ.</div>
        </div>
        <div class="feat-card">
            <div class="feat-icon"><i class="fas fa-ticket-alt"></i></div>
            <div class="feat-h">ROAD TO PARIS</div>
            <div class="feat-p">4 Tim teratas akan mewakili Pacific di panggung dunia Valorant Champions di Paris, Prancis.</div>
        </div>
    </div>
    <div class="section-head" style="margin-top: 50px;">
        <div>
            <div class="sec-subtitle">Fresh From Arena</div>
            <div class="sec-title">LATEST RESULTS</div>
        </div>
        <a href="match.php" style="color:#ff4655; text-decoration:none; font-weight:bold; letter-spacing:1px;">FULL SCHEDULE <i class="fas fa-arrow-right"></i></a>
    </div>

    <div class="rec-grid">
        <?php while($r = $q_recent->fetch_assoc()): 
            $s1 = $r['team1_score'];
            $s2 = $r['team2_score'];
            // Tentukan Stage Label
            $stageLabel = ($r['stage'] == 'Group Stage') ? 'GROUP STAGE' : strtoupper($r['stage']);
        ?>
        <div class="rec-card">
            <div class="rec-meta">
                <span><?php echo date('d M Y', strtotime($r['match_date'])); ?></span>
                <span style="color:#ff4655;"><?php echo $stageLabel; ?></span>
            </div>

            <div class="rec-row">
                <div class="rec-team">
                    <img src="<?php echo $r['t1_logo']; ?>" class="rec-logo">
                    <span style="<?php echo ($s1 > $s2) ? 'color:#fff;' : 'color:#888;'; ?>"><?php echo $r['t1_name']; ?></span>
                </div>
                <span class="rec-score <?php echo ($s1 > $s2) ? 'win' : 'lose'; ?>"><?php echo $s1; ?></span>
            </div>

            <div class="rec-row">
                <div class="rec-team">
                    <img src="<?php echo $r['t2_logo']; ?>" class="rec-logo">
                    <span style="<?php echo ($s2 > $s1) ? 'color:#fff;' : 'color:#888;'; ?>"><?php echo $r['t2_name']; ?></span>
                </div>
                <span class="rec-score <?php echo ($s2 > $s1) ? 'win' : 'lose'; ?>"><?php echo $s2; ?></span>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <div class="section-spacing paris-section">
        <div class="glitch-text" style="top:-60px; right:0;">CHAMPIONS</div>
        
        <div class="paris-header">
            <div>
                <div class="ph-sub">// THE REPRESENTATIVES</div>
                <div class="ph-title">QUALIFIED FOR PARIS</div>
            </div>
            <a href="tim.php" style="color:#ff4655; text-decoration:none; font-weight:bold; letter-spacing:1px;">VIEW ALL TEAMS <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="team-showcase">
            <?php $seed=1; while($t = $q_champs->fetch_assoc()){ ?>
                <div class="ts-card">
                    <div class="ts-bg" style="background-image: url('<?php echo $t['logo']; ?>');"></div>
                    <div class="ts-content">
                        <img src="<?php echo $t['logo']; ?>" class="ts-logo">
                        <div class="ts-name"><?php echo $t['team_name']; ?></div>
                        <span class="ts-seed">PACIFIC SEED #<?php echo $seed++; ?></span>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    
     <div class="section-head" style="margin-top: 50px;">      
          <div class="sec-title">SEASON <span>MVP</span></div>
    </div>
    <div class="mvp-grid">
        <div class="mvp-card">
            <div class="stage-badge">STAGE 1</div>
            <div class="mvp-img-box"><img src="<?php echo $mvp1['photo'] ?: 'image/default.png'; ?>" class="mvp-img"></div>
            <div class="mvp-info">
                <span class="mvp-role"><?php echo $mvp1['role']; ?></span>
                <div class="mvp-ign"><?php echo $mvp1['in_game_name']; ?></div>
                <div class="mvp-team-row"><img src="<?php echo $mvp1['team_logo']; ?>" class="mvp-team-logo"><span class="mvp-team-name"><?php echo $mvp1['team_name']; ?></span></div>
                <div class="mvp-stat"><div class="stat-box"><span class="stat-val">289</span><span class="stat-lbl">ACS</span></div><div class="stat-box"><span class="stat-val">1.4</span><span class="stat-lbl">KD</span></div><div class="stat-box"><span class="stat-val">42%</span><span class="stat-lbl">HS</span></div></div>
            </div>
        </div>
        <div class="mvp-card">
            <div class="stage-badge">STAGE 2</div>
            <div class="mvp-img-box"><img src="<?php echo $mvp2['photo'] ?: 'image/default.png'; ?>" class="mvp-img"></div>
            <div class="mvp-info">
                <span class="mvp-role"><?php echo $mvp2['role']; ?></span>
                <div class="mvp-ign"><?php echo $mvp2['in_game_name']; ?></div>
                <div class="mvp-team-row"><img src="<?php echo $mvp2['team_logo']; ?>" class="mvp-team-logo"><span class="mvp-team-name"><?php echo $mvp2['team_name']; ?></span></div>
                <div class="mvp-stat"><div class="stat-box"><span class="stat-val">275</span><span class="stat-lbl">ACS</span></div><div class="stat-box"><span class="stat-val">1.3</span><span class="stat-lbl">KD</span></div><div class="stat-box"><span class="stat-val">38%</span><span class="stat-lbl">HS</span></div></div>
            </div>
        </div>
    </div>
    <div class="section-head" style="margin-top: 50px;">      
        <div class="sec-title">META <span>WATCH</span></div>
    </div>
    <div class="meta-grid">
        <div class="meta-card">
            <div class="meta-pick">85% PICK RATE</div>
            <video src="assets/video/Jett.mp4" class="meta-img" autoplay loop muted playsinline></video>
            <div class="meta-overlay"><div class="meta-name">JETT</div><div class="meta-role">DUELIST</div></div>
        </div>
        <div class="meta-card">
            <div class="meta-pick">78% PICK RATE</div>
            <video src="assets/video/Omen.mp4" class="meta-img" autoplay loop muted playsinline></video>
            <div class="meta-overlay"><div class="meta-name">OMEN</div><div class="meta-role">CONTROLLER</div></div>
        </div>
        <div class="meta-card">
            <div class="meta-pick">72% PICK RATE</div>
            <video src="assets/video/Viper.mp4" class="meta-img" autoplay loop muted playsinline></video>
            <div class="meta-overlay"><div class="meta-name">VIPER</div><div class="meta-role">CONTROLLER</div></div>
        </div>
    </div>

    <div class="stats-banner">
        <div class="stat-item"><h3><?php echo $total_teams; ?></h3><p>Teams</p></div>
        <div class="stat-divider"></div>
        <div class="stat-item"><h3><?php echo $matches_played; ?></h3><p>Matches Played</p></div>
        <div class="stat-divider"></div>
        <div class="stat-item"><h3>250+</h3><p>Maps Played</p></div>
        <div class="stat-divider"></div>
        <div class="stat-item"><h3>$500K</h3><p>Prize Pool</p></div>
    </div>

</div>

<?php include 'config/footer.php'; ?>

<script>
    let current = 0; const slides = document.querySelectorAll('.slide');
    function nextSlide() { slides[current].classList.remove('active'); current = (current + 1) % slides.length; slides[current].classList.add('active'); }
    function prevSlide() { slides[current].classList.remove('active'); current = (current - 1 + slides.length) % slides.length; slides[current].classList.add('active'); }
    setInterval(nextSlide, 5000);
</script>

</body>
</html> 