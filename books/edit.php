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
    $_SESSION['error'] = 'Invalid book selected.';
    redirect('index.php');
}

$sql = "SELECT * FROM books WHERE id = :id AND is_deleted = FALSE";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$book = $stmt->fetch();

if (!$book) {
    $_SESSION['error'] = 'Book not found.';
    redirect('index.php');
}

$categories = $pdo->query("SELECT id, name FROM categories WHERE is_deleted = FALSE ORDER BY name")->fetchAll();
$authors = $pdo->query("SELECT id, name FROM authors WHERE is_deleted = FALSE ORDER BY name")->fetchAll();
$publishers = $pdo->query("SELECT id, name FROM publishers WHERE is_deleted = FALSE ORDER BY name")->fetchAll();
?>

<main>
    <h1>Edit Book</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" action="update.php">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($book['id']); ?>">

        <div>
            <label>Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
        </div>

        <br>

        <div>
            <label>ISBN</label>
            <input type="text" name="isbn" value="<?php echo htmlspecialchars($book['isbn']); ?>" required>
        </div>

        <br>

        <div>
            <label>Category</label>
            <select name="category_id" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo ($book['category_id'] == $category['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label>Author</label>
            <select name="author_id" required>
                <option value="">Select Author</option>
                <?php foreach ($authors as $author): ?>
                    <option value="<?php echo $author['id']; ?>" <?php echo ($book['author_id'] == $author['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($author['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label>Publisher</label>
            <select name="publisher_id" required>
                <option value="">Select Publisher</option>
                <?php foreach ($publishers as $publisher): ?>
                    <option value="<?php echo $publisher['id']; ?>" <?php echo ($book['publisher_id'] == $publisher['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($publisher['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label>Edition</label>
            <input type="text" name="edition" value="<?php echo htmlspecialchars($book['edition'] ?? ''); ?>">
        </div>

        <br>

        <div>
            <label>Published Year</label>
            <input type="number" name="published_year" value="<?php echo htmlspecialchars($book['published_year'] ?? ''); ?>">
        </div>

        <br>

        <div>
            <label>Total Copies</label>
            <input type="number" name="total_copies" min="1" value="<?php echo htmlspecialchars($book['total_copies']); ?>">
        </div>

        <br>

        <div>
            <label>Available Copies</label>
            <input type="number" name="available_copies" min="0" value="<?php echo htmlspecialchars($book['available_copies']); ?>">
        </div>

        <br>

        <div>
            <label>Shelf Location</label>
            <input type="text" name="shelf_location" value="<?php echo htmlspecialchars($book['shelf_location'] ?? ''); ?>">
        </div>

        <br>

        <div>
            <label>Description</label>
            <textarea name="description"><?php echo htmlspecialchars($book['description'] ?? ''); ?></textarea>
        </div>

        <br>

        <div>
            <label>Status</label>
            <select name="status">
                <option value="available" <?php echo ($book['status'] === 'available') ? 'selected' : ''; ?>>Available</option>
                <option value="borrowed" <?php echo ($book['status'] === 'borrowed') ? 'selected' : ''; ?>>Borrowed</option>
                <option value="issued" <?php echo ($book['status'] === 'issued') ? 'selected' : ''; ?>>Issued</option>
                <option value="damaged" <?php echo ($book['status'] === 'damaged') ? 'selected' : ''; ?>>Damaged</option>
                <option value="lost" <?php echo ($book['status'] === 'lost') ? 'selected' : ''; ?>>Lost</option>
                <option value="unavailable" <?php echo ($book['status'] === 'unavailable') ? 'selected' : ''; ?>>Unavailable</option>
            </select>
        </div>

        <br>

        <button type="submit">Update Book</button>
        <a href="index.php">Back</a>
    </form>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
