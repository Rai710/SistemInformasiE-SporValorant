<?php
// Cek status session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$username = $_SESSION['username'] ?? 'Guest';
$user_avatar = $_SESSION['avatar'] ?? 'assets/images/default_agent.png';

// Link My Team
$fav_team_id = $_SESSION['fav_team_id'] ?? null;
$link_my_team = $fav_team_id ? "detail_tim.php?id=" . $fav_team_id : "edit_profile.php";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
    /* === NAVBAR GLOBAL STYLE === */
    header { 
        background: #000; padding: 10px 40px; display: flex; 
        justify-content: space-between; align-items: center; 
        border-bottom: 2px solid #ff4655; position: relative; z-index: 999;
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

    /* DROPDOWN */
    .dropdown { position: relative; display: inline-block; cursor: pointer; }
    .dropbtn { font-weight: bold; font-size: 14px; text-transform: uppercase; color: #ece8e1; transition: 0.3s; }
    .dropdown:hover .dropbtn { color: #ff4655; }
    .dropdown-content { 
        display: none; position: absolute; background-color: #1b2733; 
        min-width: 180px; box-shadow: 0px 8px 16px rgba(0,0,0,0.5); 
        z-index: 100; border: 1px solid #ff4655; border-radius: 4px; top: 100%; left: 0; 
    }
    .dropdown-content a { color: white; padding: 12px 16px; display: block; font-size: 13px; text-align: left; border-bottom: 1px solid #333; text-decoration: none; }
    .dropdown-content a:hover { background-color: #ff4655; color: white; }
    .dropdown:hover .dropdown-content { display: block; }

    /* PROFIL DROPDOWN */
    .profile-dropdown { position: relative; cursor: pointer; margin-left: 20px; }
    .profile-trigger {
        display: flex; align-items: center; gap: 10px; color: white; font-weight: bold; 
        font-size: 14px; text-transform: uppercase; padding: 5px 10px; border-radius: 4px; transition: 0.3s;
    }
    .profile-trigger:hover { background: #1b2733; color: #ff4655; }
    .nav-avatar {
        width: 35px; height: 35px; border-radius: 50%; object-fit: cover;
        border: 2px solid #ff4655; background: #000;
    }
    .profile-menu {
        display: none; position: absolute; right: 0; top: 100%; margin-top: 10px;
        background-color: #1b2733; min-width: 220px; box-shadow: 0px 8px 20px rgba(0,0,0,0.6);
        border: 1px solid #333; border-top: 3px solid #ff4655; border-radius: 4px;
        z-index: 1000; padding: 10px 0;
    }
    .profile-dropdown:hover .profile-menu { display: block; }
    .profile-menu::before { content: ""; position: absolute; top: -20px; left: 0; width: 100%; height: 30px; background: transparent; display: block; }

    .menu-header { padding: 8px 20px; font-size: 11px; font-weight: 800; color: #666; text-transform: uppercase; letter-spacing: 1px; margin-top: 5px; }
    .menu-item { display: flex; align-items: center; gap: 12px; padding: 10px 20px; color: #ece8e1; text-decoration: none; font-size: 14px; transition: 0.2s; }
    .menu-item i { width: 20px; text-align: center; color: #aaa; }
    .menu-item:hover { background-color: #263542; color: white; }
    .menu-item:hover i { color: #ff4655; }
    .menu-divider { height: 1px; background: #333; margin: 8px 0; }
    .menu-item.logout { color: #ff4655; }
    .menu-item.logout:hover { background: #3a1c1c; }
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
        
        <div class="dropdown">
            <a href="#" class="dropbtn nav-link <?= ($current_page == 'tim.php') ? 'active' : '' ?>">Tim ▾</a>
            <div class="dropdown-content">
                <a href="tim.php?event_id=3">Pacific 2026 (New)</a>
                <a href="tim.php?event_id=1">Pacific 2025</a>
            </div>
        </div>

        <div class="dropdown">
            <a href="#" class="dropbtn nav-link <?= ($current_page == 'match.php') ? 'active' : '' ?>">Jadwal ▾</a>
            <div class="dropdown-content">
                <a href="match.php?stage=3">Pacific 2026</a>
                <div style="border-top:1px solid #333; margin:5px 0;"></div>
                <a href="match.php?stage=1">Stage 1 (2025)</a>
                <a href="match.php?stage=2">Stage 2 (2025)</a>
            </div>
        </div>
        
        <a href="prediction.php" class="nav-link <?= ($current_page == 'prediction.php') ? 'active' : '' ?>">Prediction</a>
        <a href="agent.php" class="nav-link <?= ($current_page == 'agent.php') ? 'active' : '' ?>">Agent</a>
        <a href="berita.php" class="nav-link <?= ($current_page == 'berita.php') ? 'active' : '' ?>">Berita</a>

        <div class="profile-dropdown">
            <div class="profile-trigger">
                <img src="<?php echo $user_avatar; ?>" class="nav-avatar" onerror="this.src='assets/images/default_agent.png'">
                <span><?php echo $username; ?> <i class="fas fa-caret-down"></i></span>
            </div>
            <div class="profile-menu">
                <div class="menu-header">General</div>
                <a href="profile.php" class="menu-item"><i class="fas fa-user-circle"></i> Profile</a>
                <a href="<?php echo $link_my_team; ?>" class="menu-item"><i class="fas fa-users"></i> My Team</a>
                <div class="menu-divider"></div>
                <div class="menu-header">Account</div>
                <a href="edit_profile.php" class="menu-item"><i class="fas fa-cog"></i> Edit Profile</a>
                <a href="logout.php" class="menu-item logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>
</header>