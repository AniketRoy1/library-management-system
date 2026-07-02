<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../includes/role-check.php';

allowRoles(["admin", "librarian"]);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isPostRequest()) {
    redirect('index.php');
}

$issueId = cleanInputData($_POST['issue_id'] ?? '');
$returnDate = cleanInputData($_POST['return_date'] ?? '');

if (empty($issueId) || empty($returnDate)) {
    $_SESSION['error'] = 'Please provide the required return details.';
    redirect('index.php');
}

$sql = "SELECT book_id, due_date FROM book_issues WHERE id = :id AND is_deleted = FALSE AND status = 'issued'";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $issueId]);
$issue = $stmt->fetch();

if (!$issue) {
    $_SESSION['error'] = 'Issue record not found or already returned.';
    redirect('index.php');
}

if ($returnDate < $issue['issue_date']) {
    $_SESSION['error'] = 'Return date cannot be earlier than the issue date.';
    redirect('create.php?issue_id=' . $issueId);
}

$pdo->beginTransaction();
try {
    $updateIssueSql = "UPDATE book_issues SET return_date = :return_date, status = 'returned', updated_at = CURRENT_TIMESTAMP WHERE id = :id";
    $updateIssueStmt = $pdo->prepare($updateIssueSql);
    $updateIssueStmt->execute([
        'return_date' => $returnDate,
        'id' => $issueId,
    ]);

    $updateBookSql = "UPDATE books SET available_copies = available_copies + 1, status = 'available', updated_at = CURRENT_TIMESTAMP WHERE id = :id";
    $updateBookStmt = $pdo->prepare($updateBookSql);
    $updateBookStmt->execute(['id' => $issue['book_id']]);

    $pdo->commit();
    $_SESSION['success'] = 'Book returned successfully.';
    redirect('index.php');
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = 'Unable to complete the return. Please try again.';
    redirect('create.php?issue_id=' . $issueId);
}
