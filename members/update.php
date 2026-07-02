<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin", "librarian"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isPostRequest()) {
    redirect('index.php');
}

$id = $_POST['id'] ?? null;
$name = cleanInputData($_POST['name'] ?? '');
$email = cleanInputData($_POST['email'] ?? '');
$phone = cleanInputData($_POST['phone'] ?? '');
$address = cleanInputData($_POST['address'] ?? '');
$status = cleanInputData($_POST['status'] ?? 'active');

if (!$id) {
    $_SESSION['error'] = 'Invalid member selected.';
    redirect('index.php');
}

if (empty($name)) {
    $_SESSION['error'] = 'Member name is required.';
    redirect('edit.php?id=' . $id);
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Please enter a valid email address.';
    redirect('edit.php?id=' . $id);
}

if (!in_array($status, ['active', 'inactive'], true)) {
    $_SESSION['error'] = 'Invalid status selected.';
    redirect('edit.php?id=' . $id);
}

$checkSql = "SELECT id FROM members WHERE LOWER(email) = LOWER(:email) AND id != :id AND is_deleted = FALSE";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute(['email' => $email, 'id' => $id]);

if ($email !== '' && $checkStmt->fetch()) {
    $_SESSION['error'] = 'A member with this email already exists.';
    redirect('edit.php?id=' . $id);
}

$sql = "UPDATE members SET name = :name, email = :email, phone = :phone, address = :address, status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'address' => $address,
    'status' => $status,
    'id' => $id,
]);

$_SESSION['success'] = 'Member updated successfully.';
redirect('index.php');