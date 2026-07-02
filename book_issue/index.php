<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin", "librarian"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$sql = "
    SELECT
        book_issues.id,
        books.title AS book_title,
        members.name AS member_name,
        book_issues.issue_date,
        book_issues.due_date,
        book_issues.return_date,
        book_issues.status
    FROM book_issues
    LEFT JOIN books ON books.id = book_issues.book_id
    LEFT JOIN members ON members.id = book_issues.member_id
    WHERE book_issues.is_deleted = FALSE
    ORDER BY book_issues.id DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$issues = $stmt->fetchAll();
?>

<main>
    <h1>Issue Book</h1>

    <a href="create.php" class="button">Issue New Book</a>

    <br><br>

    <?php if (isset($_SESSION["success"])): ?>
        <p class="success"><?php echo $_SESSION["success"]; ?></p>
        <?php unset($_SESSION["success"]); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION["error"])): ?>
        <p class="error"><?php echo $_SESSION["error"]; ?></p>
        <?php unset($_SESSION["error"]); ?>
    <?php endif; ?>

    <table cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>S.N.</th>
                <th>Book</th>
                <th>Member</th>
                <th>Issue Date</th>
                <th>Due Date</th>
                <th>Return Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($issues) > 0): ?>
                <?php foreach ($issues as $index => $issue): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($issue['book_title'] ?? 'Unknown'); ?></td>
                        <td><?php echo htmlspecialchars($issue['member_name'] ?? 'Unknown'); ?></td>
                        <td><?php echo htmlspecialchars($issue['issue_date'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($issue['due_date'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($issue['return_date'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($issue['status'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No issued books found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>