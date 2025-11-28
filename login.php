<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - VCT Pacific</title>

    <link rel="stylesheet" href="<?php echo $path; ?>assets/css/auth.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body class="auth-page">

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