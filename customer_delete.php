<?php
session_start();
if(!isset($_SESSION['emp_id'])){
    header("Location: login.php");
    exit();
}

// ເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
$conn = new mysqli("localhost", "root", "", "apartment_db");

if(isset($_GET['id'])){
    $id = intval($_GET['id']); // ປ້ອງກັນ SQL Injection ໂດຍການແປງເປັນຕົວເລກ

    // ກວດສອບກ່ອນລົບ: ປ້ອງກັນການລົບລູກຄ້າທີ່ມີການເຊົ່າຢູ່ (Optional ແຕ່ຄວນມີ)
    // ຖ້າຕາຕະລາງ customer ມີຄວາມສຳພັນກັບຕາຕະລາງ rental, 
    // ຄວນລົບຂໍ້ມູນໃນ rental ກ່ອນ ຫຼື ເຊັກກ່ອນ
    
    $sql = "DELETE FROM customer WHERE cus_id = $id";
    
    if($conn->query($sql) === TRUE) {
        // ລົບສຳເລັດແລ້ວສົ່ງກັບໄປໜ້າລາຍຊື່
        header("Location: customer.php?status=success");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
$conn->close();
?>