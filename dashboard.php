<?php
session_start();

if (!isset($_SESSION['emp_id'])) {
    header("Location: login.php");
    exit();
}

// 1. ເຊື່ອມຕໍ່ຖານຂໍ້ມູນ apartment_db
$conn = mysqli_connect('localhost', 'root', '', 'apartment_db');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

// 2. Query ນັບຈຳນວນຂໍ້ມູນ
$total_employee = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM employee"))['total'];
$total_customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM customer"))['total'];
$total_rooms = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM room"))['total'];

// ປັບປຸງການ Query ຫ້ອງວ່າງ: ນັບຫ້ອງທີ່ບໍ່ມີການເຊົ່າ (status = 'Active') ໃນຕາຕະລາງ rental
$sql_available = "SELECT COUNT(*) AS total FROM room WHERE room_id NOT IN (SELECT room_id FROM rental WHERE status = 'Active')";
$total_available = mysqli_fetch_assoc(mysqli_query($conn, $sql_available))['total'];

$total_occupied = $total_rooms - $total_available;

$emp_name = isset($_SESSION['emp_name']) ? $_SESSION['emp_name'] : 'ຜູ້ໃຊ້';
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>Executive Dashboard | Apartment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --bg-dark: #0f172a;       
            --sidebar-dark: #1e293b;  
            --card-dark: #334155;     
            --accent-neon: #38bdf8;   
            --text-light: #f8fafc;
            --text-muted: #94a3b8;
            --sidebar-width: 260px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', 'Noto Sans Lao', sans-serif; }
        body { background: var(--bg-dark); color: var(--text-light); display: flex; min-height: 100vh; }

        /* --- Sidebar --- */
        .sidebar { position: fixed; left: 0; top: 0; width: var(--sidebar-width); height: 100vh; background: var(--sidebar-dark); padding: 30px 20px; display: flex; flex-direction: column; border-right: 1px solid rgba(255, 255, 255, 0.05); z-index: 100; }
        .sidebar h2 { font-size: 22px; font-weight: 700; color: #fff; margin-bottom: 40px; display: flex; align-items: center; gap: 12px; }
        .sidebar h2 i { color: var(--accent-neon); filter: drop-shadow(0 0 8px var(--accent-neon)); }
        .sidebar a { display: flex; align-items: center; gap: 14px; padding: 14px 18px; color: var(--text-muted); text-decoration: none; border-radius: 12px; margin-bottom: 8px; font-weight: 500; transition: all 0.3s ease; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255, 255, 255, 0.05); color: #fff; }
        .sidebar a.active { background: linear-gradient(135deg, #0ea5e9, #2563eb); color: #fff; box-shadow: 0 4px 20px rgba(14, 165, 233, 0.4); }

        /* --- Main Content --- */
        .main { margin-left: var(--sidebar-width); padding: 40px; width: calc(100% - var(--sidebar-width)); }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .welcome-text h1 { font-size: 24px; font-weight: 700; color: #fff; }
        .welcome-text p { color: var(--text-muted); font-size: 14px; margin-top: 4px; }
        .logout { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #f87171; padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; transition: all 0.2s; }
        .logout:hover { background: #ef4444; color: #fff; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4); }

        /* --- Stats --- */
        .mini-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 35px; }
        .stat-box { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 16px; display: flex; justify-content: space-between; align-items: center; }
        .stat-info p { font-size: 13px; color: var(--text-muted); font-weight: 500; }
        .stat-info h3 { font-size: 26px; font-weight: 700; color: #fff; margin-top: 4px; }
        .stat-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; background: rgba(255, 255, 255, 0.05); }

        /* --- Charts --- */
        .charts-container { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        .main-chart-card { background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 20px; padding: 30px; backdrop-filter: blur(10px); }
        .canvas-wrapper { position: relative; width: 100%; height: 340px; }
        
        @media (max-width: 1200px) { .mini-stats { grid-template-columns: repeat(2, 1fr); } .charts-container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-cubes-stacked"></i> <span>APARTMENT</span></h2>
    <a href="dashboard.php" class="active"><i class="fa-solid fa-chart-line"></i> <span>Dashboard</span></a>
    <a href="employee.php"><i class="fa-solid fa-users-gear"></i> <span>ຂໍ້ມູນພະນັກງານ</span></a>
    <a href="customer.php"><i class="fa-solid fa-user-tie"></i> <span>ຂໍ້ມູນລູກຄ້າ</span></a>
    <a href="room.php"><i class="fa-solid fa-door-open"></i> <span>ຂໍ້ມູນຫ້ອງ</span></a>
    <a href="rental.php"><i class="fa-solid fa-file-signature"></i> <span>ການເຊົ່າ</span></a>
    <a href="payment.php"><i class="fa-solid fa-credit-card"></i> <span>ການຊຳລະເງິນ</span></a>
    <!-- 🟢 ປຸ່ມກົດເຂົ້າໜ້າ Report -->
    <a href="report.php"><i class="fa-solid fa-file-invoice-dollar"></i> <span>ລາຍງານ (Report)</span></a>
</div>

<div class="main">
    <div class="topbar">
        <div class="welcome-text">
            <h1>ສະບາຍດີ, <?php echo htmlspecialchars($emp_name); ?></h1>
        </div>
        <a class="logout" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>

    <div class="mini-stats">
        <div class="stat-box"><div class="stat-info"><p>ພະນັກງານທັງໝົດ</p><h3><?php echo $total_employee; ?></h3></div><div class="stat-icon" style="color: #6366f1;"><i class="fa-solid fa-user-shield"></i></div></div>
        <div class="stat-box"><div class="stat-info"><p>ລູກຄ້າທີ່ພັກຢູ່</p><h3><?php echo $total_customer; ?></h3></div><div class="stat-icon" style="color: #22c55e;"><i class="fa-solid fa-users"></i></div></div>
        <div class="stat-box"><div class="stat-info"><p>ຫ້ອງພັກທັງໝົດ</p><h3><?php echo $total_rooms; ?></h3></div><div class="stat-icon" style="color: #eab308;"><i class="fa-solid fa-border-all"></i></div></div>
        <div class="stat-box"><div class="stat-info"><p>ຫ້ອງວ່າງພ້ອມເຊົ່າ</p><h3><?php echo $total_available; ?></h3></div><div class="stat-icon" style="color: #0ea5e9;"><i class="fa-solid fa-door-open"></i></div></div>
    </div>

    <div class="charts-container">
        <div class="main-chart-card" style="display: flex; align-items: center; justify-content: center;">
            <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?q=80&w=1000&auto=format&fit=crop" 
                 alt="Apartment" 
                 style="width: 100%; height: 340px; object-fit: cover; border-radius: 12px;">
        </div>
        
        <div class="main-chart-card"><div class="canvas-wrapper"><canvas id="executiveDoughnutChart"></canvas></div></div>
    </div>
</div>

<script>
    const roomAvail = <?php echo $total_available; ?>;
    const roomOccupied = <?php echo $total_occupied; ?>;

    new Chart(document.getElementById('executiveDoughnutChart'), {
        type: 'doughnut',
        data: { labels: ['ວ່າງ', 'ມີຜູ້ເຊົ່າ'], datasets: [{ data: [roomAvail, roomOccupied], backgroundColor: ['#0ea5e9', '#334155'] }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '75%' }
    });
</script>

</body>
</html>