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
$name = cleanInputData($_POST["name"] ?? "");
$biography = cleanInputData($_POST["biography"] ?? "");
$status = cleanInputData($_POST["status"] ?? "active");

if (!$id) {
    $_SESSION["error"] = "Invalid author selected.";
    redirect("index.php");
}

if (empty($name)) {
    $_SESSION["error"] = "Author name is required.";
    redirect("edit.php?id=" . $id);
}

if (!in_array($status, ["active", "inactive"])) {
    $_SESSION["error"] = "Invalid status selected.";
    redirect("edit.php?id=" . $id);
}

$checkSql = "SELECT id FROM authors WHERE LOWER(name) = LOWER(:name) AND id != :id AND is_deleted = FALSE";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute(["name" => $name, "id" => $id]);

$existingAuthor = $checkStmt->fetch();

if ($existingAuthor) {
    $_SESSION["error"] = "Author already exists.";
    redirect("edit.php?id=" . $id);
}

$sql = "UPDATE authors SET name = :name, biography = :biography, status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    "name" => $name,
    "biography" => $biography,
    "status" => $status,
    "id" => $id,
]);

$_SESSION["success"] = "Author updated successfully.";
redirect("index.php");
