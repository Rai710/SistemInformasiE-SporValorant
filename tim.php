<?php 
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>VCT - Tim</title>

<style>
  * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
  
  body { 
    margin: 0; 
    padding: 0; 
    /* BACKGROUND KEREN (Sama kayak match.php) */
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

  /* ======================= HEADER ======================= */
  header {
    background: #000;
    color: #fff;
    padding: 15px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #ff4655;
  }

  header nav a {
    color: white;
    margin-left: 20px;
    text-decoration: none;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 14px;
    transition: 0.3s;
  }

  header nav a:hover {
    color: #ff4655;
  }

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
  }

  .teams-container {
    display: grid;
    /* Responsif: Minimal lebar 200px, sisanya auto-fit */
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 40px;
    max-width: 1200px;
    margin: auto;
    justify-content: center;
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
  }

  /* Body Biru Gelap + Lancip Bawah */
  .team-body {
    background-color: #1b2733; /* Warna biru gelap VCT */
    height: 240px;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    
    /* BENTUK LANCIP (Clip Path) */
    clip-path: polygon(0 0, 100% 0, 100% 85%, 50% 100%, 0 85%);
    
    /* Border halus di atas */
    border-top: 1px solid rgba(255,255,255,0.1);
  }

  .team-logo {
    width: 60%;
    height: 60%;
    object-fit: contain;
    /* Bayangan logo biar gak gepeng */
    filter: drop-shadow(0 5px 5px rgba(0,0,0,0.5)); 
    transition: transform 0.3s;
  }

  .team-card:hover .team-logo {
    transform: scale(1.1);
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
    <a href="tim.php" style="color:#ff4655;">Tim</a> <a href="match.php">Jadwal</a>
    <a href="#">Tiket</a>
    <a href="#">Statistik</a>
  </nav>
</header>


<section class="teams-section">

  <h1 class="teams-title">TIM PACIFIC</h1>
  
  <div class="teams-container">
    <?php
    // 1. Ambil data tim dari database (Urut abjad)
    $sql = "SELECT * FROM team ORDER BY team_name ASC";
    $result = $koneksi->query($sql);

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            // Link detail tim (nanti kita buat detail_tim.php?id=1)
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