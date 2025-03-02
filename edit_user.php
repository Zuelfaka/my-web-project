<?php
session_start();
require_once '../../includes/db.php';

// التحقق من أن المستخدم مسجل الدخول وله دور "admin"
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

// التحقق من أن المعرف صالح
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: manage_users.php?message=خطأ: معرف المستخدم غير صالح");
    exit();
}

$user_id = $_GET['id'];

// جلب بيانات المستخدم
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// التحقق إذا كان المستخدم موجودًا
if (!$user) {
    header("Location: manage_users.php?message=خطأ: المستخدم غير موجود");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $role = trim($_POST['role']);

    // تحديث بيانات المستخدم في قاعدة البيانات
    $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
    $stmt->execute([$username, $role, $user_id]);

    header("Location: manage_users.php?message=تم تعديل المستخدم بنجاح");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تعديل مستخدم</title>
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
    </style>
</head>
<body>

<div class="container">
    <h2>تعديل المستخدم</h2>

    <!-- عرض رسالة الخطأ أو النجاح إذا كانت موجودة -->
    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">اسم المستخدم:</label>
            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">الدور:</label>
            <select name="role" class="form-select" required>
                <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>مدير</option>
                <option value="cashier" <?php echo ($user['role'] == 'cashier') ? 'selected' : ''; ?>>كاشير</option>
                <option value="employee" <?php echo ($user['role'] == 'employee') ? 'selected' : ''; ?>>موظف</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
        <a href="manage_users.php" class="btn btn-secondary">إلغاء</a>
    </form>
</div>

</body>
</html>
