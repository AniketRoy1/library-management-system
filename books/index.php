<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin", "librarian"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$sql = "
    SELECT 
        books.id,
        books.title,
        books.isbn,
        books.edition,
        books.published_year,
        books.total_copies,
        books.available_copies,
        books.shelf_location,
        books.status,
        categories.name AS category_name,
        authors.name AS author_name,
        publishers.name AS publisher_name
    FROM books
    INNER JOIN categories ON categories.id = books.category_id
    INNER JOIN authors ON authors.id = books.author_id
    INNER JOIN publishers ON publishers.id = books.publisher_id
    WHERE books.is_deleted = FALSE
    ORDER BY books.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$books = $stmt->fetchAll();
?>

<main>
    <h1>Book Management</h1>

    <a href="create.php">Add New Book</a>

    <br><br>

    <?php if (isset($_SESSION["success"])): ?>
        <p style="color: green;"><?php echo $_SESSION["success"]; ?></p>
        <?php unset($_SESSION["success"]); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION["error"])): ?>
        <p style="color: red;"><?php echo $_SESSION["error"]; ?></p>
        <?php unset($_SESSION["error"]); ?>
    <?php endif; ?>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>S.N.</th>
                <th>Title</th>
                <th>ISBN</th>
                <th>Category</th>
                <th>Author</th>
                <th>Publisher</th>
                <th>Total</th>
                <th>Available</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php if (count($books) > 0): ?>
                <?php foreach ($books as $index => $book): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($book["title"]); ?></td>
                        <td><?php echo htmlspecialchars($book["isbn"]); ?></td>
                        <td><?php echo htmlspecialchars($book["category_name"]); ?></td>
                        <td><?php echo htmlspecialchars($book["author_name"]); ?></td>
                        <td><?php echo htmlspecialchars($book["publisher_name"]); ?></td>
                        <td><?php echo htmlspecialchars($book["total_copies"]); ?></td>
                        <td><?php echo htmlspecialchars($book["available_copies"]); ?></td>
                        <td><?php echo htmlspecialchars($book["status"]); ?></td>
                        <td>
                            <a href="view.php?id=<?php echo $book["id"]; ?>">View</a>
                            <a href="edit.php?id=<?php echo $book["id"]; ?>">Edit</a>
                            <a href="delete.php?id=<?php echo $book["id"]; ?>"
                               onclick="return confirm('Are you sure you want to delete this book?')">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10">No books found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>