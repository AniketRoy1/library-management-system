<?php

require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = 'Invalid category selected.';
    redirect('index.php');
}

$sql = "SELECT * FROM categories WHERE id = :id AND is_deleted = FALSE";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$category = $stmt->fetch();

if (!$category) {
    $_SESSION['error'] = 'Category not found.';
    redirect('index.php');
}
?>

<main>
    <h1>Edit Category</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" action="update.php">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($category['id']); ?>">

        <div>
            <label>Category Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($category['name']); ?>">
        </div>

        <br>

        <div>
            <label>Description</label>
            <textarea name="description"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
        </div>

        <br>

        <div>
            <label>Status</label>
            <select name="status">
                <option value="active" <?php echo ($category['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo ($category['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <br>

        <button type="submit">Update Category</button>
        <a href="index.php">Back</a>
    </form>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
