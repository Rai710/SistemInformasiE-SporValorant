<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - VCT Pacific</title>

    <?php 
    $path = isset($path) ? $path : ''; 
    ?>
    <link rel="stylesheet" href="<?php echo $path; ?>assets/css/auth.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/auth.css">
    
    <style>
        body {
            overflow: hidden;
            background: #0f1923;
        }

        .bg-video {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            z-index: -2; 
        }

        .overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: radial-gradient(circle, rgba(15,25,35,0.6) 0%, rgba(15,25,35,0.9) 90%);
            z-index: -1;
        }

        /* MEMASTIKAN KONTEN DI TENGAH & DI ATAS VIDEO */
        .container {
            position: relative;
            z-index: 10;
            height: 100vh;
            align-items: center;
        }

        /* ANIMASI */
        body.auth-page {
            animation: zoomInStart 0.8s ease-out forwards;
            opacity: 0;
        }

        @keyframes zoomInStart {
            0% { transform: scale(1.1); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        body.zoom-out {
            transform: scale(1.2);
            opacity: 0;
            filter: blur(5px);
            transition: all 0.6s ease-in;
        }
    </style>
</head>

<body class="auth-page">

    <video autoplay loop muted playsinline class="bg-video">
        <source src="assets/video/index.mp4" type="video/mp4">
    </video>
    <div class="overlay"></div>
    <div class="container d-flex justify-content-center">
        
        <div class="card auth-card p-4">
            <div class="card-body">
                
                <h3 class="auth-title">Valorant Login</h3>

                <form action="action/loginCon.php" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">USERNAME</label>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">PASSWORD</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="togglePass" onclick="togglePassword()">
                        <label class="form-check-label small fw-bold text-muted" for="togglePass">Lihat Password</label>
                    </div>

                    <?php 
                    if(isset($_GET['pesan'])){
                        echo "<div class='alert alert-danger py-2 text-center small fw-bold'><i class='fas fa-exclamation-circle'></i> Username atau Password Salah!</div>";
                    }
                    ?>

                    <button type="submit" name="login_btn" class="btn btn-valorant w-100">MASUK SEKARANG</button>
                
                </form>

                <div class="text-center mt-4 small">
                    Belum punya akun? <a href="register.php" class="auth-link">Daftar akun</a>
                </div>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            var x = document.getElementById("password");
            x.type = x.type === "password" ? "text" : "password";
        }
    </script>

</body>
</html>