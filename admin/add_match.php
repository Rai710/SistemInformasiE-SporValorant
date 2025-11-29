<?php
session_start();
include "../config/koneksi.php";

// Cek Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Ambil Daftar Event (Biar admin milih ini match buat event mana)
$q_events = $koneksi->query("SELECT * FROM events ORDER BY event_date DESC");

// Ambil Daftar Tim
$q_teams = $koneksi->query("SELECT * FROM team ORDER BY team_name ASC");
$teams = $q_teams->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Add New Match</title>
    <link rel="icon" type="image/png" href="../assets/images/logoValo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .form-box { max-width: 700px; margin: 0 auto; background: #1b2733; padding: 40px; border-radius: 8px; border: 1px solid #333; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #ccc; margin-bottom: 8px; font-weight: bold; }
        .form-control { width: 100%; background: #0f1923; border: 1px solid #555; color: white; padding: 12px; border-radius: 4px; }
        .btn-submit { background: #ff4655; color: white; border: none; padding: 12px 30px; border-radius: 4px; font-weight: bold; cursor: pointer; width: 100%; }
        .btn-submit:hover { background: #d93c48; }
        
        .vs-row { display: flex; gap: 20px; align-items: center; }
        .vs-text { font-weight: 900; color: #555; font-size: 24px; }
    </style>
</head>
<body class="admin-body">

<?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header-bar">
            <h2 class="page-title">ADD NEW MATCH</h2>
        </div>

        <div class="form-box">
            <form action="../action/insert_match.php" method="POST">
                
                <div class="form-group">
                    <label>Tournament Event</label>
                    <select name="event_id" class="form-control" required>
                        <?php while($ev = $q_events->fetch_assoc()): ?>
                            <option value="<?php echo $ev['event_id']; ?>"><?php echo $ev['event_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                        <?php if(isset($_GET['msg'])): ?>
                <div style="margin-bottom: 20px; padding: 15px; border-radius: 4px; font-weight: bold; text-align: center; 
                    <?php echo ($_GET['msg'] == 'success_add') ? 'background:rgba(16,185,129,0.2); color:#10b981; border:1px solid #10b981;' : 'background:rgba(255,70,85,0.2); color:#ff4655; border:1px solid #ff4655;'; ?>">
                    
                        <?php 
                        if($_GET['msg'] == 'same_team') echo "Error: Tim 1 dan Tim 2 tidak boleh sama!";
                        elseif($_GET['msg'] == 'week_limit') echo "Error: Maksimal hanya sampai Week 5!";
                        
                        elseif($_GET['msg'] == 'diff_group') echo "Error: Tim beda grup! Di Group Stage, mereka harus satu grup.";
                        elseif($_GET['msg'] == 'team_not_registered') echo "Error: Salah satu tim tidak terdaftar di Event/Tahun ini.";
                        // --------------
                        elseif($_GET['msg'] == 'limit_t1') echo "Error: Tim 1 sudah bermain 5 kali (Jatah Habis)!";
                        elseif($_GET['msg'] == 'limit_t2') echo "Error: Tim 2 sudah bermain 5 kali (Jatah Habis)!";
                        elseif($_GET['msg'] == 'error_db') echo "Error Database. Gagal menyimpan.";
                        ?>
                </div>
            <?php endif; ?>
                <div class="form-group">
                    <label>Matchup</label>
                    <div class="vs-row">
                        <select name="team1_id" class="form-control" required>
                            <option value="">-- Select Team 1 --</option>
                            <?php foreach($teams as $t): ?>
                                <option value="<?php echo $t['team_id']; ?>"><?php echo $t['team_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <span class="vs-text">VS</span>
                        
                        <select name="team2_id" class="form-control" required>
                            <option value="">-- Select Team 2 --</option>
                            <?php foreach($teams as $t): ?>
                                <option value="<?php echo $t['team_id']; ?>"><?php echo $t['team_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="vs-row">
                        <div style="flex:1;">
                            <label>Date</label>
                            <input type="date" name="match_date" class="form-control" required>
                        </div>
                        <div style="flex:1;">
                            <label>Stage</label>
                            <select name="stage" class="form-control">
                                <option value="Group Stage">Group Stage</option>
                                <option value="Playoffs">Playoffs</option>
                                <option value="Grand Final">Grand Final</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="vs-row">
                        <div style="flex:1;">
                            <label>Group (Optional)</label>
                            <select name="group_name" class="form-control">
                                <option value="">- None -</option>
                                <option value="Group A">Group A</option>
                                <option value="Group B">Group B</option>
                            </select>
                        </div>
                        <div style="flex:1;">
                            <label>Week (Optional)</label>
                            <input type="number" name="match_week" class="form-control" min="1" max="10" placeholder="1">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">CREATE MATCH</button>
            </form>
        </div>
    </div>
    <?php if (isset($_SESSION['error_msg'])): ?>
    <div style="background: rgba(255, 70, 85, 0.2); color: #ff4655; border: 1px solid #ff4655; padding: 15px; border-radius: 4px; margin-bottom: 20px; text-align: center; font-weight: bold;">
        <i class="fas fa-exclamation-circle"></i> 
        <?php 
            echo $_SESSION['error_msg']; 
            unset($_SESSION['error_msg']); // Hapus pesan biar gak muncul lagi pas refresh
        ?>
    </div>
<?php endif; ?>

</body>
</html>