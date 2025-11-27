<?php
include "../config/koneksi.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if($password !== $confirm_password){
        header("Location: register.php?pesan=password_tidak_cocok");
        exit();
    }

    try {
        $checkEmail = "SELECT email FROM users WHERE email = ?";
        $stmtCheck = $koneksi->prepare($checkEmail);
        $stmtCheck->bind_param("s", $email);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            header("Location: register.php?pesan=email_sudah_ada");
        } else {
            $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param("sss", $name, $email, $password);

            if ($stmt->execute()) {
                header("Location: ../login.php?pesan=register_berhasil");
            } else {
                header("Location: ../register.php?pesan=gagal");
            }
            $stmt->close();
        }
        $stmtCheck->close();

    } catch (Exception $e) {    
        header("Location: ../register.php?pesan=error_db");
    }
    
    $koneksi->close();
}
?>