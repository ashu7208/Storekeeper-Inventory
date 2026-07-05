<?php
// stock_out.php - Stock outward (issues to departments)
include 'includes/header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $department_id = $_POST['department_id'];
    $quantity = $_POST['quantity'];
    $date = $_POST['date'];
    $issued_by = $_POST['issued_by'];

    // Check if enough stock
    $stmt = $pdo->prepare("SELECT quantity FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $current_qty = $stmt->fetch()['quantity'];

    if ($current_qty >= $quantity) {
        // Insert stock out record
        $stmt = $pdo->prepare("INSERT INTO stock_out (product_id, department_id, quantity, date, issued_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$product_id, $department_id, $quantity, $date, $issued_by]);

        // Update product quantity
        $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
        $stmt->execute([$quantity, $product_id]);

        $message = 'Stock issued successfully';
    } else {
        $message = 'Insufficient stock';
    }
}

$products = $pdo->query("SELECT id, name FROM products ORDER BY name")->fetchAll();
$departments = $pdo->query("SELECT id, name FROM departments ORDER BY name")->fetchAll();
$stock_outs = $pdo->query("SELECT so.*, p.name as product_name, d.name as department_name FROM stock_out so JOIN products p ON so.product_id = p.id JOIN departments d ON so.department_id = d.id ORDER BY so.date DESC")->fetchAll();
?>
<div class="page-header">
    <h1 class="page-title">Stock Outward (Issue to Department)</h1>
</div>
<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Issue Stock</h5>
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
                        <select name="department_id" class="form-select" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
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
                        <input type="text" name="issued_by" class="form-control" placeholder="Issued By" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Issue Stock</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Stock Outward Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="stock-out-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Department</th>
                                <th>Quantity</th>
                                <th>Date</th>
                                <th>Issued By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stock_outs as $so): ?>
                            <tr>
                                <td><?php echo $so['id']; ?></td>
                                <td><?php echo htmlspecialchars($so['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($so['department_name']); ?></td>
                                <td><?php echo $so['quantity']; ?></td>
                                <td><?php echo $so['date']; ?></td>
                                <td><?php echo htmlspecialchars($so['issued_by']); ?></td>
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