
<?php 
include 'koneksi.php';

session_start();

if (!isset($_SESSION['username'])) {

    header("Location: login.php");
    exit();
}

$id_tim = isset($_GET['id']) ? $_GET['id'] : 1;

// Ambil Data Tim
$query_team = $koneksi->prepare("SELECT * FROM team WHERE team_id = ?");
$query_team->bind_param("i", $id_tim);
$query_team->execute();
$team = $query_team->get_result()->fetch_assoc();

if (!$team) { header("Location: tim.php"); exit(); }

// Ambil Data Pemain
$query_players = $koneksi->prepare("SELECT * FROM players WHERE team_id = ?");
$query_players->bind_param("i", $id_tim);
$query_players->execute();
$result_players = $query_players->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="referrer" content="no-referrer" /> 
<title><?= $team['team_name'] ?> – Roster</title>

<style>
  /* BASIC SETUP */
      * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }

  body { 
    margin: 0; 
    padding: 0; 
    background-image: 
        linear-gradient(
            to bottom, 
            rgba(15, 25, 35, 1) 0%,    
            rgba(15, 25, 35, 0.9) 60%, 
            rgba(15, 25, 35, 0.5) 100% 
        ),
        url('image/bg.jpg'); /* Pastikan ada file bg.jpg di folder image */

    background-repeat: no-repeat;
    background-position: center center;
    background-attachment: fixed;
    background-size: cover;
    
    color: #ece8e1; 
    padding-bottom: 80px; 
    overflow-x: hidden; 
  }

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

  /* TEAM INFO */
  .header-team { text-align: center; padding: 60px 20px 40px; animation: fadeIn 1s; }
  .team-logo-large { width: 180px; height: 180px; object-fit: contain; filter: drop-shadow(0 0 20px rgba(255, 70, 85, 0.4)); }
  .team-name { font-size: 60px; font-weight: 900; margin-top: 20px; text-transform: uppercase; letter-spacing: 2px; text-shadow: 0 5px 15px rgba(0,0,0,0.5); }
  .team-description { max-width: 800px; margin: 20px auto 0; font-size: 18px; line-height: 1.6; color: #ccc; }

  /* ROSTER TITLE */
  .roster-title { margin-top: 40px; font-size: 40px; font-weight: 900; text-align: center; text-transform: uppercase; letter-spacing: 1px; color: #ff4655; margin-bottom: 40px;}
  
  /* CONTAINER GRID */
  .roster-container { 
    display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; 
    max-width: 1400px; margin: auto; padding-bottom: 60px;
  }

  /* === DRAWER CARD CSS === */
  .drawer-card {
    position: relative;
    width: 300px;
    height: 300px;
    border-radius: 12px;
    overflow: hidden; 
    box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    background: #1b2733;
    cursor: pointer;
    border: 1px solid #333;
    transition: transform 0.3s;
  }

  .drawer-card:hover {
    transform: translateY(-10px);
    border-color: #ff4655;
    box-shadow: 0 15px 30px rgba(255, 70, 85, 0.2);
  }

  /* Gambar Pemain */
  .player-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: top;
    transition: transform 0.5s ease; /* Zoom effect */
  }

  /* Efek Zoom pas hover */
  .drawer-card:hover .player-photo {
    transform: scale(1.1);
  }

  /* Panel Informasi (Drawer) */
  .drawer-info {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    background: linear-gradient(to top, #0f1923 10%, rgba(15, 25, 35, 0.95) 80%, transparent 100%);
    padding: 20px;
    color: white;
    
    transform: translateY(65px); 
    transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  }

  .drawer-card:hover .drawer-info {
    transform: translateY(0);
    background: linear-gradient(to top, #0f1923 40%, rgba(15, 25, 35, 0.9) 100%);
  }

  /* Teks di dalam Panel */
  .p-ign { font-size: 28px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; line-height: 1; margin-bottom: 5px; }
  .p-role { font-size: 14px; color: #ff4655; font-weight: bold; text-transform: uppercase; margin-bottom: 15px; display: block; }
  
  .p-details {
    opacity: 0; 
    transition: opacity 0.3s 0.1s; 
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 10px;
    font-size: 13px;
    color: #ccc;
  }

  .drawer-card:hover .p-details {
    opacity: 1;
  }

  .detail-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
  .detail-label { color: #888; font-size: 11px; text-transform: uppercase; }
  .detail-val { font-weight: 600; }

  @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
</head>

<body>

<header>
  <div class="logos">
    <img src="image/logoValo.png" width="80">
    <img src="image/logoVCT.png" width="80">
  </div>
<nav>
    <a href="home.php" class="nav-link">Home</a>
    <a href="tim.php" style="color:#ff4655;" class="nav-link">Tim</a>
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

<div class="header-team">
  <img src="<?= $team['logo'] ?>" class="team-logo-large" alt="<?= $team['team_name'] ?>">
  <div class="team-name"><?= $team['team_name'] ?></div>
  <p class="team-description"><?= $team['description'] ?></p>
</div>

<div class="roster-title">ROSTER</div>

<div class="roster-container">
  <?php 
  if ($result_players->num_rows > 0) {
      while($p = $result_players->fetch_assoc()) { 
          $foto = !empty($p['photo']) ? $p['photo'] : 'https://valorantesports.com/static/img/player-placeholder.png';
          $negara = strtoupper($p['nationality']);
          $join = date('M Y', strtotime($p['joined_date']));
  ?>
      <div class="drawer-card">
        <img src="<?= $foto ?>" class="player-photo" onerror="this.onerror=null; this.src='https://valorantesports.com/static/img/player-placeholder.png';">
        
        <div class="drawer-info">
            <div class="p-ign"><?= $p['in_game_name'] ?></div>
            <span class="p-role"><?= $p['role'] ?></span>
            
            <div class="p-details">
                <div class="detail-row">
                    <span class="detail-label">Full Name</span>
                    <span class="detail-val"><?= $p['player_name'] ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Country</span>
                    <span class="detail-val"><?= $negara ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Joined</span>
                    <span class="detail-val"><?= $join ?></span>
                </div>
            </div>
        </div>
      </div>
  <?php 
      } 
  } else {
      echo "<p style='color:#888;'>Belum ada data pemain.</p>";
  }
  ?>
</div>

</body>
</html>
