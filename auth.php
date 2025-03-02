<?php
session_start();

// إذا لم يكن المستخدم مسجل الدخول أو ليس له صلاحية الوصول
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    $_SESSION['message'] = "Please log in with the correct credentials to access this page.";
    header("Location: ../login.php");
    exit();
}
?>
