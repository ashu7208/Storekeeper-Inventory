<?php
// products.php - Product management
include 'includes/header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare("INSERT INTO products (name, category, quantity, unit, purchase_price, vendor, purchase_date, invoice_number, storage_location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'], $_POST['category'], $_POST['quantity'], $_POST['unit'],
            $_POST['purchase_price'], $_POST['vendor'], $_POST['purchase_date'],
            $_POST['invoice_number'], $_POST['storage_location']
        ]);
        $product_id = $pdo->lastInsertId();
        
        // If quantity > 0, add to stock_in for purchase history
        if ($_POST['quantity'] > 0) {
            $stmt = $pdo->prepare("INSERT INTO stock_in (product_id, quantity, date, invoice_number, vendor) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$product_id, $_POST['quantity'], $_POST['purchase_date'], $_POST['invoice_number'], $_POST['vendor']]);
        }
        
        $message = 'Product added successfully';
    } elseif (isset($_POST['edit'])) {
        $stmt = $pdo->prepare("UPDATE products SET name=?, category=?, quantity=?, unit=?, purchase_price=?, vendor=?, purchase_date=?, invoice_number=?, storage_location=? WHERE id=?");
        $stmt->execute([
            $_POST['name'], $_POST['category'], $_POST['quantity'], $_POST['unit'],
            $_POST['purchase_price'], $_POST['vendor'], $_POST['purchase_date'],
            $_POST['invoice_number'], $_POST['storage_location'], $_POST['id']
        ]);
        $message = 'Product updated successfully';
    } elseif (isset($_POST['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
        $stmt->execute([$_POST['id']]);
        $message = 'Product deleted successfully';
    }
}

$products = $pdo->query("SELECT * FROM products ORDER BY name")->fetchAll();
?>
<div class="page-header">
    <h1>Product Management</h1>
</div>
<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<h2>Add Product</h2>
<form method="post" class="mb-4">
    <div class="row g-3">
        <div class="col-md-6">
            <input type="text" name="name" class="form-control" placeholder="Product Name" required>
        </div>
        <div class="col-md-6">
            <input type="text" name="category" class="form-control" placeholder="Category">
        </div>
        <div class="col-md-3">
            <input type="number" name="quantity" class="form-control" placeholder="Quantity" required>
        </div>
        <div class="col-md-3">
            <input type="text" name="unit" class="form-control" placeholder="Unit (pcs, kg, etc.)">
        </div>
        <div class="col-md-3">
            <input type="number" step="0.01" name="purchase_price" class="form-control" placeholder="Purchase Price">
        </div>
        <div class="col-md-3">
            <input type="text" name="vendor" class="form-control" placeholder="Vendor">
        </div>
        <div class="col-md-3">
            <input type="date" name="purchase_date" class="form-control">
        </div>
        <div class="col-md-3">
            <input type="text" name="invoice_number" class="form-control" placeholder="Invoice Number">
        </div>
        <div class="col-md-6">
            <input type="text" name="storage_location" class="form-control" placeholder="Storage Location">
        </div>
        <div class="col-md-6">
            <button type="submit" name="add" class="btn btn-primary">Add</button>
        </div>
    </div>
</form>

<h2>Products</h2>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="products-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Purchase Price</th>
                        <th>Vendor</th>
                        <th>Purchase Date</th>
                        <th>Invoice</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $prod): ?>
                    <tr>
                        <td><?php echo $prod['id']; ?></td>
                        <td><?php echo htmlspecialchars($prod['name']); ?></td>
                        <td><?php echo htmlspecialchars($prod['category']); ?></td>
                        <td><?php echo $prod['quantity']; ?></td>
                        <td><?php echo htmlspecialchars($prod['unit']); ?></td>
                        <td><?php echo $prod['purchase_price']; ?></td>
                        <td><?php echo htmlspecialchars($prod['vendor']); ?></td>
                        <td><?php echo $prod['purchase_date']; ?></td>
                        <td><?php echo htmlspecialchars($prod['invoice_number']); ?></td>
                        <td><?php echo htmlspecialchars($prod['storage_location']); ?></td>
                        <td>
                            <button onclick="editProd(<?php echo $prod['id']; ?>, '<?php echo htmlspecialchars($prod['name']); ?>', '<?php echo htmlspecialchars($prod['category']); ?>', <?php echo $prod['quantity']; ?>, '<?php echo htmlspecialchars($prod['unit']); ?>', <?php echo $prod['purchase_price']; ?>, '<?php echo htmlspecialchars($prod['vendor']); ?>', '<?php echo $prod['purchase_date']; ?>', '<?php echo htmlspecialchars($prod['invoice_number']); ?>', '<?php echo htmlspecialchars($prod['storage_location']); ?>')" class="btn btn-warning btn-sm">Edit</button>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Delete this product?')">
                                <input type="hidden" name="id" value="<?php echo $prod['id']; ?>">
                                <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <input type="hidden" name="id" id="editId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="category" id="editCategory" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="quantity" id="editQuantity" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="unit" id="editUnit" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <input type="number" step="0.01" name="purchase_price" id="editPrice" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="vendor" id="editVendor" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="purchase_date" id="editDate" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="invoice_number" id="editInvoice" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="storage_location" id="editLocation" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <button type="submit" name="edit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>