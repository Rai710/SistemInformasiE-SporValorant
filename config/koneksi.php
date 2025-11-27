<?php

$host = "localhost";
$user = "root";
$pass = "";
$db  = "db_valo";
$port = 3306;

try{
    $koneksi = new mysqli($host, $user, $pass, $db, $port);
}catch(mysqli_sql_exception $e){
    die("Koneksi gagal: " . $e->getMessage());
}


?>