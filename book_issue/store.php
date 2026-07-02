<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin", "librarian"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isPostRequest()) {
    redirect('index.php');
}

$bookId = cleanInputData($_POST['book_id'] ?? '');
$memberId = cleanInputData($_POST['member_id'] ?? '');
$issueDate = cleanInputData($_POST['issue_date'] ?? '');
$dueDate = cleanInputData($_POST['due_date'] ?? '');

if (empty($bookId) || empty($memberId) || empty($issueDate) || empty($dueDate)) {
    $_SESSION['error'] = 'All fields are required.';
    redirect('create.php');
}

if ($dueDate < $issueDate) {
    $_SESSION['error'] = 'Due date must be after issue date.';
    redirect('create.php');
}

$bookSql = "SELECT available_copies FROM books WHERE id = :id AND is_deleted = FALSE";
$bookStmt = $pdo->prepare($bookSql);
$bookStmt->execute(['id' => $bookId]);
$book = $bookStmt->fetch();

if (!$book || (int) $book['available_copies'] <= 0) {
    $_SESSION['error'] = 'Selected book is not available.';
    redirect('create.php');
}

$memberSql = "SELECT id FROM members WHERE id = :id AND is_deleted = FALSE AND status = 'active'";
$memberStmt = $pdo->prepare($memberSql);
$memberStmt->execute(['id' => $memberId]);
$member = $memberStmt->fetch();

if (!$member) {
    $_SESSION['error'] = 'Selected member is not valid.';
    redirect('create.php');
}

$pdo->beginTransaction();
try {
    $insertSql = "INSERT INTO book_issues (book_id, member_id, issue_date, due_date, status) VALUES (:book_id, :member_id, :issue_date, :due_date, 'issued')";
    $insertStmt = $pdo->prepare($insertSql);
    $insertStmt->execute([
        'book_id' => $bookId,
        'member_id' => $memberId,
        'issue_date' => $issueDate,
        'due_date' => $dueDate,
    ]);

    $updateSql = "UPDATE books SET available_copies = available_copies - 1, status = 'issued', updated_at = CURRENT_TIMESTAMP WHERE id = :id";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute(['id' => $bookId]);

    $pdo->commit();
    $_SESSION['success'] = 'Book issued successfully.';
    redirect('index.php');
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = 'Unable to issue the book. Please try again.';
    redirect('create.php');
}
