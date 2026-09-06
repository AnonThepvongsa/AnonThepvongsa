<?php
session_start();
if(!isset($_SESSION['emp_id'])){
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost","root","","apartment_db");
if($conn->connect_error){ die("Connection failed: ".$conn->connect_error); }
$conn->set_charset("utf8mb4");

if(isset($_GET['rental_id'])) {
    $rental_id = $_GET['rental_id'];

    // 1. ດຶງເອົາ room_id ຈາກສັນຍາເຊົ່ານັ້ນ
    $sql = "SELECT room_id FROM rental WHERE rental_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $rental_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $room_id = $row['room_id'];

        $conn->begin_transaction();
        try {
            // 2. ປ່ຽນສະຖານະສັນຍາເຊົ່າເປັນ 'Inactive' (ປິດສັນຍາ)
            $update_rental = $conn->prepare("UPDATE rental SET status = 'Inactive' WHERE rental_id = ?");
            $update_rental->bind_param("i", $rental_id);
            $update_rental->execute();

            // 3. ປ່ຽນສະຖານະຫ້ອງໃຫ້ກັບຄືນມາເປັນ 'free' (ຫ້ອງຫວ່າງ)
            $update_room = $conn->prepare("UPDATE room SET status = 'free' WHERE room_id = ?");
            $update_room->bind_param("i", $room_id);
            $update_room->execute();

            $conn->commit();
            echo "<script>alert('ບັນທຶກການອອກຈາກຫ້ອງສຳເລັດ, ຫ້ອງນີ້ກັບມາເປັນຫ້ອງຫວ່າງແລ້ວ!'); window.location='rental.php';</script>";
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('ເກີດຂໍ້ຜິດພາດ: " . $e->getMessage() . "'); window.location='rental.php';</script>";
        }
    }
}
?>