<?php
session_start();
if(!isset($_SESSION['emp_id'])){
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost","root","","apartment_db");
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

// ກຳນົດໃຫ້ຮອງຮັບພາສາລາວໃນການດຶງຂໍ້ມູນ
$conn->set_charset("utf8mb4");

/* 
  ຄຳສັ່ງ SQL: ດຶງຂໍ້ມູນປະເພດຫ້ອງທັງໝົດ 
  (ຖ້າຊື່ຄໍລຳເງິນມັດຈຳໃນຕາຕະລາງ room_type ຂອງທ່ານບໍ່ແມ່ນ deposit ໃຫ້ປ່ຽນຊື່ຕາມ Database ຂອງທ່ານ)
*/
$result = $conn->query("SELECT type_id, type_name, price, deposit FROM room_type ORDER BY type_id DESC");
?>

<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<title>Room Type Management | Apartment</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Google Fonts: Poppins & Noto Sans Lao ສໍາລັບພາສາລາວທີ່ສວຍງາມ -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome Icons ສໍາລັບໄອຄອນຕ່າງໆ -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #0284c7, #0369a1); /* ໂທນສີຟ້າຄາມສໍາລັບປະເພດຫ້ອງ */
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
        padding: 20px;
    }

    .main {
        width: 100%;
    }

    .container {
        max-width: 950px;
        margin: 30px auto;
        background: #fff;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }

    /* --- Header Layout --- */
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    h2 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    h2 i {
        color: #0284c7;
    }

    .btn-group {
        display: flex;
        gap: 10px;
    }

    /* --- Buttons Style --- */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .btn-back {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-back:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .btn-add {
        background: var(--primary-gradient);
        color: #fff;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(2, 132, 199, 0.3);
    }

    /* --- Table Styles --- */
    .table-wrap {
        width: 100%;
        overflow-x: auto;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        text-align: left;
        font-size: 15px;
    }

    th, td {
        padding: 16px 20px;
    }

    th {
        background: #f8fafc;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        border-bottom: 2px solid var(--border-color);
    }

    tr {
        border-bottom: 1px solid var(--border-color);
        transition: background 0.2s ease;
    }

    tr:last-child {
        border-bottom: none;
    }

    tr:hover {
        background: #f8fafc;
    }

    td {
        color: #334155;
        vertical-align: middle;
    }

    .price-text {
        font-weight: 600;
        color: #059669; /* ສີຂຽວເນັ້ນເລື່ອງລາຄາ */
    }

    .deposit-text {
        font-weight: 600;
        color: #d97706; /* ສີສົ້ມເນັ້ນເລື່ອງເງິນມັດຈຳ */
    }

    /* --- Responsive ມືຖື --- */
    @media (max-width: 576px) {
        .top-bar {
            flex-direction: column;
            align-items: flex-start;
        }
        .btn-group {
            width: 100%;
        }
        .btn {
            flex: 1;
            justify-content: center;
        }
    }
</style>
</head>
<body>

<div class="main">
    <div class="container">

        <!-- ສ່ວນຫົວ ແລະ ປຸ່ມຄວບຄຸມ -->
        <div class="top-bar">
            <h2><i class="fa-solid fa-tags"></i> ຈັດການປະເພດຫ້ອງພັກ</h2>
            <div class="btn-group">
                <a href="room.php" class="btn btn-back">
                    <i class="fa-solid fa-arrow-left"></i> ກັບຄືນ
                </a>
                <a href="room_type_add.php" class="btn btn-add">
                    <i class="fa-solid fa-plus"></i> ເພີ່ມປະເພດຫ້ອງ
                </a>
            </div>
        </div>

        <!-- ຕາຕະລາງສະແດງຜົນ -->
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width: 100px;">ID</th>
                        <th>ຊື່ປະເພດຫ້ອງ</th>
                        <th>ລາຄາ / ເດືອນ</th>
                        <th>ເງິນມັດຈຳ (Deposit)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result && $result->num_rows > 0): ?>
                        <?php 
                        $i = 1; 
                        while($row = $result->fetch_assoc()): 
                            $formatted_id = str_repeat($i, 3);
                            // ກວດສອບປ້ອງກັນ error ຖ້າຕາຕະລາງຍັງບໍ່ມີຄໍລຳ deposit
                            $deposit_val = isset($row['deposit']) ? $row['deposit'] : 0;
                        ?>
                        <tr>
                            <td><strong><?php echo $formatted_id; ?></strong></td>
                            <td><span style="font-weight: 500; color: #0f172a;"><?php echo htmlspecialchars($row['type_name']); ?></span></td>
                            <td>
                                <span class="price-text">
                                    <?php echo number_format($row['price'], 2); ?> ກີບ 
                                </span>
                            </td>
                            <td>
                                <span class="deposit-text">
                                    <?php echo number_format($deposit_val, 2); ?> ກີບ
                                </span>
                            </td>
                        </tr>
                        <?php 
                        $i++;
                        endwhile; 
                        ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 45px;">
                                <i class="fa-solid fa-folder-open fa-2x" style="margin-bottom: 10px;"></i> <br> ບໍ່ມີຂໍ້ມູນປະເພດຫ້ອງໃນລະບົບ
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>