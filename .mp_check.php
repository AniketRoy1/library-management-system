<?php
require 'config/database.php';

$tables = ['roles', 'users'];
foreach ($tables as $table) {
    echo "TABLE $table\n";
    $stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='$table' ORDER BY ordinal_position");
    foreach ($stmt as $row) {
        echo $row['column_name'] . PHP_EOL;
    }
    echo "---\n";
}

$stmt = $pdo->query("SELECT id, role_id, full_name, email, password, status FROM users ORDER BY id");
foreach ($stmt as $row) {
    echo json_encode($row) . PHP_EOL;
}
