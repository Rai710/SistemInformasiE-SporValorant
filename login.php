<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Valorant Login</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background:url('image/Ep8a1_Defiance_Riot_Client_Login_Page_1440p.png') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 380px;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
        }

        .login-title {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .toggle-label {
            cursor: pointer;
            font-size: 14px;
        }

        .login-btn {
            background: #ff4655 !important;
            border: none;
            font-weight: bold;
        }

        .login-btn:hover {
            background: #d93c48 !important;
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

<div class="login-box">

    <h3 class="login-title">Sign In</h3>

    <form action="loginCon.php" method="POST">

        <!-- Username -->
        <div class="mb-3">
            <label class="form-label">Nama Pengguna</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
        </div>

        <!-- Password -->
        <div class="mb-1">
            <label class="form-label">Kata Sandi</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
        </div>

        <!-- Show Password -->
        <div class="form-check mt-1 mb-3">
            <input class="form-check-input" type="checkbox" id="togglePass" onclick="togglePassword()">
            <label class="form-check-label toggle-label" for="togglePass">Tampilkan Password</label>
        </div>

        <!-- Error Message -->
        <?php 
        if(isset($_GET['pesan'])){
            echo "<div class='pesan-error'>Username dan password salah!</div>";
        }
        ?>

        <!-- Login Button -->
        <button type="submit" name="login_btn" class="btn login-btn w-100 py-2">Masuk</button>

    </form>

    <!-- Register Link -->
    <div class="text-center mt-3">
        <small>Tidak punya akun? <a href="register.php" style="color:#ff4655; font-weight:bold;">Buat akun</a></small>
    </div>

</div>

<script>
    function togglePassword() {
        const pass = document.getElementById('password');
        pass.type = pass.type === 'password' ? 'text' : 'password';
    }
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
