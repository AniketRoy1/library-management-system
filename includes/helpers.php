<?php
function redirect(string $path): void
{
    $basePath = dirname(__DIR__);
    $isAbsoluteUrl = preg_match('/^(https?:\/\/|\/)/', $path) === 1;

    if ($isAbsoluteUrl) {
        header("Location: $path");
        exit;
    }

    $normalizedPath = ltrim($path, '/');
    $fullPath = $basePath . DIRECTORY_SEPARATOR . $normalizedPath;

    if (file_exists($fullPath) && is_dir($fullPath)) {
        header("Location: /" . $normalizedPath . "/index.php");
        exit;
    }

    header("Location: /" . $normalizedPath);
    exit;
}

function cleanInputData(string $data): string
{
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

function showSuccess(string $message): string
{
    return "<P style='color:green; '>$message</p>";
}

function showError(string $message): string
{
    return "<P style='color:red; '>$message</p>";
}

function isPostRequest(): bool
{
    return $_SERVER["REQUEST_METHOD"] === "POST";
}