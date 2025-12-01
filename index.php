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

        /* CSS KHUSUS VIDEO BACKGROUND */
        .bg-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }

        .overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle, rgba(15,25,35,0.4) 0%, rgba(15,25,35,0.9) 90%);
            z-index: 2;
        }

        .landing-content { 
        position: relative; 
        z-index: 10; 
        text-align: center; 
        
        opacity: 0; 
        transform: translateY(50px);
        transition: opacity 1.5s ease-out, transform 1.5s ease-out; 
        
        pointer-events: none; 
    }

    /* KONDISI AKHIR */
    .landing-content.show-content {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }
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

        @keyframes slideUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <video id="introVideo" autoplay loop muted playsinline class="bg-video">
        <source src="assets/video/index.mp4" type="video/mp4">
    </video>

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
        const video = document.getElementById('introVideo');
        const content = document.querySelector('.landing-content');

        video.addEventListener('play', () => {
            setTimeout(() => {
                content.classList.add('show-content');
            }, 500); 
        });

        // FALLBACK: 
        window.addEventListener('load', () => {
            setTimeout(() => {
                if (!content.classList.contains('show-content')) {
                    content.classList.add('show-content');
                }
            }, 1000);
        });


        // 2. FUNGSI ANIMASI PINDAH HALAMAN
        function animatePage(e, url) {
            e.preventDefault(); 
            document.body.classList.add('zoom-out'); 
            setTimeout(() => { window.location.href = url; }, 800); 
        }

        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                document.body.classList.remove('zoom-out');
            }
        });
    </script>

</body>