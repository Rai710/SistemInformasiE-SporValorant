<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Valorant Login</title>
    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;

            background: url('image/Ep8a1_Defiance_Riot_Client_Login_Page_1440p.png') no-repeat center center/cover; 
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            width: 350px;
            background: rgba(255, 255, 255, 0.95); 
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: #333;
        }

        .input-group input[type="text"], .input-group input[type="email"], .input-group input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        .show-pass {
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 8px;    
            font-size: 13px;
            color: #555;
        }
        

        .show-pass input {
            width: auto;
            cursor: pointer;
        }
        .error-text {
            color: #ff4655; 
            font-size: 12px;
            margin-top: 5px;
            display: none; 
            font-style: italic;
        }

        .input-error {
            border: 1px solid #ff4655 !important;
        }
        .show-pass label {
            cursor: pointer;
            margin: 0; 
        }

        .login-btn {
            width: 100%;
            padding: 12px; 
            background: #ff4655;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s; 
        }

        .login-btn:hover {
            background: #d93c48;
        }

        .extra {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .extra a {
            color: #ff4655;
            text-decoration: none;
            font-weight: bold;
        }
        .pesan-error {
            color: #ff4655;
            text-align: center;
            font-size: 13px;
            margin-bottom: 10px;
            font-weight: bold;
        }
    </style>
</head> 
<body>
    <div class="login-container">
        <h2>Sign Up</h2>
        
        <?php 
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "password_tidak_cocok"){
                echo "<div class='pesan-error'>Password dan Konfirmasi tidak sama!</div>";
            }else if($_GET['pesan'] == "email_sudah_ada"){
                echo "<div class='pesan-error'>Email sudah terdaftar, pakai yang lain!</div>";
            }else if($_GET['pesan'] == "gagal"){
                echo "<div class='pesan-error'>Gagal mendaftar, coba lagi.</div>";
            }else if($_GET['pesan'] == "error_db"){
                echo "<div class='pesan-error'>Terjadi kesalahan sistem.</div>";
            }
        }
        ?>

        <form action="registerCon.php" method="POST">
            
            <div class="input-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" placeholder="Nama lengkap kamu" required />
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Email aktif" required />
            </div>

            <div class="input-group">
                <label>Kata Sandi</label>
                <input type="password" name="password" id="password" placeholder="Buat password" required />
            </div>

            <div class="input-group">
                <label>Konfirmasi Kata Sandi</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Ulangi password" required />
                
                <div class="show-pass">
                    <input type="checkbox" id="togglePass" onclick="togglePassword()">
                    <label for="togglePass">Tampilkan Password</label>
                </div>
            </div>

            <button type="submit" name="register_btn" class="login-btn">Daftar</button>
        </form>

        <div class="extra">
            <p>Sudah punya akun? <a href="login.php">Masuk disini</a></p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pass = document.getElementById('password');
            const confPass = document.getElementById('confirm_password');
            const type = pass.type === 'password' ? 'text' : 'password';
            pass.type = type;
            confPass.type = type;
        }
    </script>
</body>
</html>
