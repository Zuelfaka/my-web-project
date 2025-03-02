<?php
session_start();
require_once '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'cashier') {
    header("Location: ../../login.php");
    exit();
}

// جلب جميع العملاء من قاعدة البيانات
$stmt = $pdo->query("SELECT * FROM customers");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// عرض رسالة النجاح أو الخطأ في حال الحذف
if (isset($_SESSION['message'])) {
    echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['message']) . "</div>";
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إدارة العملاء</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body class="bg-light">

    <header class="bg-primary text-white text-center p-3">
        <h1>إدارة العملاء</h1>
    </header>

    <main class="container my-4">
        <a href="add_customer.php" class="btn btn-success mb-3">إضافة عميل جديد</a>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>الاسم</th>
                    <th>الهاتف</th>
                    <th>النقاط</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?php echo htmlspecialchars($customer['id']); ?></td>
                    <td><?php echo htmlspecialchars($customer['name']); ?></td>
                    <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                    <td><?php echo htmlspecialchars($customer['points']); ?></td>
                    <td>
                        <a href="edit_customer.php?id=<?php echo $customer['id']; ?>" class="btn btn-warning btn-sm">تعديل</a>
                        <a href="delete_customer.php?id=<?php echo $customer['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف العميل؟')">حذف</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <footer class="bg-dark text-white text-center p-3">
        <p>جميع الحقوق محفوظة &copy; <?php echo date('Y'); ?></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
