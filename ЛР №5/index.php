<?php
// Лабораторная работа №5
// Таблица умножения с динамической версткой

// 1. ОБРАБОТКА ПАРАМЕТРОВ

$html_type_param = isset($_GET['html_type']) ? $_GET['html_type'] : null;
$content_param = isset($_GET['content']) ? (int)$_GET['content'] : null;

// Устанавливаем значения по умолчанию для отображения
$html_type = $html_type_param !== null ? $html_type_param : 'TABLE';
$content = $content_param;

// 2. ФУНКЦИИ

function outNumAsLink($num) {
    if ($num >= 2 && $num <= 9) {
        return '<a href="?content=' . $num . '" class="num-link">' . $num . '</a>';
    } else {
        return (string)$num;
    }
}

function outRow($n) {
    for ($i = 2; $i <= 9; $i++) {
        $result = $i * $n;
        echo '<div class="multiplication-row">';
        echo outNumAsLink($n) . ' × ' . outNumAsLink($i) . ' = ' . outNumAsLink($result);
        echo '</div>';
    }
}

function outFullTable($type) {
    if ($type === 'TABLE') {
        // Табличная верстка
        echo '<table class="multiplication-table">' . "\n";
        echo '<thead>';
        echo '<tr><th>×</th>';
        for ($j = 2; $j <= 9; $j++) {
            echo '<th>' . outNumAsLink($j) . '</th>';
        }
        echo '</tr>' . "\n";
        echo '</thead>';
        echo '<tbody>' . "\n";
        
        for ($i = 2; $i <= 9; $i++) {
            echo '<tr>';
            echo '<th>' . outNumAsLink($i) . '</th>';
            for ($j = 2; $j <= 9; $j++) {
                $result = $i * $j;
                echo '<td>' . outNumAsLink($result) . '</td>';
            }
            echo '</tr>' . "\n";
        }
        echo '</tbody>';
        echo '</table>' . "\n";
    } else {
        // Блочная верстка
        echo '<div class="block-table">' . "\n";
        for ($i = 2; $i <= 9; $i++) {
            echo '<div class="block-column">' . "\n";
            echo '<div class="block-title">' . outNumAsLink($i) . '</div>' . "\n";
            outRow($i);
            echo '</div>' . "\n";
        }
        echo '</div>' . "\n";
    }
}

function outSingleColumn($n, $type) {
    if ($type === 'TABLE') {
        // Табличная верстка
        echo '<table class="multiplication-table single-column">' . "\n";
        echo '<thead>';
        echo '<tr><th>×</th><th>' . outNumAsLink($n) . '</th></tr>';
        echo '</thead>';
        echo '<tbody>' . "\n";
        
        for ($i = 2; $i <= 9; $i++) {
            $result = $i * $n;
            echo '<tr>';
            echo '<th>' . outNumAsLink($i) . '</th>';
            echo '<td>' . outNumAsLink($result) . '</td>';
            echo '</tr>' . "\n";
        }
        echo '</tbody>';
        echo '</table>' . "\n";
    } else {
        // Блочная верстка
        echo '<div class="block-table single-block">' . "\n";
        echo '<div class="block-column single">' . "\n";
        echo '<div class="block-title">' . outNumAsLink($n) . '</div>' . "\n";
        outRow($n);
        echo '</div>' . "\n";
        echo '</div>' . "\n";
    }
}

// 3. ФОРМИРОВАНИЕ ССЫЛОК ДЛЯ МЕНЮ

$main_menu_link = '?';
$side_menu_link = '?';

if ($html_type !== 'TABLE') {
    $main_menu_link .= 'html_type=' . $html_type . '&';
    $side_menu_link .= 'html_type=' . $html_type . '&';
}

if ($content !== null) {
    $main_menu_link .= 'content=' . $content;
} else {
    $main_menu_link = rtrim($main_menu_link, '&');
}


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛР5 — Мельников Кирилл, группа 241-353 — Таблица умножения</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">
        <img src="logo.png" alt="Логотип университета" onerror="this.style.display='none'">
        <div class="header-text">
            Мельников Кирилл | Группа 241-353 | Лабораторная работа №5
        </div>
    </div>
    
    <div class="main-menu">
        <?php
        // Пункт "Табличная верстка" - подсвечиваем, только если параметр html_type явно передан и равен 'TABLE'
        $table_link = '?html_type=TABLE';
        if ($content !== null) {
            $table_link .= '&content=' . $content;
        }
        // Подсветка только если параметр html_type был передан и равен 'TABLE'
        $table_selected = ($html_type_param === 'TABLE') ? 'selected' : '';
        echo '<a href="' . $table_link . '" class="' . $table_selected . '">Табличная верстка</a>';
        
        // Пункт "Блочная верстка" - подсвечиваем, только если параметр html_type явно передан и равен 'DIV'
        $div_link = '?html_type=DIV';
        if ($content !== null) {
            $div_link .= '&content=' . $content;
        }
        // Подсветка только если параметр html_type был передан и равен 'DIV'
        $div_selected = ($html_type_param === 'DIV') ? 'selected' : '';
        echo '<a href="' . $div_link . '" class="' . $div_selected . '">Блочная верстка</a>';
        ?>
    </div>
</header>

<div class="container">
    <div class="side-menu">
        <?php
        $all_link = '?';
        if ($html_type_param !== null && $html_type_param !== 'TABLE') {
            $all_link .= 'html_type=' . $html_type_param;
        }
        // Подсветка "Всё" только если параметр content НЕ передан
        $all_selected = ($content_param === null) ? 'selected' : '';
        echo '<a href="' . $all_link . '" class="' . $all_selected . '">📊 Всё</a>';
        
        for ($i = 2; $i <= 9; $i++) {
            $num_link = '?content=' . $i;
            if ($html_type_param !== null && $html_type_param !== 'TABLE') {
                $num_link .= '&html_type=' . $html_type_param;
            }
            // Подсветка текущего числа только если параметр content был передан и равен $i
            $num_selected = ($content_param === $i) ? 'selected' : '';
            echo '<a href="' . $num_link . '" class="' . $num_selected . '">📐 Таблица умножения на ' . $i . '</a>';
        }
        ?>
    </div>
    
    <div class="content">
        <h1>📚 Таблица умножения</h1>
        
        <?php
        if ($content === null) {
            outFullTable($html_type);
        } else {
            outSingleColumn($content, $html_type);
        }
        ?>
    </div>
</div>

<footer>
    <?php
    if ($html_type === 'TABLE') {
        $info = 'Табличная верстка. ';
    } else {
        $info = 'Блочная верстка. ';
    }
    
    if ($content === null) {
        $info .= 'Таблица умножения полностью (2×2 до 9×9). ';
    } else {
        $info .= 'Столбец таблицы умножения на ' . $content . '. ';
    }
    
    $info .= 'Сформировано: ' . date('d.m.Y в H:i:s');
    
    echo $info;
    ?>
</footer>

</body>
</html>