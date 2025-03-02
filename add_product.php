<?php
session_start();
require_once '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit();
}

// التحقق من إرسال البيانات
$successMessage = ''; // متغير لتخزين رسالة النجاح
$error = ''; // متغير لتخزين رسالة الخطأ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $stock = trim($_POST['stock']);
    $barcode = trim($_POST['barcode']);

    // التحقق من وجود قيم صحيحة
    if (empty($name) || empty($price) || empty($stock) || empty($barcode)) {
        $error = "جميع الحقول مطلوبة";
    } else {
        // التحقق من أن السعر والكمية أرقام صحيحة
        if (!is_numeric($price) || !is_numeric($stock)) {
            $error = "السعر والكمية يجب أن يكونا أرقاماً صحيحة";
        } else {
            // التحقق مما إذا كان الباركود موجودًا بالفعل في قاعدة البيانات
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE barcode = ?");
            $stmt->execute([$barcode]);
            $barcodeExists = $stmt->fetchColumn();

            if ($barcodeExists) {
                $error = "الباركود هذا موجود بالفعل في النظام، يرجى إدخال باركود آخر.";
            } else {
                // إضافة المنتج إلى قاعدة البيانات
                $stmt = $pdo->prepare("INSERT INTO products (name, price, stock, barcode) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $price, $stock, $barcode]);

                // رسالة تأكيد بعد إضافة المنتج
                $successMessage = "تم إضافة المنتج بنجاح";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة منتج جديد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // التحقق من المدخلات
        document.getElementById('barcode').addEventListener('input', function() {
            var barcode = this.value;
            
            // تحقق من أن الباركود يحتوي على أرقام فقط
            if (!/^\d+$/.test(barcode)) {
                alert('الباركود يجب أن يحتوي على أرقام فقط');
                this.value = ''; // مسح القيمة
            }
        });
    </script>
</head>
<body>
    <div class="container mt-5">
        <header>
            <h1 class="text-center mb-4">إضافة منتج جديد</h1>
        </header>

        <main>
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php elseif (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="add_product.php">
                <div class="mb-3">
                    <label for="name" class="form-label">اسم المنتج:</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">السعر:</label>
                    <input type="text" id="price" name="price" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="stock" class="form-label">الكمية:</label>
                    <input type="number" id="stock" name="stock" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="barcode" class="form-label">الباركود:</label>
                    <input type="text" id="barcode" name="barcode" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">إضافة المنتج</button>
            </form>
        </main>
    </div>

    <footer class="text-center mt-5">
        <p>جميع الحقوق محفوظة &copy; <?php echo date('Y'); ?></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
