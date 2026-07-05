<?php
// reports.php - Reports and views
include 'includes/header.php';

$report = $_GET['report'] ?? 'current_stock';
?>
<div class="page-header">
    <h1 class="page-title">Reports</h1>
</div>
<nav class="nav nav-pills mb-4">
    <a class="nav-link <?php echo $report == 'current_stock' ? 'active' : ''; ?>" href="?report=current_stock">Current Stock</a>
    <a class="nav-link <?php echo $report == 'department_issued' ? 'active' : ''; ?>" href="?report=department_issued">Department Issued</a>
    <a class="nav-link <?php echo $report == 'purchase_history' ? 'active' : ''; ?>" href="?report=purchase_history">Purchase History</a>
    <a class="nav-link <?php echo $report == 'low_stock' ? 'active' : ''; ?>" href="?report=low_stock">Low Stock Alert</a>
</nav>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <?php
            if ($report == 'current_stock') echo 'Current Stock Report';
            elseif ($report == 'department_issued') echo 'Department-wise Issued Items';
            elseif ($report == 'purchase_history') echo 'Purchase History';
            elseif ($report == 'low_stock') echo 'Low Stock Alert (Quantity < 10)';
            ?>
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?php if ($report == 'current_stock'): ?>
            <table class="table table-striped" id="current_stock-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT name, category, quantity, unit, storage_location FROM products ORDER BY name");
                    while ($row = $stmt->fetch()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo htmlspecialchars($row['unit']); ?></td>
                        <td><?php echo htmlspecialchars($row['storage_location']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <?php elseif ($report == 'department_issued'): ?>
            <table class="table table-striped" id="department_issued-table">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Product</th>
                        <th>Quantity Issued</th>
                        <th>Date</th>
                        <th>Issued By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT d.name as dept, p.name as prod, so.quantity, so.date, so.issued_by FROM stock_out so JOIN departments d ON so.department_id = d.id JOIN products p ON so.product_id = p.id ORDER BY d.name, so.date DESC");
                    while ($row = $stmt->fetch()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['dept']); ?></td>
                        <td><?php echo htmlspecialchars($row['prod']); ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['date']; ?></td>
                        <td><?php echo htmlspecialchars($row['issued_by']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <?php elseif ($report == 'purchase_history'): ?>
            <table class="table table-striped" id="purchase_history-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Vendor</th>
                        <th>Quantity</th>
                        <th>Date</th>
                        <th>Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT p.name as prod, si.vendor, si.quantity, si.date, si.invoice_number FROM stock_in si JOIN products p ON si.product_id = p.id ORDER BY si.date DESC");
                    while ($row = $stmt->fetch()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['prod']); ?></td>
                        <td><?php echo htmlspecialchars($row['vendor']); ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['date']; ?></td>
                        <td><?php echo htmlspecialchars($row['invoice_number']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <?php elseif ($report == 'low_stock'): ?>
            <table class="table table-striped" id="low_stock-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Current Quantity</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT name, category, quantity, unit FROM products WHERE quantity < 10 ORDER BY quantity ASC");
                    while ($row = $stmt->fetch()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo htmlspecialchars($row['unit']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>