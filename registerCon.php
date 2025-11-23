<?php
include 'koneksi.php';

if (isset($_POST['register_btn'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "<script>
                alert('Konfirmasi sandi tidak cocok!');
                window.location.href='register.php';
              </script>";
        exit();
    }

    try {
        $checkEmail = "SELECT email FROM users WHERE email = ?";
        $stmtCheck = $koneksi->prepare($checkEmail);
        $stmtCheck->bind_param("s", $email);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            echo "<script>
                    alert('Email sudah terdaftar, gunakan email lain!');
                    window.location.href='register.php';
                  </script>";
        } else {
            $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param("sss", $name, $email, $password);

            if ($stmt->execute()) {
                echo "<script>
                        alert('Pendaftaran Berhasil! Silakan Login.');
                        window.location.href='login.php';
                      </script>";
            } else {
                echo "<script>
                        alert('Gagal mendaftar, silakan coba lagi.');
                        window.location.href='register.php';
                      </script>";
            }
            $stmt->close();
        }
        $stmtCheck->close();

    } catch (Exception $e) {
        echo "<script>
                alert('Terjadi Error Sistem: " . addslashes($e->getMessage()) . "');
                window.location.href='register.php';
              </script>";
    }
} else {
    header("Location: register.php");
    exit();
}
?>