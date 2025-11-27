<?php

session_start();
// Daftar berita manual tanpa database
$berita = [
    [
        "id" => 1,
        "judul" => "VCT Pacific Memanas! Pertarungan Tim Terkuat Musim Ini",
        "gambar" => "https://cdn.sanity.io/images/dsfx7636/news/fc97feea36d52a821a53c50ca5b38f9c3e4d403e-1920x1080.png",
        "konten" => "Kompetisi VCT Pacific tahun ini menjadi salah satu yang paling sengit...",
    ],
    [
        "id" => 2,
        "judul" => "Roster Baru Diumumkan, Perubahan Mengejutkan!",
        "gambar" => "https://dailyspin.id/wp-content/uploads/2022/11/Roster-VCT-Pacific-League-2023.jpg",
        "konten" => "Beberapa tim top resmi melakukan pergantian roster besar-besaran...",
    ],
    [
        "id" => 3,
        "judul" => "Map Baru Resmi Hadir di Valorant!",
        "gambar" => "https://hybrid.co.id/wp-content/uploads/2022/06/a14763a84d7f5ce0b6ab002468f96a90_valorant-pearl-1.jpg",
        "konten" => "Riot Games akhirnya secara resmi meluncurkan map terbaru...",
    ],
    [
        "id" => 4,
        "judul" => "Sang Raja Telah Kembali!",
        "gambar" => "https://cdn.oneesports.gg/cdn-data/2025/05/Valorant_RRQ_VCTPacificStage1_Trophy-1024x576.jpg",
        "konten" => "Dengan kemenangan ini di VCT stage 1, RRQ tak hanya mengangkat trofi perdana mereka, tetapi...",
    ],
    [
        "id" => 5,
        "judul" => "PRX KEMBALI GANAS!",
        "gambar" => "https://valo2asia.com/wp-content/uploads/2025/08/54721215025_694f9dd835_k-1170x780.jpg",
        "konten" => "RRQ sudah memenangkan Stage 1, sementara PRX, meskipun sudah mengantongi trofi Masters, masih...",
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>VCT - Berita</title>
<link rel="stylesheet" href="assets/css/body.css">
<style>


    .news-section {
    text-align: center;
    padding: 60px 20px;
    }

    .news-container {
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
        gap:30px;
    }

    .news-card {
        background:#1b2733; border-radius:8px; text-decoration:none; color:white;
        overflow:hidden; transition:0.3s; cursor:pointer;
    }
    .news-card:hover {
        transform:translateY(-10px);
        box-shadow:0 10px 20px rgba(255,70,85,0.4);
    }
    .news-img { width:100%; height:180px; object-fit:cover; }
    .news-body { padding:20px; }
    .news-body p { opacity:0.75; }

    .news-title {
    font-size: 60px;
    font-weight: 900;
    text-transform: uppercase;
    margin-bottom: 50px;
    letter-spacing: 2px;
    text-shadow: 0 5px 15px rgba(0,0,0,0.5);
  }
</style>
</head>

<body>



<?php include 'config/navbar.php'; ?>

<section class="news-section">

    <h1 class="news-title">Berita Terbaru</h1>

    <div class="news-container">

        <?php foreach($berita as $b): ?>
            <a class="news-card" href="#">
                <img src="<?php echo $b['gambar']; ?>" class="news-img">

                <div class="news-body">
                    <h3><?php echo $b['judul']; ?></h3>
                    <p><?php echo substr($b['konten'], 0, 90); ?>...</p>
                </div>
            </a>
        <?php endforeach; ?>

    </div>
</section>

<?php include 'config/footer.php'; ?>
</body>
</html>
