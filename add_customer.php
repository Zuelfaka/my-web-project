<?php
session_start();
require_once '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'cashier') {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // التحقق من البيانات المدخلة
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $points = trim($_POST['points']);

    // التحقق من صحة رقم الهاتف
    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $error = "رقم الهاتف يجب أن يتكون من 10 أرقام.";
    } elseif ($points < 0) {
        $error = "النقاط يجب أن تكون قيمة صحيحة موجبة.";
    } else {
        // إدخال البيانات في قاعدة البيانات
        $stmt = $pdo->prepare("INSERT INTO customers (name, phone, points) VALUES (?, ?, ?)");
        $stmt->execute([$name, $phone, $points]);

        // إعادة التوجيه إلى صفحة العملاء مع رسالة نجاح
        $_SESSION['message'] = "تم إضافة العميل بنجاح.";
        header("Location: manage_customers.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إضافة عميل جديد</title>
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body>
    <h1>إضافة عميل جديد</h1>
    
    <?php
    if (isset($error)) {
        echo "<div style='color: red;'>$error</div>";
    }
    ?>

    <form method="POST">
        <label>الاسم:</label>
        <input type="text" name="name" required>
        <label>الهاتف:</label>
        <input type="text" name="phone" required>
        <label>النقاط:</label>
        <input type="number" name="points" required>
        <button type="submit">إضافة</button>
    </form>

</body>
</html>
