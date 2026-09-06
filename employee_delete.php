<?php
session_start();
if(!isset($_SESSION['emp_id'])){
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "apartment_db");

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    
    // ກັນບໍ່ໃຫ້ພະນັກງານລົບຕົວເອງອອກຈາກລະບົບ (ທາງເລືອກ)
    if($id == $_SESSION['emp_id']){
        echo "<script>alert('ເຈົ້າບໍ່ສາມາດລົບບັນຊີຂອງຕົນເອງໄດ້!'); window.location='employee.php';</script>";
        exit();
    }

    $sql = "DELETE FROM employee WHERE emp_id = $id";
    
    if($conn->query($sql) === TRUE) {
        header("Location: employee.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>