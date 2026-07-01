<?php

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';

function allowRoles(array $allowedRoles)
{
    if (!isset($_SESSION["role_name"])){
        $_SESSION["error"]= "Please login first.";
        redirect('auth/login.php');
    }

    $currentRole = strtolower(trim((string) $_SESSION["role_name"]));
    $normalizedAllowedRoles = array_map(function ($role) {
        return strtolower(trim((string) $role));
    }, $allowedRoles);

    if (!in_array($currentRole, $normalizedAllowedRoles, true)){
        $_SESSION["error"]= "You are not allowed to acccess this page.";
        redirect('dashboard/index.php');
    }
}