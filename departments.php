<?php
// departments.php - Department management
include 'includes/header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = trim($_POST['name']);
        if ($name) {
            $stmt = $pdo->prepare("INSERT INTO departments (name) VALUES (?)");
            $stmt->execute([$name]);
            $message = 'Department added successfully';
        }
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $name = trim($_POST['name']);
        if ($name) {
            $stmt = $pdo->prepare("UPDATE departments SET name = ? WHERE id = ?");
            $stmt->execute([$name, $id]);
            $message = 'Department updated successfully';
        }
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Department deleted successfully';
    }
}

$departments = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll();
?>
<div class="page-header">
    <h1 class="page-title">Department Management</h1>
</div>
<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Add Department</h5>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Department Name" required>
                    </div>
                    <button type="submit" name="add" class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Departments</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="departments-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($departments as $dept): ?>
                            <tr>
                                <td><?php echo $dept['id']; ?></td>
                                <td><?php echo htmlspecialchars($dept['name']); ?></td>
                                <td><?php echo $dept['created_date']; ?></td>
                                <td>
                                    <button onclick="editDept(<?php echo $dept['id']; ?>, '<?php echo htmlspecialchars($dept['name']); ?>')" class="btn btn-warning btn-sm">Edit</button>
                                    <form method="post" style="display:inline;" onsubmit="return confirm('Delete this department?')">
                                        <input type="hidden" name="id" value="<?php echo $dept['id']; ?>">
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
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-3">
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <button type="submit" name="edit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>