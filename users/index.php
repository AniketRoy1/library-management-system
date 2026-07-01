<?php

require_once "../includes/auth-check.php";
require_once "../includes/role-check.php";

allowRoles(["admin"]);

require_once "../includes/header.php";
require_once "../includes/sidebar.php";

?>

<main>
    <h1>User Management</h1>
    <p>Only Admin can access this page.</p>
</main>

<?php require_once "../includes/footer.php"; ?>