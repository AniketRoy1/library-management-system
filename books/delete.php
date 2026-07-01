<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin", "librarian"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = 'Invalid book selected.';
    redirect('index.php');
}

$sql = "UPDATE books SET is_deleted = TRUE, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);

$_SESSION['success'] = 'Book deleted successfully.';
redirect('index.php');
