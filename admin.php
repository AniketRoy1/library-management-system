<?php
require_once __DIR__ . '/config/database.php';

$role_id = 1;
$full_name = "System Admin";
$email = "admin@test.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$status = "active";

$roleSql = "INSERT INTO roles (id, name) VALUES (:id, :name) ON CONFLICT (id) DO UPDATE SET name = EXCLUDED.name";
$roleStmt = $pdo->prepare($roleSql);
$roleStmt->execute(['id' => $role_id, 'name' => 'admin']);

$checkSql = "SELECT id FROM users WHERE email = :email LIMIT 1";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute(['email' => $email]);

if ($checkStmt->fetch()) {
    echo "Admin user already exists.";
    exit;
}

$sql = "INSERT INTO users (role_id, full_name, email, password, status)
VALUES (:role_id, :full_name, :email, :password, :status)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':role_id' => $role_id,
    ':full_name' => $full_name,
    ':email' => $email,
    ':password' => $password,
    ':status' => $status,
]);

echo "Admin user created successfully.";
?>