<?php
session_start();
if(!isset($_SESSION['emp_id'])){ header("Location: login.php"); exit(); }

$conn = new mysqli("localhost", "root", "", "apartment_db");
if($conn->connect_error){ die("Connection failed: " . $conn->connect_error); }
mysqli_set_charset($conn, "utf8mb4");

if(!isset($_GET['id']) || empty($_GET['id'])){ die("Error: ບໍ່ພົບ ID ຫ້ອງ"); }
$id = intval($_GET['id']);

/* ຈັດການການອັບເດດ */
if(isset($_POST['update'])){
    $room_no = mysqli_real_escape_string($conn, $_POST['room_no']);
    $floor = intval($_POST['floor']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $type_id = intval($_POST['type_id']);

    $sql = "UPDATE `room` SET `room_no`='$room_no', `floor`=$floor, `status`='$status', `type_id`=$type_id WHERE `room_id`=$id";
    
    if($conn->query($sql) === TRUE){
        // ແກ້ໄຂບ່ອນນີ້ໃຫ້ໄປ room.php
        echo "<script>alert('ອັບເດດຂໍ້ມູນສຳເລັດ!'); window.location='room.php';</script>";
        exit();
    } else {
        die("Error Database: " . $conn->error);
    }
}

$room = $conn->query("SELECT * FROM `room` WHERE `room_id`=$id")->fetch_assoc();
$type = $conn->query("SELECT * FROM `room_type`");
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ແກ້ໄຂຂໍ້ມູນຫ້ອງ</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Noto+Sans+Lao:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', 'Noto Sans Lao', sans-serif; background: #f8fafc; display: flex; justify-content: center; padding: 40px 20px; }
        .container { background: #fff; width: 100%; max-width: 450px; padding: 30px; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 25px; color: #1e293b; text-align: center; }
        label { display: block; font-weight: 600; margin-bottom: 5px; color: #475569; }
        input, select { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; }
        button { width: 100%; padding: 12px; background: #4f46e5; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        button:hover { background: #4338ca; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #64748b; text-decoration: none; font-size: 14px; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fa-solid fa-pen-to-square"></i> ແກ້ໄຂຂໍ້ມູນຫ້ອງ</h2>
    <form method="post">
        <label>ເລກຫ້ອງ</label>
        <input type="text" name="room_no" value="<?php echo htmlspecialchars($room['room_no'] ?? ''); ?>" required>

        <label>ຊັ້ນ</label>
        <input type="number" name="floor" value="<?php echo htmlspecialchars($room['floor'] ?? ''); ?>" required>

        <label>ສະຖານະຫ້ອງ</label>
        <select name="status">
            <option value="free" <?php if(($room['status']??'') == 'free') echo 'selected'; ?>>ວ່າງ (Free)</option>
            <option value="occupied" <?php if(($room['status']??'') == 'occupied') echo 'selected'; ?>>ຖືກເຊົ່າ (Occupied)</option>
            <option value="repair" <?php if(($room['status']??'') == 'repair') echo 'selected'; ?>>ກຳລັງສ້ອມແປງ (Repair)</option>
        </select>

        <label>ປະເພດຫ້ອງ</label>
        <select name="type_id">
            <?php while($row = $type->fetch_assoc()){ ?>
                <option value="<?php echo $row['type_id']; ?>" <?php if(($room['type_id']??'') == $row['type_id']) echo 'selected'; ?>>
                    <?php echo $row['type_name']; ?>
                </option>
            <?php } ?>
        </select>

        <button type="submit" name="update">ບັນທຶກການປ່ຽນແປງ</button>
        <!-- ແກ້ໄຂບ່ອນນີ້ໃຫ້ໄປ room.php -->
        <a href="room.php" class="back-link">ກັບຄືນຫາໜ້າລາຍການ</a>
    </form>
</div>
</body>
</html>