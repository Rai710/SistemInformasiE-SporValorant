<?php
session_start();
include "../config/koneksi.php";

// 1. CEK AKSES ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// 2. AMBIL DAFTAR EVENT (Untuk Filter Dropdown)
$q_events = $koneksi->query("SELECT * FROM events ORDER BY event_date DESC");

// 3. TENTUKAN EVENT ID YANG DIPILIH
if (isset($_GET['event_id'])) {
    $selected_event = (int)$_GET['event_id'];
} else {
    // Default: Ambil event dengan ID terbesar (paling baru dibuat)
    $q_latest = $koneksi->query("SELECT event_id FROM events ORDER BY event_id DESC LIMIT 1");
    $latest = $q_latest->fetch_assoc();
    $selected_event = $latest['event_id'] ?? 1;
}

// 4. AMBIL DATA MATCH
$sql = "SELECT m.*, 
               t1.team_name as t1_name, t1.logo as t1_logo,
               t2.team_name as t2_name, t2.logo as t2_logo
        FROM match_esports m
        LEFT JOIN team t1 ON m.team1_id = t1.team_id
        LEFT JOIN team t2 ON m.team2_id = t2.team_id
        WHERE m.event_id = $selected_event
        ORDER BY m.match_date ASC, m.match_id ASC";

$matches = $koneksi->query($sql);

