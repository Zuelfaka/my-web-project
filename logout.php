<?php
session_start();

// إعادة تعيين جميع متغيرات الجلسة
$_SESSION = [];

// حذف كوكي الجلسة (لضمان إنهاء الجلسة بالكامل)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        true, // Set to true for HTTPS
        $params["httponly"]
    );
}

// تدمير الجلسة
session_destroy();

// زيادة الأمان بإعادة إنشاء معرف الجلسة
session_regenerate_id(true);

// توجيه المستخدم إلى صفحة تسجيل الدخول مع رسالة الخروج
header("Location: login.php?logout=success");
exit();
?>
