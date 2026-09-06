<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if(!isset($_SESSION['emp_id'])){
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "apartment_db");
if($conn->connect_error){ 
    die("Database Error : " . $conn->connect_error); 
}
$conn->set_charset("utf8mb4");

// รับค่า cus_id ແລະ start_date ທີ່ສົ່ງມາຈາກຕາຕະລາງ rental.php
$cus_id = $_GET['cus_id'] ?? '';
$start_date = $_GET['start_date'] ?? '';

if(!empty($cus_id) && !empty($start_date)){
    // ເປີດ Transaction ປ້ອງກັນຜິດພາດ
    $conn->begin_transaction();

    try {
        // 1. ດຶງເອົາ room_id ທຸກຫ້ອງທີ່ກ່ຽວຂ້ອງກັບສັນຍານີ້ ມາປ່ຽນສະຖານະຫ້ອງກັບຄືນເປັນ 'free' (ວ່າງ) ໃຫ້ຄົບທຸກຫ້ອງ
        $stmt_room = $conn->prepare("SELECT room_id FROM rental WHERE cus_id = ? AND start_date = ?");
        $stmt_room->bind_param("is", $cus_id, $start_date);
        $stmt_room->execute();
        $result_room = $stmt_room->get_result();

        while($row_room = $result_room->fetch_assoc()){
            $room_id = $row_room['room_id'];
            
            // ປ່ຽນສະຖານະຫ້ອງໃຫ້ກັບມາເປັນວ່າງ
            $update_room = $conn->prepare("UPDATE room SET status = 'free' WHERE room_id = ?");
            $update_room->bind_param("i", $room_id);
            $update_room->execute();
            $update_room->close();
        }
        $stmt_room->close();

        // 2. ລົບຂໍ້ມູນການເຊົ່າອອກຈາກຕາຕະລາງ rental ທຸກຫ້ອງທີ່ມີ cus_id ແລະ start_date ດຽວກັນນີ້ພ້ອມກັນ
        $stmt_del = $conn->prepare("DELETE FROM rental WHERE cus_id = ? AND start_date = ?");
        $stmt_del->bind_param("is", $cus_id, $start_date);
        $stmt_del->execute();
        $stmt_del->close();

        // ຢືນຢັນການເຮັດວຽກ
        $conn->commit();
        echo "<script>alert('ລົບຂໍ້ມູນການເຊົ່າທຸກຫ້ອງໃນສັນຍານີ້ສຳເລັດແລ້ວ!'); window.location='rental.php';</script>";
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $error_msg = addslashes($e->getMessage());
        echo "<script>alert('ເກີດຂໍ້ຜິດພາດ: $error_msg'); window.history.back();</script>";
        exit();
    }
} else {
    echo "<script>alert('ຂໍ້ມູນບໍ່ຖືກຕ້ອງ!'); window.location='rental.php';</script>";
    exit();
}

$conn->close();
?>