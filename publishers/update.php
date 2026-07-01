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
$address = cleanInputData($_POST["address"] ?? "");
$email = cleanInputData($_POST["email"] ?? "");
$status = cleanInputData($_POST["status"] ?? "active");

if (!$id) {
    $_SESSION["error"] = "Invalid publisher selected.";
    redirect("index.php");
}

if (empty($name)) {
    $_SESSION["error"] = "Publisher name is required.";
    redirect("edit.php?id=" . $id);
}

if (!in_array($status, ["active", "inactive"])) {
    $_SESSION["error"] = "Invalid status selected.";
    redirect("edit.php?id=" . $id);
}

if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["error"] = "Please enter a valid email address.";
    redirect("edit.php?id=" . $id);
}

try {
    $pdo->exec("ALTER TABLE publishers ADD COLUMN IF NOT EXISTS email VARCHAR(255)");
} catch (PDOException $e) {
    $_SESSION["error"] = "Unable to prepare publisher email field.";
    redirect("edit.php?id=" . $id);
}

$checkSql = "SELECT id FROM publishers WHERE LOWER(name) = LOWER(:name) AND id != :id AND is_deleted = FALSE";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute(["name" => $name, "id" => $id]);

$existingPublisher = $checkStmt->fetch();

if ($existingPublisher) {
    $_SESSION["error"] = "Publisher already exists.";
    redirect("edit.php?id=" . $id);
}

$sql = "UPDATE publishers SET name = :name, address = :address, email = :email, status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    "name" => $name,
    "address" => $address,
    "email" => $email,
    "status" => $status,
    "id" => $id,
]);

$_SESSION["success"] = "Publisher updated successfully.";
redirect("index.php");
