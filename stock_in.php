<?php
// stock_in.php - Stock inward (purchases)
include 'includes/header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $date = $_POST['date'];
    $invoice_number = $_POST['invoice_number'];
    $vendor = $_POST['vendor'];

    // Insert stock in record
    $stmt = $pdo->prepare("INSERT INTO stock_in (product_id, quantity, date, invoice_number, vendor) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$product_id, $quantity, $date, $invoice_number, $vendor]);

    // Update product quantity
    $stmt = $pdo->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
    $stmt->execute([$quantity, $product_id]);

    $message = 'Stock added successfully';
}

$products = $pdo->query("SELECT id, name FROM products ORDER BY name")->fetchAll();
$stock_ins = $pdo->query("SELECT si.*, p.name as product_name FROM stock_in si JOIN products p ON si.product_id = p.id ORDER BY si.date DESC")->fetchAll();
?>
<div class="page-header">
    <h1 class="page-title">Stock Inward (Purchases)</h1>
</div>
<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Add Stock</h5>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <select name="product_id" class="form-select" required>
                            <option value="">Select Product</option>
                            <?php foreach ($products as $prod): ?>
                                <option value="<?php echo $prod['id']; ?>"><?php echo htmlspecialchars($prod['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="number" name="quantity" class="form-control" placeholder="Quantity" required>
                    </div>
                    <div class="mb-3">
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="invoice_number" class="form-control" placeholder="Invoice Number">
                    </div>
                    <div class="mb-3">
                        <input type="text" name="vendor" class="form-control" placeholder="Vendor">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Stock</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Stock Inward Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="stock-in-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Date</th>
                                <th>Invoice</th>
                                <th>Vendor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stock_ins as $si): ?>
                            <tr>
                                <td><?php echo $si['id']; ?></td>
                                <td><?php echo htmlspecialchars($si['product_name']); ?></td>
                                <td><?php echo $si['quantity']; ?></td>
                                <td><?php echo $si['date']; ?></td>
                                <td><?php echo htmlspecialchars($si['invoice_number']); ?></td>
                                <td><?php echo htmlspecialchars($si['vendor']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>