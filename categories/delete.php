<?php

require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isPostRequest()) {
    $id = $_GET['id'] ?? null;

    if (!$id) {
        $_SESSION['error'] = 'Invalid category selected.';
        redirect('index.php');
    }

    $sql = "UPDATE categories SET is_deleted = TRUE, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    $_SESSION['success'] = 'Category deleted successfully.';
    redirect('index.php');
}

$_SESSION['error'] = 'Invalid request method.';
redirect('index.php');
