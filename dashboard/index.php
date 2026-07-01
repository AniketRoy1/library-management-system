<?php
    require_once "../includes/auth-check.php";
    require_once "../config/database.php";
    require_once "../includes/header.php";
    require_once "../includes/sidebar.php";

    $totalBooksStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM books WHERE is_deleted = FALSE");
$totalBooksStmt->execute();
$totalBooks = $totalBooksStmt->fetch()["total"];

$totalMembersStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM members WHERE is_deleted = FALSE");
$totalMembersStmt->execute();
$totalMembers = $totalMembersStmt->fetch()["total"];

$totalIssuedStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM book_issues WHERE status = 'issued'");
$totalIssuedStmt->execute();
$totalIssuedBooks = $totalIssuedStmt->fetch()["total"];

$totalCategoriesStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM categories WHERE is_deleted = FALSE");
$totalCategoriesStmt->execute();
$totalCategories = $totalCategoriesStmt->fetch()["total"];
?>

<main>
    <h1>Dashboard</h1>

    <p>
        Welcome, <?php echo htmlspecialchars($_SESSION["full_name"]); ?>.
    </p>

    <p>
        Your role is: <?php echo htmlspecialchars($_SESSION["role_name"]); ?>.
    </p>

    <div style="display: flex; gap: 20px; flex-wrap: wrap;">

        <div style="border: 1px solid #ccc; padding: 20px; width: 200px;">
            <h3>Total Books</h3>
            <p><?php echo $totalBooks; ?></p>
        </div>

        <div style="border: 1px solid #ccc; padding: 20px; width: 200px;">
            <h3>Total Members</h3>
            <p><?php echo $totalMembers; ?></p>
        </div>

        <div style="border: 1px solid #ccc; padding: 20px; width: 200px;">
            <h3>Issued Books</h3>
            <p><?php echo $totalIssuedBooks; ?></p>
        </div>

        <div style="border: 1px solid #ccc; padding: 20px; width: 200px;">
            <h3>Categories</h3>
            <p><?php echo $totalCategories; ?></p>
        </div>

    </div>
</main>

<?php require_once "../includes/footer.php"; ?>