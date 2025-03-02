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

// جلب بيانات العميل
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    header("Location: manage_customers.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $points = trim($_POST['points']);

    $stmt = $pdo->prepare("UPDATE customers SET name = ?, phone = ?, points = ? WHERE id = ?");
    $stmt->execute([$name, $phone, $points, $customer_id]);

    $_SESSION['message'] = "تم حفظ التعديلات بنجاح";
    header("Location: manage_customers.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تعديل عميل</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body class="bg-light">

    <header class="bg-primary text-white text-center p-3">
        <h1>تعديل عميل</h1>
    </header>

    <main class="container my-4">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php
                    echo htmlspecialchars($_SESSION['message']);
                    unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="border p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="name" class="form-label">الاسم:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">الهاتف:</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="points" class="form-label">النقاط:</label>
                <input type="number" class="form-control" id="points" name="points" value="<?php echo htmlspecialchars($customer['points']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
        </form>
    </main>

    <footer class="bg-dark text-white text-center p-3">
        <p>جميع الحقوق محفوظة &copy; <?php echo date('Y'); ?></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
