<?php
$current_page = basename($_SERVER['PHP_SELF']);


$user_avatar = isset($_SESSION['avatar']) ? $_SESSION['avatar'] : 'assets/images/default_agent.png';
if (isset($_SESSION['fav_team_id']) && !empty($_SESSION['fav_team_id'])) {
    $link_my_team = "detail_tim.php?id=" . $_SESSION['fav_team_id'];
} else {
    $link_my_team = "edit_profile.php"; 
}

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
    /* NAVBAR STYLE */
    header { 
        background: #000; 
        padding: 10px 40px; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        border-bottom: 2px solid #ff4655; 
        position: relative;
        z-index: 999;
    }
    
    .header-left { display: flex; align-items: center; gap: 30px; }
    .logos img { height: 40px; width: auto; margin-right: 10px; }
    
    header nav { display: flex; align-items: center; gap: 20px; }
    header nav a.nav-link { 
        color: #ece8e1; text-decoration: none; font-weight: bold; 
        font-size: 14px; text-transform: uppercase; transition: 0.3s; letter-spacing: 1px;
    }
    header nav a.nav-link:hover, header nav a.nav-link.active { color: #ff4655; }

    /* SEARCH BAR */
    .search-box { display: flex; align-items: center; }
    .search-input { background: #1b2733; border: 1px solid #555; border-radius: 4px 0 0 4px; padding: 8px 12px; color: white; font-size: 13px; outline: none; width: 180px; }
    .search-input:focus { border-color: #ff4655; }
    .search-btn { background: #ff4655; border: none; padding: 9px 15px; border-radius: 0 4px 4px 0; cursor: pointer; color: white; }
    .search-btn:hover { background: #d93c48; }


    .profile-dropdown { position: relative; cursor: pointer; margin-left: 20px; }
    
    /* Tombol Pemicu (Foto + Nama) */
    .profile-trigger {
        display: flex; align-items: center; gap: 10px;
        color: white; font-weight: bold; font-size: 14px; text-transform: uppercase;
        padding: 5px 10px; border-radius: 4px; transition: 0.3s;
    }
    .profile-trigger:hover { background: #1b2733; color: #ff4655; }
    
    .nav-avatar {
        width: 35px; height: 35px; border-radius: 50%; object-fit: cover;
        border: 2px solid #ff4655; background: #000;
    }

    /* Isi Dropdown */
    .profile-menu {
        display: none; position: absolute; right: 0; top: 120%;
        background-color: #1b2733; min-width: 220px;
        box-shadow: 0px 8px 20px rgba(0,0,0,0.6);
        border: 1px solid #333; border-top: 3px solid #ff4655; border-radius: 4px;
        z-index: 1000; padding: 10px 0;
        animation: slideDown 0.2s ease;
    }
    .profile-menu::before {
        content: "";
        position: absolute;
        

        top: -20px;  
        left: 0;
        

        width: 100%;
        height: 20px; 
        
        background: transparent; 
        display: block;
    }

    .profile-dropdown:hover .profile-menu { display: block; }


    .menu-header {
        padding: 8px 20px; font-size: 11px; font-weight: 800; color: #666;
        text-transform: uppercase; letter-spacing: 1px; margin-top: 5px;
    }
    
    .menu-item {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 20px; color: #ece8e1; text-decoration: none;
        font-size: 14px; transition: 0.2s;
    }
    
    .menu-item i { width: 20px; text-align: center; color: #aaa; }
    .menu-item:hover { background-color: #263542; color: white; }
    .menu-item:hover i { color: #ff4655; }
    
    .menu-divider { height: 1px; background: #333; margin: 8px 0; }
    
    .menu-item.logout { color: #ff4655; }
    .menu-item.logout:hover { background: #3a1c1c; }

    @keyframes slideDown { from { opacity:0; transform: translateY(-10px); } to { opacity:1; transform: translateY(0); } }

    /* Dropdown Jadwal */
    .dropdown { position: relative; display: inline-block; cursor: pointer; }
    .dropbtn { font-weight: bold; font-size: 14px; text-transform: uppercase; color: #ece8e1; transition: 0.3s; }
    .dropdown:hover .dropbtn { color: #ff4655; }
    .dropdown-content { display: none; position: absolute; background-color: #1b2733; min-width: 140px; box-shadow: 0px 8px 16px rgba(0,0,0,0.5); z-index: 100; border: 1px solid #ff4655; border-radius: 4px; top: 100%; left: 0; }
    .dropdown-content a { color: white; padding: 12px 16px; display: block; font-size: 13px; text-align: left; border-bottom: 1px solid #333; text-decoration: none; }
    .dropdown-content a:hover { background-color: #ff4655; color: white; }
    .dropdown:hover .dropdown-content { display: block; }
</style>

<header>
    <div class="header-left">
        <div class="logos">
            <a href="home.php">
                <img src="assets/images/logoValo.png" alt="Valorant">
                <img src="assets/images/logoVCT.png" alt="VCT">
            </a>
        </div>
        
        <form action="search.php" method="GET" class="search-box">
            <input type="text" name="keyword" class="search-input" placeholder="Cari Tim / Pemain..." required>
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <nav>
        <a href="home.php" class="nav-link <?= ($current_page == 'home.php') ? 'active' : '' ?>">Home</a>
        <a href="tim.php" class="nav-link <?= ($current_page == 'tim.php') ? 'active' : '' ?>">Tim</a>
        
        <div class="dropdown">
            <span class="dropbtn <?= ($current_page == 'match.php') ? 'active' : '' ?>">Jadwal ▾</span>
            <div class="dropdown-content">
                <a href="match.php?stage=1">STAGE 1</a>
                <a href="match.php?stage=2">STAGE 2</a>
            </div>
        </div>

        <a href="berita.php" class="nav-link <?= ($current_page == 'berita.php') ? 'active' : '' ?>">Berita</a>

        <div class="profile-dropdown">
            <div class="profile-trigger">
                <img src="<?php echo $user_avatar; ?>" class="nav-avatar" onerror="this.src='assets/images/avatar.png'">
                <span><?php echo $_SESSION['username']; ?> <i class="fas fa-caret-down"></i></span> 
            </div>

            <div class="profile-menu">
                <div class="menu-header">General</div>
                
                <a href="profile.php" class="menu-item">
                    <i class="fas fa-user-circle"></i> Edit Profile
                </a>

                <a href="<?php echo $link_my_team; ?>" class="menu-item">
                    <i class="fas fa-users"></i> My Team
                </a>

                <?php if (!isset($_SESSION['fav_team_id']) || empty($_SESSION['fav_team_id'])): ?>
                <script>
                    document.querySelector('a[href="edit_profile.php"].menu-item').onclick = function() {
                        alert("Lu belum milih Tim Favorit, Bang! Pilih dulu di Edit Profile ya.");
                    };
                </script>
                <?php endif; ?>

                <div class="menu-divider"></div>

                <div class="menu-header">System</div>
                
                <a href="#" class="menu-item">
                    <i class="fas fa-cog"></i> Settings
                </a>

                <a href="logout.php" class="menu-item logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        </nav>
</header>