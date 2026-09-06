<?php
session_start();
// ກວດສອບຄວາມປອດໄພໃຫ້ຕ້ອງ Login ກ່ອນສະເໝີ
if(!isset($_SESSION['emp_id'])){
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost","root","","apartment_db");

if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}

// ກຳນົດໃຫ້ຮອງຮັບພາສາລາວໃນການດຶງຂໍ້ມູນ
$conn->set_charset("utf8mb4");

// ຮັບຄ່າຜ່ານ URL parameter ທີ່ສົ່ງມາຈາກໜ້າ rental.php
$cus_id = isset($_GET['cus_id']) ? intval($_GET['cus_id']) : 0;
$start_date = isset($_GET['start_date']) ? $conn->real_escape_string($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? $conn->real_escape_string($_GET['end_date']) : '';

// ຖ້າມີການສົ່ງ id ແບບເກົ່າມາ (ກໍລະນີລິ້ງເກົ່າ) ໃຫ້ດຶງຂໍ້ມູນມາຊອກຫາ cus_id ແລະ ວັນທີກ່ອນ ເພື່ອຄວາມປອດໄພ
if($cus_id == 0 && isset($_GET['id'])) {
    $old_id = intval($_GET['id']);
    $chk_q = $conn->query("SELECT cus_id, start_date, end_date FROM rental WHERE rent_id = $old_id");
    if($chk_q && $chk_q->num_rows > 0) {
        $chk_r = $chk_q->fetch_assoc();
        $cus_id = $chk_r['cus_id'];
        $start_date = $chk_r['start_date'];
        $end_date = $chk_r['end_date'];
    }
}

// SQL ດຶງຂໍ້ມູນລວມທຸກຫ້ອງທີ່ຢູ່ໃນສັນຍາດຽວກັນ ພ້ອມທັງ JOIN ເພື່ອດຶງຊື່ພະນັກງານທີ່ອອກສັນຍາ
$sql = "SELECT c.cus_name, 
               MAX(r.status) AS status,
               r.start_date, 
               r.end_date, 
               SUM(r.deposit) AS total_deposit, 
               SUM(r.rent_price) AS total_rent, 
               GROUP_CONCAT(rm.room_no ORDER BY rm.room_no ASC SEPARATOR ', ') AS room_nos,
               MIN(r.rent_id) AS rent_id,
               e.emp_name
        FROM rental r
        JOIN customer c ON r.cus_id = c.cus_id
        LEFT JOIN room rm ON r.room_id = rm.room_id
        LEFT JOIN employee e ON r.emp_id = e.emp_id
        WHERE r.cus_id = $cus_id AND r.start_date = '$start_date' AND r.end_date = '$end_date'
        GROUP BY r.cus_id, r.start_date, r.end_date";

$result = $conn->query($sql);
if($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>ບໍ່ພົບຂໍ້ມູນສັນຍາການເຊົ່າທີ່ທ່ານຕ້ອງການ</div>");
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<title>Rental Contract #<?php echo $row['rent_id']; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Google Fonts: Noto Sans Lao ສໍາລັບພາສາລາວທີ່ເປັນທາງການ -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Font Awesome Icons ສໍາລັບປຸ່ມກົດ -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --text-color: #1e293b;
        --border-document: #cbd5e1;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Poppins', 'Noto Sans Lao', sans-serif;
    }

    body {
        background: #f1f5f9;
        color: var(--text-color);
        padding: 40px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .action-bar {
        width: 100%;
        max-width: 800px;
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease;
    }

    .btn-back {
        background: #fff;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .btn-back:hover {
        background: #f8fafc;
        color: #0f172a;
    }

    .btn-print {
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: #fff;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
    }

    .btn-print:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(2, 132, 199, 0.3);
    }

    .contract-box {
        width: 100%;
        max-width: 800px;
        background: #fff;
        padding: 60px 70px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border-document);
        position: relative;
    }

    .contract-header {
        text-align: center;
        margin-bottom: 40px;
        border-bottom: 3px double #0f172a;
        padding-bottom: 20px;
    }

    .contract-header h2 {
        font-size: 26px;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .contract-header p {
        font-size: 14px;
        color: #64748b;
    }

    .info-section {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        margin-bottom: 40px;
    }

    .info-item {
        border-bottom: 1px dashed #e2e8f0;
        padding-bottom: 10px;
    }

    .info-item.full-width {
        grid-column: span 2;
    }

    .info-item label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .info-item .value {
        font-size: 16px;
        font-weight: 500;
        color: #0f172a;
    }

    .price-highlight {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #059669 !important;
    }

    .terms-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 50px;
        font-size: 13px;
        color: #475569;
        line-height: 1.6;
    }

    .terms-box h4 {
        color: #1e293b;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .signature-section {
        display: flex;
        justify-content: space-between;
        margin-top: 60px;
        padding: 0 20px;
    }

    .signature-block {
        text-align: center;
        width: 240px;
    }

    .signature-line {
        border-bottom: 1px solid #0f172a;
        margin-bottom: 10px;
        height: 40px;
    }

    .signature-title {
        font-size: 14px;
        font-weight: 600;
        color: #334155;
    }

    @media print {
        body {
            background: #fff;
            padding: 0;
        }

        .action-bar {
            display: none;
        }

        .contract-box {
            border: none;
            box-shadow: none;
            padding: 20px 0;
            width: 100%;
            max-width: 100%;
        }

        .terms-box {
            background: #fff;
            border: 1px solid #cbd5e1;
        }
    }
