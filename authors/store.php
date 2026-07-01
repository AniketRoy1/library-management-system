<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin", "librarian"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isPostRequest()) {
    redirect("index.php");
}

$name = cleanInputData($_POST["name"] ?? "");
$biography = cleanInputData($_POST["biography"] ?? "");
$status = cleanInputData($_POST["status"] ?? "active");

if (empty($name)) {
    $_SESSION["error"] = "Author name is required.";
    redirect("create.php");
}

if (!in_array($status, ["active", "inactive"])) {
    $_SESSION["error"] = "Invalid status selected.";
    redirect("create.php");
}

$checkSql = "SELECT id FROM authors WHERE LOWER(name) = LOWER(:name) AND is_deleted = FALSE";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute(["name" => $name]);

$existingAuthor = $checkStmt->fetch();

if ($existingAuthor) {
    $_SESSION["error"] = "Author already exists.";
    redirect("create.php");
}

$sql = "INSERT INTO authors (name, biography, status) VALUES (:name, :biography, :status)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    "name" => $name,
    "biography" => $biography,
    "status" => $status,
]);

$_SESSION["success"] = "Author created successfully.";
redirect("index.php");
