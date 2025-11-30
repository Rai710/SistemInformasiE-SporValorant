<?php 
include 'config/koneksi.php';

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// --- KONFIGURASI WHATSAPP ---
// Ganti dengan nomor WhatsApp admin (gunakan format internasional tanpa +, contoh: 628...)
$wa_number = "6282120185813"; 

// Pesan default saat user klik tombol
$text_message = "Halo Admin Valorant Esports Hub, saya butuh bantuan/info mengenai website.";

// Buat Link API WhatsApp
$wa_link = "https://wa.me/" . $wa_number . "?text=" . urlencode($text_message);

$site_name = "VALORANT ESPORTS HUB";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="referrer" content="no-referrer" /> 
<title>Contact Us – <?= $site_name ?></title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?php include 'config/head.php'; ?>
<style>
  /* --- LAYOUT UMUM (Konsisten dengan halaman lain) --- */
  .header-contact { text-align: center; padding: 60px 20px 40px; animation: fadeIn 1s; }
  
  .contact-title { font-size: 50px; font-weight: 900; margin-top: 10px; text-transform: uppercase; letter-spacing: 2px; text-shadow: 0 5px 15px rgba(0,0,0,0.5); color: white; }
  .contact-desc { max-width: 600px; margin: 20px auto 0; font-size: 18px; line-height: 1.6; color: #ccc; }

  /* --- CONTAINER UNTUK KARTU WHATSAPP --- */
  .contact-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding-bottom: 80px;
    padding-top: 20px;
  }

  /* --- KARTU WHATSAPP --- */
  .wa-card {
    background: #1b2733;
    width: 400px;
    padding: 40px 30px;
    border-radius: 12px;
    text-align: center;
    border: 1px solid #333;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  /* Efek Hover pada Kartu */
  .wa-card:hover {
    transform: translateY(-10px);
    border-color: #25D366; /* Warna Hijau WA */
    box-shadow: 0 15px 40px rgba(37, 211, 102, 0.2);
  }

  /* Icon Besar */
  .wa-icon-large {
    font-size: 80px;
    color: #ccc;
    margin-bottom: 20px;
    transition: color 0.3s;
  }

  .wa-card:hover .wa-icon-large {
    color: #25D366;
  }

  .wa-label {
    font-size: 24px;
    font-weight: 800;
    color: white;
    text-transform: uppercase;
    margin-bottom: 10px;
    letter-spacing: 1px;
  }

  .wa-sub {
    font-size: 14px;
    color: #888;
    margin-bottom: 30px;
  }

  /* --- TOMBOL CHAT --- */
  .btn-chat {
    display: inline-block;
    padding: 15px 40px;
    background-color: transparent;
    border: 2px solid #25D366;
    color: #25D366;
    font-weight: 700;
    text-transform: uppercase;
    text-decoration: none;
    border-radius: 4px;
    font-size: 16px;
    transition: all 0.3s;
    width: 100%;
    letter-spacing: 1px;
  }

  .btn-chat:hover {
    background-color: #25D366;
    color: white;
    box-shadow: 0 0 15px rgba(37, 211, 102, 0.5);
  }

  .btn-chat i { margin-right: 8px; }

  /* Garis Dekorasi ala Valorant */
  .deco-line {
    height: 4px;
    width: 50px;
    background: #ff4655;
    margin: 0 auto 20px;
  }

  @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
</head>

<body>

<?php include 'config/navbar.php'; ?>

<div class="header-contact">
  <div class="contact-title">CONTACT SUPPORT</div>
  <p class="contact-desc">Ada pertanyaan seputar statistik, tim, atau kendala teknis? Hubungi admin kami langsung melalui WhatsApp untuk respon cepat.</p>
</div>

<div class="contact-container">
    
    <div class="wa-card">
        <div class="deco-line"></div>

        <i class="fab fa-whatsapp wa-icon-large"></i>

        <div class="wa-label">WhatsApp Support</div>
        
        <div class="wa-sub">
            Online Hours: 09:00 - 21:00 WIB<br>
            Average Response: &lt; 15 Mins
        </div>

        <a href="<?= $wa_link ?>" target="_blank" class="btn-chat">
            <i class="fab fa-whatsapp"></i> Chat Admin
        </a>
    </div>

</div>

<?php include 'config/footer.php'; ?>

</body>
</html>