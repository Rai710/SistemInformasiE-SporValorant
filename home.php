<?php 
include 'koneksi.php';

// Contoh: Kita ambil data 1 pemain buat jadi "MVP of the Week" (Misal ID 2 = f0rsaken)
// Nanti bisa diganti ID-nya sesuai keinginan
$mvp_id = 2; 
$query_mvp = $koneksi->query("SELECT p.*, t.team_name, t.logo as team_logo FROM players p JOIN team t ON p.team_id = t.team_id WHERE p.player_id = $mvp_id");
$mvp = $query_mvp->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>VCT Pacific - Home</title>
<style>
  /* === GLOBAL STYLE === */
  * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
  
  body { 
    margin: 0; padding: 0; 
    background-color: #0f1923; color: #ece8e1; 
    padding-bottom: 80px; overflow-x: hidden; 
  }

  /* === HEADER === */
  header { background: #000; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ff4655; position: sticky; top: 0; z-index: 100; }
  .logos img { vertical-align: middle; margin-right: 10px; }
  header nav a { color: white; margin-left: 20px; text-decoration: none; font-weight: bold; text-transform: uppercase; font-size: 14px; transition: 0.3s; }
  header nav a:hover { color: #ff4655; }

  /* === HERO SLIDER (Fixed Height) === */
  .hero-section { position: relative; width: 100%; height: 600px; overflow: hidden; }
  
  .slide {
      position: absolute; top: 0; left: 0; width: 100%; height: 100%;
      opacity: 0; transition: opacity 1s ease-in-out;
  }
  .slide.active { opacity: 1; }
  
  .slide img { width: 100%; height: 100%; object-fit: cover; object-position: center 20%; }
  
  /* Overlay Gelap di Slider buat Teks */
  .slide-overlay {
      position: absolute; bottom: 0; left: 0; width: 100%; height: 50%;
      background: linear-gradient(to top, #0f1923 10%, transparent 100%);
      display: flex; flex-direction: column; justify-content: flex-end; padding: 40px 60px;
  }
  
  .slide-title { font-size: 48px; font-weight: 900; text-transform: uppercase; color: #fff; text-shadow: 0 5px 10px rgba(0,0,0,0.5); margin-bottom: 10px; }
  .slide-desc { font-size: 18px; color: #ccc; max-width: 600px; margin-bottom: 20px; }
  .cta-btn { 
      display: inline-block; padding: 12px 30px; background: #ff4655; color: white; 
      text-decoration: none; font-weight: bold; text-transform: uppercase; border-radius: 4px; 
      transition: 0.3s; border: 1px solid #ff4655; width: fit-content;
  }
  .cta-btn:hover { background: transparent; color: #ff4655; }

  /* Tombol Panah */
  .arrow { position: absolute; top: 50%; transform: translateY(-50%); font-size: 40px; color: white; cursor: pointer; padding: 20px; z-index: 10; opacity: 0.5; transition: 0.3s; }
  .arrow:hover { opacity: 1; transform: translateY(-50%) scale(1.2); }
  .arrow.left { left: 20px; } .arrow.right { right: 20px; }


  /* === RUNNING TICKER (JADWAL JALAN) === */
  .ticker-wrap {
      width: 100%; background: #ff4655; overflow: hidden; height: 40px; display: flex; align-items: center;
  }
  .ticker {
      display: inline-block; white-space: nowrap;
      animation: marquee 20s linear infinite;
      padding-left: 100%; /* Start dari luar layar kanan */
  }
  .ticker-item { display: inline-block; padding: 0 30px; font-size: 14px; font-weight: bold; color: white; text-transform: uppercase; }
  
  @keyframes marquee { 0% { transform: translate(0, 0); } 100% { transform: translate(-100%, 0); } }


  /* === CONTENT CONTAINER === */
  .content-container { max-width: 1200px; margin: 60px auto; padding: 0 20px; }
  .section-title { font-size: 36px; font-weight: 900; text-transform: uppercase; margin-bottom: 40px; border-left: 5px solid #ff4655; padding-left: 15px; color: #fff; }


  /* === MVP SECTION (Split Layout) === */
  .mvp-section {
      display: flex; gap: 50px; align-items: center; background: #1b2733; padding: 40px; border-radius: 12px; border: 1px solid #333;
      box-shadow: 0 20px 50px rgba(0,0,0,0.3); margin-bottom: 80px;
  }
  .mvp-text { flex: 1; }
  .mvp-label { color: #ff4655; font-weight: 800; letter-spacing: 2px; margin-bottom: 10px; display: block; }
  .mvp-name { font-size: 50px; font-weight: 900; text-transform: uppercase; line-height: 1; margin-bottom: 15px; }
  .mvp-team { display: flex; align-items: center; gap: 15px; font-size: 20px; font-weight: bold; color: #ccc; margin-bottom: 30px; }
  .mvp-team img { width: 40px; }
  
  /* Kartu Pemain (Reuse Style Lama) */
  .mvp-card-wrapper { width: 240px; height: 340px; flex-shrink: 0; position: relative; }
  .mvp-img { width: 100%; height: 100%; object-fit: cover; border-radius: 10px; border: 2px solid #ff4655; }


  /* === LATEST NEWS GRID === */
  .news-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
  
  .news-card { background: #1b2733; border-radius: 8px; overflow: hidden; transition: 0.3s; cursor: pointer; border: 1px solid #333; }
  .news-card:hover { transform: translateY(-10px); border-color: #ff4655; }
  
  .news-img { width: 100%; height: 180px; object-fit: cover; }
  .news-content { padding: 20px; }
  .news-date { font-size: 12px; color: #888; margin-bottom: 10px; display: block; }
  .news-title { font-size: 18px; font-weight: 800; margin-bottom: 10px; line-height: 1.4; }
  .news-excerpt { font-size: 14px; color: #ccc; line-height: 1.6; }

</style>
</head>

<body>

<header>
  <div class="logos">
    <img src="image/logoValo.png" width="80">
    <img src="image/logoVCT.png" width="80">
  </div>
  <nav>
    <a href="home.php" style="color:#ff4655;">Home</a>
    <a href="tim.php">Tim</a>
    <a href="match.php">Jadwal</a> 
    <a href="#">Tiket</a>
    <a href="#">Statistik</a>
  </nav>
</header>

<div class="hero-section">
    
    <div class="slide active">
        <img src="https://cdn.oneesports.gg/cdn-data/2025/05/Valorant_RRQ_VCTPacificStage1_Trophy-1024x576.jpg">
        <div class="slide-overlay">
            <div class="slide-title">RRQ JUARA PACIFIC?</div>
            <div class="slide-desc">Simak perjalanan tim Rex Regum Qeon mendominasi panggung VCT Pacific Stage 1.</div>
            <a href="match.php" class="cta-btn">LIHAT BRACKET</a>
        </div>
    </div>

    <div class="slide">
        <img src="https://valo2asia.com/wp-content/uploads/2025/08/54721215025_694f9dd835_k-1170x780.jpg">
        <div class="slide-overlay">
            <div class="slide-title">PRX KEMBALI GANAS</div>
            <div class="slide-desc">Paper Rex menunjukkan taringnya dengan roster terbaru di Group A.</div>
            <a href="tim.php" class="cta-btn">LIHAT ROSTER</a>
        </div>
    </div>

    <div class="arrow left" onclick="prevSlide()">&#10094;</div>
    <div class="arrow right" onclick="nextSlide()">&#10095;</div>
</div>

<div class="ticker-wrap">
    <div class="ticker">
        <span class="ticker-item">NEXT MATCH: RRQ vs PRX (Sabtu, 15:00 WIB)</span>
        <span class="ticker-item"> • </span>
        <span class="ticker-item">RESULT: DRX 2 - 0 T1</span>
        <span class="ticker-item"> • </span>
        <span class="ticker-item">LIVE NOW: GEN.G vs TEAM SECRET</span>
        <span class="ticker-item"> • </span>
        <span class="ticker-item">DON'T MISS: GRAND FINAL 10 MEI 2025</span>
    </div>
</div>

<div class="content-container">

    <h2 class="section-title">PLAYER OF THE WEEK</h2>
    <div class="mvp-section">
        <div class="mvp-card-wrapper">
            <img src="<?php echo $mvp['photo'] ?: 'https://valorantesports.com/static/img/player-placeholder.png'; ?>" class="mvp-img">
        </div>
        <div class="mvp-text">
            <span class="mvp-label">// WEEK 4 MVP</span>
            <div class="mvp-name"><?php echo $mvp['in_game_name']; ?></div>
            <div class="mvp-team">
                <img src="<?php echo $mvp['team_logo']; ?>">
                <?php echo $mvp['team_name']; ?> - <?php echo $mvp['role']; ?>
            </div>
            <p style="color:#aaa; line-height:1.6;">
                Penampilan luar biasa dari <?php echo $mvp['in_game_name']; ?> minggu ini berhasil membawa timnya mengamankan slot Upper Bracket. Dengan rata-rata ACS 280 dan Clutch yang krusial, dia layak dinobatkan sebagai MVP.
            </p>
            <br>
            <a href="detail_tim.php?id=<?php echo $mvp['team_id']; ?>" class="cta-btn" style="background:transparent; border:1px solid #fff;">LIHAT PROFIL</a>
        </div>
    </div>

    <h2 class="section-title">LATEST NEWS</h2>
    <div class="news-grid">
        
        <div class="news-card">
            <img src="https://www.blix.gg/wp-content/uploads/2023/03/vct-pacific-teams.jpg" class="news-img">
            <div class="news-content">
                <span class="news-date">24 Maret 2025</span>
                <div class="news-title">Rekap Minggu Pertama: Kejutan dari Tim Underdog</div>
                <div class="news-excerpt">Banyak hasil tak terduga terjadi di minggu pembuka VCT Pacific Stage 1...</div>
            </div>
        </div>

        <div class="news-card">
            <img src="https://esports.id/img/article/6833/20230404061532.jpg" class="news-img">
            <div class="news-content">
                <span class="news-date">22 Maret 2025</span>
                <div class="news-title">Format Baru Playoff: Double Elimination Bracket</div>
                <div class="news-excerpt">Riot Games mengumumkan perubahan format playoff untuk musim ini...</div>
            </div>
        </div>

        <div class="news-card">
            <img src="https://media.valorant-api.com/maps/2fb9a4fd-47b8-4e7d-a969-74b4046ebd54/splash.png" class="news-img">
            <div class="news-content">
                <span class="news-date">20 Maret 2025</span>
                <div class="news-title">Map Pool Update: Split Kembali, Haven Keluar</div>
                <div class="news-excerpt">Rotasi map terbaru akan diterapkan mulai Week 3. Simak detailnya...</div>
            </div>
        </div>

    </div>

</div>

<script>
    let current = 0;
    const slides = document.querySelectorAll('.slide');
    
    function show(index) {
        slides.forEach(s => s.classList.remove('active'));
        // Loop index biar muter terus
        current = (index + slides.length) % slides.length;
        slides[current].classList.add('active');
    }

    function nextSlide() { show(current + 1); }
    function prevSlide() { show(current - 1); }

    // Auto Slide 5 detik
    setInterval(nextSlide, 5000);
</script>

</body>
</html>