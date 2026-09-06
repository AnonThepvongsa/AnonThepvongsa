<?php
session_start();
if(!isset($_SESSION['emp_id'])){
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost","root","","apartment_db");
if($conn->connect_error){
    die("DB Error");
}

// ກຳນົດໃຫ້ຮອງຮັບພາສາລາວໃນການບັນທຶກຂໍ້ມູນ
$conn->set_charset("utf8mb4");

$msg = "";

if(isset($_POST['save'])){
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $position = trim($_POST['position']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if($name=="" || $username=="" || $password==""){
        $msg = "❌ ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບຖ້ວນ";
    }else{

        // check username duplicate
        $check = $conn->prepare("SELECT emp_id FROM employee WHERE username=?");
        $check->bind_param("s",$username);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0){
            $msg = "❌ Username ນີ້ມີຄົນໃຊ້ໃນລະບົບແລ້ວ";
        }else{

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO employee(emp_name,phone,address,position,username,password)
                    VALUES(?,?,?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss",$name,$phone,$address,$position,$username,$hash);
            $stmt->execute();

            header("Location: employee.php?success=1");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<title>Add Employee | Apartment Management</title>
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

    .box {
        width: 100%;
        max-width: 550px;
        background: #fff;
        padding: 35px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,.05);
    }

    h3 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 25px;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    h3 i {
        color: #4f46e5;
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
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
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
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
    }

    .save:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
    }

    .back {
        background: #f1f5f9;
        color: #475569;
    }

    .back:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    /* --- Message Alert --- */
    .msg {
        background: #fef2f2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 500;
    }
</style>
</head>
<body>

<div class="box">
    <h3><i class="fa-solid fa-user-plus"></i> ເພີ່ມຂໍ້ມູນພະນັກງານ</h3>

    <?php if($msg!=""): ?>
    <div class="msg"><?php echo $msg; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>ຊື່ ແລະ ນາມສະກຸນ <span style="color: red;">*</span></label>
            <input type="text" name="name" placeholder="ປ້ອນຊື່ພະນັກງານ" required>
        </div>

        <div class="form-group">
            <label>ເບີໂທລະສັບ</label>
            <input type="text" name="phone" placeholder="ປ້ອນເບີໂທລະສັບ">
        </div>

        <div class="form-group">
            <label>ທີ່ຢູ່ປັດຈຸບັນ</label>
            <input type="text" name="address" placeholder="ປ້ອນທີ່ຢູ່">
        </div>

        <div class="form-group">
            <label>ຕຳແໜ່ງ</label>
            <input type="text" name="position" placeholder="ປ້ອນຕຳແໜ່ງງານ">
        </div>

        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">

        <div class="form-group">
            <label>Username <span style="color: red;">*</span></label>
            <input type="text" name="username" placeholder="ຕັ້ງ Username ສໍາລັບເຂົ້າລະບົບ" required>
        </div>

        <div class="form-group">
            <label>Password <span style="color: red;">*</span></label>
            <input type="password" name="password" placeholder="ຕັ້ງ Password" required>
        </div>

        <div class="btn-group">
            <a href="employee.php" class="btn back">
                <i class="fa-solid fa-arrow-left"></i> ກັບຄືນ
            </a>
            <button class="btn save" type="submit" name="save">
                <i class="fa-solid fa-floppy-disk"></i> ບັນທຶກຂໍ້ມູນ
            </button>
        </div>
    </form>
</div>

</body>
</html>