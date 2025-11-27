<?php
session_start();
include "../config/koneksi.php"; 

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {

        $sql = "SELECT user_id, name, password, email, role, avatar_image, favorite_team_id FROM users WHERE name = ?";
        
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if ($password == $row['password']) {
   
                $_SESSION['user_id']      = $row['user_id'];
                $_SESSION['username']     = $row['name']; 
                $_SESSION['role']         = $row['role'];
                $_SESSION['avatar']       = $row['avatar_image'];
                $_SESSION['fav_team_id']  = $row['favorite_team_id']; 

                $_SESSION['status']       = "login";

                if($row['role'] == 'admin'){
                    header("Location: ../home.php");
                } else {

                    header("Location: ../home.php");
                }
                exit();

            } else {
                header("Location: ../login.php?pesan=password_salah");
                exit();
            }
        } else {
            header("Location: ../login.php?pesan=user_tidak_ditemukan");
            exit();
        }
        $stmt->close();

    } catch (mysqli_sql_exception $e) {

        echo "Error: " . $e->getMessage();
        echo "<br>Saran: Pastikan kolom 'role' dan 'avatar_image' sudah dibuat di database.";
        exit();
    }
    
    $koneksi->close();
}
?>