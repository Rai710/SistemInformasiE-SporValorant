<?php
// Cek halaman mana yang lagi dibuka
$current_page = basename($_SERVER['PHP_SELF']);
?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">

<div class="sidebar">
    <div class="admin-brand"><i class="fas fa-cube"></i> VCT ADMIN</div>
    
    <div class="menu-group">
        <div class="menu-label">Main Menu</div>
        
        <a href="admin_dashboard.php" class="nav-item <?php echo ($current_page == 'admin_dashboard.php') ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        
        <a href="manage_matches.php" class="nav-item <?php echo ($current_page == 'manage_matches.php' || $current_page == 'edit_match.php' || $current_page == 'add_match.php') ? 'active' : ''; ?>">
            <i class="fas fa-gamepad"></i> Match Room
        </a>
        
        <a href="manage_teams.php" class="nav-item <?php echo ($current_page == 'manage_teams.php') ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Teams & Players
        </a>
    </div>
    
    <div class="menu-group">
        <div class="menu-label">System</div>
        
        <a href="manage_week.php" class="nav-item <?php echo ($current_page == 'manage_week.php') ? 'active' : ''; ?>">
            <i class="fas fa-cogs"></i> Settings
        </a>
    </div>
    
    <div style="margin-top: auto;">
        <a href="../logout.php" class="nav-item" style="color: #ff4655;">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>