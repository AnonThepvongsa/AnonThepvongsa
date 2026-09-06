<?php
session_start();
if(!isset($_SESSION['emp_id'])){ header("Location: login.php"); exit(); }

$conn = new mysqli("localhost", "root", "", "apartment_db");
$conn->set_charset("utf8mb4");

$current_year_month = date('Y-m');

$sql = "SELECT r.rent_id, r.rent_price, c.cus_name, rm.room_no, 
        p.pay_id, p.pay_date, p.pay_month, p.amount, p.pay_method, p.pay_status
        FROM rental r
        JOIN customer c ON r.cus_id = c.cus_id
        JOIN room rm ON r.room_id = rm.room_id
        LEFT JOIN payment p ON r.rent_id = p.rent_id AND p.pay_month = '$current_year_month'
        WHERE r.status = 'Active'";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ຈັດການການຊຳລະເງິນ</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Noto+Sans+Lao:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #4f46e5; --success: #22c55e; --warning: #f59e0b; --bg: #f8fafc; }
        body { font-family: 'Poppins', 'Noto Sans Lao', sans-serif; background: var(--bg); padding: 40px 20px; color: #334155; }
        .container { max-width: 1250px; margin: auto; background: white; padding: 30px; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; }
        .btn-home { background: #64748b; color: white; }
        .btn:hover { opacity: 0.9; transform: translateY(-2px); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        
        th, td { 
            padding: 16px 12px; 
            border-bottom: 1px solid #e2e8f0; 
            text-align: center; 
            vertical-align: middle;
            font-size: 14px;
        }
        th { background: #f1f5f9; color: #475569; font-weight: 600; }

        .status-badge { 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 13px; 
            font-weight: 600; 
            display: inline-block; 
        }
        .paid { background: #dcfce7; color: #166534; }
        .pending { background: #fef3c7; color: #92400e; }
        
        .btn-edit { 
            background: #e0f2fe; 
            color: #0284c7; 
            padding: 8px 14px; 
            border-radius: 6px; 
            text-decoration: none; 
            font-size: 13px; 
            font-weight: 600; 
            transition: 0.2s; 
            display: inline-flex; 
            align-items: center; 
            gap: 6px; 
        }
        .btn-edit:hover { background: #0284c7; color: #fff; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2><i class="fa-solid fa-wallet" style="color:var(--primary)"></i> ສະຖານະການຊຳລະເງິນ (ປະຈຳເດືອນ <?php echo date('m/Y'); ?>)</h2>
        <div>
            <a href="dashboard.php" class="btn btn-home"><i class="fa-solid fa-house"></i> ໜ້າຫຼັກ</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ລະຫັດຈ່າຍ</th>
                <th>ຫ້ອງ</th> 
                <th>ລູກຄ້າ</th> 
                <th>ວັນທີຈ່າຍ </th>
                <th>ງວດເດືອນ </th> 
                <th>ຈຳນວນເງິນ </th> 
                <th>ວິທີຈ່າຍ </th>
                <th>ສະຖານະ </th> 
                <th>ຈັດການ</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            while($row = $result->fetch_assoc()){ 
                $is_paid = (!empty($row['pay_status']) && strtolower($row['pay_status']) == 'paid');
                
                $target_id = $row['pay_id'] ? $row['pay_id'] : $row['rent_id'];
                $type_param = $row['pay_id'] ? 'pay_id' : 'rent_id';
            ?>
            <tr>
                <td><strong><?php echo $row['pay_id'] ? '#' . $row['pay_id'] : '<span style="color:#94a3b8">-</span>'; ?></strong></td>
                <td><strong><?php echo htmlspecialchars($row['room_no']); ?></strong></td>
                <td><?php echo htmlspecialchars($row['cus_name']); ?></td>
                <td><?php echo $row['pay_date'] ? date('d/m/Y', strtotime($row['pay_date'])) : '<span style="color:#94a3b8">-</span>'; ?></td>
                <td><?php echo $row['pay_month'] ? htmlspecialchars($row['pay_month']) : '<span style="color:#94a3b8">-</span>'; ?></td>
                <td><?php echo $row['amount'] ? number_format($row['amount'], 2) . ' ₭' : number_format($row['rent_price'], 2) . ' ₭ (ຄ່າເຊົ່າ)'; ?></td>
                <td><?php echo $row['pay_method'] ? htmlspecialchars($row['pay_method']) : '-'; ?></td>
                <td>
                    <?php if($is_paid): ?>
                        <span class="status-badge paid">✅ ຈ່າຍແລ້ວ (Paid)</span>
                    <?php else: ?>
                        <span class="status-badge pending">⏳ ຍັງບໍ່ຈ່າຍ (Pending)</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="payment_edit.php?<?php echo $type_param; ?>=<?php echo $target_id; ?>&rent_id=<?php echo $row['rent_id']; ?>" class="btn-edit">
                        <i class="fa-solid fa-pen"></i> ອັບເດດຈ່າຍ
                    </a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>