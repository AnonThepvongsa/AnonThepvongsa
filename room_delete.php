<?php
session_start();
if(!isset($_SESSION['emp_id'])){
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost","root","","apartment_db");
if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}

if(isset($_GET['id']) && !empty($_GET['id'])){
    $room_id = $_GET['id'];

    // ກວດສອບກ່ອນວ່າຫ້ອງນີ້ກຳລັງມີຄົນເຊົ່າຢູ່ຫຼືບໍ່ (ປ້ອງກັນການລົບຫ້ອງທີ່ມີລູກຄ້າຢູ່)
    $check = $conn->prepare("SELECT status FROM room WHERE room_id = ?");
    $check->bind_param("i", $room_id);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();

    if($res && ($res['status'] == 'Occupied' || $res['status'] == 'ຖືກເຊົ່າແລ້ວ')){
        header("Location: room_list.php?msg=occupied");
        exit();
    }

    // ເລີ່ມການລົບອອກຈາກ Database ຕົວຈິງ
    $stmt = $conn->prepare("DELETE FROM room WHERE room_id = ?");
    $stmt->bind_param("i", $room_id);
    
    if($stmt->execute()){
        header("Location: room_list.php?msg=del_success");
    } else {
        header("Location: room_list.php?msg=error");
    }
    $stmt->close();
} else {
    header("Location: room_list.php");
}
$conn->close();
?>