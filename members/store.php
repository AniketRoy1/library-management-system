<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin", "librarian"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isPostRequest()) {
    redirect('index.php');
}

$name = cleanInputData($_POST['name'] ?? '');
$email = cleanInputData($_POST['email'] ?? '');
$phone = cleanInputData($_POST['phone'] ?? '');
$address = cleanInputData($_POST['address'] ?? '');
$status = cleanInputData($_POST['status'] ?? 'active');

if (empty($name)) {
    $_SESSION['error'] = 'Member name is required.';
    redirect('create.php');
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Please enter a valid email address.';
    redirect('create.php');
}

if (!in_array($status, ['active', 'inactive'], true)) {
    $_SESSION['error'] = 'Invalid status selected.';
    redirect('create.php');
}

$checkSql = "SELECT id FROM members WHERE LOWER(email) = LOWER(:email) AND is_deleted = FALSE";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute(['email' => $email]);

if ($email !== '' && $checkStmt->fetch()) {
    $_SESSION['error'] = 'A member with this email already exists.';
    redirect('create.php');
}

$sql = "INSERT INTO members (name, email, phone, address, status) VALUES (:name, :email, :phone, :address, :status)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'address' => $address,
    'status' => $status,
]);

$_SESSION['success'] = 'Member created successfully.';
redirect('index.php');