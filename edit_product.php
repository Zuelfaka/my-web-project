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

// جلب بيانات المنتج
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// التحقق إذا كان المنتج موجودًا
if (!$product) {
    header("Location: manage_products.php?message=خطأ: المنتج غير موجود");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $stock = trim($_POST['stock']);
    $barcode = trim($_POST['barcode']);

    // تحديث بيانات المنتج في قاعدة البيانات
    $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, stock = ?, barcode = ? WHERE id = ?");
    $stmt->execute([$name, $price, $stock, $barcode, $product_id]);

    header("Location: manage_products.php?message=تم تعديل المنتج بنجاح");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تعديل منتج</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #001f3f, #0074d9);
            color: white;
            padding: 50px;
            text-align: center;
        }
        .container {
            background: white;
            color: black;
            max-width: 400px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin: auto;
        }
        .form-label {
            font-weight: bold;
        }
        button, .btn-secondary {
            background: linear-gradient(to right, #001f3f, #0074d9);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }
        button:hover, .btn-secondary:hover {
            background: #0056b3;
        }
        .mb-3 {
            margin-bottom: 15px;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>تعديل المنتج</h2>

    <!-- عرض رسالة الخطأ أو النجاح إذا كانت موجودة -->
    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">الاسم:</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">السعر:</label>
            <input type="number" name="price" step="0.01" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">الكمية:</label>
            <input type="number" name="stock" class="form-control" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">الباركود:</label>
            <input type="text" name="barcode" class="form-control" value="<?php echo htmlspecialchars($product['barcode']); ?>">
        </div>
        <button type="submit">حفظ التعديلات</button>
        <a href="manage_products.php" class="btn btn-secondary">إلغاء</a>
    </form>
</div>

</body>
</html>
