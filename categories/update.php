<?php

require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isPostRequest()) {
    redirect("index.php");
}

$id = $_POST["id"] ?? null;
$name = cleanInputData($_POST["name"] ?? "");
$description = cleanInputData($_POST["description"] ?? "");
$status = cleanInputData($_POST["status"] ?? "active");

if (!$id) {
    $_SESSION["error"] = "Invalid category selected.";
    redirect("index.php");
}

if (empty($name)) {
    $_SESSION["error"] = "Category name is required.";
    redirect("edit.php?id=" . $id);
}

if (!in_array($status, ["active", "inactive"])) {
    $_SESSION["error"] = "Invalid status selected.";
    redirect("edit.php?id=" . $id);
}

$checkSql = "
    SELECT id 
    FROM categories 
    WHERE LOWER(name) = LOWER(:name) 
    AND id != :id 
    AND is_deleted = FALSE
";

$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute([
    "name" => $name,
    "id" => $id
]);

$existingCategory = $checkStmt->fetch();

if ($existingCategory) {
    $_SESSION["error"] = "Category already exists.";
    redirect("edit.php?id=" . $id);
}

$sql = "
    UPDATE categories
    SET 
        name = :name,
        description = :description,
        status = :status,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = :id
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    "name" => $name,
    "description" => $description,
    "status" => $status,
    "id" => $id
]);

$_SESSION["success"] = "Category updated successfully.";
redirect("index.php");