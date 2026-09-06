<?php
session_start();
if(!isset($_SESSION['emp_id'])){ header("Location: login.php"); exit(); }

$conn = new mysqli("localhost", "root", "", "apartment_db");
mysqli_set_charset($conn, "utf8mb4");

$report_type = isset($_GET['type']) ? $_GET['type'] : 'payment';
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// ** ແກ້ໄຂໃຫ້ເປັນ YYYY-MM ໃຫ້ກົງກັບຕອນບັນທຶກ (ເຊັ່ນ: 2026-06) **
$search_month_year = $selected_year . '-' . $selected_month; 
$search_date_prefix = $selected_year . '-' . $selected_month; 
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ລະບົບລາຍງານ | Apartment Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --success: #22c55e;
            --warning: #f59e0b;
            --bg-color: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', 'Noto Sans Lao', sans-serif;
        }

        body {
            background: var(--bg-color);
            color: var(--text-main);
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h2 i { color: var(--primary); }

        .btn-group { display: flex; gap: 10px; }
        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
            cursor: pointer;
            border: none;
        }

        .btn-home { background: #64748b; color: white; }
        .btn-print { background: #0ea5e9; color: white; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }

        .report-tabs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 25px;
        }

        .tab-btn {
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: #f1f5f9;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .tab-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .tab-btn:hover:not(.active) {
            background: #e2e8f0;
            color: var(--text-main);
        }

        .filter-form {
            background: #f8fafc;
            padding: 18px 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .filter-form label { font-weight: 600; font-size: 14px; }
        .filter-form select {
            padding: 8px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            background: white;
        }

        .btn-search { background: var(--primary); color: white; padding: 8px 16px; }

        .summary-card {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
            padding: 20px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-card h3 { font-size: 15px; font-weight: 400; }
        .summary-card .amount { font-size: 24px; font-weight: 700; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th {
            background: #f8fafc;
            padding: 14px 16px;
            text-align: left;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 13px;
            border-bottom: 2px solid var(--border-color);
        }
        td { padding: 14px 16px; border-bottom: 1px solid var(--border-color); font-size: 14px; }

        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .paid, .active-status { background: #dcfce7; color: #166534; }
        .pending, .inactive-status { background: #fef3c7; color: #92400e; }

        .no-data { text-align: center; padding: 40px; color: var(--text-muted); font-style: italic; }

        @media print {
            body { background: white; padding: 0; }
            .container { box-shadow: none; padding: 0; max-width: 100%; }
            .header div, .filter-form, .report-tabs, .btn-print, .btn-home { display: none !important; }
            .summary-card { border: 1px solid #000; color: #000; background: none; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2><i class="fa-solid fa-file-invoice-dollar"></i> ລາຍງານຂໍ້ມູນ Apartment</h2>
        <div class="btn-group">
            <a href="dashboard.php" class="btn btn-home"><i class="fa-solid fa-house"></i> ໜ້າຫຼັກ</a>
            <button onclick="window.print()" class="btn btn-print"><i class="fa-solid fa-print"></i> ພິມລາຍງານ</button>
        </div>
    </div>

    <!-- ແຖບເມນູລາຍງານຕ່າງໆ -->
    <div class="report-tabs">
        <a href="report.php?type=payment" class="tab-btn <?php echo ($report_type == 'payment') ? 'active' : ''; ?>">
            <i class="fa-solid fa-credit-card"></i> ການຊຳລະເງິນ
        </a>
        <a href="report.php?type=income" class="tab-btn <?php echo ($report_type == 'income') ? 'active' : ''; ?>">
            <i class="fa-solid fa-wallet"></i> ລາຍຮັບ
        </a>
        <a href="report.php?type=rental" class="tab-btn <?php echo ($report_type == 'rental') ? 'active' : ''; ?>">
            <i class="fa-solid fa-file-signature"></i> ການເຊົ່າ
        </a>
        <a href="report.php?type=contract" class="tab-btn <?php echo ($report_type == 'contract') ? 'active' : ''; ?>">
            <i class="fa-solid fa-file-contract"></i> ໃບສັນຍາເຊົ່າ
        </a>
        <a href="report.php?type=room" class="tab-btn <?php echo ($report_type == 'room') ? 'active' : ''; ?>">
            <i class="fa-solid fa-door-open"></i> ຂໍ້ມູນຫ້ອງ
        </a>
    </div>

    <!-- ໃຫ້ສະແດງຟອມເລືອກເດືອນ/ປີ ຍົກເວັ້ນແຕ່ໜ້າ room ເທົ່ານັ້ນ -->
    <?php if($report_type != 'room'): ?>
    <form method="GET" class="filter-form">
        <input type="hidden" name="type" value="<?php echo $report_type; ?>">
        <label>ເລືອກເດືອນ:</label>
        <select name="month">
            <?php 
            $months = [
                '01' => 'ມັງກອນ', '02' => 'ກຸມພາ', '03' => 'ມີນາ', '04' => 'ເມສາ', 
                '05' => 'ພຶດສະພາ', '06' => 'ມິຖຸນາ', '07' => 'ກໍລະກົດ', '08' => 'ສິງຫາ', 
                '09' => 'ກັນຍາ', '10' => 'ຕຸລາ', '11' => 'ພະຈິກ', '12' => 'ທັນວາ'
            ];
            foreach($months as $m_num => $m_name){
                $selected = ($selected_month == $m_num) ? 'selected' : '';
                echo "<option value='$m_num' $selected>$m_name</option>";
            }
            ?>
        </select>

        <label>ປີ:</label>
        <select name="year">
            <?php 
            $current_y = date('Y');
            for($y = $current_y; $y >= $current_y - 3; $y--){
                $selected = ($selected_year == $y) ? 'selected' : '';
                echo "<option value='$y' $selected>$y</option>";
            }
            ?>
        </select>
        <button type="submit" class="btn btn-search"><i class="fa-solid fa-magnifying-glass"></i> ຄົ້ນຫາ</button>
    </form>
    <?php endif; ?>

    <!-- 1. ລາຍງານການຊຳລະເງິນ -->
    <?php if($report_type == 'payment'): ?>
        <?php 
        $sql = "SELECT p.*, r.rent_id, c.cus_name, rm.room_no FROM payment p JOIN rental r ON p.rent_id = r.rent_id JOIN customer c ON r.cus_id = c.cus_id JOIN room rm ON r.room_id = rm.room_id WHERE p.pay_month = '$search_month_year' ORDER BY p.pay_date DESC";
        $result = $conn->query($sql);
        ?>
        <h3 style="margin-bottom: 15px; font-size: 18px;"><i class="fa-solid fa-credit-card"></i> ລາຍລະອຽດການຊຳລະເງິນ ເດືອນ <?php echo $search_month_year; ?></h3>
        <table>
            <thead>
                <tr>
                    <th>ວັນທີຈ່າຍ</th>
                    <th>ຫ້ອງ</th>
                    <th>ລູກຄ້າ</th>
                    <th>ຈຳນວນເງິນ</th>
                    <th>ວິທີຊຳລະ</th>
                    <th>ສະຖານະ</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result && $result->num_rows > 0): while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($row['pay_date'])); ?></td>
                    <td><strong><?php echo $row['room_no']; ?></strong></td>
                    <td><?php echo $row['cus_name']; ?></td>
                    <td><strong><?php echo number_format($row['amount'], 2); ?> ₭</strong></td>
                    <td><?php echo strtoupper($row['pay_method']); ?></td>
                    <td>
                        <?php if(strtolower($row['pay_status']) == 'paid'): ?>
                            <span class="status-badge paid">✅ ຈ່າຍແລ້ວ</span>
                        <?php else: ?>
                            <span class="status-badge pending">⏳ ຄ້າງຈ່າຍ</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="6" class="no-data">ບໍ່ມີຂໍ້ມູນການຊຳລະເງິນໃນເດືອນນີ້</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    <!-- 2. ລາຍງານລາຍຮັບ -->
    <?php elseif($report_type == 'income'): ?>
        <?php 
        $sql_total = "SELECT SUM(amount) as total_amount FROM payment WHERE pay_month = '$search_month_year' AND (pay_status = 'paid' OR pay_status = 'Paid')";
        $total_result = $conn->query($sql_total)->fetch_assoc();
        $grand_total = $total_result['total_amount'] ? $total_result['total_amount'] : 0;

        $sql = "SELECT p.*, r.rent_id, c.cus_name, rm.room_no FROM payment p JOIN rental r ON p.rent_id = r.rent_id JOIN customer c ON r.cus_id = c.cus_id JOIN room rm ON r.room_id = rm.room_id WHERE p.pay_month = '$search_month_year' AND (p.pay_status = 'paid' OR p.pay_status = 'Paid') ORDER BY p.pay_date DESC";
        $result = $conn->query($sql);
        ?>
        <div class="summary-card">
            <div>
                <h3>ຍອດລາຍຮັບລວມທັງໝົດ (ສະຖານະຈ່າຍແລ້ວ)</h3>
                <p style="font-size: 13px; opacity: 0.9; margin-top: 4px;">ປະຈຳເດືອນ: <?php echo $search_month_year; ?></p>
            </div>
            <div class="amount"><?php echo number_format($grand_total, 2); ?> ₭</div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ວັນທີຮັບເງິນ</th>
                    <th>ຫ້ອງ</th>
                    <th>ລູກຄ້າ</th>
                    <th>ຈຳນວນເງິນຮັບ</th>
                    <th>ວິທີຊຳລະ</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result && $result->num_rows > 0): while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($row['pay_date'])); ?></td>
                    <td><strong><?php echo $row['room_no']; ?></strong></td>
                    <td><?php echo $row['cus_name']; ?></td>
                    <td><strong style="color: #166534;"><?php echo number_format($row['amount'], 2); ?> ₭</strong></td>
                    <td><?php echo strtoupper($row['pay_method']); ?></td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="5" class="no-data">ບໍ່ມີລາຍຮັບໃນເດືອນນີ້</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    <!-- 3. ລາຍງານການເຊົ່າ -->
    <?php elseif($report_type == 'rental'): ?>
        <?php 
        $sql = "SELECT r.*, c.cus_name, rm.room_no FROM rental r JOIN customer c ON r.cus_id = c.cus_id JOIN room rm ON r.room_id = rm.room_id WHERE r.start_date LIKE '$search_date_prefix%' ORDER BY r.rent_id DESC";
        $result = $conn->query($sql);
        ?>
        <h3 style="margin-bottom: 15px; font-size: 18px;"><i class="fa-solid fa-file-signature"></i> ລາຍງານປະຫວັດ ແລະ ສະຖານະການເຊົ່າຫ້ອງພັກ (ເດືອນ <?php echo $search_month_year; ?>)</h3>
        <table>
            <thead>
                <tr>
                    <th>ລະຫັດເຊົ່າ</th>
                    <th>ລູກຄ້າ</th>
                    <th>ຫ້ອງ</th>
                    <th>ວັນທີເລີ່ມເຊົ່າ</th>
                    <th>ສະຖານະການເຊົ່າ</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result && $result->num_rows > 0): while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['rent_id']; ?></td>
                    <td><?php echo $row['cus_name']; ?></td>
                    <td><strong><?php echo $row['room_no']; ?></strong></td>
                    <td><?php echo date('d/m/Y', strtotime($row['start_date'])); ?></td>
                    <td>
                        <?php if(strtolower($row['status']) == 'active'): ?>
                            <span class="status-badge active-status">🟢 ກຳລັງເຊົ່າ (Active)</span>
                        <?php else: ?>
                            <span class="status-badge inactive-status">🔴 ຍົກເລີກ/ສິ້ນສຸດ</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="5" class="no-data">ບໍ່ມີຂໍ້ມູນການເຊົ່າໃນເດືອນນີ້</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    <!-- 4. ລາຍງານໃບສັນຍາເຊົ່າ -->
    <?php elseif($report_type == 'contract'): ?>
        <?php 
        $sql = "SELECT r.*, c.cus_name, c.phone, rm.room_no 
                FROM rental r 
                JOIN customer c ON r.cus_id = c.cus_id 
                JOIN room rm ON r.room_id = rm.room_id 
                WHERE r.start_date LIKE '$search_date_prefix%'
                ORDER BY r.rent_id DESC";
        $result = $conn->query($sql);
        ?>
        <h3 style="margin-bottom: 15px; font-size: 18px;"><i class="fa-solid fa-file-contract"></i> ລາຍງານໃບສັນຍາເຊົ່າຫ້ອງພັກ (ເດືອນ <?php echo $search_month_year; ?>)</h3>
        <table>
            <thead>
                <tr>
                    <th>ເລກທີສັນຍາ</th>
                    <th>ລູກຄ້າ</th>
                    <th>ເບີໂທລະສັບ</th>
                    <th>ຫ້ອງ</th>
                    <th>ວັນທີເລີ່ມສັນຍາ</th>
                    <th>ສະຖານະສັນຍາ</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result && $result->num_rows > 0): while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><strong>#CNT-<?php echo str_pad($row['rent_id'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['cus_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone'] ?? '-'); ?></td>
                    <td><strong><?php echo htmlspecialchars($row['room_no']); ?></strong></td>
                    <td><?php echo date('d/m/Y', strtotime($row['start_date'])); ?></td>
                    <td>
                        <?php if(strtolower($row['status']) == 'active'): ?>
                            <span class="status-badge active-status">🟢 ມີຜົນສຳເລັດ (Active)</span>
                        <?php else: ?>
                            <span class="status-badge inactive-status">🔴 ສິ້ນສຸດສັນຍາ</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="6" class="no-data">ບໍ່ມີຂໍ້ມູນໃບສັນຍາເຊົ່າໃນເດືອນນີ້</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    <!-- 5. ລາຍງານຂໍ້ມູນຫ້ອງ -->
    <?php elseif($report_type == 'room'): ?>
        <?php 
        $sql = "SELECT r.*, 
                rt.type_name, rt.price,
                (SELECT c.cus_name FROM rental rent_t JOIN customer c ON rent_t.cus_id = c.cus_id WHERE rent_t.room_id = r.room_id AND rent_t.status = 'Active' LIMIT 1) as current_customer 
                FROM room r 
                LEFT JOIN room_type rt ON r.type_id = rt.type_id 
                ORDER BY r.room_no ASC";
        $result = $conn->query($sql);
        ?>
        <h3 style="margin-bottom: 15px; font-size: 18px;"><i class="fa-solid fa-door-open"></i> ລາຍງານສະຖານະຫ້ອງພັກທັງໝົດ</h3>
        <table>
            <thead>
                <tr>
                    <th>ເບີຫ້ອງ</th>
                    <th>ປະເພດຫ້ອງ</th>
                    <th>ລາຄາ (₭)</th>
                    <th>ຜູ້ເຊົ່າປັດຈຸບັນ</th>
                    <th>ສະຖານະຫ້ອງ</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result && $result->num_rows > 0): while($row = $result->fetch_assoc()): ?>
                <?php 
                    $room_type_name = $row['type_name'] ?? '-';
                    $room_price = $row['price'] ?? 0;
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['room_no'] ?? '-'); ?></strong></td>
                    <td><?php echo htmlspecialchars($room_type_name); ?></td>
                    <td><strong><?php echo number_format((float)$room_price, 2); ?> ₭</strong></td>
                    <td><?php echo $row['current_customer'] ? $row['current_customer'] : '<span style="color:#94a3b8; font-style:italic;">- ບໍ່ມີ -</span>'; ?></td>
                    <td>
                        <?php if($row['current_customer']): ?>
                            <span class="status-badge inactive-status">🔴 มีผู้เช่าແລ້ວ</span>
                        <?php else: ?>
                            <span class="status-badge active-status">🟢 ຫ້ອງວ່າງ</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="5" class="no-data">ບໍ່ມີຂໍ້ມູນຫ້ອງພັກ</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

</body>
</html>