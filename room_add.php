<?php
$conn = new mysqli("localhost","root","","apartment_db");

if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}
mysqli_set_charset($conn, "utf8mb4");

/* ດຶງ room type */
$type = $conn->query("SELECT * FROM room_type");

$success_msg = "";
$error_msg = "";

if(isset($_POST['save'])){
    $room_no = $_POST['room_no'];
    $floor = $_POST['floor'];
    $status = $_POST['status'];
    $type_id = $_POST['type_id'];

    $stmt = $conn->prepare("INSERT INTO room(room_no,floor,status,type_id) VALUES(?,?,?,?)");
    $stmt->bind_param("ssss",$room_no,$floor,$status,$type_id);

    if($stmt->execute()){
        $success_msg = "ບັນທຶກສຳເລັດແລ້ວ!";
    }else{
        $error_msg = "ຜິດພາດ: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ເພີ່ມຫ້ອງພັກ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Google Font ພາສາລາວໃຫ້ຟອນຕ໌ມົນງາມ -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Noto Sans Lao', sans-serif;
        }

        body {
            background-color: #f4f6f9; /* ພື້ນຫຼັງສີເທົາອ່ອນແບບຄລາສສິກ */
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .main {
            width: 100%;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .container {
            background-color: #ffffff; /* ກ່ອງຟອມສີຂາວ */
            width: 100%;
            max-width: 450px;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); /* ເງົາແບບນຸ່ມນວນ */
            border: 1px solid #e1e4e8;
        }

        h2 {
            font-size: 22px;
            color: #2c3e50;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
        }

        /* ຈັດການຮູບແບບ Input ແລະ Select */
        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            background-color: #fff;
            color: #333;
            outline: none;
            transition: all 0.2s ease;
        }

        /* ເມື່ອກົດຄລິກໃສ່ຊ່ອງປ້ອນຂໍ້ມູນ */
        input:focus, select:focus {
            border-color: #3498db; /* ປ່ຽນເປັນຂອບສີຟ້າເວລາໂຟກັສ */
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }

        /* ກ່ອງແຈ້ງເຕືອນ */
        .alert {
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 16px;
            text-align: center;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* ຈັດການປຸ່ມກົດ */
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            font-size: 15px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: background 0.2s ease;
        }

        .btn.save {
            background-color: #2ecc71; /* ສີຂຽວ */
            color: white;
        }

        .btn.save:hover {
            background-color: #27ae60;
        }

        .btn.back {
            background-color: #95a5a6; /* ສີເທົາ */
            color: white;
        }

        .btn.back:hover {
            background-color: #7f8c8d;
        }
    </style>
</head>
<body>

<div class="main">
    <div class="container">

        <h2>➕ ເພີ່ມຫ້ອງພັກ</h2>

        <!-- ສະແດງຂໍ້ຄວາມສະຖານະການບັນທຶກ -->
        <?php if(!empty($success_msg)){ ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php } ?>
        
        <?php if(!empty($error_msg)){ ?>
            <div class="alert alert-error"><?php echo $error_msg; ?></div>
        <?php } ?>

        <form method="post">

            <input type="text" name="room_no" placeholder="ເລກຫ້ອງ" required>

            <input type="number" name="floor" placeholder="ຊັ້ນ" required>

            <select name="status" required>
                <option value="">-- ເລືອກສະຖານະ --</option>
                <option value="free">ວ່າງ</option>
                <option value="rented">ຖືກເຊົ່າ</option>
            </select>

            <select name="type_id" required>
                <option value="">-- ເລືອກປະເພດຫ້ອງ --</option>
                <?php
                if($type->num_rows > 0){
                    while($row = $type->fetch_assoc()){
                        ?>
                        <option value="<?php echo $row['type_id']; ?>">
                            <?php echo htmlspecialchars($row['type_name']); ?>
                        </option>
                        <?php
                    }
                }
                ?>
            </select>

            <div class="btn-group">
                <button type="submit" name="save" class="btn save">💾 ບັນທຶກ</button>
                <a href="room.php" class="btn back">⬅ ກັບຄືນ</a>
            </div>

        </form>

    </div>
</div>

</body>
</html>