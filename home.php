
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
?>

<!DOCTYPE html>
<html lang="id">
    <head>
<meta charset="UTF-8" />

<title>VCT Pacific - Home</title>
<?php include 'config/head.php'; ?>
<style>
    
 
    /* SLIDER */
  .hero-section { position: relative; width: 100%; height: 600px; overflow: hidden; border-bottom: 1px solid #333; }
  .slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 1s ease-in-out; }
  .slide.active { opacity: 1; }
  .slide img { width: 100%; height: 100%; object-fit: cover; object-position: center 20%; }
  .slide-overlay { position: absolute; bottom: 0; left: 0; width: 100%; height: 60%; background: linear-gradient(to top, #0f1923 20%, transparent 100%); display: flex; flex-direction: column; justify-content: flex-end; padding: 50px 80px; }
  .slide-tag { background: #ff4655; color: white; padding: 5px 15px; font-weight: bold; text-transform: uppercase; width: fit-content; margin-bottom: 10px; font-size: 12px; letter-spacing: 1px; }
  .slide-title { font-size: 56px; font-weight: 900; text-transform: uppercase; color: #fff; text-shadow: 0 5px 20px rgba(0,0,0,0.8); margin-bottom: 10px; line-height: 1; }
  .slide-desc { font-size: 18px; color: #ccc; max-width: 700px; margin-bottom: 25px; }
  .cta-btn { display: inline-block; padding: 12px 30px; background: #ff4655; color: white; text-decoration: none; font-weight: bold; text-transform: uppercase; border-radius: 4px; transition: 0.3s; border: 1px solid #ff4655; width: fit-content; }
  .cta-btn:hover { background: transparent; color: #ff4655; }
  .arrow { position: absolute; top: 50%; transform: translateY(-50%); font-size: 40px; color: white; cursor: pointer; padding: 20px; z-index: 10; opacity: 0.5; transition: 0.3s; }
  .arrow:hover { opacity: 1; transform: translateY(-50%) scale(1.2); }
  .arrow.left { left: 20px; } .arrow.right { right: 20px; }

  /* === TICKER === */
  .ticker-wrap { width: 100%; background: #ff4655; overflow: hidden; height: 40px; display: flex; align-items: center; }
  .ticker { display: inline-block; white-space: nowrap; animation: marquee 20s linear infinite; padding-left: 100%; }
  .ticker-item { display: inline-block; padding: 0 30px; font-size: 14px; font-weight: bold; color: white; text-transform: uppercase; }
  @keyframes marquee { 0% { transform: translate(0, 0); } 100% { transform: translate(-100%, 0); } }

  /*  CONTAINER & TITLES  */
  .content-container { max-width: 1300px; margin: 80px auto; padding: 0 20px; }
  .section-title { font-size: 36px; font-weight: 900; text-transform: uppercase; margin-bottom: 40px; text-align: center; color: #fff; letter-spacing: 2px; position: relative; }
  .section-title span { color: #ff4655; }
  .section-title::after { content: ''; display: block; width: 60px; height: 4px; background: #ff4655; margin: 15px auto 0; }

  /*  NEW: PACIFIC FACTS */
  .facts-grid { 
      display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-bottom: 100px; 
  }
  .fact-card {
      background: rgba(255, 255, 255, 0.03);
      padding: 30px;
      border-left: 4px solid #ff4655; 
      border-radius: 0 8px 8px 0;
      transition: 0.3s;
  }
  .fact-card:hover { background: rgba(255, 255, 255, 0.08); transform: translateX(5px); }
  .fact-icon { font-size: 32px; margin-bottom: 15px; }
  .fact-title { font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; }
  .fact-desc { font-size: 14px; color: #aaa; line-height: 1.6; }


  /*  1. CHAMPIONS QUALIFIED  */
  .qualified-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-bottom: 100px; }
  .q-card {
      background: linear-gradient(145deg, #1b2733 0%, #141e26 100%);
      padding: 40px 20px; border-radius: 8px; border: 1px solid #333; border-top: 4px solid #ffd700;
      display: flex; flex-direction: column; align-items: center; text-align: center;
      box-shadow: 0 10px 30px rgba(0,0,0,0.3); transition: 0.3s; cursor: default;
  }
  .q-card:hover { transform: translateY(-10px); box-shadow: 0 20px 50px rgba(255, 215, 0, 0.15); border-color: #ffd700; }
  .q-logo { width: 80px; height: 80px; object-fit: contain; margin-bottom: 20px; filter: drop-shadow(0 0 10px rgba(255,255,255,0.1)); transition: 0.3s; }
  .q-card:hover .q-logo { transform: scale(1.1); filter: drop-shadow(0 0 15px rgba(255, 215, 0, 0.4)); }
  .q-name { font-size: 22px; font-weight: 800; text-transform: uppercase; color: #fff; margin-bottom: 10px; }
  .q-badge { background: rgba(255, 215, 0, 0.1); color: #ffd700; font-size: 11px; font-weight: bold; padding: 6px 15px; border-radius: 20px; text-transform: uppercase; border: 1px solid #ffd700; }

  /*  2. MVP SECTION  */
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
  
  /*  3. META WATCH */
  .meta-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-bottom: 100px; }
  .meta-card { 
      position: relative; height: 400px; background: #1b2733; border-radius: 8px; overflow: hidden; 
      border: 1px solid #333; transition: 0.3s;
  }
  .meta-card:hover { border-color: #ff4655; transform: scale(1.02); }
  .meta-img { width: 100%; height: 100%; object-fit: cover; object-position: center top; transition: 0.3s; }
  .meta-overlay {
      position: absolute; bottom: 0; left: 0; width: 100%; padding: 20px;
      background: linear-gradient(to top, #0f1923 10%, transparent 100%);
  }
  .meta-name { font-size: 32px; font-weight: 900; text-transform: uppercase; color: #fff; line-height: 1; }
  .meta-role { color: #ff4655; font-weight: bold; text-transform: uppercase; font-size: 12px; letter-spacing: 2px; }
  .meta-pick { position: absolute; top: 20px; right: 20px; background: #fff; color: #000; font-weight: 800; padding: 5px 10px; border-radius: 4px; font-size: 12px; }

  /*  5. TOURNAMENT STATS */
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
            <span class="slide-tag">STAGE 1 CHAMPION</span>
            <div class="slide-title">RRQ RAJA PACIFIC</div>
            <div class="slide-desc">Rex Regum Qeon mencetak sejarah dengan menjuarai VCT Pacific Stage 1 2025.</div>
            <a href="match.php?stage=1" class="cta-btn">LIHAT BRACKET</a>
        </div>
    </div>
    <div class="slide">
        <img src="https://valo2asia.com/wp-content/uploads/2025/08/54721215025_694f9dd835_k-1170x780.jpg">
        <div class="slide-overlay">
            <span class="slide-tag">STAGE 2 CHAMPION</span>
            <div class="slide-title">PRX BALAS DENDAM</div>
            <div class="slide-desc">Paper Rex mendominasi Stage 2 dan mengamankan tiket ke Paris.</div>
            <a href="match.php?stage=2" class="cta-btn">LIHAT BRACKET</a>
        </div>
    </div>
    <div class="arrow left" onclick="prevSlide()">&#10094;</div>
    <div class="arrow right" onclick="nextSlide()">&#10095;</div>
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

    <div class="section-title">LEAGUE <span>INSIGHTS</span></div>
    <div class="facts-grid">
        <div class="fact-card">
            <div class="fact-icon">🌏</div>
            <div class="fact-title">THE TERRITORY</div>
            <div class="fact-desc">VCT Pacific menyatukan tim terbaik dari Korea, Jepang, Asia Tenggara, dan Asia Selatan. Pusat kompetisi berada di Seoul, Korea Selatan.</div>
        </div>
        <div class="fact-card">
            <div class="fact-icon">⚔️</div>
            <div class="fact-title">AGGRESSIVE PLAYSTYLE</div>
            <div class="fact-desc">Region ini dikenal dengan gaya main "W-Gaming" yang agresif dan tidak terduga, dipimpin oleh tim seperti Paper Rex dan PRX.</div>
        </div>
        <div class="fact-card">
            <div class="fact-icon">🏆</div>
            <div class="fact-title">ROAD TO CHAMPIONS</div>
            <div class="fact-desc">4 Tim teratas dari akumulasi poin Stage 1 & 2 akan mewakili region Pacific di panggung dunia Valorant Champions.</div>
        </div>
    </div>

    <div class="section-title">QUALIFIED FOR <span>CHAMPIONS</span></div>
    <div class="qualified-grid">
        <?php while($t = $q_champs->fetch_assoc()){ ?>
            <div class="q-card">
                <img src="<?php echo $t['logo']; ?>" class="q-logo">
                <div class="q-name"><?php echo $t['team_name']; ?></div>
                <div class="q-badge">PACIFIC SEED</div>
            </div>
        <?php } ?>
    </div>

    <div class="section-title">SEASON <span>MVP</span></div>
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

    <div class="section-title">META <span>WATCH</span></div>
    <div class="meta-grid">
        <div class="meta-card">
            <div class="meta-pick">85% PICK RATE</div>
            <img src="https://cmsassets.rgpub.io/sanity/images/dsfx7636/news/d41286dc9017bf79c0b4d907b7a260c27b0adb69-616x822.png?auto=format&fit=fill&q=80&w=352" class="meta-img">
            <div class="meta-overlay"><div class="meta-name">JETT</div><div class="meta-role">DUELIST</div></div>
        </div>
        <div class="meta-card">
            <div class="meta-pick">78% PICK RATE</div>
            <img src="https://cmsassets.rgpub.io/sanity/images/dsfx7636/news/015a083717e9687de8a741cfceddb836775b5f9f-616x822.png?auto=format&fit=fill&q=80&w=352" class="meta-img">
            <div class="meta-overlay"><div class="meta-name">OMEN</div><div class="meta-role">CONTROLLER</div></div>
        </div>
        <div class="meta-card">
            <div class="meta-pick">72% PICK RATE</div>
            <img src="https://media.valorant-api.com/agents/707eab51-4836-f488-046a-cda6bf494859/fullportrait.png" class="meta-img">
            <div class="meta-overlay"><div class="meta-name">VIPER</div><div class="meta-role">CONTROLLER</div></div>
        </div>
    </div>

    <div class="stats-banner">
        <div class="stat-item"><h3>12</h3><p>Teams</p></div>
        <div class="stat-divider"></div>
        <div class="stat-item"><h3>85</h3><p>Matches Played</p></div>
        <div class="stat-divider"></div>
        <div class="stat-item"><h3>214</h3><p>Maps Played</p></div>
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
