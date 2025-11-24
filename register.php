<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Valorant Register</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('image/Ep8a1_Defiance_Riot_Client_Login_Page_1440p.png') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        .register-box {
            width: 400px;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
        }

        .title {
            text-align: center;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .btn-register {
            background: #ff4655 !important;
            border: none;
            font-weight: bold;
        }

        .btn-register:hover {
            background: #d93c48 !important;
        }

        .toggle-label {
            cursor: pointer;
        }

        .pesan-error {
            color: #ff4655;
            text-align: center;
            font-size: 14px;
            margin-bottom: 10px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="register-box">

        <h3 class="title">Sign Up</h3>

        <!-- Error Handling -->
        <?php 
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "password_tidak_cocok"){
                echo "<div class='pesan-error'>Password dan Konfirmasi tidak sama!</div>";
            }else if($_GET['pesan'] == "email_sudah_ada"){
                echo "<div class='pesan-error'>Email sudah terdaftar!</div>";
            }else if($_GET['pesan'] == 'gagal'){
                echo "<div class='pesan-error'>Gagal mendaftar, coba lagi.</div>";
            }else if($_GET['pesan'] == 'error_db'){
                echo "<div class='pesan-error'>Kesalahan pada server.</div>";
            }
        }
        ?>

        <form action="registerCon.php" method="POST">

            <!-- Nama -->
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" placeholder="Nama lengkap kamu" required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Email aktif" required>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label">Kata Sandi</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Buat password" required>
            </div>

            <!-- Confirm Password -->
            <div class="mb-1">
                <label class="form-label">Konfirmasi Kata Sandi</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Ulangi password" required>
            </div>

            <!-- Checkbox Show Password -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="togglePass" onclick="togglePassword()">
                <label class="form-check-label toggle-label" for="togglePass">Tampilkan Password</label>
            </div>

            <!-- Button -->
            <button type="submit" name="register_btn" class="btn btn-register w-100 py-2">Daftar</button>

        </form>

        <!-- Login Link -->
        <div class="text-center mt-3">
            <small>Sudah punya akun? 
                <a href="login.php" style="color:#ff4655; font-weight:bold;">Masuk disini</a>
            </small>
        </div>

    </div>

    <script>
        function togglePassword() {
            const pass = document.getElementById('password');
            const conf = document.getElementById('confirm_password');
            
            const type = pass.type === 'password' ? 'text' : 'password';
            pass.type = type;
            conf.type = type;
        }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
