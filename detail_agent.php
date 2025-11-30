<?php 
include 'config/koneksi.php';
session_start();

// Cek Login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 1. Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Query Data Agent
$sql = "SELECT * FROM agents WHERE agent_id = $id";
$result = $koneksi->query($sql);

// Jika agent tidak ditemukan, kembalikan ke halaman daftar
if ($result->num_rows == 0) {
    header("Location: agent.php");
    exit();
}

$row = $result->fetch_assoc();

// 3. Logika Media
$source = $row['agent_image'] ? $row['agent_image'] : 'image/default_agent.png';
$ext = pathinfo($source, PATHINFO_EXTENSION);
$is_video = in_array(strtolower($ext), ['mp4', 'webm', 'ogg']);

// 4. Deskripsi Statis Berdasarkan Role
$role_desc = "";
switch ($row['role']) {
    case 'Duelist':
        $role_desc = "Duelist adalah pembunuh bayaran mandiri yang diharapkan timnya, melalui kemampuan dan skill, untuk mendapatkan kill tinggi dan memimpin pertempuran.";
        break;
    case 'Controller':
        $role_desc = "Controller adalah ahli dalam membelah wilayah berbahaya agar timnya sukses menyerang atau bertahan.";
        break;
    case 'Initiator':
        $role_desc = "Initiator menantang situasi dengan mengganggu lawan, membuka jalan bagi tim untuk masuk ke lokasi yang diperebutkan.";
        break;
    case 'Sentinel':
        $role_desc = "Sentinel adalah ahli pertahanan yang dapat mengunci area dan mengawasi sisi sayap, baik saat menyerang maupun bertahan.";
        break;
    default:
        $role_desc = "Agent dengan kemampuan unik.";
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title><?php echo $row['agent_name']; ?> - Detail Agent</title>
<?php include 'config/head.php'; ?>
<style>
    body {
        background-color: #0f1923;
        color: white;
        overflow-x: hidden;
    }

    /* CONTAINER UTAMA */
    .detail-section {
        display: flex;
        min-height: 85vh;
        align-items: center;
        justify-content: center;
        position: relative;
        padding: 40px 20px;
        background-image: url('assets/images/bg-grid.png');
        background-size: cover;
    }

    /* BACKGROUND HURUF BESAR */
    .bg-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 15vw;
        font-weight: 900;
        color: rgba(255, 255, 255, 0.03); 
        z-index: 0;
        text-transform: uppercase;
        pointer-events: none;
        white-space: nowrap;
    }

    .detail-container {
        display: flex;
        flex-wrap: wrap;
        max-width: 1200px;
        width: 100%;
        z-index: 1;
    }

    /* KOLOM KIRI (MEDIA) */
    .detail-media {
        flex: 1;
        min-width: 300px;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    .media-content {
        max-width: 100%;
        max-height: 600px;
        filter: drop-shadow(0 0 20px rgba(255, 70, 85, 0.3));
        mask-image: linear-gradient(to bottom, black 80%, transparent 100%);
        -webkit-mask-image: linear-gradient(to bottom, black 80%, transparent 100%);
    }

    /* KOLOM KANAN (INFO) */
    .detail-info {
        flex: 1;
        min-width: 300px;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .agent-role-label {
        color: #ff4655;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 1.2rem;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .agent-name-title {
        font-size: 5rem;
        font-weight: 900;
        text-transform: uppercase;
        line-height: 1;
        margin-bottom: 30px;
        font-family: 'Tungsten', sans-serif;
    }

    .info-box {
        background: rgba(255,255,255,0.05);
        border-left: 4px solid #ff4655;
        padding: 20px;
        margin-bottom: 30px;
    }

    .info-title {
        font-size: 1.1rem;
        font-weight: bold;
        margin-bottom: 10px;
        color: #ece8e1;
    }

    .info-desc {
        line-height: 1.6;
        color: #a1a1a1;
        font-size: 1rem;
    }

    .btn-back {
        display: inline-block;
        padding: 15px 30px;
        background-color: #ff4655;
        color: white;
        text-decoration: none;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: 0.3s;
        width: fit-content;
        clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px);
    }

    .btn-back:hover {
        background-color: #111;
        color: #ff4655;
        box-shadow: inset 0 0 0 2px #ff4655;
    }

    /* RESPONSIF */
    @media (max-width: 768px) {
        .detail-container {
            flex-direction: column;
        }
        .detail-media {
            order: 1;
        }
        .detail-info {
            order: 2;
            padding: 20px 0;
            text-align: center;
        }
        .agent-name-title {
            font-size: 3rem;
        }
        .btn-back {
            margin: auto;
        }
        .agent-role-label {
            justify-content: center;
        }
    }
</style>
</head>

<body>

<?php include 'config/navbar.php'; ?>

<section class="detail-section">
    
    <div class="bg-text"><?php echo htmlspecialchars($row['agent_name']); ?></div>

    <div class="detail-container">
        
        <div class="detail-media">
            <?php if ($is_video): ?>
            <?php else: ?>
                <img 
                    src="<?php echo $source; ?>" 
                    class="media-content" 
                    alt="<?php echo $row['agent_name']; ?>">
            <?php endif; ?>
        </div>

        <div class="detail-info">
            
            <div class="agent-role-label">
                <span style="width:10px; height:10px; background:#ff4655; display:inline-block;"></span> 
                <?php echo htmlspecialchars($row['role']); ?>
            </div>
            
            <h1 class="agent-name-title"><?php echo htmlspecialchars($row['agent_name']); ?></h1>

            <div class="info-box">
                <div class="info-title">// ROLE DESCRIPTION</div>
                <p class="info-desc">
                    <?php echo $role_desc; ?>
                </p>
            </div>

            <a href="agent.php" class="btn-back">KEMBALI KE AGENT</a>
        </div>

    </div>
</section>

<?php include 'config/footer.php'; ?>
</body>
</html>