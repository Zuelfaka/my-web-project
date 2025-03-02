<?php
session_start();
require_once '../../includes/db.php';

// التحقق من صلاحيات المستخدم
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'cashier') {
    header("Location: ../../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: view_invoices.php");
    exit();
}

$invoice_id = $_GET['id'];

// جلب بيانات الفاتورة بأمان
$stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ?");
$stmt->execute([$invoice_id]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invoice) {
    header("Location: view_invoices.php");
    exit();
}

// جلب بيانات العميل
$stmt = $pdo->prepare("SELECT name FROM customers WHERE id = ?");
$stmt->execute([$invoice['customer_id']]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

// جلب عناصر الفاتورة
$stmt = $pdo->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
$stmt->execute([$invoice_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>عرض الفاتورة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body class="bg-light">

    <header class="bg-primary text-white text-center p-3">
        <h1>فاتورة #<?php echo htmlspecialchars($invoice['id']); ?></h1>
    </header>

    <main class="container my-4">
        <p><strong>العميل:</strong> <?php echo htmlspecialchars($customer['name']); ?></p>
        <p><strong>المبلغ الإجمالي:</strong> <?php echo number_format(htmlspecialchars($invoice['total_amount']), 2); ?> ريال</p>
        <h2>العناصر:</h2>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>الكمية</th>
                    <th>السعر</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalAmount = 0; // لتخزين الإجمالي العام
                foreach ($items as $item): 
                    // جلب اسم المنتج
                    $stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
                    $stmt->execute([$item['product_id']]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);
                    $totalAmount += $item['total'];
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo number_format(htmlspecialchars($item['price']), 2); ?> ريال</td>
                    <td><?php echo number_format(htmlspecialchars($item['total']), 2); ?> ريال</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr>
        <h4 class="text-end">الإجمالي الكلي: <?php echo number_format($totalAmount, 2); ?> ريال</h4>

        <!-- إضافة زر للطباعة -->
        <div class="text-center mt-4">
            <button class="btn btn-info" onclick="window.print()">طباعة الفاتورة</button>
        </div>
    </main>

    <footer class="bg-dark text-white text-center p-3">
        <p>جميع الحقوق محفوظة &copy; <?php echo date('Y'); ?></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
