<?php 
include 'config/koneksi.php';
  
session_start();

if (!isset($_SESSION['username'])) {

    header("Location: login.php");
    exit();
}

// Tangkap Event ID dari URL
$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 1;

// Ambil Nama Event untuk Judul Halaman
$q_event = $koneksi->query("SELECT event_name FROM events WHERE event_id = $event_id")->fetch_assoc();
$event_name = $q_event['event_name'] ?? 'VCT PACIFIC';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title><?php echo $event_name; ?> - Tim</title>
<?php include 'config/head.php'; ?>
<style>

  /* TIM SECTION */
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
  /* DESIGN KARTU "BANNER" VCT */
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
    
    /* BENTUK LANCIP */
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



<?php include 'config/navbar.php'; ?>

<section class="teams-section">

  <h1 class="teams-title"><?php echo $event_name; ?></h1>
  
  <div class="teams-container">
    <?php
    // Update SQL Query: JOIN ke event_teams dan filter berdasarkan event_id
    $sql = "SELECT t.* FROM team t
            JOIN event_teams et ON t.team_id = et.team_id
            WHERE et.event_id = $event_id
            ORDER BY t.team_name ASC";
    
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
        echo "<p style='color:white; font-size:20px; margin-top:50px;'>Tidak ada tim terdaftar untuk event ini.</p>";
    }
    ?>
  </div>

</section>


<?php include 'config/footer.php'; ?>
</body>

</html>