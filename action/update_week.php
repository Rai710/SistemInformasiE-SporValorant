<?php
session_start();
include "../config/koneksi.php";

// Cek Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_week = $_POST['active_week'];
    $sql = "INSERT INTO system_settings (setting_key, setting_value) 
            VALUES ('active_week', ?) 
            ON DUPLICATE KEY UPDATE setting_value = ?";
            
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ss", $new_week, $new_week);

    if ($stmt->execute()) {
        header("Location: ../admin/manage_week.php?msg=updated");
    } else {
        echo "Error: " . $koneksi->error;
    }
    
    $stmt->close();
}
$koneksi->close();
?>