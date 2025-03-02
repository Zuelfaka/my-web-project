<?php
session_start();
require_once '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'cashier') {
    header("Location: ../../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_customers.php");
    exit();
}

$customer_id = $_GET['id'];

// جلب بيانات العميل للتأكد من وجوده
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    header("Location: manage_customers.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // حذف العميل بعد التأكيد
    $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->execute([$customer_id]);

    // إضافة رسالة تأكيد بعد الحذف
    $_SESSION['message'] = "تم حذف العميل بنجاح.";
    header("Location: manage_customers.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تأكيد الحذف</title>
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body>
    <h1>تأكيد حذف العميل</h1>
    <p>هل أنت متأكد من حذف العميل <strong><?php echo htmlspecialchars($customer['name']); ?></strong>؟</p>
    <form method="POST">
        <button type="submit">نعم، حذف العميل</button>
    </form>
    <a href="manage_customers.php">لا، العودة إلى قائمة العملاء</a>
</body>
</html>
