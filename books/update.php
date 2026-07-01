<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin", "librarian"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isPostRequest()) {
    redirect("index.php");
}

$id = $_POST["id"] ?? null;
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
$status = cleanInputData($_POST["status"] ?? "available");

if (!$id) {
    $_SESSION["error"] = "Invalid book selected.";
    redirect("index.php");
}

if (empty($title) || empty($isbn)) {
    $_SESSION["error"] = "Title and ISBN are required.";
    redirect("edit.php?id=" . $id);
}

if (!in_array($status, ["available", "unavailable"])) {
    $_SESSION["error"] = "Invalid status selected.";
    redirect("edit.php?id=" . $id);
}

if ($totalCopies < 1) {
    $_SESSION["error"] = "Total copies must be at least 1.";
    redirect("edit.php?id=" . $id);
}

if ($availableCopies < 0 || $availableCopies > $totalCopies) {
    $_SESSION["error"] = "Available copies must be between 0 and total copies.";
    redirect("edit.php?id=" . $id);
}

$checkSql = "SELECT id FROM books WHERE LOWER(isbn) = LOWER(:isbn) AND id != :id AND is_deleted = FALSE";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute(["isbn" => $isbn, "id" => $id]);

$existingBook = $checkStmt->fetch();

if ($existingBook) {
    $_SESSION["error"] = "Book with this ISBN already exists.";
    redirect("edit.php?id=" . $id);
}

$sql = "UPDATE books SET category_id = :category_id, author_id = :author_id, publisher_id = :publisher_id, title = :title, isbn = :isbn, edition = :edition, published_year = :published_year, total_copies = :total_copies, available_copies = :available_copies, shelf_location = :shelf_location, description = :description, status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
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
    "id" => $id,
]);

$_SESSION["success"] = "Book updated successfully.";
redirect("index.php");
