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
$address = cleanInputData($_POST["address"] ?? "");
$email = cleanInputData($_POST["email"] ?? "");
$status = cleanInputData($_POST["status"] ?? "active");

if (empty($name)) {
    $_SESSION["error"] = "Publisher name is required.";
    redirect("create.php");
}

if (!in_array($status, ["active", "inactive"])) {
    $_SESSION["error"] = "Invalid status selected.";
    redirect("create.php");
}

if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["error"] = "Please enter a valid email address.";
    redirect("create.php");
}

try {
    $pdo->exec("ALTER TABLE publishers ADD COLUMN IF NOT EXISTS email VARCHAR(255)");
} catch (PDOException $e) {
    $_SESSION["error"] = "Unable to prepare publisher email field.";
    redirect("create.php");
}

$checkSql = "SELECT id FROM publishers WHERE LOWER(name) = LOWER(:name) AND is_deleted = FALSE";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute(["name" => $name]);

$existingPublisher = $checkStmt->fetch();

if ($existingPublisher) {
    $_SESSION["error"] = "Publisher already exists.";
    redirect("create.php");
}

$sql = "INSERT INTO publishers (name, address, email, status) VALUES (:name, :address, :email, :status)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    "name" => $name,
    "address" => $address,
    "email" => $email,
    "status" => $status,
]);

$_SESSION["success"] = "Publisher created successfully.";
redirect("index.php");
