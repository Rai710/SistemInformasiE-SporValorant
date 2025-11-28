<!DOCTYPE html>
<html lang="id">
<head>
    <title>Register - VCT Pacific</title>
    <link rel="stylesheet" href="<?php echo $path; ?>assets/css/auth.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

    <style>
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
<body class="auth-page">

    <div class="container d-flex justify-content-center">
        
        <div class="card auth-card p-4">
            <div class="card-body">
                
                <h3 class="auth-title">Daftar Akun</h3>

                <?php 
                if(isset($_GET['pesan'])){
                    $msg = "";
                    if($_GET['pesan']=="password_tidak_cocok") $msg = "Password & Konfirmasi beda!";
                    else if($_GET['pesan']=="email_sudah_ada") $msg = "Email sudah terdaftar!";
                    else if($_GET['pesan']=="gagal") $msg = "Gagal daftar.";
                    
                    if($msg != "") echo "<div class='alert alert-danger py-2 text-center small fw-bold'>$msg</div>";
                }
                ?>

                <form action="action/registerCon.php" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">USERNAME</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">EMAIL</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold text-secondary small">PASSWORD</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold text-secondary small">KONFIRMASI</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="togglePass" onclick="togglePassword()">
                        <label class="form-check-label small fw-bold text-muted" for="togglePass">Lihat Password</label>
                    </div>

                    <button type="submit" name="register_btn" class="btn btn-valorant w-100">Daftar Sekarang</button>
                
                </form>

                <div class="text-center mt-4 small">
                    Sudah punya akun? <a href="login.php" class="auth-link">Login di sini</a>
                </div>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            var p = document.getElementById("password");
            var c = document.getElementById("confirm_password");
            var type = p.type === "password" ? "text" : "password";
            p.type = type;
            c.type = type;
        }
    </script>

</body>
</html>