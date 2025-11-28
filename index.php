<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WELCOME // VCT PACIFIC</title>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        
        body {
            height: 100vh; overflow: hidden; background: #0f1923;
            display: flex; justify-content: center; align-items: center; color: white;
            transition: transform 0.8s ease-in, opacity 0.8s ease-in;
        }

        body.zoom-out { transform: scale(3); opacity: 0; filter: blur(10px); }

        .bg-image {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: url('assets/images/Ep8a1_Defiance_Riot_Client_Login_Page_1440p.png') no-repeat center center/cover;
            z-index: 1; animation: zoomSlow 20s infinite alternate;
        }

        .overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle, rgba(15,25,35,0.7) 0%, rgba(15,25,35,1) 90%);
            z-index: 2;
        }

        .landing-content { position: relative; z-index: 10; text-align: center; animation: slideUp 1s ease-out; }
        .logo-vct { width: 120px; margin-bottom: 20px; filter: drop-shadow(0 0 20px rgba(255, 70, 85, 0.6)); }
        h1 { font-size: 80px; font-weight: 900; letter-spacing: 5px; margin: 0; line-height: 0.9; text-transform: uppercase; }
        h1 span { color: #ff4655; }
        p.subtitle { font-size: 18px; color: #ccc; letter-spacing: 3px; margin-top: 15px; margin-bottom: 50px; text-transform: uppercase; font-weight: 600; }

        .btn-group { display: flex; gap: 20px; justify-content: center; }
        .btn-main {
            padding: 15px 40px; font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
            text-decoration: none; color: white; border: 2px solid #ff4655; background: #ff4655;
            transition: 0.3s; clip-path: polygon(10% 0, 100% 0, 100% 100%, 0% 100%); cursor: pointer;
        }
        .btn-main:hover { background: #d93c48; transform: translateY(-3px); box-shadow: 0 10px 30px rgba(255, 70, 85, 0.4); }

        .btn-outline {
            padding: 15px 40px; font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
            text-decoration: none; color: white; border: 2px solid rgba(255,255,255,0.5); background: transparent;
            transition: 0.3s; clip-path: polygon(0 0, 100% 0, 90% 100%, 0% 100%); cursor: pointer;
        }
        .btn-outline:hover { border-color: white; background: rgba(255,255,255,0.1); }

        @keyframes zoomSlow { from { transform: scale(1); } to { transform: scale(1.1); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <div class="bg-image"></div>
    <div class="overlay"></div>

    <div class="landing-content">
        <img src="assets/images/logoVCT.png" class="logo-vct" alt="VCT Logo">
        <h1>PACIFIC <br> <span>PROTOCOL</span></h1>
        <p class="subtitle">Make Waves // Break Limits</p>

        <div class="btn-group">
            <a href="login.php" class="btn-main" onclick="animatePage(event, this.href)">ENTER SYSTEM</a>
            <a href="register.php" class="btn-outline" onclick="animatePage(event, this.href)">NEW AGENT</a>
        </div>
    </div>

    <script>
        // 1. FUNGSI ANIMASI PINDAH HALAMAN
        function animatePage(e, url) {
            e.preventDefault(); 
            document.body.classList.add('zoom-out'); // Mulai animasi gelap
            setTimeout(() => { window.location.href = url; }, 800); // Pindah setelah 0.8 detik
        }

        // 2. FUNGSI RESET KALAU USER TEKAN BACK (FIX LAYAR HITAM)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Kalau halaman diambil dari cache (history back), hapus class zoom-out
                document.body.classList.remove('zoom-out');
            }
        });
    </script>

</body>
</html> 