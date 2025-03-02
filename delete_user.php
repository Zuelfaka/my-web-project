<?php
session_start();
require_once '../../includes/db.php';

// التحقق من أن المستخدم مسجل الدخول وله دور "admin"
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

// التحقق من أن معرف المستخدم صالح
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: manage_users.php?message=خطأ: معرف المستخدم غير صالح");
    exit();
}

$user_id = $_GET['id'];

// التحقق من وجود المستخدم قبل الحذف
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: manage_users.php?message=خطأ: المستخدم غير موجود");
    exit();
}

// التأكيد قبل الحذف
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    header("Location: manage_users.php?message=تم حذف المستخدم بنجاح");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>حذف المستخدم</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 50px;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: inline-block;
        }
        button {
            background: linear-gradient(to right, #cc0000, #990000);
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }
        button:hover {
            background: #660000;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>هل أنت متأكد من حذف المستخدم؟</h2>
    <p><strong><?php echo htmlspecialchars($user['username']); ?></strong></p>
    
    <form method="POST">
        <button type="submit">نعم، حذف</button>
        <a href="manage_users.php" class="btn-secondary">إلغاء</a>
    </form>
</div>

</body>
</html>
