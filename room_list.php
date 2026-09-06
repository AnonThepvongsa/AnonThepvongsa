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
    .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    h2 { font-size: 22px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px; }
    .table-wrap { width: 100%; overflow-x: auto; border: 1px solid var(--border-color); border-radius: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 16px 20px; border-bottom: 1px solid var(--border-color); }
    th { background: #f8fafc; color: var(--text-muted); font-size: 14px; text-transform: uppercase; }
    .badge { display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; }
    .green { background: #dcfce7; color: #15803d; }
    .red { background: #fee2e2; color: #b91c1c; }
    .yellow { background: #fef3c7; color: #b45309; }
    .action-btn { width: 36px; height: 36px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
    .edit { background: #fef3c7; color: #d97706; }
    .delete { background: #fee2e2; color: #b91c1c; }
</style>
</head>
<body>

<div class="container">
    <div class="top-bar">
        <h2><i class="fa-solid fa-door-open"></i> ຈັດການຂໍ້ມູນຫ້ອງພັກ</h2>
        <a href="room_add.php" class="btn-add" style="padding:10px 18px; background:#4f46e5; color:white; border-radius:10px; text-decoration:none;">➕ ເພີ່ມຫ້ອງພັກ</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>ເລກຫ້ອງ</th>
                    <th>ຊັ້ນ</th>
                    <th>ສະຖານະ</th>
                    <th>ຈັດການ</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()){ 
                    $s = strtolower($row['status']); 
                ?>
                <tr>
                    <td><?php echo $row['room_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['room_no']); ?></td>
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
                    <td>
                        <div class="action">
                            <a href="room_edit.php?id=<?php echo $row['room_id']; ?>" class="action-btn edit"><i class="fa-solid fa-pen"></i></a>
                            <a href="room_delete.php?id=<?php echo $row['room_id']; ?>" class="action-btn delete"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>