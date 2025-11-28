<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php 
// Deteksi folder biar path css bener (Root vs Admin)
$path = (basename(dirname($_SERVER['PHP_SELF'])) == 'admin' || basename(dirname($_SERVER['PHP_SELF'])) == 'action') ? '../' : ''; 
?>

<link rel="stylesheet" href="<?php echo $path; ?>assets/css/style.css">
<link rel="stylesheet" href="<?php echo $path; ?>assets/css/match.css"> 

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="icon" type="image/png" href="<?php echo $path; ?>assets/images/logoValo.png">