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

// ປ່ຽນເປັນ ASC ເພື່ອໃຫ້ ID ເລີ່ມຈາກຄ່າໜ້ອຍໄປຫາຫຼາຍ (101, 102...)
$result = $conn->query("SELECT * FROM employee ORDER BY emp_id ASC");
?>

<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<title>Employee Management | Apartment</title>
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

    .container {
        max-width: 1200px;
        margin: 30px auto;
        background: #fff;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }

    /* --- Top Layout --- */
    .top {
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
        color: #4f46e5;
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
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
    }

    /* --- Table Styles --- */
    .table-responsive {
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
        background: #f8fafc; /* ເຮັດໃຫ້ມີ Highlight ເວລາເອົາເມົ້າໄປຊີ້ */
    }

    td {
        color: #334155;
        vertical-align: middle;
    }

    /* Badge ຕົບແຕ່ງຕຳແໜ່ງ */
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
    }
    .badge-position { background: #eff6ff; color: #1d4ed8; }

    /* --- Action Buttons --- */
    .action {
        display: flex;
        gap: 8px;
    }

    .action a {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        color: #fff;
        transition: all 0.2s ease;
    }

    .edit {
        background: #dcfce7;
        color: #15803d;
    }

    .edit:hover {
        background: #15803d;
        color: #fff;
    }

    .delete {
        background: #fee2e2;
        color: #b91c1c;
    }

    .delete:hover {
        background: #b91c1c;
        color: #fff;
    }

    /* --- Responsive ມືຖື --- */
    @media (max-width: 576px) {
        .top {
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

<div class="container">
    <!-- ສ່ວນຫົວ ແລະ ປຸ່ມກົດ -->
    <div class="top">
        <h2><i class="fa-solid fa-users-gear"></i> ຂໍ້ມູນພະນັກງານ</h2>
        <div class="btn-group">
            <a href="dashboard.php" class="btn btn-back">
                <i class="fa-solid fa-arrow-left"></i> ກັບໜ້າຫຼັກ
            </a>
            <a href="employee_add.php" class="btn btn-add">
                <i class="fa-solid fa-plus"></i> ເພີ່ມພະນັກງານ
            </a>
        </div>
    </div>

    <!-- ສ່ວນຕາຕະລາງສະແດງຜົນ -->
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ຊື່ພະນັກງານ</th>
                    <th>ເບີໂທລະສັບ</th>
                    <th>ທີ່ຢູ່</th>
                    <th>ຕຳແໜ່ງ</th>
                    <th>Username</th>
                    <th style="text-align: center;">ຈັດການ</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result->num_rows > 0): ?>
                    <?php 
                    $id_counter = 101; // ກຳນົດເລີ່ມຕົ້ນທີ່ 101
                    while($row = $result->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><strong><?php echo $id_counter++; ?></strong></td> <!-- ສະແດງ ແລະ ບວກເພີ່ມຂຶ້ນ -->
                        <td><?php echo htmlspecialchars($row['emp_name']); ?></td>
                        <td><i class="fa-solid fa-phone fa-sm text-muted"></i> <?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td>
                            <span class="badge badge-position">
                                <?php echo htmlspecialchars($row['position']); ?>
                            </span>
                        </td>
                        <td><code><?php echo htmlspecialchars($row['username']); ?></code></td>
                        <td>
                            <div class="action" style="justify-content: center;">
                                <!-- ຍັງຄົງສົ່ງ emp_id ຕົວຈິງໄປທີ່ປຸ່ມແກ້ໄຂ ແລະ ລົບ ເພື່ອໃຫ້ລະບົບເຮັດວຽກໄດ້ປົກກະຕິ -->
                                <a href="employee_edit.php?id=<?php echo $row['emp_id']; ?>" class="edit">
                                    <i class="fa-solid fa-pen-to-square"></i> ແກ້ໄຂ
                                </a>
                                <a href="employee_delete.php?id=<?php echo $row['emp_id']; ?>" class="delete" onclick="return confirm('ຢືນຢັນການລົບຂໍ້ມູນພະນັກງານຄົນນີ້?')">
                                    <i class="fa-solid fa-trash-can"></i> ລົບ
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            <i class="fa-solid fa-folder-open fa-2x"></i> <br> ບໍ່ມີຂໍ້ມູນພະນັກງານໃນລະບົບ
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>