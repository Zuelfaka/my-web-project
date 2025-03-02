<?php
session_start();
require_once '../../includes/db.php';

// Initialize the PDO instance
$dsn = 'mysql:host=localhost;dbname=cashier_system';
$username = 'root';
$password = '';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'cashier') {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_POST['customer_id'];
    $products = $_POST['products']; // Array of product IDs and quantities

    // إنشاء الفاتورة
    $stmt = $pdo->prepare("INSERT INTO invoices (customer_id, total_amount) VALUES (?, ?)");
    $stmt->execute([$customer_id, 0]); // Total amount will be calculated later
    $invoice_id = $pdo->lastInsertId();

    // إضافة العناصر إلى الفاتورة
    $total_amount = 0;
    foreach ($products as $product) {
        $product_id = $product['id'];
        $quantity = $product['quantity'];

        // جلب سعر المنتج
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $price = $product_data['price'];

        // إضافة العنصر إلى الفاتورة
        $stmt = $pdo->prepare("INSERT INTO invoice_items (invoice_id, product_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$invoice_id, $product_id, $quantity, $price, $price * $quantity]);

        $total_amount += $price * $quantity;
    }

    // تحديث المبلغ الإجمالي للفاتورة
    $stmt = $pdo->prepare("UPDATE invoices SET total_amount = ? WHERE id = ?");
    $stmt->execute([$total_amount, $invoice_id]);

    header("Location: view_invoice.php?id=$invoice_id");
    exit();
}

// جلب العملاء والمنتجات
$customers = $pdo->query("SELECT * FROM customers")->fetchAll(PDO::FETCH_ASSOC);
$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إنشاء فاتورة</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f7f7f7;
            padding-top: 50px;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 700px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #0074d9;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn-submit {
            background-color: #0074d9;
            color: white;
            border: none;
        }
        .btn-submit:hover {
            background-color: #005fa3;
        }
        .btn-danger {
            background-color: #d9534f;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>إنشاء فاتورة جديدة</h1>
    <form method="POST">
        <div class="form-group">
            <label for="customer_id">اختر العميل:</label>
            <select name="customer_id" class="form-control" required>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?php echo $customer['id']; ?>"><?php echo htmlspecialchars($customer['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" id="products">
            <label for="product_id">اختر المنتجات:</label>
            <div class="product-item mb-3" id="product-0">
                <div class="row">
                    <div class="col-md-6">
                        <select name="products[0][id]" class="form-control" required>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?> - <?php echo htmlspecialchars($product['price']); ?> ريال</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="number" name="products[0][quantity]" class="form-control" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger" onclick="removeProduct(0)">حذف</button>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary" onclick="addProduct()">إضافة منتج آخر</button>
        <button type="submit" class="btn btn-submit" herf="/cashier_system/public/cashier/view_invoices">إنشاء الفاتورة</button>
    </form>
    <a href="/cashier_system/public/dashboard.php" class="btn btn-back">العودة إلى لوحة التحكم</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let productCount = 1;

    function addProduct() {
        const productsDiv = document.getElementById('products');
        const newProduct = document.createElement('div');
        newProduct.classList.add('product-item', 'mb-3');
        newProduct.id = 'product-' + productCount;

        newProduct.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <select name="products[${productCount}][id]" class="form-control" required>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?> - <?php echo htmlspecialchars($product['price']); ?> ريال</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" name="products[${productCount}][quantity]" class="form-control" min="1" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger" onclick="removeProduct(${productCount})">حذف</button>
                </div>
            </div>
        `;
        productsDiv.appendChild(newProduct);
        productCount++;
    }

    function removeProduct(productId) {
        const productDiv = document.getElementById('product-' + productId);
        productDiv.remove();
    }
</script>

</body>
</html>
