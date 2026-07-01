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

$sql = "
    SELECT 
        books.*, 
        categories.name AS category_name,
        authors.name AS author_name,
        publishers.name AS publisher_name
    FROM books
    LEFT JOIN categories ON categories.id = books.category_id
    LEFT JOIN authors ON authors.id = books.author_id
    LEFT JOIN publishers ON publishers.id = books.publisher_id
    WHERE books.id = :id AND books.is_deleted = FALSE
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$book = $stmt->fetch();

if (!$book) {
    $_SESSION['error'] = 'Book not found.';
    redirect('index.php');
}
?>

<main>
    <h1>Book Details</h1>

    <p><strong>Title:</strong> <?php echo htmlspecialchars($book['title']); ?></p>
    <p><strong>ISBN:</strong> <?php echo htmlspecialchars($book['isbn']); ?></p>
    <p><strong>Category:</strong> <?php echo htmlspecialchars($book['category_name'] ?? ''); ?></p>
    <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author_name'] ?? ''); ?></p>
    <p><strong>Publisher:</strong> <?php echo htmlspecialchars($book['publisher_name'] ?? ''); ?></p>
    <p><strong>Edition:</strong> <?php echo htmlspecialchars($book['edition'] ?? ''); ?></p>
    <p><strong>Published Year:</strong> <?php echo htmlspecialchars($book['published_year'] ?? ''); ?></p>
    <p><strong>Total Copies:</strong> <?php echo htmlspecialchars($book['total_copies']); ?></p>
    <p><strong>Available Copies:</strong> <?php echo htmlspecialchars($book['available_copies']); ?></p>
    <p><strong>Shelf Location:</strong> <?php echo htmlspecialchars($book['shelf_location'] ?? ''); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($book['status']); ?></p>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($book['description'] ?? ''); ?></p>

    <br>
    <a href="index.php">Back</a>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
