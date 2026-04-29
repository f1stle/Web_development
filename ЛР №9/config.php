<?php

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '123';
$DB_NAME = 'notebook_db';

// Создание подключения mysqli
function getDBConnection() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    
    $conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    
    if (!$conn) {
        die('Ошибка подключения к БД: ' . mysqli_connect_error());
    }
    
    mysqli_set_charset($conn, 'utf8mb4');
    
    return $conn;
}

function closeDBConnection($conn) {
    if ($conn) {
        mysqli_close($conn);
    }
}
?>