</style>
</head>
<body>

    <div class="action-bar">
        <a href="rental.php" class="btn btn-back">
            <i class="fa-solid fa-arrow-left"></i> ກັບຄືນ
        </a>
        <button onclick="window.print()" class="btn btn-print">
            <i class="fa-solid fa-print"></i> ພິມເອກະສານສັນຍາ (Print)
        </button>
    </div>

    <div class="contract-box">
        
        <div class="contract-header">
            <h2>Apartment Rental Contract</h2>
            <p>ສັນຍາການເຊົ່າຫ້ອງພັກ ແລະ ຄໍ້າປະກັນ</p>
        </div>

        <div class="info-section">
            
            <div class="info-item">
                <label>ເລກທີສັນຍາ (Contract ID)</label>
                <div class="value">#<?php echo str_pad($row['rent_id'], 5, '0', STR_PAD_LEFT); ?></div>
            </div>

            <div class="info-item">
                <label>ສະຖານະສັນຍາ (Status)</label>
                <div class="value">
                    <span style="font-weight: 600; color: <?php echo ($row['status'] == 'Active') ? '#15803d' : '#64748b'; ?>;">
                        <?php echo ($row['status'] == 'Active') ? '🟢 ກຳລັງເຊົ່າ (Active)' : '⚪ ສິ້ນສຸດແລ້ວ (Finished)'; ?>
                    </span>
                </div>
            </div>

            <div class="info-item full-width">
                <label>ຊື່ຜູ້ເຊົ່າພັກ (Customer Name)</label>
                <div class="value"><i class="fa-solid fa-user fa-sm" style="color: #94a3b8; margin-right: 5px;"></i> <?php echo htmlspecialchars($row['cus_name']); ?></div>
            </div>

            <div class="info-item full-width">
                <label>ຫ້ອງພັກທີ່ເຊົ່າ (Room Numbers)</label>
                <div class="value"><i class="fa-solid fa-door-closed fa-sm" style="color: #94a3b8; margin-right: 5px;"></i> ຫ້ອງ <strong><?php echo htmlspecialchars($row['room_nos'] ?? '-'); ?></strong></div>
            </div>

            <div class="info-item">
                <label>ວັນທີເລີ່ມຕົ້ນສັນຍາ (Start Date)</label>
                <div class="value"><i class="fa-regular fa-calendar-check" style="color: #94a3b8; margin-right: 5px;"></i> <?php echo date('d/m/Y', strtotime($row['start_date'])); ?></div>
            </div>

            <div class="info-item">
                <label>ວັນທີສິ້ນສຸດສັນຍາ (End Date)</label>
                <div class="value"><i class="fa-regular fa-calendar-minus" style="color: #94a3b8; margin-right: 5px;"></i> <?php echo ($row['end_date'] != '0000-00-00' && $row['end_date'] != '') ? date('d/m/Y', strtotime($row['end_date'])) : '-'; ?></div>
            </div>

            <div class="info-item">
                <label>ລວມເງິນຄໍ້າປະກັນ (Total Deposit)</label>
                <div class="value price-highlight"><?php echo number_format($row['total_deposit'], 2); ?> ₭</div>
            </div>

            <div class="info-item">
                <label>ລວມຄ່າເຊົ່າລາຍເດືອນ (Total Rent Price)</label>
                <div class="value price-highlight"><?php echo number_format($row['total_rent'], 2); ?> ₭</div>
            </div>

        </div>

        <div class="terms-box">
            <h4>ຂໍ້ຕົກລົງ ແລະ ເງື່ອນໄຂ (Terms & Conditions)</h4>
            <p>1. ຜູ້ເຊົ່າຕ້ອງຊຳລະຄ່າເຊົ່າໃຫ້ກົງເວລາທີ່ກຳນົດໄວ້ໃນແຕ່ລະເດືອນ.</p>
            <p>2. ເງິນຄໍ້າປະກັນ (Deposit) ຈະໄດ້ຮັບຄືນຫຼັງຈາກສິ້ນສຸດສັນຍາ ແລະ ຫັກຄ່າເສຍຫາຍຂອງຊັບສິນ (ຖ້າມີ).</p>
            <p>3. ຜູ້ເຊົ່າຕ້ອງຮັກສາຄວາມສະອາດ ແລະ ບໍ່ກະທຳສິ່ງທີ່ຜິດຕໍ່ລະບຽບກົດໝາຍພາຍໃນຫ້ອງພັກ.</p>
        </div>

        <!-- ບ່ອນລົງລາຍເຊັນ (ເພີ່ມຊື່ພະນັກງານອອກສັນຍາພາຍໃຕ້ລາຍເຊັນຜູ້ໃຫ້ເຊົ່າ) -->
        <div class="signature-section">
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-title">ລາຍເຊັນຜູ້ເຊົ່າພັກ</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 5px;">(Customer Signature)</div>
            </div>
            
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-title">ລາຍເຊັນຜູ້ໃຫ້ເຊົ່າ / ພະນັກງານ</div>
                <div style="font-size: 13px; font-weight: 500; color: #0f172a; margin-top: 5px;">
                    <?php echo htmlspecialchars($row['emp_name'] ?? 'Admin'); ?>
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">(Authorized Signature)</div>
            </div>
        </div>

    </div>

</body>
</html>