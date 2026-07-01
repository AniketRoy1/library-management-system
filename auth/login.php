<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';

if (isset($_SESSION['user_id'])) {
    redirect('dashboard/index.php');
}

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';

unset($_SESSION['error'], $_SESSION['success']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login - lobrary management system</title>
</head>
<body>
    <h2>login</h2>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="login-process.php" method="POST">
    <div>
        <label for="">email</label>
        <input type="email" name="email" placeholder="enter email">
    </div>
    <br>
    <div>
        <label for="">password</label>
        <input type="password" name="password" placeholder="enter password">
    </div>
    <br>
    
    
        <button type="submit">login</button>
    </form>
</body>
</html>