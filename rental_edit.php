<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if(!isset($_SESSION['emp_id'])){ header("Location: login.php"); exit(); }

$conn = new mysqli("localhost", "root", "", "apartment_db");
$conn->set_charset("utf8mb4");

if(!isset($_GET['id'])){ die("ບໍ່ພົບ ID ສັນຍາ"); }
$rent_id = intval($_GET['id']);

// 1. ດຶງ cus_id ແລະ start_date ຈາກ rent_id ທີ່ສົ່ງມາ ເພື່ອໃຫ້ຮູ້ວ່າເປັນສັນຍາຊຸດດຽວກັນ
$stmt_info = $conn->prepare("SELECT cus_id, start_date FROM rental WHERE rent_id = ?");
$stmt_info->bind_param("i", $rent_id);
$stmt_info->execute();
$info_res = $stmt_info->get_result()->fetch_assoc();

if(!$info_res){ die("ບໍ່ພົບຂໍ້ມູນສັນຍາໃນລະບົບ"); }

$cus_id = $info_res['cus_id'];
$start_date_original = $info_res['start_date'];
$stmt_info->close();

// ບັນທຶກການແກ້ໄຂ
if(isset($_POST['update'])){
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $deposit = $_POST['deposit'];
    $rent_price = $_POST['rent_price'];
    $status = $_POST['status'];

    // ເລີ່ມຕົ້ນ Transaction ເພື່ອຄວາມປອດໄພຂອງຂໍ້ມູນ
    $conn->begin_transaction();
    try {
        // 2. ອັບເດດຂໍ້ມູນສັນຍາເຊົ່າ "ທຸກຫ້ອງ" ທີ່ມີ cus_id ແລະ start_date ດຽວກັນນີ້ພ້ອມກັນ
        $sql = "UPDATE rental SET start_date=?, end_date=?, deposit=?, rent_price=?, status=? WHERE cus_id=? AND start_date=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssddsis", $start_date, $end_date, $deposit, $rent_price, $status, $cus_id, $start_date_original);
        $stmt->execute();
        $stmt->close();

        // 3. ດຶງເອົາ room_id ທຸກຫ້ອງທີ່ກ່ຽວຂ້ອງກັບສັນຍານີ້ ມາປ່ຽນສະຖານະຫ້ອງໃຫ້ຄົບທຸກຫ້ອງ
        $stmt_rooms = $conn->prepare("SELECT room_id FROM rental WHERE cus_id = ? AND start_date = ?");
        $stmt_rooms->bind_param("is", $cus_id, $start_date);
        $stmt_rooms->execute();
        $res_rooms = $stmt_rooms->get_result();

        while($r = $res_rooms->fetch_assoc()){
            $room_id = $r['room_id'];

            if($status == 'Finished'){
                $update_room = $conn->prepare("UPDATE room SET status = 'free' WHERE room_id = ?");
                $update_room->bind_param("i", $room_id);
                $update_room->execute();
                $update_room->close();
            } 
            else if($status == 'Active'){
                $update_room = $conn->prepare("UPDATE room SET status = 'occupied' WHERE room_id = ?");
                $update_room->bind_param("i", $room_id);
                $update_room->execute();
                $update_room->close();
            }
        }
        $stmt_rooms->close();

        $conn->commit();
        echo "<script>alert('ອັບເດດຂໍ້ມູນ ແລະ ສະຖານະຫ້ອງທຸກຫ້ອງສຳເລັດ!'); window.location='rental.php';</script>";
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('ເກີດຂໍ້ຜິດພາດ: " . $e->getMessage() . "');</script>";
    }
}

// ດຶງຂໍ້ມູນເກົ່າມາສະແດງໃນຟອມ (ດຶງຈາກ rent_id ທີ່ກົດເຂົ້າມາ)
$data = $conn->query("SELECT * FROM rental WHERE rent_id=$rent_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ແກ້ໄຂຂໍ້ມູນເຊົ່າ</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&family=Noto+Sans+Lao&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', 'Noto Sans Lao', sans-serif; background: #f8fafc; padding: 40px 20px; }
        .container { max-width: 500px; margin: auto; background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #e2e8f0; border-radius: 8px; }
        button { width: 100%; padding: 12px; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
    </style>
</head>
<body>
<div class="container">
    <h2>ແກ້ໄຂຂໍ້ມູນເຊົ່າ (ທຸກຫ້ອງໃນສັນຍານີ້)</h2>
    <form method="POST">
        <label>ວັນທີເລີ່ມ</label>
        <input type="date" name="start_date" value="<?php echo $data['start_date']; ?>" required>
        
        <label>ວັນທີສິ້ນສຸດ</label>
        <input type="date" name="end_date" value="<?php echo $data['end_date']; ?>">
        
        <label>ຄ່າຄໍ້າປະກັນ (ຕໍ່ຫ້ອງ/ລວມ)</label>
        <input type="number" name="deposit" value="<?php echo $data['deposit']; ?>" required>
        
        <label>ຄ່າເຊົ່າ</label>
        <input type="number" name="rent_price" value="<?php echo $data['rent_price']; ?>" required>
        
        <label>ສະຖານະ</label>
        <select name="status">
            <option value="Active" <?php if($data['status']=='Active') echo 'selected'; ?>>ກຳລັງເຊົ່າ (Active)</option>
            <option value="Finished" <?php if($data['status']=='Finished') echo 'selected'; ?>>ສິ້ນສຸດແລ້ວ (Finished)</option>
        </select>
        
        <button type="submit" name="update">ບັນທຶກການແກ້ໄຂ</button>
        <a href="rental.php" style="display:block; text-align:center; margin-top:15px; color:#64748b; text-decoration: none;">ກັບຄືນ</a>
    </form>
</div>
</body>
</html>