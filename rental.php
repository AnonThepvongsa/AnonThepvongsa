<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if(!isset($_SESSION['emp_id'])){
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost","root","","apartment_db");
if($conn->connect_error){ die("Database Error : ".$conn->connect_error); }
$conn->set_charset("utf8mb4");

/* 
  ແກ້ໄຂ SQL: เอา r.status ອອກຈາກ GROUP BY 
  ເພື່ອໃຫ້ລູກຄ້າຄົນດຽວ ແລະ ເລີ່ມວັນທີດຽວກັນ ລວມເປັນສັນຍາດຽວກັນຕະຫຼອດ
  (ໃຊ້ MAX(r.status) ເພື່ອສະແດງສະຖານະລວມ)
*/
$sql = "SELECT r.cus_id, c.cus_name, MAX(r.status) AS status,
               r.start_date, 
               r.end_date, 
               SUM(r.deposit) AS total_deposit, 
               SUM(r.rent_price) AS total_rent, 
               GROUP_CONCAT(rm.room_no ORDER BY rm.room_no ASC SEPARATOR ', ') AS room_nos,
               MIN(r.rent_id) AS rent_id
        FROM rental r
        JOIN customer c ON r.cus_id = c.cus_id
        LEFT JOIN room rm ON r.room_id = rm.room_id
        GROUP BY r.cus_id, r.start_date, r.end_date
        ORDER BY r.start_date DESC, rent_id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<title>Rental Management | Apartment</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root { --primary-gradient: linear-gradient(135deg, #0ea5e9, #2563eb); --bg-color: #f8fafc; --text-main: #1e293b; --text-muted: #64748b; --border-color: #e2e8f0; }
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', 'Noto Sans Lao', sans-serif; }
    body { background: var(--bg-color); color: var(--text-main); padding: 20px; }
    
    .container { max-width: 100%; width: 1450px; margin: 20px auto; background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    
    .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; }
    h2 { font-size: 22px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
    .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; transition: 0.2s; border: none; cursor: pointer; }
    .btn-back { background: #f1f5f9; color: #475569; }
    .btn-add { background: var(--primary-gradient); color: #fff; }
    .table-box { width: 100%; overflow-x: auto; border: 1px solid var(--border-color); border-radius: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 16px 20px; border-bottom: 1px solid var(--border-color); white-space: nowrap; }
    th { background: #f8fafc; color: var(--text-muted); font-size: 13px; text-transform: uppercase; }
    .action-btn { display: inline-flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 8px; text-decoration: none; transition: 0.2s; margin: 0 2px; }
    
    .btn-view { background: #dcfce7; color: #15803d; }
    .btn-edit { background: #fef3c7; color: #d97706; }
    .action-btn:hover { opacity: 0.8; }
    .status { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .active { background: #dcfce7; color: #15803d; }
    .finish { background: #f1f5f9; color: #64748b; }
</style>
</head>
<body>

<div class="container">
    <div class="top-bar">
        <h2><i class="fa-solid fa-file-invoice"></i> ຈັດການຂໍ້ມູນການເຊົ່າ</h2>
        <div class="btn-group">
            <a href="dashboard.php" class="btn btn-back"><i class="fa-solid fa-house"></i> ກັບໜ້າຫຼັກ</a>
            <a href="rental_add.php" class="btn btn-add"><i class="fa-solid fa-plus"></i> ເພີ່ມການເຊົ່າ</a>
        </div>
    </div>

    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ຜູ້ເຊົ່າ</th>
                    <th>ຫ້ອງທີ່ເຊົ່າ</th>
                    <th>ລວມເງິນມັດຈຳ</th>
                    <th>ລວມຄ່າເຊົ່າ</th>
                    <th>ເລີ່ມຕົ້ນ</th>
                    <th>ສິ້ນສຸດ</th>
                    <th>ສະຖານະ</th>
                    <th style="text-align: center;">ຈັດການ</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1; 
                if($result && $result->num_rows > 0){
                    while($row = $result->fetch_assoc()){ 
                        $rent_id = $row['rent_id'];
                        $cus_id = $row['cus_id'];
                        $start_date = $row['start_date'];
                        $end_date = $row['end_date'];
                ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($row['cus_name']); ?></td>
                    <td><strong><?php echo htmlspecialchars($row['room_nos'] ?? '-'); ?></strong></td>
                    <td><?php echo number_format($row['total_deposit'], 2); ?> ₭</td>
                    <td><strong style="color: #2563eb;"><?php echo number_format($row['total_rent'], 2); ?> ₭</strong></td>
                    <td><?php echo date('d/m/Y', strtotime($row['start_date'])); ?></td>
                    <td><?php echo ($row['end_date'] != '0000-00-00') ? date('d/m/Y', strtotime($row['end_date'])) : '-'; ?></td>
                    <td>
                        <span class="status <?php echo ($row['status'] == 'Active') ? 'active' : 'finish'; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <!-- ສົ່ງທັງ rent_id, cus_id, start_date, end_date ໄປພ້ອມກັນ ເພື່ອໃຫ້ຮອງຮັບທັງລະບົບເກົ່າແລະໃໝ່ -->
                        <a href="rental_view.php?id=<?php echo $rent_id; ?>&cus_id=<?php echo $cus_id; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="action-btn btn-view" title="ເບິ່ງຂໍ້ມູນ"><i class="fa-solid fa-eye"></i></a>
                        <a href="rental_edit.php?id=<?php echo $rent_id; ?>&cus_id=<?php echo $cus_id; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="action-btn btn-edit" title="ແກ້ໄຂ"><i class="fa-solid fa-pen"></i></a>
                        <a href="rental_delete.php?id=<?php echo $rent_id; ?>&cus_id=<?php echo $cus_id; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="action-btn" style="background:#fee2e2; color:#b91c1c;" title="ລົບ" onclick="return confirm('ຢືນຢັນການລົບລາຍການເຊົ່ານี้ທັງໝົດ?')">
                            <i class="fa-solid fa-trash-can"></i>
                        </a>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                ?>
                <tr>
                    <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 40px;">
                        ບໍ່ມີຂໍ້ມູນການເຊົ່າໃນລະບົບ
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>