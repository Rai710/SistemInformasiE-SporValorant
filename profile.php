<?php
session_start();
include "config/koneksi.php";

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Query User + Team + Agent
$sql = "SELECT u.*, 
               t.team_name as fav_team_name, t.logo as fav_team_logo, t.team_id as fav_team_id,
               a.agent_name as fav_agent_name, a.role as agent_role
        FROM users u
        LEFT JOIN team t ON u.favorite_team_id = t.team_id
        LEFT JOIN agents a ON u.agent_id = a.agent_id  
        WHERE u.user_id = ?";

$query = $koneksi->prepare($sql);
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// [FIX] Cek apakah user ketemu?
if (!$user) {
    echo "Error: User data not found for ID: " . $user_id;
    exit();
}

// Default Values
$avatar = !empty($user['avatar_image']) ? $user['avatar_image'] : 'assets/images/default_agent.png';
$rank   = !empty($user['rank_tier']) ? $user['rank_tier'] : 'Unranked';
$agent_name = !empty($user['fav_agent_name']) ? $user['fav_agent_name'] : 'Jett';

// Cek Tim Favorit
$has_team = !empty($user['fav_team_name']);
$team_name = $has_team ? $user['fav_team_name'] : "No Team Selected";
$team_logo = $has_team ? $user['fav_team_logo'] : "assets/images/logoValo.png"; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dossier - <?php echo htmlspecialchars($user['name']); ?></title>
    
    <?php include 'config/head.php'; ?>

    <style>
        .profile-container { max-width: 1100px; margin: 50px auto; padding: 0 20px; }
        .dossier-grid { display: grid; grid-template-columns: 350px 1fr; gap: 30px; }
        .id-card { background: #1b2733; border: 1px solid #333; border-radius: 4px; overflow: hidden; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .id-header { background: #ff4655; padding: 15px; color: white; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; font-size: 14px; }
        .avatar-box { padding: 40px 0; background: url('assets/images/bg.jpg') center/cover; position: relative; }
        .avatar-real { width: 180px; height: 180px; border-radius: 50%; object-fit: cover; border: 5px solid #1b2733; box-shadow: 0 0 20px rgba(255, 70, 85, 0.5); }
        .id-body { padding: 20px; color: #ece8e1; }
        .user-ign { font-size: 32px; font-weight: 900; text-transform: uppercase; margin-bottom: 5px; line-height: 1; }
        .user-tag { font-size: 16px; color: #888; font-weight: bold; margin-bottom: 20px; }
        .rank-display { background: #0f1923; padding: 10px; border-radius: 4px; border: 1px solid #444; display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 20px; }
        .rank-txt { font-weight: bold; color: #ffd700; text-transform: uppercase; letter-spacing: 1px; }
        .btn-edit { display: block; width: 100%; background: transparent; border: 1px solid #ff4655; color: #ff4655; padding: 12px; text-transform: uppercase; font-weight: bold; text-decoration: none; transition: 0.3s; }
        .btn-edit:hover { background: #ff4655; color: white; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .info-box { background: #1b2733; padding: 25px; border-radius: 4px; border: 1px solid #333; position: relative; overflow: hidden; }
        .box-label { font-size: 12px; color: #888; text-transform: uppercase; font-weight: bold; margin-bottom: 10px; display: block; }
        .box-value { font-size: 18px; color: white; font-weight: bold; }
        .my-team-card { grid-column: 1 / -1; background: linear-gradient(135deg, #1b2733 0%, #0f1923 100%); border: 1px solid #444; display: flex; align-items: center; justify-content: space-between; padding: 30px; position: relative; }
        .my-team-card::before { content: "SUPPORTING"; position: absolute; top: 10px; left: 20px; font-size: 10px; color: #ff4655; font-weight: 900; letter-spacing: 2px; }
        .team-info { z-index: 2; }
        .fav-team-name { font-size: 36px; font-weight: 900; text-transform: uppercase; color: white; line-height: 1; }
        .fav-team-region { color: #888; font-size: 14px; margin-top: 5px; }
        .team-logo-bg { width: 100px; opacity: 0.8; filter: drop-shadow(0 0 20px rgba(255,255,255,0.1)); transition: 0.3s; }
        .my-team-card:hover .team-logo-bg { transform: scale(1.1) rotate(-5deg); opacity: 1; }
        .bio-box { grid-column: 1 / -1; background: rgba(0,0,0,0.3); padding: 20px; border-left: 3px solid #ff4655; font-style: italic; color: #ccc; }
        @media (max-width: 768px) { .dossier-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <?php include 'config/navbar.php'; ?>

    <div class="profile-container">

        <h1 style="color:white; text-transform:uppercase; font-weight:900; margin-bottom:30px; border-bottom:1px solid #333; padding-bottom:10px;">
            <?php echo htmlspecialchars($user['name']); ?>
            <span style="color:#ff4655;"> // PROFILE</span>
        </h1>

        <div class="dossier-grid">
            
            <div class="id-card">
                <div class="id-header">VALORANT PACIFIC ID</div>
                <div class="avatar-box">
                    <img src="<?php echo $avatar; ?>" class="avatar-real" onerror="this.src='assets/images/default_agent.png'">
                </div>
                <div class="id-body">
                    <div class="user-ign"><?php echo !empty($user['riot_id']) ? explode('#', $user['riot_id'])[0] : $user['name']; ?></div>
                    <div class="user-tag">#<?php echo !empty($user['riot_id']) && strpos($user['riot_id'], '#') !== false ? explode('#', $user['riot_id'])[1] : 'TAG'; ?></div>

                    <div class="rank-display">
                        <i class="fas fa-medal" style="color:#ffd700;"></i>
                        <span class="rank-txt"><?php echo $rank; ?></span>
                    </div>

                    <a href="edit_profile.php" class="btn-edit">
                        <i class="fas fa-cog"></i> Edit Data
                    </a>
                </div>
            </div>

            <div class="right-col">
                
                <a href="<?php echo $has_team ? 'detail_tim.php?id='.$user['fav_team_id'] : '#'; ?>" style="text-decoration:none;">
                    <div class="info-box my-team-card">
                        <div class="team-info">
                            <span class="fav-team-name"><?php echo $team_name; ?></span>
                            <div class="fav-team-region"><?php echo $has_team ? "Official Fanbase" : "Belum memilih tim"; ?></div>
                        </div>
                        <img src="<?php echo $team_logo; ?>" class="team-logo-bg" onerror="this.src='assets/images/logoValo.png'">
                    </div>
                </a>

                <div style="height:20px;"></div>

                <div class="info-grid">
                    <div class="info-box">
                        <span class="box-label">Agent Favorit</span>
                        <div class="box-value"><?php echo $agent_name; ?></div>
                        <div style="position:absolute; right:-10px; bottom:-10px; opacity:0.1; font-size:60px; font-weight:900;">
                            <?php echo substr($agent_name , 0, 1); ?>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="box-label">Discord</span>
                        <div class="box-value" style="font-size:16px;">
                            <?php echo !empty($user['discord_username']) ? $user['discord_username'] : '-'; ?>
                        </div>
                        <i class="fab fa-discord" style="position:absolute; right:15px; top:15px; color:#5865F2; font-size:24px;"></i>
                    </div>

                    <div class="info-box">
                        <span class="box-label">Email Terdaftar</span>
                        <div class="box-value" style="font-size:14px;"><?php echo $user['email']; ?></div>
                    </div>
                    
                    <div class="info-box">
                        <span class="box-label">Status Akun</span>
                        <div class="box-value" style="color:#10b981;">ACTIVE</div>
                    </div>

                    <div class="bio-box">
                        "<?php echo !empty($user['bio']) ? $user['bio'] : 'No bio yet.'; ?>"
                    </div>
                </div>

            </div>

        </div>
    </div>

    <?php include 'config/footer.php'; ?>

</body>
</html>