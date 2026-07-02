<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin", "librarian"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$sql = "SELECT * FROM members WHERE is_deleted = FALSE ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$members = $stmt->fetchAll();
?>

<main>
    <h1>Member Management</h1>

    <a href="create.php">Add New Member</a>

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
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php if (count($members) > 0): ?>
                <?php foreach ($members as $index => $member): ?>
                    <tr style="border-bottom: 1px solid #ccc;">
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($member["name"]); ?></td>
                        <td><?php echo htmlspecialchars($member["email"] ?? ""); ?></td>
                        <td><?php echo htmlspecialchars($member["phone"] ?? ""); ?></td>
                        <td><?php echo htmlspecialchars($member["address"] ?? ""); ?></td>
                        <td><?php echo htmlspecialchars($member["status"]); ?></td>
                        <td><?php echo htmlspecialchars($member["created_at"]); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $member["id"]; ?>">Edit</a>
                            <a href="delete.php?id=<?php echo $member["id"]; ?>" onclick="return confirm('Are you sure you want to delete this member?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No members found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>