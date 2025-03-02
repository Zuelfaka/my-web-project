<?php
session_start();
require_once '../../includes/db.php';

// التحقق من أن المستخدم مسجل الدخول وله دور "admin"
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // التحقق من البيانات المدخلة
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // التأكد من أن كلمة المرور ليست فارغة
    if (empty($username) || empty($password)) {
        $error_message = "يجب ملء جميع الحقول.";
    } else {
        // تشفير كلمة المرور
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // إضافة المستخدم إلى قاعدة البيانات
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $role]);

        // إعادة توجيه إلى صفحة إدارة المستخدمين مع رسالة تأكيد
        header("Location: manage_users.php?message=تم إضافة المستخدم بنجاح");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إضافة مستخدم</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        header {
            background: linear-gradient(to right, #003366, #000000);
            color: #ffffff;
            text-align: center;
            padding: 15px;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: #f8f9fa;
        }

        button {
            background: linear-gradient(to right, #003366, #007bff);
            color: #ffffff;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
            width: 100%;
        }

        button:hover {
            background: #0056b3;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<header>
    <h1>إضافة مستخدم جديد</h1>
</header>

<div class="container">
    <?php if (isset($error_message)) { ?>
        <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php } ?>

    <form method="POST">
        <label>اسم المستخدم:</label>
        <input type="text" name="username" required>
        
        <label>كلمة المرور:</label>
        <input type="password" name="password" required>
        
        <label>الدور:</label>
        <select name="role" required>
            <option value="admin">مدير</option>
            <option value="cashier">كاشير</option>
            <option value="employee">موظف</option>
        </select>
        
        <button type="submit">إضافة</button>
    </form>
</div>

</body>
</html>
