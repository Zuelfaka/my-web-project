<?php
// إعداد الاتصال بقاعدة البيانات
$config = include('config.php');
$host = $config['host'];
$dbname = $config['dbname'];
$username = $config['username'];
$password = $config['password'];

try {
    // إنشاء الاتصال
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // سجل الخطأ بدلاً من إظهاره للمستخدم
    error_log($e->getMessage(), 3, 'errors.log');
    die("فشل الاتصال بقاعدة البيانات.");
}
?>