// Debugging: Cek error SQL jika ada
if (!$matches) {
    die("Error Database: " . $koneksi->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Match Room - Admin</title>
    <link rel="icon" type="image/png" href="../assets/images/logoValo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    
    <style>
        .filter-bar { 
            display: flex; gap: 15px; margin-bottom: 30px; align-items: center; 
            background: #1b2733; padding: 20px; border-radius: 8px; border: 1px solid #333; 
        }
        .filter-select { 
            background: #0f1923; border: 1px solid #555; color: white; 
            padding: 10px; border-radius: 4px; min-width: 200px; 
        }
        .btn-filter { 
            background: #ff4655; color: white; border: none; 
            padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; 
        }
        
        .match-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .match-table th { 
            text-align: left; padding: 15px; background: #263542; 
            color: #aaa; font-size: 12px; text-transform: uppercase; 
        }
        .match-table td { 
            padding: 15px; border-bottom: 1px solid #333; 
            color: white; vertical-align: middle; 
        }
        .match-table tr:hover { background: rgba(255,255,255,0.02); }
        
        .team-cell { display: flex; align-items: center; gap: 10px; font-weight: bold; }
        /* Fix ukuran gambar biar gak gepeng */
        .team-cell img { width: 30px; height: 30px; object-fit: contain; background: rgba(0,0,0,0.3); border-radius: 4px; padding: 2px; }
        
        .score-badge { 
            background: #0f1923; border: 1px solid #555; 
            padding: 5px 10px; border-radius: 4px; 
            font-family: monospace; font-size: 14px; 
        }
        
        .status-live { color: #ff4655; font-weight: bold; }
        .status-done { color: #10b981; font-weight: bold; }
        .status-up { color: #aaa; }

        .btn-action { 
            display: inline-flex; align-items: center; gap: 8px;
            background: transparent; color: #ccc; 
            padding: 8px 16px; border-radius: 4px; text-decoration: none; 
            font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
            border: 1px solid #555; transition: all 0.2s ease-in-out;
        }
        .btn-action:hover { 
            border-color: #ff4655; background: #ff4655; color: white; 
            box-shadow: 0 0 15px rgba(255, 70, 85, 0.4); transform: translateY(-2px);
        }
        .btn-action i { font-size: 12px; }
    </style>
</head>
<body class="admin-body">

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header-bar">
            <h2 class="page-title">MATCH ROOM</h2>
            <div class="admin-profile">
                <span><?php echo $_SESSION['username']; ?></span>
                <img src="../<?php echo $_SESSION['avatar'] ?? 'assets/images/default_agent.png'; ?>" class="admin-avatar">
            </div>
        </div>

        <form method="GET" class="filter-bar">
            <label style="color:#ccc; font-weight:bold;">PILIH EVENT:</label>
            <select name="event_id" class="filter-select">
                <?php 

                $q_events->data_seek(0); 
                while($ev = $q_events->fetch_assoc()): 
                ?>
                    <option value="<?php echo $ev['event_id']; ?>" <?php echo ($selected_event == $ev['event_id']) ? 'selected' : ''; ?>>
                        <?php echo $ev['event_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn-filter">LOAD DATA</button>
            <a href="add_match.php" class="btn-filter" style="margin-left:auto; background:#10b981;">
                <i class="fas fa-plus-circle"></i> ADD NEW MATCH
            </a>
        </form>
        <div style="display:flex; gap:10px;">
            <a href="../action/generate_playoff.php" class="btn-filter" style="background:#7289da; text-decoration:none; display:flex; align-items:center; gap:8px;" onclick="return confirm('Yakin mau generate ulang? Jadwal Playoff lama akan dihapus!')">
                <i class="fas fa-bolt"></i> GENERATE PLAYOFF
            </a>
            
            <a href="add_match.php" class="btn-filter" style="background:#10b981; text-decoration:none;">
                <i class="fas fa-plus"></i> ADD MANUAL
            </a>
        </div>

        <div style="background: #1b2733; border-radius: 8px; border: 1px solid #333; overflow: hidden;">
            <table class="match-table">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th width="120">DATE</th>
                        <th width="80">WEEK</th>
                        <th>MATCHUP</th>
                        <th width="100" style="text-align:center;">SCORE</th>
                        <th width="100">STATUS</th>
                        <th width="100">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($matches->num_rows > 0): ?>
                        <?php while($m = $matches->fetch_assoc()): 
                            $is_finished = ($m['team1_score'] > 0 || $m['team2_score'] > 0);


                            // Cek Tim 1
                            $logo1 = $m['t1_logo'];

                            if (empty($logo1)) $src1 = "../assets/images/default.png";
                            elseif (strpos($logo1, 'http') === 0) $src1 = $logo1;
                            else $src1 = "../" . $logo1;

                            // Cek Tim 2
                            $logo2 = $m['t2_logo'];
                            if (empty($logo2)) $src2 = "../assets/images/default.png";
                            elseif (strpos($logo2, 'http') === 0) $src2 = $logo2;
                            else $src2 = "../" . $logo2;
                            // ===========================================
                        ?>
                        <tr>
                            <td>#<?php echo $m['match_id']; ?></td>
                            <td style="color:#ccc;"><?php echo date('d M', strtotime($m['match_date'])); ?></td>
                            <td><?php echo $m['match_week'] ? 'Week '.$m['match_week'] : '-'; ?></td>
                            
                            <td>
                                <div style="display:flex; align-items:center; gap:15px;">
                                    <div class="team-cell">
                                        <img src="<?php echo $src1; ?>"> 
                                        <?php echo $m['t1_name']; ?>
                                    </div>
                                    <span style="color:#666; font-size:12px;">VS</span>
                                    <div class="team-cell">
                                        <img src="<?php echo $src2; ?>"> 
                                        <?php echo $m['t2_name']; ?>
                                    </div>
                                </div>
                            </td>

                            <td style="text-align:center;">
                                <span class="score-badge">
                                    <?php echo $m['team1_score']; ?> - <?php echo $m['team2_score']; ?>
                                </span>
                            </td>

                            <td>
                                <?php if($is_finished): ?>
                                    <span class="status-done">FINISHED</span>
                                <?php else: ?>
                                    <span class="status-up">UPCOMING</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="edit_match.php?id=<?php echo $m['match_id']; ?>" class="btn-action">
                                    <i class="fas fa-edit"></i> Update
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center; padding:30px; color:#666;">Belum ada jadwal untuk event ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>