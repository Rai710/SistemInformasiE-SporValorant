<?php
session_start();
include "config/koneksi.php";

// Cek Login (Opsional: Kalau mau leaderboard bisa dilihat publik, hapus aja ini)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// QUERY: Ambil 50 User dengan Poin Tertinggi
// Diurutkan berdasarkan Poin Terbesar, lalu Nama (A-Z) kalau poin sama
$sql = "SELECT name, avatar_image, total_pickem_points, rank_tier, favorite_team_id 
        FROM users 
        ORDER BY total_pickem_points DESC, name ASC 
        LIMIT 50";

$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Global Leaderboard - VCT Pick'em</title>
    <?php include 'config/head.php'; ?>
    <style>
        .lb-container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        
        .lb-header { text-align: center; margin-bottom: 40px; }
        .lb-title { font-size: 48px; font-weight: 900; color: white; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; text-shadow: 0 0 20px rgba(255,70,85,0.5); }
        .lb-desc { color: #aaa; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }

        /* TABEL LEADERBOARD */
        .lb-table-wrapper {
            background: #1b2733; border-radius: 8px; border: 1px solid #333; overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }
        
        .lb-table { width: 100%; border-collapse: collapse; }
        
        .lb-table th { 
            background: #263542; color: #888; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
            padding: 20px; text-align: left;
        }
        
        .lb-table td { padding: 15px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); color: white; font-weight: 600; vertical-align: middle; }
        .lb-table tr:last-child td { border-bottom: none; }
        .lb-table tr:hover { background: rgba(255,255,255,0.02); }

        /* KOLOM RANK */
        .rank-num { font-size: 18px; font-weight: 900; color: #666; width: 50px; text-align: center; display: inline-block; }
        
        /* BADGES JUARA (1-3) */
        .rank-1 .rank-num { color: #ffd700; text-shadow: 0 0 10px rgba(255, 215, 0, 0.5); font-size: 24px; } /* Emas */
        .rank-2 .rank-num { color: #c0c0c0; text-shadow: 0 0 10px rgba(192, 192, 192, 0.5); font-size: 22px; } /* Perak */
        .rank-3 .rank-num { color: #cd7f32; text-shadow: 0 0 10px rgba(205, 127, 50, 0.5); font-size: 20px; } /* Perunggu */
        
        /* ROW HIGHLIGHT JUARA */
        .rank-1 { background: linear-gradient(90deg, rgba(255, 215, 0, 0.1), transparent); border-left: 4px solid #ffd700; }
        .rank-2 { background: linear-gradient(90deg, rgba(192, 192, 192, 0.05), transparent); border-left: 4px solid #c0c0c0; }
        .rank-3 { background: linear-gradient(90deg, rgba(205, 127, 50, 0.05), transparent); border-left: 4px solid #cd7f32; }

        /* USER PROFILE */
        .user-cell { display: flex; align-items: center; gap: 15px; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #333; }
        .user-name { font-size: 16px; font-weight: 700; }
        .user-rank-tier { font-size: 11px; color: #aaa; text-transform: uppercase; display: block; margin-top: 2px; }

        /* POINTS */
        .points-cell { font-size: 20px; font-weight: 900; color: #ff4655; text-align: right; }
        
        /* BADGE PEMAIN */
        .tier-badge { padding: 2px 8px; border-radius: 4px; font-size: 10px; background: #333; color: #ccc; margin-left: 10px; vertical-align: middle; }

        /* YOUR RANK (Sticky Bottom jika mau, tapi ini versi standar dulu) */
        .my-rank-bar {
            margin-top: 30px; padding: 15px; background: #ff4655; color: white; border-radius: 6px;
            text-align: center; font-weight: bold;
        }
    </style>
</head>
<body>

<?php include 'config/navbar.php'; ?>

<div class="lb-container">
    
    <div class="lb-header">
        <div class="lb-title">TOP PREDICTORS</div>
        <div class="lb-desc">Siapa peramal terhebat di VCT Pacific?</div>
    </div>

    <div class="lb-table-wrapper">
        <table class="lb-table">
            <thead>
                <tr>
                    <th width="80" style="text-align:center;">RANK</th>
                    <th>AGENT NAME</th>
                    <th width="150" style="text-align:right;">TOTAL POINTS</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $rank = 1;
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        // Tentukan Class buat Top 3
                        $rankClass = "";
                        if ($rank == 1) $rankClass = "rank-1";
                        elseif ($rank == 2) $rankClass = "rank-2";
                        elseif ($rank == 3) $rankClass = "rank-3";
                        
                        // Cek Avatar
                        $avatar = !empty($row['avatar_image']) ? $row['avatar_image'] : 'assets/images/default_agent.png';
                ?>
                    <tr class="<?php echo $rankClass; ?>">
                        <td style="text-align:center;">
                            <?php if($rank == 1) echo "👑"; ?>
                            <span class="rank-num"><?php echo $rank; ?></span>
                        </td>
                        <td>
                            <div class="user-cell">
                                <img src="<?php echo $avatar; ?>" class="user-avatar" onerror="this.src='assets/images/default_agent.png'">
                                <div>
                                    <div class="user-name">
                                        <?php echo htmlspecialchars($row['name']); ?>
                                        <?php if($rank <= 3): ?>
                                            <span class="tier-badge" style="background:#ffd700; color:black;">ORACLE</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="user-rank-tier"><?php echo $row['rank_tier']; ?> Player</span>
                                </div>
                            </div>
                        </td>
                        <td class="points-cell">
                            <?php echo $row['total_pickem_points']; ?> PTS
                        </td>
                    </tr>
                <?php 
                        $rank++;
                    }
                } else {
                    echo "<tr><td colspan='3' style='text-align:center; padding:30px; color:#666;'>Belum ada data prediksi. Jadilah yang pertama!</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px; text-align: center; color: #666; font-size: 13px;">
        *Leaderboard diupdate secara real-time setiap match selesai.<br>
        Point: Winner (+5), Correct Score (+15).
    </div>

</div>

<?php include 'config/footer.php'; ?>

</body>
</html>