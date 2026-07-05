<?php
// index.php - Dashboard
include 'includes/header.php';

// Get some stats
$stmt = $pdo->query("SELECT COUNT(*) as total_products FROM products");
$total_products = $stmt->fetch()['total_products'];

$stmt = $pdo->query("SELECT COUNT(*) as total_departments FROM departments");
$total_departments = $stmt->fetch()['total_departments'];

$stmt = $pdo->query("SELECT SUM(quantity) as total_stock FROM products");
$total_stock = $stmt->fetch()['total_stock'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as low_stock FROM products WHERE quantity < 10");
$low_stock = $stmt->fetch()['low_stock'];
?>
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
</div>
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-center dashboard-card" onclick="window.location.href='products.php'">
            <div class="card-body">
                <h5 class="card-title">Total Products</h5>
                <p class="card-text"><?php echo $total_products; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-center dashboard-card" onclick="window.location.href='departments.php'">
            <div class="card-body">
                <h5 class="card-title">Total Departments</h5>
                <p class="card-text"><?php echo $total_departments; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-center dashboard-card" onclick="window.location.href='reports.php?report=current_stock'">
            <div class="card-body">
                <h5 class="card-title">Total Stock</h5>
                <p class="card-text"><?php echo $total_stock; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-center dashboard-card" onclick="window.location.href='reports.php?report=low_stock'">
            <div class="card-body">
                <h5 class="card-title">Low Stock Items</h5>
                <p class="card-text"><?php echo $low_stock; ?></p>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>