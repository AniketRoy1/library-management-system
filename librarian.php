<?php

require_once __DIR__ . '/config/database.php';

$full_name = "Library Admin";
$email = "librarian@example.com";
$password = password_hash("librarian123", PASSWORD_DEFAULT);
$role_id = 2;
$status = "active";

$roleSql = "INSERT INTO roles (id, name) VALUES (:id, :name) ON CONFLICT (id) DO UPDATE SET name = EXCLUDED.name";
$roleStmt = $pdo->prepare($roleSql);
$roleStmt->execute(['id' => $role_id, 'name' => 'librarian']);

$checkSql = "SELECT id FROM users WHERE email = :email LIMIT 1";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute(['email' => $email]);

if ($checkStmt->fetch()) {
    echo "Librarian user already exists.";
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

echo "Librarian user created successfully.";