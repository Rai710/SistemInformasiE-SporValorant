<?php
include "koneksi.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    try{
        $sql = "INSERT INTO users (name, email, password) values (?,?,?)";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $password);

        if($stmt->execute()){
            header("Location: login.php");
            exit();
        } else {
            echo "Gagal mendaftar.";
        }
    } catch (mysqli_sql_exception $e){
        echo "Pendaftaran gagal: " . $e->getMessage();
    }

    $stmt->close();
    $koneksi->close();
}
?>