<?php
session_start();
include "config/koneksi.php";

// 1. Ambil Keyword dari URL
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$keyword_db = "%" . $keyword . "%"; // Tambahin % buat fitur LIKE di SQL

// 2. Logic Pencarian (Cuma jalan kalau ada keyword)
$teams = [];
$players = [];

if(!empty($keyword)) {
    // A. CARI DI TABEL TEAM
    $query_team = $koneksi->prepare("SELECT * FROM team WHERE team_name LIKE ?");
    $query_team->bind_param("s", $keyword_db);
    $query_team->execute();
    $res_team = $query_team->get_result();

    // B. CARI DI TABEL PLAYERS
    $query_player = $koneksi->prepare("SELECT p.*, t.team_name, t.logo as team_logo 
                                       FROM players p 
                                       LEFT JOIN team t ON p.team_id = t.team_id 
                                       WHERE p.in_game_name LIKE ? OR p.player_name LIKE ?");
    $query_player->bind_param("ss", $keyword_db, $keyword_db); // Cek IGN atau Nama Asli
    $query_player->execute();
    $res_player = $query_player->get_result();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search: <?php echo htmlspecialchars($keyword); ?></title>
    
    <?php include 'config/head.php'; ?>
    <style>
        .search-header {
            text-align: center;
            padding: 60px 20px 40px;
            border-bottom: 1px solid #333;
            background: linear-gradient(to bottom, #0f1923, #1b2733);
        }
        .search-title { font-size: 24px; color: #888; text-transform: uppercase; letter-spacing: 1px; }
        .search-keyword { font-size: 48px; font-weight: 900; color: white; margin-top: 10px; }
        .highlight { color: #ff4655; }

        .result-section { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .section-label { 
            font-size: 20px; font-weight: 800; color: white; 
            border-left: 5px solid #ff4655; padding-left: 15px; margin-bottom: 25px; 
            display: flex; align-items: center; justify-content: space-between;
        }
        .count-badge { background: #333; color: #ccc; font-size: 12px; padding: 4px 10px; border-radius: 4px; }

        /* GRID SYSTEM */
        .result-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;
        }

        /* TEAM CARD */
        .result-card-team {
            background: #1b2733; border: 1px solid #333; padding: 20px; border-radius: 4px;
            display: flex; flex-direction: column; align-items: center; text-decoration: none;
            transition: 0.3s;
        }
        .result-card-team:hover { transform: translateY(-5px); border-color: #ff4655; background: #24303c; }
        .res-t-logo { width: 80px; height: 80px; object-fit: contain; margin-bottom: 15px; }
        .res-t-name { font-size: 18px; font-weight: 800; color: white; text-transform: uppercase; }

        /* PLAYER CARD */
        .result-card-player {
            background: #1b2733; border: 1px solid #333; border-radius: 4px; overflow: hidden;
            display: flex; text-decoration: none; transition: 0.3s; height: 100px;
        }
        .result-card-player:hover { transform: translateX(5px); border-color: #ff4655; }
        .res-p-img-box { width: 100px; background: #0f1923; position: relative; }
        .res-p-img { width: 100%; height: 100%; object-fit: cover; object-position: top; }
        .res-p-info { padding: 15px; display: flex; flex-direction: column; justify-content: center; width: 100%; }
        .res-p-ign { font-size: 20px; font-weight: 900; color: white; line-height: 1; }
        .res-p-name { font-size: 12px; color: #888; margin-bottom: 5px; }
        .res-p-team { display: flex; align-items: center; gap: 5px; font-size: 12px; color: #ff4655; font-weight: bold; }

        /* EMPTY STATE */
        .empty-state { text-align: center; padding: 50px; color: #666; font-size: 18px; }
        .empty-icon { font-size: 60px; margin-bottom: 20px; display: block; opacity: 0.5; }
    </style>
</head>
<body>

    <?php include 'config/navbar.php'; ?>

    <div class="search-header">
        <div class="search-title">Hasil Pencarian Untuk</div>
        <div class="search-keyword">" <span class="highlight"><?php echo htmlspecialchars($keyword); ?></span> "</div>
    </div>

    <div class="result-section">
        <div class="section-label">
            TIM DITEMUKAN 
            <?php if(isset($res_team)) echo "<span class='count-badge'>".$res_team->num_rows."</span>"; ?>
        </div>

        <div class="result-grid">
            <?php 
            if(isset($res_team) && $res_team->num_rows > 0) {
                while($t = $res_team->fetch_assoc()) { 
            ?>
                <a href="detail_tim.php?id=<?php echo $t['team_id']; ?>" class="result-card-team">
                    <img src="<?php echo $t['logo']; ?>" class="res-t-logo">
                    <span class="res-t-name"><?php echo $t['team_name']; ?></span>
                </a>
            <?php 
                } 
            } else {
                echo '<div style="color:#666; font-style:italic;">Tidak ada tim yang cocok.</div>';
            }
            ?>
        </div>
    </div>

    <div class="result-section">
        <div class="section-label">
            PEMAIN DITEMUKAN
            <?php if(isset($res_player)) echo "<span class='count-badge'>".$res_player->num_rows."</span>"; ?>
        </div>

        <div class="result-grid">
            <?php 
            if(isset($res_player) && $res_player->num_rows > 0) {
                while($p = $res_player->fetch_assoc()) { 
                    $p_img = !empty($p['photo']) ? $p['photo'] : 'https://valorantesports.com/static/img/player-placeholder.png';
            ?>
                <a href="detail_tim.php?id=<?php echo $p['team_id']; ?>" class="result-card-player">
                    <div class="res-p-img-box">
                        <img src="<?php echo $p_img; ?>" class="res-p-img">
                    </div>
                    <div class="res-p-info">
                        <span class="res-p-ign"><?php echo $p['in_game_name']; ?></span>
                        <span class="res-p-name"><?php echo $p['player_name']; ?></span>
                        <div class="res-p-team">
                            <img src="<?php echo $p['team_logo']; ?>" width="15"> 
                            <?php echo $p['team_name']; ?>
                        </div>
                    </div>
                </a>
            <?php 
                } 
            } else {
                echo '<div style="color:#666; font-style:italic;">Tidak ada pemain yang cocok.</div>';
            }
            ?>
        </div>
    </div>
    
    <?php if((!isset($res_team) || $res_team->num_rows == 0) && (!isset($res_player) || $res_player->num_rows == 0)): ?>
        <div class="empty-state">
            <i class="fas fa-ghost empty-icon"></i>
            Yah, data tidak ditemukan!!.<br>Coba kata kunci lain kayak "RRQ" atau "f0rsakeN".
        </div>
    <?php endif; ?>

    <?php include 'config/footer.php'; ?>

</body>
</html>