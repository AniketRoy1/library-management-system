<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isPostRequest()) {
    redirect('auth/login.php');
}

$email = cleanInputData($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if (empty($email) || empty($password)) {
    $_SESSION["error"] = "Email and password are required.";
    redirect('auth/login.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["error"] = "Please enter a valid email address.";
    redirect('auth/login.php');
}

$sql = "
    SELECT 
        users.id,
        users.full_name,
        users.email,
        users.password,
        users.status,
        users.role_id,
        roles.name AS role_name
    FROM users
    INNER JOIN roles ON roles.id = users.role_id
    WHERE users.email = :email
    LIMIT 1
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    "email" => $email
]);

$user = $stmt->fetch();

if (!$user) {
    $_SESSION["error"] = "Invalid email or password.";
    redirect('auth/login.php');
}

if ($user["status"] !== "active") {
    $_SESSION["error"] = "Your account is inactive. Please contact admin.";
    redirect('auth/login.php');
}

if (!password_verify($password, $user["password"])) {
    $_SESSION["error"] = "Invalid email or password.";
    redirect('auth/login.php');
}

session_regenerate_id(true);

$_SESSION["user_id"] = $user["id"];
$_SESSION["full_name"] = $user["full_name"];
$_SESSION["email"] = $user["email"];
$_SESSION["role_id"] = $user["role_id"];
$_SESSION["role_name"] = $user["role_name"];

redirect('dashboard/index.php');