<?php 
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>VCT Home</title>

<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  
  body { 
    margin: 0; 
    padding: 0; 
    font-family: 'Segoe UI', Arial, sans-serif; /* Font dirapiin dikit */
    
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
    border-bottom: 2px solid #ff4655; /* Aksen Merah Valorant */
  }

  .logos img {
      vertical-align: middle;
      margin-right: 10px;
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
  
  /* ======================= IMAGE SLIDER ======================= */
  .image-slider-section {
    width: 100%;
    padding: 40px 0;
    background: rgba(255, 255, 255, 0.05); /* Transparan dikit biar nyatu sama bg */
    position: relative;
    backdrop-filter: blur(5px);
  }

  .image-slider-box {
    width: 100%;
    max-width: 1200px; /* Gue kecilin dikit biar enak diliat */
    margin: auto;
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
  }

  .img-slide {
    display: none;
    width: 100%;
  }

  .img-slide.active {
    display: block;
    animation: fade 0.5s;
  }
  
  @keyframes fade {
      from { opacity: 0.8; } to { opacity: 1; }
  }

  .img-slide-content {
    width: 100%;
    display: block;
    /* object-fit: cover; */
  }

  .img-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background: rgba(0,0,0,0.5);
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    font-size: 24px;
    font-weight: bold;
    z-index: 10;
    transition: 0.3s;
  }
  .img-arrow:hover { background: #ff4655; }

  .img-arrow.left { left: 0; }
  .img-arrow.right { right: 0; }

  /* ======================= RANKING / BRACKET GAMBAR ======================= */
  .ranking-section {
    width: 100%;
    text-align: center;
    padding: 60px 0;
    /* background: transparent; */
  }

  .ranking-title {
    font-size: 48px;
    font-weight: 900;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 10px;
    color: #fff;
  }

  .ranking-subtitle {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 5px;
    color: #ff4655;
  }

  .subtitle-line {
    width: 100px;
    height: 4px;
    background: #ff4655;
    margin: 10px auto 40px;
    border-radius: 3px;
  }

  .ranking-table-box {
    width: 90%;
    max-width: 1100px;
    margin: auto;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(255, 70, 85, 0.1);
    border: 1px solid #333;
  }

  .ranking-image {
    width: 100%;
    display: block;
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
    <a href="home.php" style="color:#ff4655;">Home</a>
    <a href="tim.php">Tim</a>
    
    <a href="match.php">Jadwal</a> 
    
    <a href="#">Tiket</a>
    <a href="#">Statistik</a>
  </nav>
</header>

<section class="image-slider-section">
  <div class="image-slider-box">
    <div class="img-arrow left" onclick="prevImgSlide()">&#10094;</div>
    <div class="img-arrow right" onclick="nextImgSlide()">&#10095;</div>

    <div class="img-slide active">
      <img src="https://cdn.oneesports.gg/cdn-data/2025/05/Valorant_RRQ_VCTPacificStage1_Trophy-1024x576.jpg" class="img-slide-content">
    </div>

    <div class="img-slide">
      <img src="https://valo2asia.com/wp-content/uploads/2025/08/54721215025_694f9dd835_k-1170x780.jpg" class="img-slide-content">
    </div>
  </div>
</section>

<section class="ranking-section">
  <div class="ranking-title">HIGHLIGHTS</div>
  
  <div class="ranking-subtitle">VCT STAGE 1 BRACKET</div>
  <div class="subtitle-line"></div>

  <div class="ranking-table-box">
    <img src="image/bracket1.png" class="ranking-image" alt="Bracket Stage 1">
  </div>
</section>

<section class="ranking-section">
  <div class="ranking-subtitle">VCT STAGE 2 BRACKET</div>
  <div class="subtitle-line"></div>

  <div class="ranking-table-box">
    <img src="image/bracket2.png" class="ranking-image" alt="Bracket Stage 2">
  </div>
</section>

<script>
/* IMAGE SLIDER LOGIC */
let imgIndex = 0;
const imgSlides = document.querySelectorAll(".img-slide");

function showImgSlide(i) {
  imgSlides.forEach(s => s.classList.remove("active"));
  imgSlides[i].classList.add("active");
}

function nextImgSlide() {
  imgIndex = (imgIndex + 1) % imgSlides.length;
  showImgSlide(imgIndex);
}

function prevImgSlide() {
  imgIndex = (imgIndex - 1 + imgSlides.length) % imgSlides.length;
  showImgSlide(imgIndex);
}

// Auto slide setiap 5 detik (biar gak sepi)
setInterval(nextImgSlide, 5000);
</script>

</body>
</html>