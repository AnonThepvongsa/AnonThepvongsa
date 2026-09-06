<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$conn = new mysqli("localhost","root","","apartment_db");
if($conn->connect_error){
    die("DB Error: ".$conn->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM employee WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$username);
$stmt->execute();
$result = $stmt->get_result();

if($row = $result->fetch_assoc()){

    // เช็คแบบตรง ๆ (ไม่ใช้ hash)
    if($password == "1234"){

        $_SESSION['emp_id']   = $row['emp_id'];
        $_SESSION['emp_name'] = $row['emp_name'];

        header("Location: dashboard.php");
        exit();

    } else {
        echo "❌ Password ບໍ່ຖືກ";
    }

} else {
    echo "❌ Username ບໍ່ພົບ";
}
?>