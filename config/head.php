<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php 
// Cek folder aktif. Jika 'admin' atau 'action', set prefix '../'. Jika root, kosong.
$folder_sekarang = basename(dirname($_SERVER['PHP_SELF']));
$path_prefix = ($folder_sekarang == 'admin' || $folder_sekarang == 'action' || $folder_sekarang == 'config') ? '../' : ''; 
?>

<link rel="stylesheet" href="<?php echo $path_prefix; ?>assets/css/style.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<link rel="icon" type="image/png" href="<?php echo $path_prefix; ?>assets/images/logoValo.png">