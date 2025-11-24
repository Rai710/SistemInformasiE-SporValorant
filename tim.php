<?php 
include 'koneksi.php';

session_start();

if (!isset($_SESSION['username'])) {

    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>VCT - Tim</title>

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
        url('image/bg.jpg'); 

    background-repeat: no-repeat;
    background-position: center center;
    background-attachment: fixed;
    background-size: cover;
    
    color: #ece8e1; 
    padding-bottom: 80px; 
    overflow-x: hidden; 
  }

  /* ======================= HEADER ======================= */

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

  /* ======================= TIM SECTION ======================= */
  .teams-section {
    text-align: center;
    padding: 60px 20px;
  }

  .teams-title {
    font-size: 60px;
    font-weight: 900;
    text-transform: uppercase;
    margin-bottom: 50px;
    letter-spacing: 2px;
    text-shadow: 0 5px 15px rgba(0,0,0,0.5);
    color: #fff;
  }

.teams-container {
    display: grid;
    /* KUNCI: repeat(4, 1fr) artinya 4 kolom dengan lebar sama */
    grid-template-columns: repeat(4, 1fr); 
    
    gap: 30px;               
    max-width: 1200px;        
    margin: auto;
    justify-content: center;
  }
  /* RESPONSIF: Kalau layar mengecil, kolomnya berkurang */
  @media (max-width: 1024px) { 
      .teams-container { grid-template-columns: repeat(3, 1fr); } 
  }
  @media (max-width: 768px) { 
      .teams-container { grid-template-columns: repeat(2, 1fr); } 
  }
  @media (max-width: 480px) { 
      .teams-container { grid-template-columns: repeat(1, 1fr); } 
}
  /* === DESIGN KARTU "BANNER" VCT === */
.team-card {
    position: relative;
    width: 100%; 
    text-decoration: none;
    transition: transform 0.3s ease;
    filter: drop-shadow(0 10px 10px rgba(0,0,0,0.5)); 
  }
  .team-card:hover {
    transform: translateY(-10px); 
    filter: drop-shadow(0 15px 20px rgba(255, 70, 85, 0.4)); 
  }

  /* Header Merah */
  .team-header {
    background-color: #ff4655; 
    color: white;
    padding: 15px 5px;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 14px;
    letter-spacing: 1px;
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
    white-space: nowrap; 
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .team-body {
    /* Gradasi Spotlight */
    background: radial-gradient(circle, #34495e 0%, #1b2733 80%);
    height: 240px;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    
    /* BENTUK LANCIP (Clip Path) */
    clip-path: polygon(0 0, 100% 0, 100% 85%, 50% 100%, 0 85%);
    
    border-top: 1px solid rgba(255,255,255,0.1);
  }

  .team-logo {
    width: 60%;
    height: 60%;
    object-fit: contain;
    filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.2)); 
    transition: transform 0.3s;
  }

  .team-card:hover .team-logo {
    transform: scale(1.15);
    filter: drop-shadow(0 0 12px rgba(255, 255, 255, 0.3));
  }

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
    <a href="tim.php" style="color:#ff4655;">Tim</a> 
    <div class="dropdown">
        <a href="#" class="dropbtn">Jadwal ▾</a>
        <div class="dropdown-content">
            <a href="match.php?stage=1">STAGE 1</a>
            <a href="match.php?stage=2">STAGE 2</a>
        </div>
    </div>
    <a href="berita.php">berita</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<section class="teams-section">

  <h1 class="teams-title">TIM PACIFIC</h1>
  
  <div class="teams-container">
    <?php
    // Ambil data tim
    $sql = "SELECT * FROM team ORDER BY team_name ASC";
    $result = $koneksi->query($sql);

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $link = "detail_tim.php?id=" . $row['team_id'];
            ?>
            
            <a href="<?php echo $link; ?>" class="team-card">
                <div class="team-header">
                    <?php echo $row['team_name']; ?>
                </div>
                <div class="team-body">
                    <img src="<?php echo $row['logo'] ? $row['logo'] : 'image/default.png'; ?>" class="team-logo">
                </div>
            </a>

            <?php
        }
    } else {
        echo "<p>Data tim belum tersedia.</p>";
    }
    ?>
  </div>

</section>

</body>
</html>