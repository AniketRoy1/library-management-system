<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin", "librarian"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$books = $pdo->query("SELECT id, title FROM books WHERE is_deleted = FALSE AND available_copies > 0 ORDER BY title")->fetchAll();
$members = $pdo->query("SELECT id, name FROM members WHERE is_deleted = FALSE AND status = 'active' ORDER BY name")->fetchAll();
?>

<main>
    <h1>Issue New Book</h1>

    <?php if (isset($_SESSION["error"])): ?>
        <p class="error"><?php echo $_SESSION["error"]; ?></p>
        <?php unset($_SESSION["error"]); ?>
    <?php endif; ?>

    <form method="POST" action="store.php">
        <div>
            <label>Book</label>
            <select name="book_id" required>
                <option value="">Select a book</option>
                <?php foreach ($books as $book): ?>
                    <option value="<?php echo $book['id']; ?>"><?php echo htmlspecialchars($book['title']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Member</label>
            <select name="member_id" required>
                <option value="">Select a member</option>
                <?php foreach ($members as $member): ?>
                    <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Issue Date</label>
            <input type="date" name="issue_date" required value="<?php echo date('Y-m-d'); ?>">
        </div>

        <div>
            <label>Due Date</label>
            <input type="date" name="due_date" required>
        </div>

        <button type="submit">Issue Book</button>
        <a href="index.php">Cancel</a>
    </form>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>