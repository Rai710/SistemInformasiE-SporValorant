<?php
// action/generate_playoff.php
session_start();
include "../config/koneksi.php";

// 1. CEK ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit();
}

// ==========================================
// BAGIAN 1: PROSES GENERATE (JALAN KALAU DI-POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $event_id   = (int)$_POST['event_id'];
    $start_date = $_POST['start_date'];

    if (empty($event_id) || empty($start_date)) {
        echo "<script>alert('Pilih Event dan Tanggal dulu!'); window.history.back();</script>";
        exit();
    }

    try {
        $koneksi->begin_transaction();

        // 1. SIAPKAN TBD
        $koneksi->query("INSERT IGNORE INTO team (team_id, team_name, logo) VALUES (999, 'TBD', 'assets/images/default.png')");
        $tbd = 999;

        // 2. SAFETY DELETE (Hapus Playoff Event Terpilih Saja)
        $koneksi->query("DELETE FROM match_esports WHERE event_id = $event_id AND stage IN ('Playoffs', 'Grand Final')");

        // 3. HITUNG KLASEMEN (Logic Klasemen)
        $sql_teams = "SELECT t.team_id, et.group_name FROM team t JOIN event_teams et ON t.team_id = et.team_id WHERE et.event_id = $event_id";
        $res = $koneksi->query($sql_teams);
        
        $standings = ['Group A' => [], 'Group B' => []];
        while($t = $res->fetch_assoc()){ 
            if(empty($t['group_name'])) continue; 
            $standings[$t['group_name']][$t['team_id']] = ['id' => (int)$t['team_id'], 'win' => 0, 'diff' => 0]; 
        }

        $res_m = $koneksi->query("SELECT * FROM match_esports WHERE stage = 'Group Stage' AND event_id = $event_id");
        while($m = $res_m->fetch_assoc()){
            if ($m['team1_score'] == 0 && $m['team2_score'] == 0) continue;
            $id1 = $m['team1_id']; $id2 = $m['team2_id'];
            $diff = $m['team1_score'] - $m['team2_score'];
            $win1 = $m['team1_score'] > $m['team2_score'];
            
            // Cek Grup (Manual check via array key biar aman)
            foreach(['Group A', 'Group B'] as $g) {
                if(isset($standings[$g][$id1])) { $standings[$g][$id1]['diff'] += $diff; if($win1) $standings[$g][$id1]['win']++; }
                if(isset($standings[$g][$id2])) { $standings[$g][$id2]['diff'] -= $diff; if(!$win1) $standings[$g][$id2]['win']++; }
            }
        }

        function cmp($a, $b) { if ($a['win'] == $b['win']) return $b['diff'] - $a['diff']; return $b['win'] - $a['win']; }
        if (!empty($standings['Group A'])) usort($standings['Group A'], "cmp");
        if (!empty($standings['Group B'])) usort($standings['Group B'], "cmp");

        // Ambil Posisi (Pake TBD kalau tim kurang)
        $A1 = $standings['Group A'][0]['id'] ?? $tbd; $A2 = $standings['Group A'][1]['id'] ?? $tbd; 
        $A3 = $standings['Group A'][2]['id'] ?? $tbd; $A4 = $standings['Group A'][3]['id'] ?? $tbd; 
        $B1 = $standings['Group B'][0]['id'] ?? $tbd; $B2 = $standings['Group B'][1]['id'] ?? $tbd; 
        $B3 = $standings['Group B'][2]['id'] ?? $tbd; $B4 = $standings['Group B'][3]['id'] ?? $tbd;

        // 4. INSERT MATCH (Format Tanggal Dinamis)
        $sql_in = "INSERT INTO match_esports (team1_id, team2_id, match_date, event_id, stage, team1_score, team2_score, match_week) VALUES (?, ?, ?, ?, ?, 0, 0, ?)";
        $stmt = $koneksi->prepare($sql_in);
        $st = 'Playoffs';

        // WEEK 1: UB QF
        $d = $start_date; $w = 1;
        $stmt->bind_param("iisssi", $A2, $B3, $d, $event_id, $st, $w); $stmt->execute();
        $stmt->bind_param("iisssi", $B2, $A3, $d, $event_id, $st, $w); $stmt->execute();

        // WEEK 2: UB SEMI
        $d = date('Y-m-d', strtotime($start_date . ' + 2 days')); $w = 2;
        $stmt->bind_param("iisssi", $A1, $tbd, $d, $event_id, $st, $w); $stmt->execute();
        $stmt->bind_param("iisssi", $B1, $tbd, $d, $event_id, $st, $w); $stmt->execute();

        // WEEK 4: LB R1
        $d = date('Y-m-d', strtotime($start_date . ' + 1 day')); $w = 4;
        $stmt->bind_param("iisssi", $tbd, $B4, $d, $event_id, $st, $w); $stmt->execute();
        $stmt->bind_param("iisssi", $tbd, $A4, $d, $event_id, $st, $w); $stmt->execute();

        // WEEK 5: LB R2
        $d = date('Y-m-d', strtotime($start_date . ' + 3 days')); $w = 5;
        $stmt->bind_param("iisssi", $tbd, $tbd, $d, $event_id, $st, $w); $stmt->execute();
        $stmt->bind_param("iisssi", $tbd, $tbd, $d, $event_id, $st, $w); $stmt->execute();

        // WEEK 3: UB FINAL
        $d = date('Y-m-d', strtotime($start_date . ' + 5 days')); $w = 3;
        $stmt->bind_param("iisssi", $tbd, $tbd, $d, $event_id, $st, $w); $stmt->execute();

        // WEEK 6: LB SEMI
        $d = date('Y-m-d', strtotime($start_date . ' + 4 days')); $w = 6;
        $stmt->bind_param("iisssi", $tbd, $tbd, $d, $event_id, $st, $w); $stmt->execute();

        // WEEK 7: LB FINAL
        $d = date('Y-m-d', strtotime($start_date . ' + 6 days')); $w = 7;
        $stmt->bind_param("iisssi", $tbd, $tbd, $d, $event_id, $st, $w); $stmt->execute();

        // WEEK 8: GRAND FINAL
        $st_gf = 'Grand Final'; $d = date('Y-m-d', strtotime($start_date . ' + 7 days')); $w = 8;
        $stmt->bind_param("iisssi", $tbd, $tbd, $d, $event_id, $st_gf, $w); $stmt->execute();

        $koneksi->commit();
        
        // Redirect balik ke manage matches dengan filter event yang baru digenerate
        header("Location: ../admin/manage_matches.php?event_id=$event_id&msg=generated_success");
        exit();

    } catch (Exception $e) {
        $koneksi->rollback();
        die("Error: " . $e->getMessage());
    }
}
    

