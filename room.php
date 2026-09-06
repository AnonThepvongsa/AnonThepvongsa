<?php
session_start();
if(!isset($_SESSION['emp_id'])){
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost","root","","apartment_db");
if($conn->connect_error){ die("Connection failed: ".$conn->connect_error); }
$conn->set_charset("utf8mb4");

$sql = "SELECT room.*, room_type.type_name 
        FROM room
        LEFT JOIN room_type ON room.type_id = room_type.type_id
        ORDER BY room.room_id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<title>Room Management | Apartment</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root { --primary-color: #4f46e5; --bg-color: #f8fafc; --text-main: #1e293b; --text-muted: #64748b; --border-color: #e2e8f0; }
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', 'Noto Sans Lao', sans-serif; }
    body { background: var(--bg-color); color: var(--text-main); padding: 20px; }
    .container { max-width: 1200px; margin: 30px auto; background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    
    /* Top Bar */
    .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 10px; }
    h2 { font-size: 22px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px; }
    
    /* Buttons */
    .btn-group { display: flex; gap: 10px; }
    .btn { padding: 10px 18px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; transition: 0.3s; display: flex; align-items: center; gap: 8px; border: none; }
    .btn-home { background: #f1f5f9; color: #475569; }
    .btn-type { background: #e0f2fe; color: #0369a1; }
    .btn-add { background: var(--primary-color); color: white; }
    .btn:hover { opacity: 0.9; transform: translateY(-1px); }

    /* Table */
    .table-wrap { width: 100%; overflow-x: auto; border: 1px solid var(--border-color); border-radius: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 16px 20px; border-bottom: 1px solid var(--border-color); }
    th { background: #f8fafc; color: var(--text-muted); font-size: 13px; text-transform: uppercase; }
    
    /* จัดໃຫ້ຄໍລຳ room_id อยู่กາງຊ່ອງ */
    .col-center {
        text-align: center;
    }

    /* Badges */
    .badge { display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .green { background: #dcfce7; color: #15803d; }
    .red { background: #fee2e2; color: #b91c1c; }
    .yellow { background: #fef3c7; color: #b45309; }
    
    /* Actions */
    .action { display: flex; gap: 8px; }
    .action-btn { width: 36px; height: 36px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: 0.3s; }
    .edit { background: #fef3c7; color: #d97706; }
    .delete { background: #fee2e2; color: #b91c1c; }
    .edit:hover, .delete:hover { opacity: 0.8; }
</style>
</head>
<body>

<div class="container">
    <div class="top-bar">
        <h2><i class="fa-solid fa-door-open"></i> ຈັດການຂໍ້ມູນຫ້ອງພັກ</h2>
        <div class="btn-group">
            <a href="dashboard.php" class="btn btn-home"><i class="fa-solid fa-house"></i> ໜ້າຫຼັກ</a>
            <a href="room_type.php" class="btn btn-type"><i class="fa-solid fa-tags"></i> ປະເພດຫ້ອງ</a>
            <a href="room_add.php" class="btn btn-add"><i class="fa-solid fa-plus"></i> ເພີ່ມຫ້ອງ</a>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th class="col-center">id</th>
                    <th>room_no</th>
                    <th>floor</th>
                    <th>status</th>
                    <th>type</th>
                    <th style="text-align: center;">ຈັດການ</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if($result && $result->num_rows > 0){
                    $i = 1;
                    while($row = $result->fetch_assoc()){ 
                        $s = strtolower($row['status']); 
                        // จัดรูปแบบໃຫ້ເປັນ 3 ຫຼັກ (001, 002, ...)
                        $formatted_id = str_pad($i, 3, '0', STR_PAD_LEFT);
                ?>
                <tr>
                    <td class="col-center"><strong><?php echo $formatted_id; ?></strong></td>
                    <td><strong><?php echo htmlspecialchars($row['room_no']); ?></strong></td>
                    <td><?php echo $row['floor']; ?></td>
                    <td>
                        <?php if($s == 'free') { ?>
                            <span class="badge green">✅ ວ່າງ</span>
                        <?php } elseif($s == 'occupied') { ?>
                            <span class="badge red">❌ ຖືກເຊົ່າ</span>
                        <?php } else { ?>
                            <span class="badge yellow">🛠️ ສ້ອມແປງ</span>
                        <?php } ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['type_name'] ?? $row['type_id']); ?></td>
                    <td style="text-align: center;">
                        <div class="action" style="justify-content: center;">
                            <a href="room_edit.php?id=<?php echo $row['room_id']; ?>" class="action-btn edit"><i class="fa-solid fa-pen"></i></a>
                            <a href="room_delete.php?id=<?php echo $row['room_id']; ?>" class="action-btn delete" onclick="return confirm('ລົບຫ້ອງນີ້?')"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php 
                        $i++;
                    }
                } else { 
                ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 45px;">
                        <i class="fa-solid fa-folder-open fa-2x" style="margin-bottom: 10px;"></i> <br> ບໍ່ມີຂໍ້ມູນຫ້ອງພັກໃນລະບົບ
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>