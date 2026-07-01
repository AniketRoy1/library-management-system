<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin", "librarian"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$sql = "SELECT * FROM publishers WHERE is_deleted = FALSE ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$publishers = $stmt->fetchAll();
?>

<main>
    <h1>Publisher Management</h1>

    <a href="create.php">Add New Publisher</a>

    <br><br>

    <?php if (isset($_SESSION["success"])): ?>
        <p style="color: green;"><?php echo $_SESSION["success"]; ?></p>
        <?php unset($_SESSION["success"]); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION["error"])): ?>
        <p style="color: red;"><?php echo $_SESSION["error"]; ?></p>
        <?php unset($_SESSION["error"]); ?>
    <?php endif; ?>

    <table cellpadding="10" cellspacing="0" style="border: 1px solid #ccc; width: 100%;">
        <thead style="background-color: #f2f2f2;">
            <tr>
                <th>S.N.</th>
                <th>Name</th>
                <th>Address</th>
                <th>Email</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php if (count($publishers) > 0): ?>
                <?php foreach ($publishers as $index => $publisher): ?>
                    <tr style="border-bottom: 1px solid #ccc;">
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($publisher["name"]); ?></td>
                        <td><?php echo htmlspecialchars($publisher["address"] ?? ""); ?></td>
                        <td><?php echo htmlspecialchars($publisher["email"] ?? ""); ?></td>
                        <td><?php echo htmlspecialchars($publisher["status"]); ?></td>
                        <td><?php echo htmlspecialchars($publisher["created_at"]); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $publisher["id"]; ?>">Edit</a>
                            <a href="delete.php?id=<?php echo $publisher["id"]; ?>" onclick="return confirm('Are you sure you want to delete this publisher?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No publishers found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
