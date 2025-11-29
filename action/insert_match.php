<?php
session_start();
include "../config/koneksi.php";

// Cek Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Kita bungkus semua proses berbahaya dalam TRY
    try {
        $event_id   = (int)$_POST['event_id'];
        $team1_id   = (int)$_POST['team1_id'];
        $team2_id   = (int)$_POST['team2_id'];
        $match_date = $_POST['match_date'];
        $stage      = $_POST['stage'];
        $match_week = !empty($_POST['match_week']) ? (int)$_POST['match_week'] : NULL;
        $group_name_insert = NULL; 

        // --- VALIDASI 1: DASAR ---
        if ($team1_id == $team2_id) {
            throw new Exception("Tim 1 dan Tim 2 gak boleh sama, kocak!");
        }

        if ($match_week > 5) {
            // Kecuali Grand Final, mungkin mau lebih dari 5 week? Sesuaikan aja.
            throw new Exception("Week maksimal cuma sampai 5 bro!");
        }

        // --- VALIDASI 2: KHUSUS GROUP STAGE ---
        if ($stage == 'Group Stage') {
            
            // Helper Function: Ambil Grup Tim
            function getGroup($conn, $tid, $evid) {
                $q = $conn->prepare("SELECT group_name FROM event_teams WHERE event_id = ? AND team_id = ?");
                $q->bind_param("ii", $evid, $tid);
                $q->execute();
                $res = $q->get_result()->fetch_assoc();
                return $res ? $res['group_name'] : false;
            }

            $group1 = getGroup($koneksi, $team1_id, $event_id);
            $group2 = getGroup($koneksi, $team2_id, $event_id);

            // Cek Registrasi
            if ($group1 === false || $group2 === false) {
                throw new Exception("Salah satu tim belum terdaftar di Event ini!");
            }

            // Cek Grup Sama
            if ($group1 !== $group2) {
                throw new Exception("Gak bisa adu tim beda grup di fase Group Stage!");
            }

            // Cek Kuota Main (Max 5x)
            function countMain($conn, $tid, $evid) {
                $q = $conn->prepare("SELECT COUNT(*) as total FROM match_esports WHERE event_id = ? AND stage = 'Group Stage' AND (team1_id = ? OR team2_id = ?)");
                $q->bind_param("iii", $evid, $tid, $tid);
                $q->execute();
                return $q->get_result()->fetch_assoc()['total'];
            }

            if (countMain($koneksi, $team1_id, $event_id) >= 5) {
                throw new Exception("Tim 1 udah main 5 kali, jatah abis!");
            }
            if (countMain($koneksi, $team2_id, $event_id) >= 5) {
                throw new Exception("Tim 2 udah main 5 kali, jatah abis!");
            }
            
            // Kalau lolos, set group name buat di-insert
            $group_name_insert = $group1;
        }

        // --- VALIDASI 3: INSERT KE DATABASE ---
        $sql = "INSERT INTO match_esports (team1_id, team2_id, match_date, event_id, stage, group_name, match_week, team1_score, team2_score) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0)";
        
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("iissssi", $team1_id, $team2_id, $match_date, $event_id, $stage, $group_name_insert, $match_week);

        if (!$stmt->execute()) {
            throw new Exception("Gagal simpan ke database: " . $stmt->error);
        }
        $stmt->close();

        // SUKSES? 
        $_SESSION['success_msg'] = "Match berhasil dibuat!";
        header("Location: ../admin/manage_matches.php");
        exit();

    } catch (Exception $e) {
        // Tangkap pesan error dari 'throw' di atas
        $_SESSION['error_msg'] = $e->getMessage();
        
        // Balikin ke halaman Add Match
        header("Location: ../admin/add_match.php");
        exit();
    }
}
?>