// BAGIAN 2: TAMPILAN FORM (KALAU BELUM DI-POST)
// Ambil daftar event buat dropdown
$q_events = $koneksi->query("SELECT * FROM events ORDER BY event_date DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Generate Playoff</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        body { background: #0f1923; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .gen-box { background: #1b2733; padding: 40px; border-radius: 8px; border: 1px solid #333; width: 400px; text-align: center; }
        .form-control { width: 100%; padding: 10px; margin: 10px 0; background: #0f1923; border: 1px solid #555; color: white; border-radius: 4px; }
        .btn-gen { background: #ff4655; color: white; border: none; padding: 12px; width: 100%; font-weight: bold; cursor: pointer; border-radius: 4px; margin-top: 10px; }
        .btn-gen:hover { background: #d93c48; }
        .warning { color: #ffcccc; font-size: 12px; margin-bottom: 20px; display: block; }
    </style>
</head>
<body>

    <div class="gen-box">
        <h2 style="color: #ff4655;">GENERATE BRACKET</h2>
        <p>Pilih Event & Tanggal Mulai Playoff</p>
        
        <form method="POST">
            <label style="display:block; text-align:left; font-weight:bold; font-size:14px;">Pilih Turnamen:</label>
            <select name="event_id" class="form-control" required>
                <?php while($ev = $q_events->fetch_assoc()): ?>
                    <option value="<?php echo $ev['event_id']; ?>"><?php echo $ev['event_name']; ?></option>
                <?php endwhile; ?>
            </select>

            <label style="display:block; text-align:left; font-weight:bold; font-size:14px; margin-top:10px;">Tanggal Mulai Playoff:</label>
            <input type="date" name="start_date" class="form-control" required>

            <span class="warning">
                <i class="fas fa-exclamation-triangle"></i> PERINGATAN: <br>
                Jadwal Playoff lama di event ini akan DIHAPUS & DI-RESET!
            </span>

            <button type="submit" class="btn-gen">GENERATE SEKARANG</button>
            <a href="../admin/manage_matches.php" style="display:block; margin-top:15px; color:#aaa; text-decoration:none;">Batal</a>
        </form>
    </div>

</body>
</html>