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

// ກຳນົດໃຫ້ຮອງຮັບພາສາລາວໃນການບັນທຶກຂໍ້ມູນ
$conn->set_charset("utf8mb4");

if(isset($_POST['save'])){
    $type_name = $_POST['type_name'];
    $price = $_POST['price'];
    $deposit = $_POST['deposit']; // ຮັບຄ່າຄ່າມັດຈຳຈາກຟອມ

    // ໝາຍເຫດ: ໃຫ້ກວດສອບວ່າໃນຕາຕະລາງ room_type ຂອງທ່ານມີຄໍລຳ deposit ແລ້ວຫຼືຍັງ 
    // ຖ່າຍັງບໍ່ມີ ໃຫ້ໄປ ALTER TABLE ເພີ່ມຄໍລຳ deposit 0 ກ່ອນ (ຕົວຢ່າງ: ALTER TABLE room_type ADD COLUMN deposit DECIMAL(10,2) NOT NULL DEFAULT 0.00;)
    $stmt = $conn->prepare("INSERT INTO room_type(type_name, price, deposit) VALUES(?, ?, ?)");
    $stmt->bind_param("sdd", $type_name, $price, $deposit);
    $stmt->execute();

    header("Location: room_type.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<title>Add Room Type | Apartment</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Google Fonts: Poppins & Noto Sans Lao ສໍາລັບພາສາລາວທີ່ສວຍງາມ -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome Icons ສໍາລັບໄອຄອນຕ່າງໆ -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #0284c7, #0369a1);
        --bg-color: #f8fafc;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --focus-color: #38bdf8;
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
        min-height: 90vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .container {
        width: 100%;
        max-width: 500px;
        background: #fff;
        padding: 35px;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
    }

    h2 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 15px;
    }

    h2 i {
        color: #0284c7;
    }

    /* --- Form Styling --- */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
    }

    .input-wrapper {
        position: relative;
    }

    .input-wrapper i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 16px;
    }

    .form-control {
        width: 100%;
        padding: 12px 14px 12px 42px; /* ເວັ້ນໄລຍະດ້ານຊ້າຍໄວ້ໃສ່ Icon */
        border: 1px solid var(--border-color);
        border-radius: 10px;
        font-size: 15px;
        color: var(--text-main);
        background: #f8fafc;
        transition: all 0.2s ease;
        outline: none;
    }

    .form-control:focus {
        background: #fff;
        border-color: #0284c7;
        box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.15);
    }

    /* --- Actions Button Layout --- */
    .btn-group {
        display: flex;
        flex-direction: row-reverse; /* ເອົາປຸ່ມບັນທຶກໄວ້ຂວາ ປຸ່ມກັບໄວ້ຊ້າຍ */
        gap: 12px;
        margin-top: 30px;
    }

    .btn {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .btn-save {
        background: var(--primary-gradient);
        color: #fff;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
    }

    .btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(2, 132, 199, 0.3);
    }

    .btn-back {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-back:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    /* --- Responsive ສໍາລັບຈໍນ້ອຍ --- */
    @media (max-width: 480px) {
        .container {
            padding: 20px;
        }
        .btn-group {
            flex-direction: column; /* ປ່ຽນເປັນລຽງລົງລຸ່ມໃນມືຖື */
        }
    }
</style>
</head>
<body>

<div class="main">
    <div class="container">

        <h2><i class="fa-solid fa-square-plus"></i> ເພີ່ມປະເພດຫ້ອງພັກ</h2>

        <form method="post">
            
            <!-- 1. ຊື່ປະເພດຫ້ອງ -->
            <div class="form-group">
                <label for="type_name">ຊື່ປະເພດຫ້ອງ <span style="color: red;">*</span></label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-tag"></i>
                    <input type="text" id="type_name" name="type_name" class="form-control" placeholder="ຕົວຢ່າງ: ຫ້ອງ VIP, ຫ້ອງທຳມະດາ A" required autocomplete="off">
                </div>
            </div>

            <!-- 2. ລາຄາຄ່າເຊົ່າ -->
            <div class="form-group">
                <label for="price">ລາຄາຄ່າເຊົ່າ / ເດືອນ <span style="color: red;">*</span></label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-money-bill-wave"></i>
                    <input type="number" id="price" name="price" class="form-control" placeholder="ກຳນົດລາຄາຄ່າເຊົ່າ" min="0" step="any" required>
                </div>
            </div>

            <!-- 3. ຄ່າມັດຈຳ -->
            <div class="form-group">
                <label for="deposit">ຄ່າມັດຈຳ (Deposit) <span style="color: red;">*</span></label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <input type="number" id="deposit" name="deposit" class="form-control" placeholder="ກຳນົດຄ່າມັດຈຳ" min="0" step="any" required>
                </div>
            </div>

            <!-- ປຸ່ມກົດ -->
            <div class="btn-group">
                <button type="submit" class="btn btn-save" name="save">
                    <i class="fa-solid fa-floppy-disk"></i> ບັນທຶກຂໍ້ມູນ
                </button>
                <a href="room_type.php" class="btn btn-back">
                    <i class="fa-solid fa-arrow-left"></i> ຍົກເລີກ
                </a>
            </div>

        </form>

    </div>
</div>

</body>
</html>