<nav>
    <ul>
        <li>
            <a href="/dashboard/index.php">Dashboard</a>
        </li>

        <?php if ($_SESSION["role_name"] === "admin"): ?>
            <li>
                <a href="/users/index.php">User Management</a>
            </li>
            <li>
                <a href="/categories/index.php">Categories</a>
            </li>
            <li>
                <a href="/authors/index.php">Authors</a>
            </li>
            <li>
                <a href="/publishers/index.php">Publishers</a>
            </li>
        <?php endif; ?>

        <li>
            <a href="/books/index.php">Books</a>
        </li>

        <li>
            <a href="/members/index.php">Members</a>
        </li>

        <li>
            <a href="/book_issue/index.php">Book Issue</a>
        </li>

        <li>
            <a href="/returns/index.php">Return Book</a>
        </li>

        <?php if ($_SESSION["role_name"] === "admin"): ?>
            <li>
                <a href="/reports/index.php">Reports</a>
            </li>
        <?php endif; ?>

        <li>
            <a href="/auth/logout.php">Logout</a>
        </li>
    </ul>
</nav>

<hr>