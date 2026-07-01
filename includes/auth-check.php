<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to access this page.";
    redirect('auth/login.php');
}
