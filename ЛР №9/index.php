<?php
require_once 'config.php';

$page = isset($_GET['p']) ? $_GET['p'] : 'viewer';
$allowed = ['viewer', 'add', 'edit', 'delete'];

if (!in_array($page, $allowed)) {
    $page = 'viewer';
}

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$allowedSorts = ['id', 'lastname', 'birth_date'];
if (!in_array($sort, $allowedSorts)) {
    $sort = 'id';
}

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 0;
if ($currentPage < 0) $currentPage = 0;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Записная книжка — Мельников Кирилл, группа 241-353, ЛР9</title>
    <link rel="stylesheet" href="style9.css">
</head>
<body>
    <header>
        <img src="./logo.png" alt="Логотип" width="60">
        <span>Мельников Кирилл | Группа 241-353 | Лабораторная работа №9</span>
    </header>

    <main>
        <div class="container">
            <?php
            require_once 'menu.php';
            echo renderMenu();
            
            switch ($page) {
                case 'viewer':
                    require_once 'viewer.php';
                    echo renderViewer($sort, $currentPage);
                    break;
                case 'add':
                    require_once 'add.php';
                    break;
                case 'edit':
                    require_once 'edit.php';
                    break;
                case 'delete':
                    require_once 'delete.php';
                    break;
                default:
                    require_once 'viewer.php';
                    echo renderViewer($sort, $currentPage);
                    break;
            }
            ?>
        </div>
    </main>

    <footer>
        Записная книжка | © <?php echo date('Y'); ?>
    </footer>
</body>
</html>