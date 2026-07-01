<?php

require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isPostRequest()) {
    redirect("index.php");
}

$name = cleanInputData($_POST["name"] ?? "");
$description = cleanInputData($_POST["description"] ?? "");
$status = cleanInputData($_POST["status"] ?? "active");

if (empty($name)) {
    $_SESSION["error"] = "Category name is required.";
    redirect("create.php");
}

if (!in_array($status, ["active", "inactive"])) {
    $_SESSION["error"] = "Invalid status selected.";
    redirect("create.php");
}

$checkSql = "SELECT id FROM categories WHERE LOWER(name) = LOWER(:name) AND is_deleted = FALSE";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute([
    "name" => $name
]);

$existingCategory = $checkStmt->fetch();

if ($existingCategory) {
    $_SESSION["error"] = "Category already exists.";
    redirect("create.php");
}

$sql = "
    INSERT INTO categories (name, description, status)
    VALUES (:name, :description, :status)
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    "name" => $name,
    "description" => $description,
    "status" => $status
]);

$_SESSION["success"] = "Category created successfully.";
redirect("index.php");