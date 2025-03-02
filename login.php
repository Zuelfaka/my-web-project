<?php
session_start();

// If the user is already logged in, redirect them to the dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar"  dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسجيل الدخول</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow p-4" style="width: 350px;">
        <h2 class="text-center mb-3">تسجيل الدخول</h2>

        <!-- Display logout success message if the logout_message parameter is present in the URL -->
        <?php if (isset($_GET['logout_message']) && $_GET['logout_message'] != ''): ?>
            <p class="alert alert-success"><?= htmlspecialchars($_GET['logout_message'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">اسم المستخدم:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">كلمة المرور:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">تسجيل الدخول</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
