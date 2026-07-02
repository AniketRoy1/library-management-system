<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin", "librarian"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = 'Invalid member selected.';
    redirect('index.php');
}

$sql = "SELECT * FROM members WHERE id = :id AND is_deleted = FALSE";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$member = $stmt->fetch();

if (!$member) {
    $_SESSION['error'] = 'Member not found.';
    redirect('index.php');
}
?>

<main>
    <h1>Edit Member</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" action="update.php">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($member['id']); ?>">

        <div>
            <label>Full Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($member['name']); ?>" required>
        </div>

        <br>

        <div>
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($member['email'] ?? ''); ?>">
        </div>

        <br>

        <div>
            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($member['phone'] ?? ''); ?>">
        </div>

        <br>

        <div>
            <label>Address</label>
            <textarea name="address"><?php echo htmlspecialchars($member['address'] ?? ''); ?></textarea>
        </div>

        <br>

        <div>
            <label>Status</label>
            <select name="status">
                <option value="active" <?php echo ($member['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo ($member['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <br>

        <button type="submit">Update Member</button>
        <a href="index.php">Back</a>
    </form>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>