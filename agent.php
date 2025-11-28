<?php 
include 'config/koneksi.php';
  
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
<title>Daftar Agent - Valorant</title>
<?php include 'config/head.php'; ?>
<style>

  /* AGENTS SECTION */
  .agents-section {
    text-align: center;
    padding: 60px 20px;
    background-color: #111;
  }

  .agents-title {
    font-size: 60px;
    font-weight: 900;
    text-transform: uppercase;
    margin-bottom: 50px;
    letter-spacing: 2px;
    text-shadow: 0 5px 15px rgba(0,0,0,0.5);
    color: #fff;
  }

  .agents-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr); 
    gap: 30px;               
    max-width: 1200px;        
    margin: auto;
    justify-content: center;
  }

  @media (max-width: 1024px) { 
      .agents-container { grid-template-columns: repeat(3, 1fr); } 
  }
  @media (max-width: 768px) { 
      .agents-container { grid-template-columns: repeat(2, 1fr); } 
  }
  @media (max-width: 480px) { 
      .agents-container { grid-template-columns: repeat(1, 1fr); } 
  }

  /* CARD DESIGN */
  .agent-card {
    position: relative;
    width: 100%; 
    text-decoration: none;
    transition: transform 0.3s ease;
    filter: drop-shadow(0 10px 10px rgba(0,0,0,0.5)); 
    background: transparent;
  }
  
  .agent-card:hover {
    transform: translateY(-10px); 
    filter: drop-shadow(0 15px 20px rgba(255, 70, 85, 0.4)); 
  }

  /* Header Merah */
  .agent-header {
    background-color: #ff4655; 
    color: white;
    padding: 15px 10px;
    display: flex;
    justify-content: space-between; 
    align-items: center;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 14px;
    letter-spacing: 1px;
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
    position: relative;
    z-index: 2; /* Agar header di atas video */
  }

  .agent-role {
    font-size: 10px;
    background: rgba(0,0,0,0.3);
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
  }

  .agent-body {
    background: radial-gradient(circle, #34495e 0%, #1b2733 80%);
    height: 300px; /* Tinggi diperbesar sedikit */
    display: flex;
    justify-content: center;
    align-items: flex-end; 
    position: relative;
    clip-path: polygon(0 0, 100% 0, 100% 85%, 50% 100%, 0 85%);
    border-top: 1px solid rgba(255,255,255,0.1);
    overflow: hidden;
  }

  /* CSS KHUSUS MEDIA (Gambar/Video) */
  .agent-media {
    height: 100%;
    width: 100%;
    object-fit: cover; /* Agar video/gambar memenuhi kotak */
    object-position: top center; /* Fokus ke bagian atas (wajah agent) */
    transition: transform 0.3s;
    /* Hilangkan background agar video transparan (webm) bisa menyatu */
    background: transparent; 
  }
  
  /* Khusus jika ingin video/gambar sedikit zoom saat hover */
  .agent-card:hover .agent-media {
    transform: scale(1.05);
  }

</style>
</head>

<body>

<?php include 'config/navbar.php'; ?>

<section class="agents-section">

  <h1 class="agents-title">AGENTS</h1>
  
  <div class="agents-container">
    <?php
    $sql = "SELECT * FROM agents ORDER BY agent_name ASC";
    $result = $koneksi->query($sql);

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            
            $link = "detail_agent.php?id=" . $row['agent_id'];
            
            // 1. Ambil source dari database
            $source = $row['agent_image'] ? $row['agent_image'] : 'image/default_agent.png';
            
            // 2. Cek Ekstensi File
            $ext = pathinfo($source, PATHINFO_EXTENSION);
            
            // 3. Tentukan apakah ini video (mp4, webm, ogg)
            $is_video = in_array(strtolower($ext), ['mp4', 'webm', 'ogg']);
            ?>
            
            <a href="<?php echo $link; ?>" class="agent-card">
                <div class="agent-header">
                    <span><?php echo htmlspecialchars($row['agent_name']); ?></span>
                    <span class="agent-role"><?php echo htmlspecialchars($row['role']); ?></span>
                </div>
                <div class="agent-body">
                    <?php if ($is_video): ?>
                        <video 
                            src="<?php echo $source; ?>" 
                            class="agent-media" 
                            autoplay 
                            loop 
                            muted 
                            playsinline>
                        </video>
                    <?php else: ?>
                        <img 
                            src="<?php echo $source; ?>" 
                            class="agent-media" 
                            alt="<?php echo $row['agent_name']; ?>">
                    <?php endif; ?>
                </div>
            </a>

            <?php
        }
    } else {
        echo "<p style='color:white; font-size:20px; margin-top:50px; grid-column: 1/-1;'>Belum ada data Agent.</p>";
    }
    ?>
  </div>

</section>

<?php include 'config/footer.php'; ?>
</body>
</html>