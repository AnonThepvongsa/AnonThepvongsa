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

// ກຳນົດໃຫ້ຮອງຮັບພາສາລາວໃນການດຶງ ແລະ ອັບເດດຂໍ້ມູນ
$conn->set_charset("utf8mb4");

// ເອົາ id ຈາກ URL (ປ່ຽນໃຫ້ກົງກັບໄຟລ໌ຫຼັກ customer.php ທີ່ເຮົາໃຊ້)
if(!isset($_GET['id'])){
    header("Location: customer.php");
    exit();
}
$id = intval($_GET['id']);

// ເອົາຂໍ້ມູນລູກຄ້າ
$sql = "SELECT * FROM customer WHERE cus_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if(!$customer){
    die("<div style='font-family: sans-serif; text-align: center; padding: 50px; color: #ef4444;'>❌ ບໍ່ພົບຂໍ້ມູນລູກຄ້າຄົນນີ້ໃນລະບົບ</div>");
}

$error_msg = "";

// ເມື່ອກົດບັນທຶກການແກ້ໄຂ
if(isset($_POST['save'])){
    $name = trim($_POST['cus_name']);
    $id_card = trim($_POST['id_card']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if($name == "") {
        $error_msg = "❌ ກະລຸນາປ້ອນຊື່ລູກຄ້າ";
    } else {
        $update = "UPDATE customer SET cus_name=?, id_card=?, phone=?, address=? WHERE cus_id=?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("ssssi", $name, $id_card, $phone, $address, $id);

        if($stmt->execute()){
            header("Location: customer.php?update_success=1");
            exit();
        } else {
            $error_msg = "❌ ບໍ່ສາມາດບັນທຶກຂໍ້ມູນໄດ້: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<title>Edit Customer | Apartment Management</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Google Fonts: Poppins & Noto Sans Lao ສໍາລັບພາສາລາວທີ່ສວຍງາມ -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome Icons ສໍາລັບໄອຄອນຕ່າງໆ -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #10b981, #059669); /* ໂທນສີຂຽວທີ່ສະແດງເຖິງການຈັດການຂໍ້ມູນ */
        --bg-color: #f8fafc;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border-color: #cbd5e1;
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
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .main {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .container {
        width: 100%;
        max-width: 520px;
        background: #fff;
        padding: 35px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,.05);
    }

    h2 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 25px;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    h2 i {
        color: #f59e0b; /* ໃຊ້ໄອຄອນສີສົ້ມ/ເຫຼືອງ ແທນການ Edit */
    }

    /* --- Form Styling --- */
    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #475569;
        margin-bottom: 6px;
    }

    input, select, textarea {
        width: 100%;
        padding: 12px 14px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        font-size: 15px;
        color: var(--text-main);
        background-color: #fff;
        transition: all 0.2s ease;
        outline: none;
    }

    textarea {
        resize: vertical;
        min-height: 90px;
    }

    input:focus, select:focus, textarea:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
    }

    /* --- Buttons --- */
    .btn-group {
        display: flex;
        gap: 12px;
        margin-top: 25px;
    }

    .btn {
        flex: 1;
        padding: 12px;
        border-radius: 10px;
        border: none;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .save {
        background: var(--primary-gradient);
        color: #fff;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }

    .save:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
    }

    .cancel {
        background: #f1f5f9;
        color: #64748b;
    }

    .cancel:hover {
        background: #fee2e2;
        color: #ef4444;
    }

    /* --- Error Box --- */
    .error-box {
        background: #fef2f2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }
</style>
</head>
<body>

<div class="main">
    <div class="container">

        <h2><i class="fa-solid fa-user-pen"></i> ແກ້ໄຂຂໍ້ມູນລູກຄ້າ</h2>

        <?php if($error_msg != ""): ?>
            <div class="error-box"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>ຊື່ ແລະ ນາມສະກຸນ <span style="color: red;">*</span></label>
                <input type="text" name="cus_name" value="<?php echo htmlspecialchars($customer['cus_name']); ?>" placeholder="ປ້ອນຊື່ລູກຄ້າ" required>
            </div>

            <div class="form-group">
                <label>ເລກບັດປະຈຳຕົວ</label>
                <input type="text" name="id_card" value="<?php echo htmlspecialchars($customer['id_card']); ?>" placeholder="ປ້ອນເລກບັດປະຈຳຕົວ ຫຼື ພາສປອດ">
            </div>

            <div class="form-group">
                <label>ເບີໂທລະສັບ <span style="color: red;">*</span></label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" placeholder="ປ້ອນເບີໂທລະສັບ" required>
            </div>

            <div class="form-group">
                <label>ທີ່ຢູ່ປັດຈຸບັນ <span style="color: red;">*</span></label>
                <textarea name="address" placeholder="ປ້ອນທີ່ຢູ່ລະອຽດ..." required><?php echo htmlspecialchars($customer['address']); ?></textarea>
            </div>

            <div class="btn-group">
                <a href="customer.php" class="btn cancel">
                    <i class="fa-solid fa-xmark"></i> ຍົກເລີກ
                </a>
                <button type="submit" name="save" class="btn save">
                    <i class="fa-solid fa-floppy-disk"></i> ບັນທຶກການແກ້ໄຂ
                </button>
            </div>
        </form>

    </div>
</div>

</body>
</html>