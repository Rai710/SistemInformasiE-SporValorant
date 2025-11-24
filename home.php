<?php 
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>VCT</title>

<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, sans-serif;
    background: url('/Jawa_praktikum/Praktikum/Projek/image/gambarBG.png');
    background-size: cover;
    height: 100vh;
    }

  /* ======================= HEADER ======================= */
  header {
    background: #000;
    color: #fff;
    padding: 15px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  header nav a {
    color: white;
    margin-left: 20px;
    text-decoration: none;
  }

  header nav a:hover {
    text-decoration: underline;
  }
  
  /* ======================= IMAGE SLIDER ======================= */
  .image-slider-section {
    width: 100%;
    padding: 40px 0;
    background: #ffffff;
    position: relative;
  }

  .image-slider-box {
    width: 100%;
    max-width: 1700px;
    margin: auto;
    position: relative;
  }

  .img-slide {
    display: none;
    width: 100%;
  }

  .img-slide.active {
    display: block;
  }

  .img-slide-content {
    width: 100%;
    display: block;
    border-radius: 10px;
  }

  .img-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: white;
    border: 3px solid #ccc;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    font-size: 26px;
    font-weight: bold;
    z-index: 10;
  }

  .img-arrow.left { left: -25px; }
  .img-arrow.right { right: -25px; }

  /* ======================= RANKING ======================= */
  .ranking-section {
    width: 100%;
    text-align: center;
    padding: 80px 0;
    background: #fafafa;
  }

  .ranking-title {
    font-size: 54px;
    font-weight: 900;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-bottom: 10px;
  }

  .ranking-subtitle {
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 5px;
  }

  .subtitle-line {
    width: 120px;
    height: 4px;
    background: #c10000;
    margin: 10px auto 40px;
    border-radius: 3px;
  }

  .ranking-table-box {
    width: 100%;
    max-width: 1500px;
    margin: auto;
    border-radius: 12px;
    overflow: hidden;
  }

  .ranking-image {
    width: 100%;
    display: block;
  }
</style>
</head>

<body>

<!-- ======================= HEADER ======================= -->
<header>
  <div class="logos">
    <img src="/Jawa_praktikum/Praktikum/Projek/image/logoValo.png" width="100">
    <img src="/Jawa_praktikum/Praktikum/Projek/image/logoVCT.png" width="100">
  </div>

  <nav>
    <a href="home.php">Home</a>
    <a href="tim.php">Tim</a>
    <a href="#">Jadwal</a>
    <a href="#">Tiket</a>
    <a href="#">Statistik</a>
  </nav>
</header>

<!-- ======================= IMAGE SLIDER (2 SLIDE) ======================= -->
<section class="image-slider-section">

  <div class="image-slider-box">

    <div class="img-arrow left" onclick="prevImgSlide()">&#8249;</div>
    <div class="img-arrow right" onclick="nextImgSlide()">&#8250;</div>

    <div class="img-slide active">
      <img src="https://cdn.oneesports.gg/cdn-data/2025/05/Valorant_RRQ_VCTPacificStage1_Trophy-1024x576.jpg"
           class="img-slide-content">
    </div>

    <div class="img-slide">
      <img src="https://valo2asia.com/wp-content/uploads/2025/08/54721215025_694f9dd835_k-1170x780.jpg"
           class="img-slide-content">
    </div>

  </div>
</section>


<!-- ======================= RANKING SECTION ======================= -->
<section class="ranking-section">

  <div class="ranking-title">BRACKET</div>
  <div class="ranking-subtitle">VCT STAGE 1</div>
  <div class="subtitle-line"></div>

  <div class="ranking-table-box">
    <img src="/Jawa_praktikum/Praktikum/Projek/image/bracket1.png" class="ranking-image">
  </div>

</section>

<section class="ranking-section">

  <div class="ranking-subtitle">VCT STAGE 2</div>
  <div class="subtitle-line"></div>

  <div class="ranking-table-box">
    <img src="/Jawa_praktikum/Praktikum/Projek/image/bracket2.png" class="ranking-image">
  </div>

</section>


<!-- ======================= SCRIPTS ======================= -->
<script>
let currentSlide = 0;
const slides = document.querySelectorAll(".slide");

function showSlide(n) {
  slides.forEach(s => s.classList.remove("active"));
  slides[n].classList.add("active");
}

function nextSlide() {
  currentSlide = (currentSlide + 1) % slides.length;
  showSlide(currentSlide);
}

function prevSlide() {
  currentSlide = (currentSlide - 1 + slides.length) % slides.length;
  showSlide(currentSlide);
}

/* IMAGE SLIDER */
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
</script>

</body>
</html>
