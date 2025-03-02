<?php
session_start();
require_once '../../includes/db.php';

// التحقق من أن المستخدم مسجل الدخول وله دور "admin"
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

// جلب جميع المنتجات من قاعدة البيانات
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إدارة المنتجات</title>
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
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
        }
        .table th, .table td {
            text-align: center;
        }
        button, a {
            margin: 0 10px;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .btn-add {
            background: #0074d9;
            color: white;
        }
        .btn-add:hover {
            background: #0056b3;
        }
        .btn-edit {
            background: #ffc107;
            color: black;
        }
        .btn-edit:hover {
            background: #e0a800;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .btn-delete:hover {
            background: #c82333;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>إدارة المنتجات</h2>
    <a href="add_product.php" class="btn btn-add">إضافة منتج جديد</a>

    <?php if (empty($products)): ?>
        <div class="alert alert-warning" role="alert">
            لا توجد منتجات في النظام حالياً.
        </div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>الاسم</th>
                    <th>السعر</th>
                    <th>الكمية</th>
                    <th>الباركود</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['price']); ?></td>
                    <td><?php echo htmlspecialchars($product['stock']); ?></td>
                    <td><?php echo htmlspecialchars($product['barcode']); ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-edit">تعديل</a>
                        <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-delete" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذا المنتج؟')">حذف</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<a href="/cashier_system/public/dashboard.php" class="btn btn-back">العودة إلى لوحة التحكم</a>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
