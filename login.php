<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>Apartment Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            background: #fff;
            width: 100%;
            max-width: 380px;
            padding: 40px 35px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            text-align: center;
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(15px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .login-box h2 {
            margin-bottom: 10px;
            color: #333;
            font-weight: 600;
        }

        .login-box p {
            color: #777;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            color: #444;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #ddd;
            outline: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102,126,234,0.15);
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .footer-text {
            margin-top: 20px;
            font-size: 13px;
            color: #888;
        }
    </style>
</head>

<body>

<div class="login-box">
    <h2>Apartment System</h2>
    <p>ລະບົບຈັດການອາພາດເມັ້ນ</p>

    <form action="login_process.php" method="post">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="ປ້ອນ Username" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="ປ້ອນ Password" required>
        </div>

        <button type="submit">Login</button>
    </form>

    <div class="footer-text">
        © 2026 Apartment Management System
    </div>
</div>

</body>
</html>