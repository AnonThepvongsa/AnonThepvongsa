<?php
$conn = new mysqli("localhost","root","","apartment_db");
if($conn->connect_error){
    die("DB Error: ".$conn->connect_error);
}

// ກຳນົດໃຫ້ຮອງຮັບພາສາລາວໃນການບັນທຶກຂໍ້ມູນ
$conn->set_charset("utf8mb4");

if(isset($_POST['save'])){
    $name = trim($_POST['name']);
    $id_card = trim($_POST['id_card']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $stmt = $conn->prepare("INSERT INTO customer(cus_name,id_card,phone,address) VALUES(?,?,?,?)");
    $stmt->bind_param("ssss",$name,$id_card,$phone,$address);
    $stmt->execute();

    header("Location: customer.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<title>Add Customer | Apartment Management</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Google Fonts: Poppins & Noto Sans Lao ສໍາລັບພາສາລາວທີ່ສວຍງາມ -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome Icons ສໍາລັບໄອຄອນຕ່າງໆ -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #10b981, #059669); /* ໃຊ້ໂທນສີຂຽວໃຫ້ກົງກັບປຸ່ມເພີ່ມລູກຄ້າ */
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
        color: #10b981;
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

    input, select {
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

    input:focus, select:focus {
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

    .back {
        background: #f1f5f9;
        color: #475569;
    }

    .back:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
</style>
</head>
<body>

<div class="main">
    <div class="container">

        <h2><i class="fa-solid fa-user-plus"></i> ເພີ່ມຂໍ້ມູນລູກຄ້າ</h2>

        <form method="post">
            <div class="form-group">
                <label>ຊື່ ແລະ ນາມສະກຸນ <span style="color: red;">*</span></label>
                <input type="text" name="name" placeholder="ປ້ອນຊື່ລູກຄ້າ" required>
            </div>

            <div class="form-group">
                <label>ເລກບັດປະຈຳຕົວ</label>
                <input type="text" name="id_card" placeholder="ປ້ອນເລກບັດປະຈຳຕົວ ຫຼື ພາສປອດ">
            </div>

            <div class="form-group">
                <label>ເບີໂທລະສັບ</label>
                <input type="text" name="phone" placeholder="ປ້ອນເບີໂທລະສັບ">
            </div>

            <div class="form-group">
                <label>ທີ່ຢູ່ປັດຈຸບັນ</label>
                <input type="text" name="address" placeholder="ປ້ອນທີ່ຢູ່">
            </div>

            <div class="btn-group">
                <a href="customer.php" class="btn back">
                    <i class="fa-solid fa-arrow-left"></i> ກັບຄືນ
                </a>
                <button class="btn save" type="submit" name="save">
                    <i class="fa-solid fa-floppy-disk"></i> ບັນທຶກຂໍ້ມູນ
                </button>
            </div>
        </form>

    </div>
</div>

</body>
</html>