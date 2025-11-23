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
            background: url('background.jpg') no-repeat center center/cover; 
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .input-group input[type="text"], .input-group input[type="email"], .input-group input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }
        .login-container {
            width: 350px;
            background: rgba(255, 255, 255, 0.95); /* Sedikit lebih solid */
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
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Sign In</h2>
            <form action="loginCon.php" method="POST">
                        
                <div class="input-group">
                    <label>Nama Pengguna</label>
                    <input type="text" name="username" placeholder="Masukkan username" required />
                </div>

                <div class="input-group">
                    <label>Kata Sandi</label>
                    <input type="password" name="password" id="password" placeholder="Masukkan password" required />
                    
                    <div class="show-pass">
                        <input type="checkbox" id="togglePass" onclick="togglePassword()">
                        <label for="togglePass">Tampilkan Password</label>
                    </div>
                </div>

                <button type="submit" name="login_btn" class="login-btn">Masuk</button>

            </form>
        <div class="extra">
            <p>Tidak punya akun? <a href="register.php">Buat akun</a></p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pass = document.getElementById('password');
            pass.type = pass.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>