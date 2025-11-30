<?php 
include 'config/koneksi.php';

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// --- DATA PENDIRI PENGEMBANG ---
$developers = [
    [
        "ign" => "Pak Kambing",
        "name" => "Bisma Putra Pangestu",
        "origin" => "INDONESIA",
        "joined" => "2025-10-01",
        "photo" => "https://avatars.githubusercontent.com/u/188665753?v=4", 
        "socials" => [
            "instagram" => "https://instagram.com/bisma0202",
            "github" => "https://github.com/Ambatubis"
        ]
    ],
    [
        "ign" => "AkGntG",
        "name" => "RAIHAN BUONO PUTRA",
        "origin" => "INDONESIA",
        "joined" => "2025-10-01",
        "photo" => "assets/images/buono.jpg",
        "socials" => [
            "instagram" => "https://www.instagram.com/hannbp?igsh=MXM4Z3cwYndmaTJ6MQ==",
            "github" => "https://github.com/Rai710"
        ]
    ],
    [
        "ign" => "Syiawaw",
        "name" => "MUHAMMAD SYAWAL AZZAMI",
        "origin" => "INDONESIA",
        "joined" => "2025-10-01",
        "photo" => "assets/images/syawal.jpg",
        "socials" => [
            "instagram" => "https://www.instagram.com/sywls.id?igsh=aHRpMWEybGkwMTFl",
            "github" => "https://github.com/Syiawaw"
        ]
    ]
];

$site_name = "VALORANT ESPORTS HUB";
$site_desc = "Kami adalah platform terdepan yang menyajikan informasi statistik, jadwal, dan detail tim Valorant terkini. Dibangun dengan semangat komunitas untuk memfasilitasi penggemar esports di seluruh dunia.";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="referrer" content="no-referrer" /> 
<title>About Us – <?= $site_name ?></title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?php include 'config/head.php'; ?>
<style>
  /* --- BASE STYLES --- */
  .header-about { text-align: center; padding: 60px 20px 40px; animation: fadeIn 1s; }
  .about-logo { width: 120px; height: 120px; object-fit: contain; filter: drop-shadow(0 0 20px rgba(255, 70, 85, 0.6)); margin-bottom: 20px; }
  .site-name { font-size: 50px; font-weight: 900; margin-top: 10px; text-transform: uppercase; letter-spacing: 2px; text-shadow: 0 5px 15px rgba(0,0,0,0.5); color: white; }
  .site-description { max-width: 800px; margin: 20px auto 0; font-size: 18px; line-height: 1.6; color: #ccc; }
  .section-title { margin-top: 50px; font-size: 40px; font-weight: 900; text-align: center; text-transform: uppercase; letter-spacing: 1px; color: #ff4655; margin-bottom: 40px;}
  
  /* GRID CONTAINER */
  .card-container { 
    display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; 
    max-width: 1400px; margin: auto; padding-bottom: 60px;
  }

  /* DRAWER CARD */
  .drawer-card {
    position: relative;
    width: 300px;
    height: 380px;
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

  .profile-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: top center;
    transition: transform 0.5s ease;
  }

  .drawer-card:hover .profile-photo { transform: scale(1.1); }

  /* PANEL INFO */
  .drawer-info {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    background: linear-gradient(to top, #0f1923 10%, rgba(15, 25, 35, 0.98) 90%, transparent 100%);
    padding: 25px 20px 20px;
    color: white;
    transform: translateY(110px);
    transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  }

  .drawer-card:hover .drawer-info {
    transform: translateY(0);
    background: linear-gradient(to top, #0f1923 50%, rgba(15, 25, 35, 0.95) 100%);
  }

  .p-ign { font-size: 26px; font-weight: 900;  letter-spacing: 1px; line-height: 1; margin-bottom: 5px; }
  
  .p-details {
    opacity: 0; 
    transition: opacity 0.3s 0.1s; 
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 15px;
    margin-top: 10px;
    font-size: 13px;
    color: #ccc;
  }

  .drawer-card:hover .p-details { opacity: 1; }

  .detail-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
  .detail-label { color: #888; font-size: 11px; text-transform: uppercase; }
  .detail-val { font-weight: 600; text-align: right; }

  /* --- SOCIAL MEDIA ICONS --- */
  .social-links {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid rgba(255,255,255,0.1);
  }

  .social-item {
    color: #ccc;
    font-size: 18px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
    text-decoration: none;
  }

  .social-item:hover {
    color: white;
    background: #ff4655;
    transform: translateY(-3px);
  }

  @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
</head>

<body>

<?php include 'config/navbar.php'; ?>

<div class="header-about">
  <div class="site-name">ABOUT US</div>
  <p class="site-description"><?= $site_desc ?></p>
</div>

<div class="section-title">MEET THE TEAM</div>

<div class="card-container">
  <?php foreach($developers as $dev): 
      $join_date = date('M Y', strtotime($dev['joined']));
  ?>
      <div class="drawer-card">
        <img src="<?= $dev['photo'] ?>" class="profile-photo" alt="<?= $dev['name'] ?>">
        
        <div class="drawer-info">
            <div class="p-ign"><?= $dev['ign'] ?></div>
            
            <div class="p-details">
                <div class="detail-row">
                    <span class="detail-label">Full Name</span>
                    <span class="detail-val"><?= $dev['name'] ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Origin</span>
                    <span class="detail-val"><?= $dev['origin'] ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Est.</span>
                    <span class="detail-val"><?= $join_date ?></span>
                </div>

                <?php if(isset($dev['socials']) && !empty($dev['socials'])): ?>
                <div class="social-links">
                    <?php if(!empty($dev['socials']['instagram'])): ?>
                        <a href="<?= $dev['socials']['instagram'] ?>" target="_blank" class="social-item" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    <?php endif; ?>

                    <?php if(!empty($dev['socials']['github'])): ?>
                        <a href="<?= $dev['socials']['github'] ?>" target="_blank" class="social-item" title="GitHub">
                            <i class="fab fa-github"></i>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
            </div>
        </div>
      </div>
  <?php endforeach; ?>
</div>

<?php include 'config/footer.php'; ?>

</body>
</html>