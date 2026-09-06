<?php
session_start();
if(!isset($_SESSION['emp_id'])){ header("Location: login.php"); exit(); }

$conn = new mysqli("localhost", "root", "", "apartment_db");
$conn->set_charset("utf8mb4");

$rent_id = isset($_GET['rent_id']) ? intval($_GET['rent_id']) : 0;
$pay_id = isset($_GET['pay_id']) ? intval($_GET['pay_id']) : 0;

// ດຶງຂໍ້ມູນການເຊົ່າ ແລະ ຂໍ້ມູນຫ້ອງມາສະແດງ
$sql_rent = "SELECT r.*, c.cus_name, rm.room_no FROM rental r
             JOIN customer c ON r.cus_id = c.cus_id
             JOIN room rm ON r.room_id = rm.room_id
             WHERE r.rent_id = $rent_id";
$res_rent = $conn->query($sql_rent);
if($res_rent->num_rows == 0) { die("ບໍ່ພົບຂໍ້ມູນການເຊົ່າ"); }
$rent = $res_rent->fetch_assoc();

// ຄ່າເລີ່ມຕົ້ນສຳລັບຟອມ (ກໍລະນີເພີ່ມໃໝ່)
$pay_data = [
    'pay_date' => date('Y-m-d'),
    'pay_month' => date('Y-m'),
    'amount' => $rent['rent_price'],
    'pay_method' => 'ເງິນສົດ',
    'pay_status' => 'paid'
];

// ຖົາມີ pay_id ສົ່ງມາ ໝາຍເຖິງກຳລັງແก້ໄຂຂໍ້ມູນເກົ່າ ໃຫ້ດຶງຂໍ້ມູນເກົ່າຂຶ້ນມາສະແດງ
if($pay_id > 0) {
    $sql_pay = "SELECT * FROM payment WHERE pay_id = $pay_id";
    $res_pay = $conn->query($sql_pay);
    if($res_pay->num_rows > 0) {
        $pay_data = $res_pay->fetch_assoc();
    }
}

// ເມື່ອກົດບັນທຶກຟອມ POST
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pay_date = $conn->real_escape_string($_POST['pay_date']);
    $pay_month = $conn->real_escape_string($_POST['pay_month']);
    $amount = floatval($_POST['amount']);
    $pay_method = $conn->real_escape_string($_POST['pay_method']);
    $pay_status = $conn->real_escape_string($_POST['pay_status']);

    if($pay_id > 0) {
        // ຄຳສັ່ງ SQL ສຳລັບອັບເດດ (UPDATE)
        $sql_save = "UPDATE payment SET pay_date='$pay_date', pay_month='$pay_month', amount=$amount, pay_method='$pay_method', pay_status='$pay_status' WHERE pay_id=$pay_id";
    } else {
        // ຄຳສັ່ງ SQL ສຳລັບເພີ່ມໃໝ່ (INSERT)
        $sql_save = "INSERT INTO payment (rent_id, pay_date, pay_month, amount, pay_method, pay_status) VALUES ($rent_id, '$pay_date', '$pay_month', $amount, '$pay_method', '$pay_status')";
    }

    if($conn->query($sql_save)) {
        header("Location: payment.php");
        exit();
    } else {
        $error = "ເກີດຂໍ້ຜິດພາດ: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ຈັດການການຊຳລະເງິນ</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Noto+Sans+Lao:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #4f46e5; --bg: #f8fafc; }
        body { font-family: 'Poppins', 'Noto Sans Lao', sans-serif; background: var(--bg); padding: 40px 20px; color: #334155; }
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 20px; color: #1e293b; font-size: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; }
        input, select { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; font-family: inherit; }
        .btn-submit { background: var(--primary); color: white; padding: 12px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; width: 100%; font-family: inherit; }
        .btn-submit:hover { opacity: 0.9; }
        .back-link { display: inline-block; margin-top: 15px; color: #64748b; text-decoration: none; font-size: 14px; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fa-solid fa-file-invoice-dollar" style="color:var(--primary)"></i> ບັນທຶກ/ອັບເດດການຊຳລະເງິນ: ຫ້ອງ <?php echo htmlspecialchars($rent['room_no']); ?></h2>
    
    <?php if(isset($error)): ?>
        <div style="background:#fee2e2; color:#991b1b; padding:10px; border-radius:6px; margin-bottom:15px; font-size:14px;"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>ຊື່ລູກຄ້າ:</label>
            <input type="text" value="<?php echo htmlspecialchars($rent['cus_name']); ?>" disabled style="background:#f1f5f9; color:#64748b;">
        </div>

        <div class="form-group">
            <label>ວັນທີຈ່າຍ :</label>
            <input type="date" name="pay_date" value="<?php echo $pay_data['pay_date']; ?>" required>
        </div>

        <div class="form-group">
            <label>ງວດເດືອນ :</label>
            <input type="text" name="pay_month" value="<?php echo $pay_data['pay_month']; ?>" placeholder="ຕົວຢ່າງ: 2026-06" required>
        </div>

        <div class="form-group">
            <label>ຈຳນວນເງິນ :</label>
            <input type="number" step="0.01" name="amount" value="<?php echo $pay_data['amount']; ?>" required>
        </div>

        <div class="form-group">
            <label>ວິທີຈ່າຍ :</label>
            <select name="pay_method">
                <option value="ເງິນສົດ" <?php if($pay_data['pay_method']=='ເງິນສົດ') echo 'selected'; ?>>ເງິນສົດ</option>
                <option value="ໂອນຜ່ານທະນາຄານ" <?php if($pay_data['pay_method']=='ໂອນຜ່ານທະນາຄານ') echo 'selected'; ?>>ໂອນຜ່ານທະນາຄານ</option>
            </select>
        </div>

        <div class="form-group">
            <label>ສະຖານະການຈ່າຍ :</label>
            <select name="pay_status">
                <option value="paid" <?php if($pay_data['pay_status']=='paid') echo 'selected'; ?>>Paid (ຈ່າຍແລ້ວ)</option>
                <option value="pending" <?php if($pay_data['pay_status']=='pending') echo 'selected'; ?>>Pending (ຍັງບໍ່ຈ່າຍ)</option>
            </select>
        </div>

        <button type="submit" class="btn-submit"><i class="fa-solid fa-save"></i> ບັນທຶກຂໍ້ມູນ</button>
    </form>

    <a href="payment.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> ກັບຄືນໜ້າສະຖານະການຊຳລະເງິນ</a>
</div>
</body>
</html>