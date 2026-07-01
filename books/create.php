<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin", "librarian"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$categories = $pdo->query("SELECT id, name FROM categories WHERE is_deleted = FALSE ORDER BY name")->fetchAll();
$authors = $pdo->query("SELECT id, name FROM authors WHERE is_deleted = FALSE ORDER BY name")->fetchAll();
$publishers = $pdo->query("SELECT id, name FROM publishers WHERE is_deleted = FALSE ORDER BY name")->fetchAll();
?>

<main>
    <h1>Add Book</h1>

    <?php if (isset($_SESSION["error"])): ?>
        <p style="color: red;"><?php echo $_SESSION["error"]; ?></p>
        <?php unset($_SESSION["error"]); ?>
    <?php endif; ?>

    <form method="POST" action="store.php">
        <div>
            <label>Title</label>
            <input type="text" name="title" required>
        </div>

        <br>

        <div>
            <label>ISBN</label>
            <input type="text" name="isbn" required>
        </div>

        <br>

        <div>
            <label>Category</label>
            <select name="category_id" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category["id"]; ?>"><?php echo htmlspecialchars($category["name"]); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label>Author</label>
            <select name="author_id" required>
                <option value="">Select Author</option>
                <?php foreach ($authors as $author): ?>
                    <option value="<?php echo $author["id"]; ?>"><?php echo htmlspecialchars($author["name"]); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label>Publisher</label>
            <select name="publisher_id" required>
                <option value="">Select Publisher</option>
                <?php foreach ($publishers as $publisher): ?>
                    <option value="<?php echo $publisher["id"]; ?>"><?php echo htmlspecialchars($publisher["name"]); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label>Edition</label>
            <input type="text" name="edition">
        </div>

        <br>

        <div>
            <label>Published Year</label>
            <input type="number" name="published_year">
        </div>

        <br>

        <div>
            <label>Total Copies</label>
            <input type="number" name="total_copies" min="1" value="1">
        </div>

        <br>

        <div>
            <label>Available Copies</label>
            <input type="number" name="available_copies" min="0" value="1">
        </div>

        <br>

        <div>
            <label>Shelf Location</label>
            <input type="text" name="shelf_location">
        </div>

        <br>

        <div>
            <label>Description</label>
            <textarea name="description"></textarea>
        </div>

        <br>

        <div>
            <label>Status</label>
            <select name="status">
                <option value="available">Available</option>
                <option value="borrowed">Borrowed</option>
                <option value="issued">Issued</option>
                <option value="damaged">Damaged</option>
                <option value="lost">Lost</option>
                <option value="unavailable">Unavailable</option>
            </select>
        </div>

        <br>

        <button type="submit">Save Book</button>
        <a href="index.php">Back</a>
    </form>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
