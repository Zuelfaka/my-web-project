<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
} else {
    if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
        $logout_message = "تم تسجيل الخروج بنجاح";
        header("Location: login.php?logout_message=" . urlencode($logout_message));
        exit();
    } else {
        header("Location: login.php");
        exit();
    }
}
?>
