<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if(!isset($_SESSION['emp_id'])){
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost","root","","apartment_db");
if($conn->connect_error){ 
    die("Connection failed: ".$conn->connect_error); 
}
$conn->set_charset("utf8mb4");

/* ກວດສອບການກົດບັນທຶກ */
if(isset($_POST['save'])){
    $cus_name_input = trim($_POST['cus_name'] ?? '');
    $emp_id = $_POST['emp_id'] ?? '';
    $room_ids = $_POST['room_id'] ?? []; 
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $deposit = $_POST['deposit'] ?? 0;
    $rent_price = $_POST['rent_price'] ?? 0;
    $status = 'Active';

    // ຫາ cus_id ຈາກຊື່ທີ່ພິມເຂົ້າມາ
    $cus_id = '';
    if(!empty($cus_name_input)){
        $stmt_cus = $conn->prepare("SELECT cus_id FROM customer WHERE cus_name = ? LIMIT 1");
        $stmt_cus->bind_param("s", $cus_name_input);
        $stmt_cus->execute();
        $res_cus = $stmt_cus->get_result();
        if($row_cus = $res_cus->fetch_assoc()){
            $cus_id = $row_cus['cus_id'];
        }
        $stmt_cus->close();
    }

    if(empty($cus_id) || empty($emp_id) || empty($room_ids) || empty($start_date) || empty($end_date)){
        echo "<script>alert('ກະລຸນາເລືອກຊື່ລູກຄ້າຈາກລາຍການ, ຕື່ມຂໍ້ມູນ ແລະ ເລືອກຫ້ອງໃຫ້ຄົບຖ້ວນ!'); window.history.back();</script>";
        exit();
    }

    $conn->begin_transaction();

    try {
        foreach($room_ids as $room_id){
            $stmt = $conn->prepare("INSERT INTO rental (cus_id, emp_id, room_id, start_date, end_date, deposit, rent_price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if(!$stmt){ throw new Exception("Prepare failed (rental): " . $conn->error); }
            $stmt->bind_param("iiissdds", $cus_id, $emp_id, $room_id, $start_date, $end_date, $deposit, $rent_price, $status);
            if(!$stmt->execute()){ throw new Exception("Execute failed (rental): " . $stmt->error); }
            $stmt->close();

            $update_room = $conn->prepare("UPDATE room SET status = 'occupied' WHERE room_id = ?");
            if(!$update_room){ throw new Exception("Prepare failed (room): " . $conn->error); }
            $update_room->bind_param("i", $room_id);
            if(!$update_room->execute()){ throw new Exception("Execute failed (room): " . $update_room->error); }
            $update_room->close();
        }

        $conn->commit();
        echo "<script>alert('ບັນທຶກຂໍ້ມູນການເຊົ່າສຳເລັດ!'); window.location='rental.php';</script>";
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $error_msg = addslashes($e->getMessage());
        echo "<script>alert('ເກີດຂໍ້ຜິດພາດ: $error_msg'); window.history.back();</script>";
        exit();
    }
}

$customer = $conn->query("SELECT * FROM customer ORDER BY cus_id DESC");
$employee = $conn->query("SELECT * FROM employee ORDER BY emp_id DESC");

/* ດຶງຂໍ້ມູນຫ້ອງ ພ້ອມ JOIN ກັບ room_type (ດຶງທັງ rt.price ແລະ rt.deposit) */
$room = $conn->query("SELECT r.*, rt.price, rt.deposit 
                      FROM room r 
                      JOIN room_type rt ON r.type_id = rt.type_id 
                      WHERE r.status = 'free' OR r.room_no IN ('202', '205') 
                      ORDER BY r.room_no ASC");
?>

<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<title>Add Rental | Apartment</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root { 
        --primary-color: #4f46e5; 
        --bg-color: #f8fafc; 
        --border-color: #e2e8f0;
        --input-height: 48px;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', 'Noto Sans Lao', sans-serif; }
    body { background: var(--bg-color); padding: 20px; }
    .container { max-width: 750px; margin: 30px auto; background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    h2 { margin-bottom: 25px; color: #1e293b; display: flex; align-items: center; gap: 10px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { display: flex; flex-direction: column; }
    .full { grid-column: span 2; }
    label { font-weight: 600; margin-bottom: 8px; font-size: 14px; color: #334155; }
    
    input, select { 
        padding: 0 14px; 
        border: 1px solid var(--border-color); 
        border-radius: 8px; 
        font-size: 14px; 
        height: var(--input-height); 
        width: 100%;
        outline: none;
        background-color: #fff;
        color: #1e293b;
    }
    input:focus, select:focus { 
        border-color: var(--primary-color); 
        box-shadow: 0 0 0 3px rgba(79,70,229,0.1); 
    }
    
    .room-checkbox-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        max-height: 170px;
        overflow-y: auto;
        padding: 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: #fafafa;
    }
    .room-checkbox-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        cursor: pointer;
        padding: 6px 8px;
        border-radius: 6px;
        transition: background 0.15s;
    }
    .room-checkbox-item:hover {
        background: #f1f5f9;
    }
    .room-checkbox-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--primary-color);
    }

    button { 
        padding: 14px; 
        background: var(--primary-color); 
        color: white; 
        border: none; 
        border-radius: 8px; 
        font-weight: 600; 
        cursor: pointer; 
        margin-top: 25px; 
        width: 100%; 
        transition: opacity 0.2s; 
        font-size: 16px; 
    }
    button:hover { opacity: 0.9; }
    .back { display: block; text-align: center; margin-top: 15px; color: #64748b; text-decoration: none; font-size: 14px; }
    .back:hover { color: #1e293b; }
</style>
</head>
<body>

<div class="container">
    <h2><i class="fa-solid fa-file-contract"></i> ເພີ່ມຂໍ້ມູນການເຊົ່າຫ້ອງພັກ</h2>
    <form method="POST">
        <div class="form-grid">
            
            <!-- ຊ່ອງຄົ້ນຫາລູກຄ້າ -->
            <div class="form-group">
                <label><i class="fa-solid fa-user"></i> ລູກຄ້າ (ພິມຄົ້ນຫາຊື່)</label>
                <input type="text" name="cus_name" list="customer-list" placeholder="-- ພິມ ຫຼື ເລືອກຊື່ລູກຄ້າ --" required autocomplete="off">
                <datalist id="customer-list">
                    <?php if($customer && $customer->num_rows > 0): ?>
                        <?php while($c = $customer->fetch_assoc()){ ?>
                            <option value="<?php echo htmlspecialchars($c['cus_name']); ?>">
                                <?php echo isset($c['cus_tel']) ? 'ເບີໂທ: '.$c['cus_tel'] : ''; ?>
                            </option>
                        <?php } ?>
                    <?php endif; ?>
                </datalist>
            </div>
            
            <!-- ຊ່ອງເລືອກພະນັກງານ -->
            <div class="form-group">
                <label><i class="fa-solid fa-user-tie"></i> ພະນັກງານຜູ້ເຮັດສັນຍາ</label>
                <select name="emp_id" required>
                    <?php if($employee && $employee->num_rows > 0): ?>
                        <?php while($e = $employee->fetch_assoc()){ ?>
                            <option value="<?php echo $e['emp_id']; ?>" <?php echo ($_SESSION['emp_id'] == $e['emp_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($e['emp_name']); ?>
                            </option>
                        <?php } ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- ເລືອກຫ້ອງພັກແບບ Checkbox -->
            <div class="form-group full">
                <label><i class="fa-solid fa-door-open"></i> ເລືອກຫ້ອງພັກ (ຕິກເລືອກໄດ້ຫຼາຍຫ້ອງ)</label>
                <div class="room-checkbox-container">
                    <?php if($room && $room->num_rows > 0): ?>
                        <?php while($r = $room->fetch_assoc()){ 
                            $room_price = isset($r['price']) ? $r['price'] : 0; 
                            $room_deposit = isset($r['deposit']) ? $r['deposit'] : 0; 
                        ?>
                            <label class="room-checkbox-item">
                                <input type="checkbox" name="room_id[]" value="<?php echo $r['room_id']; ?>" 
                                       data-price="<?php echo $room_price; ?>"
                                       data-deposit="<?php echo $room_deposit; ?>"
                                       onchange="calculateTotal()">
                                ຫ້ອງ <?php echo htmlspecialchars($r['room_no']); ?>
                            </label>
                        <?php } ?>
                    <?php else: ?>
                        <span style="color: red; grid-column: span 3; padding: 10px;">ບໍ່ມີຫ້ອງຫວ່າງໃນຂະນະນີ້</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ວັນທີເລີ່ມ ແລະ ສິ້ນສຸດ -->
            <div class="form-group">
                <label><i class="fa-solid fa-calendar-days"></i> ວັນທີເລີ່ມເຊົ່າ</label>
                <input type="date" name="start_date" required value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label><i class="fa-solid fa-calendar-check"></i> ວັນທີສິ້ນສຸດສັນຍາ</label>
                <input type="date" name="end_date" required>
            </div>

            <!-- ຄ່າໃຊ້ຈ່າຍ (ຄິດໄລ່ອັດຕະໂນມັດແຍກລະຫວ່າງມັດຈຳ ແລະ ຄ່າເຊົ່າ) -->
            <div class="form-group">
                <label><i class="fa-solid fa-money-bill-wave"></i> ລວມເງິນມັດຈຳ (Deposit)</label>
                <input type="number" step="0.01" name="deposit" id="deposit" placeholder="ຕົວຢ່າງ: 1000000" required>
            </div>
            <div class="form-group">
                <label><i class="fa-solid fa-coins"></i> ລວມຄ່າເຊົ່າຕໍ່ເດືອນ (Rent Price)</label>
                <input type="number" step="0.01" name="rent_price" id="rent_price" placeholder="ຕົວຢ່າງ: 1500000" required>
            </div>
        </div>
        
        <!-- ປຸ່ມບັນທຶກ -->
        <button type="submit" name="save"><i class="fa-solid fa-save"></i> ບັນທຶກສັນຍາການເຊົ່າ</button>
        <a href="rental.php" class="back"><i class="fa-solid fa-arrow-left"></i> ກັບຄືນໜ້າລາຍການເຊົ່າ</a>
    </form>
</div>

<!-- JavaScript ຄິດໄລ່ລາຄາ ແລະ ເງິນມັດຈຳອັດຕະໂນມັດ -->
<script>
function calculateTotal() {
    let checkboxes = document.querySelectorAll('input[name="room_id[]"]:checked');
    let totalRent = 0;
    let totalDeposit = 0;

    checkboxes.forEach((checkbox) => {
        let price = parseFloat(checkbox.getAttribute('data-price')) || 0;
        let deposit = parseFloat(checkbox.getAttribute('data-deposit')) || 0;
        
        totalRent += price;
        totalDeposit += deposit;
    });

    // ຍອດລວມຄ່າເຊົ່າ ແລະ ເງິນມັດຈຳຈະຖືກໃສ່ໃນຊ່ອງ Input ອັດຕະໂນມັດຕາມຫ້ອງທີ່ເລືອກ
    document.getElementById('rent_price').value = totalRent > 0 ? totalRent : '';
    document.getElementById('deposit').value = totalDeposit > 0 ? totalDeposit : ''; 
}
</script>

</body>
</html>