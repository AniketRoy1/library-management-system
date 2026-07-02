<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin", "librarian"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$issueId = $_GET['issue_id'] ?? null;

if (!$issueId) {
    $_SESSION['error'] = 'No issue selected for return.';
    redirect('index.php');
}

$sql = "
    SELECT
        book_issues.id,
        book_issues.issue_date,
        book_issues.due_date,
        books.title AS book_title,
        members.name AS member_name
    FROM book_issues
    LEFT JOIN books ON books.id = book_issues.book_id
    LEFT JOIN members ON members.id = book_issues.member_id
    WHERE book_issues.id = :id
      AND book_issues.is_deleted = FALSE
      AND book_issues.status = 'issued'
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $issueId]);
$issue = $stmt->fetch();

if (!$issue) {
    $_SESSION['error'] = 'Requested issue record not found.';
    redirect('index.php');
}
?>

<main>
    <h1>Return Book</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?php echo $_SESSION['error']; ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" action="store.php">
        <input type="hidden" name="issue_id" value="<?php echo htmlspecialchars($issue['id']); ?>">

        <div>
            <label>Book</label>
            <input type="text" value="<?php echo htmlspecialchars($issue['book_title']); ?>" disabled>
        </div>

        <div>
            <label>Member</label>
            <input type="text" value="<?php echo htmlspecialchars($issue['member_name']); ?>" disabled>
        </div>

        <div>
            <label>Issue Date</label>
            <input type="date" value="<?php echo htmlspecialchars($issue['issue_date']); ?>" disabled>
        </div>

        <div>
            <label>Due Date</label>
            <input type="date" value="<?php echo htmlspecialchars($issue['due_date']); ?>" disabled>
        </div>

        <div>
            <label>Return Date</label>
            <input type="date" name="return_date" required value="<?php echo date('Y-m-d'); ?>">
        </div>

        <button type="submit">Complete Return</button>
        <a href="index.php">Cancel</a>
    </form>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>