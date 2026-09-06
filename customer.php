<?php
session_start();
if(!isset($_SESSION['emp_id'])){
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost","root","","apartment_db");
if($conn->connect_error){
    die("DB Error: ".$conn->connect_error);
}

// ກຳນົດໃຫ້ຮອງຮັບພາສາລາວໃນການດຶງຂໍ້ມູນ
$conn->set_charset("utf8mb4");

// ປ່ຽນເປັນ ASC ເພື່ອໃຫ້ ID ລຽງຈາກໜ້ອຍໄປຫາຫຼາຍ
$result = $conn->query("SELECT * FROM customer ORDER BY cus_id ASC");
?>

<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<title>Customer Management | Apartment</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Google Fonts: Poppins & Noto Sans Lao ສໍາລັບພາສາລາວທີ່ສວຍງາມ -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome Icons ສໍາລັບໄອຄອນຕ່າງໆ -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4f46e5, #7c3aed);
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
        max-width: 1200px;
        margin: 30px auto;
        background: #fff;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }

    /* --- Header & Layout --- */
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
        color: #16a34a; /* ໂທນສີຂຽວໃຫ້ກົງກັບ Icon ລູກຄ້າ */
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
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
    }

    /* --- Table Styles --- */
    .table-wrap {
        width: 100%;
        overflow-x: auto; /* ປ້ອງກັນຕາຕະລາງລົ້ນໜ້າຈໍໃນມືຖື */
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

    /* --- Action Buttons --- */
    .action {
        display: flex;
        gap: 8px;
    }

    .action .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    /* ປ່ຽນປຸ່ມແກ້ໄຂ (Edit) ເປັນໂທນສີຟ້າ */
    .action .edit {
        background: #e0f2fe;
        color: #0284c7;
    }

    .action .edit:hover {
        background: #0284c7;
        color: #fff;
    }

    .action .delete {
        background: #fee2e2;
        color: #b91c1c;
    }

    .action .delete:hover {
        background: #b91c1c;
        color: #fff;
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

        <!-- ສ່ວນຫົວ ແລະ ປຸ່ມກົດ -->
        <div class="top-bar">
            <h2><i class="fa-solid fa-address-book"></i> ຂໍ້ມູນລູກຄ້າ</h2>
            <div class="btn-group">
                <a href="dashboard.php" class="btn btn-back">
                    <i class="fa-solid fa-house"></i> ໜ້າຫຼັກ
                </a>
                <a href="customer_add.php" class="btn btn-add">
                    <i class="fa-solid fa-user-plus"></i> ເພີ່ມລູກຄ້າ
                </a>
            </div>
        </div>

        <!-- ຕາຕະລາງສະແດງຜົນ -->
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ຊື່ລູກຄ້າ</th>
                        <th>ເລກບັດປະຈຳຕົວ</th>
                        <th>ເບີໂທລະສັບ</th>
                        <th>ທີ່ຢູ່</th>
                        <th style="text-align: center;">ຈັດການ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php 
                        $id_counter = 1; // ກຳນົດຕົວແປເລີ່ມຕົ້ນ
                        while($row = $result->fetch_assoc()): 
                            // ຈັດູບແບບຕົວເລກໃຫ້ເປັນ 3 ຫຼັກ (001, 002, 003...)
                            $formatted_id = str_pad($id_counter, 3, '0', STR_PAD_LEFT);
                            $id_counter++;
                        ?>
                        <tr>
                            <td><strong><?php echo $formatted_id; ?></strong></td>
                            <td><?php echo htmlspecialchars($row['cus_name']); ?></td>
                            <td><i class="fa-solid fa-id-card fa-sm" style="color: var(--text-muted); margin-right: 5px;"></i> <?php echo htmlspecialchars($row['id_card']); ?></td>
                            <td><i class="fa-solid fa-phone fa-sm" style="color: var(--text-muted); margin-right: 5px;"></i> <?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td>
                                <div class="action" style="justify-content: center;">
                                    <a href="customer_edit.php?id=<?php echo $row['cus_id']; ?>" class="action-btn edit" title="ແກ້ໄຂ">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="customer_delete.php?id=<?php echo $row['cus_id']; ?>" class="action-btn delete" title="ລົບ" onclick="return confirm('ຢືນຢັນການລົບຂໍ້ມູນລູກຄ້າຄົນນີ້?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                <i class="fa-solid fa-users-slash fa-2x"></i> <br><br> ບໍ່ມີຂໍ້ມູນລູກຄ້າໃນລະບົບ
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