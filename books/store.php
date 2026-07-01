<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin", "librarian"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isPostRequest()) {
    redirect("index.php");
}

$title = cleanInputData($_POST["title"] ?? "");
$isbn = cleanInputData($_POST["isbn"] ?? "");
$categoryId = $_POST["category_id"] ?? null;
$authorId = $_POST["author_id"] ?? null;
$publisherId = $_POST["publisher_id"] ?? null;
$edition = cleanInputData($_POST["edition"] ?? "");
$publishedYear = (int)($_POST["published_year"] ?? 0);
$totalCopies = (int)($_POST["total_copies"] ?? 1);
$availableCopies = (int)($_POST["available_copies"] ?? 1);
$shelfLocation = cleanInputData($_POST["shelf_location"] ?? "");
$description = cleanInputData($_POST["description"] ?? "");
$status = strtolower(cleanInputData($_POST["status"] ?? "available"));

if (empty($title) || empty($isbn)) {
    $_SESSION["error"] = "Title and ISBN are required.";
    redirect("create.php");
}

$allowedStatuses = ["available", "borrowed", "issued", "damaged", "lost", "unavailable"];
if (!in_array($status, $allowedStatuses, true)) {
    $status = "available";
}

if (empty($categoryId) || empty($authorId) || empty($publisherId)) {
    $_SESSION["error"] = "Category, author, and publisher are required.";
    redirect("create.php");
}

if ($totalCopies < 1) {
    $_SESSION["error"] = "Total copies must be at least 1.";
    redirect("create.php");
}

if ($availableCopies < 0 || $availableCopies > $totalCopies) {
    $_SESSION["error"] = "Available copies must be between 0 and total copies.";
    redirect("create.php");
}

$checkSql = "SELECT id FROM books WHERE LOWER(isbn) = LOWER(:isbn) AND is_deleted = FALSE";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute(["isbn" => $isbn]);

$existingBook = $checkStmt->fetch();

if ($existingBook) {
    $_SESSION["error"] = "Book with this ISBN already exists.";
    redirect("create.php");
}

$sql = "INSERT INTO books (category_id, author_id, publisher_id, title, isbn, edition, published_year, total_copies, available_copies, shelf_location, description, status)
        VALUES (:category_id, :author_id, :publisher_id, :title, :isbn, :edition, :published_year, :total_copies, :available_copies, :shelf_location, :description, :status)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    "category_id" => $categoryId ?: null,
    "author_id" => $authorId ?: null,
    "publisher_id" => $publisherId ?: null,
    "title" => $title,
    "isbn" => $isbn,
    "edition" => $edition,
    "published_year" => $publishedYear ?: null,
    "total_copies" => $totalCopies,
    "available_copies" => $availableCopies,
    "shelf_location" => $shelfLocation,
    "description" => $description,
    "status" => $status,
]);

$_SESSION["success"] = "Book created successfully.";
redirect("index.php");
