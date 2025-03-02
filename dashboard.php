<?php
session_start();

// التحقق من تسجيل الدخول وصلاحيات المستخدم
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// التحقق من وقت الجلسة (التسجيل التلقائي في حال انتهت الجلسة)
$timeout_duration = 1800; // 30 دقيقة
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset(); // إلغاء جميع المتغيرات
    session_destroy(); // تدمير الجلسة
    header("Location: login.php?session_expired=1");
    exit();
}
$_SESSION['last_activity'] = time(); // تحديث الوقت الحالي للجلسة

require_once '../includes/db.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// حماية ضد هجمات XSS
$username = htmlspecialchars($username);
$role = htmlspecialchars($role);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body class="bg-light">

    <header class="bg-primary text-white text-center p-3">
        <h1>مرحبًا، <?php echo $username; ?></h1>
        <p>دورك: <?php echo $role; ?></p>
        <a href="logout.php" class="btn btn-danger">تسجيل الخروج</a>
    </header>

    <main class="container my-4">
        <?php if ($role == 'admin'): ?>
            <h2 class="text-center">إدارة النظام</h2>
            <ul class="list-group">
                <li class="list-group-item"><a href="admin/manage_users.php" class="text-decoration-none">إدارة المستخدمين</a></li>
                <li class="list-group-item"><a href="admin/manage_products.php" class="text-decoration-none">إدارة المنتجات</a></li>
                <li class="list-group-item"><a href="admin/view_reports.php" class="text-decoration-none">عرض التقارير</a></li>
            </ul>
        <?php elseif ($role == 'cashier'): ?>
            <h2 class="text-center">إدارة المبيعات</h2>
            <ul class="list-group">
                <li class="list-group-item"><a href="cashier/create_invoice.php" class="text-decoration-none">إنشاء فاتورة جديدة</a></li>
                <li class="list-group-item"><a href="cashier/view_invoices.php" class="text-decoration-none">عرض الفواتير</a></li>
                <li class="list-group-item"><a href="cashier/manage_customers.php" class="text-decoration-none">إدارة العملاء</a></li>
            </ul>
        <?php elseif ($role == 'employee'): ?>
            <h2 class="text-center">المهام المتاحة</h2>
            <ul class="list-group">
                <li class="list-group-item"><a href="employee/view_products.php" class="text-decoration-none">عرض المنتجات</a></li>
                <li class="list-group-item"><a href="employee/view_invoices.php" class="text-decoration-none">عرض الفواتير</a></li>
            </ul>
        <?php endif; ?>
    </main>

    <footer class="bg-dark text-white text-center p-2">
        <p>جميع الحقوق محفوظة &copy; <?php echo date('Y'); ?></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
