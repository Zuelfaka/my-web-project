<?php
session_start();
require_once '../../includes/db.php';

// التحقق من أن المستخدم مسجل الدخول وله دور "admin"
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

// التحقق من أن معرف المنتج صالح
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: manage_products.php?message=خطأ: معرف المنتج غير صالح");
    exit();
}

$product_id = $_GET['id'];

// التحقق من وجود المنتج قبل الحذف
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: manage_products.php?message=خطأ: المنتج غير موجود");
    exit();
}

// التأكيد قبل الحذف
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    header("Location: manage_products.php?message=تم حذف المنتج بنجاح");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>حذف المنتج</title>
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
    <h2>هل أنت متأكد من حذف المنتج؟</h2>
    <p><strong><?php echo htmlspecialchars($product['name']); ?></strong></p>
    
    <form method="POST">
        <button type="submit">نعم، حذف</button>
        <a href="manage_products.php" class="btn-secondary">إلغاء</a>
    </form>
</div>

</body>
</html>
