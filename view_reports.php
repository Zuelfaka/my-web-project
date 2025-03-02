<?php
session_start();
require_once '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

// جلب التقارير (مثال: عدد الفواتير، إجمالي المبيعات)
$total_invoices = $pdo->query("SELECT COUNT(*) as total FROM invoices")->fetch(PDO::FETCH_ASSOC);
$total_sales = $pdo->query("SELECT SUM(total_amount) as total FROM invoices")->fetch(PDO::FETCH_ASSOC);

// إذا كانت المبيعات غير موجودة أو صفر
$total_sales_amount = $total_sales['total'] ? $total_sales['total'] : 0;
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>عرض التقارير</title>
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
        }
        h1 {
            color: #0074d9;
        }
        .report-card {
            background-color: #f1f1f1;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 6px;
            text-decoration: none;
        }
        .btn-back {
            background-color: #28a745;
            color: white;
        }
        .btn-back:hover {
            background-color: #218838;
        }
        .alert {
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
            background-color: #f8d7da;
            color: #721c24;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>التقارير</h1>

    <?php if ($total_invoices['total'] == 0 && $total_sales_amount == 0): ?>
        <div class="alert">
            لا توجد بيانات للتقارير حتى الآن.
        </div>
    <?php endif; ?>

    <div class="report-card">
        <h4>إجمالي الفواتير:</h4>
        <p><?php echo htmlspecialchars($total_invoices['total']); ?> فاتورة</p>
    </div>

    <div class="report-card">
        <h4>إجمالي المبيعات:</h4>
        <p><?php echo htmlspecialchars($total_sales_amount); ?> ريال</p>
    </div>

    <a href="/cashier_system/public/dashboard.php" class="btn btn-back">العودة إلى لوحة التحكم</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
