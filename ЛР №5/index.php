<?php
// Лабораторная работа №5
// Таблица умножения с динамической версткой

// 1. ОБРАБОТКА ПАРАМЕТРОВ

// Тип верстки: 'TABLE' или 'DIV' (по умолчанию 'TABLE')
$html_type = isset($_GET['html_type']) ? $_GET['html_type'] : 'TABLE';

// Содержание: число от 2 до 9 или null (вся таблица)
$content = isset($_GET['content']) ? (int)$_GET['content'] : null;

// 2. ФУНКЦИИ

/**
 * Функция преобразует число в ссылку (если число от 2 до 9 или результат умножения до 81)
 * @param int $num Число для преобразования
 * @return string HTML-код ссылки или просто число
 */
function outNumAsLink($num) {
    // Ссылки делаем только для чисел от 2 до 9 (таблица умножения) 
    // и для результатов до 81 (но по условию только если число <=9)
    if ($num >= 2 && $num <= 9) {
        // Сохраняем текущий тип верстки? Нет, по заданию ссылки "сбрасывают" тип верстки
        return '<a href="?content=' . $num . '" class="num-link">' . $num . '</a>';
    } else {
        return (string)$num;
    }
}

/**
 * Функция выводит столбец таблицы умножения
 * @param int $n Число, на которое умножаем (от 2 до 9)
 */
function outRow($n) {
    for ($i = 2; $i <= 9; $i++) {
        $result = $i * $n;
        echo '<div class="multiplication-row">';
        echo outNumAsLink($n) . ' × ' . outNumAsLink($i) . ' = ' . outNumAsLink($result);
        echo '</div>';
    }
}

/**
 * Функция выводит всю таблицу умножения (8 столбцов)
 * @param string $type Тип верстки ('TABLE' или 'DIV')
 */
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
        // Блочная верстка (горизонтальные блоки)
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

/**
 * Функция выводит один столбец таблицы умножения
 * @param int $n Число, на которое умножаем
 * @param string $type Тип верстки ('TABLE' или 'DIV')
 */
function outSingleColumn($n, $type) {
    if ($type === 'TABLE') {
        // Табличная верстка (один столбец)
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
        // Блочная верстка (один блок)
        echo '<div class="block-table single-block">' . "\n";
        echo '<div class="block-column single">' . "\n";
        echo '<div class="block-title">' . outNumAsLink($n) . '</div>' . "\n";
        outRow($n);
        echo '</div>' . "\n";
        echo '</div>' . "\n";
    }
}

// 3. ФОРМИРОВАНИЕ ССЫЛОК ДЛЯ МЕНЮ (с сохранением параметров)

// Базовые параметры для ссылок
$main_menu_link = '?';
$side_menu_link = '?';

// Добавляем параметр html_type в ссылки, если он есть
if ($html_type !== 'TABLE') {
    $main_menu_link .= 'html_type=' . $html_type . '&';
    $side_menu_link .= 'html_type=' . $html_type . '&';
}

// Добавляем параметр content в ссылки главного меню, если он есть
if ($content !== null) {
    $main_menu_link .= 'content=' . $content;
} else {
    $main_menu_link = rtrim($main_menu_link, '&');
}

// Для бокового меню: параметр content будет добавляться отдельно для каждого пункта

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
    
    <!-- Главное меню (горизонтальное) -->
    <div class="main-menu">
        <?php
        // Пункт "Табличная верстка"
        $table_link = '?html_type=TABLE';
        if ($content !== null) {
            $table_link .= '&content=' . $content;
        }
        $table_selected = ($html_type === 'TABLE') ? 'selected' : '';
        echo '<a href="' . $table_link . '" class="' . $table_selected . '">Табличная верстка</a>';
        
        // Пункт "Блочная верстка"
        $div_link = '?html_type=DIV';
        if ($content !== null) {
            $div_link .= '&content=' . $content;
        }
        $div_selected = ($html_type === 'DIV') ? 'selected' : '';
        echo '<a href="' . $div_link . '" class="' . $div_selected . '">Блочная верстка</a>';
        ?>
    </div>
</header>

<div class="container">
    <!-- Основное меню (вертикальное, слева) -->
    <div class="side-menu">
        <?php
        // Пункт "Всё" (вся таблица умножения)
        $all_link = '?';
        if ($html_type !== 'TABLE') {
            $all_link .= 'html_type=' . $html_type;
        }
        $all_selected = ($content === null) ? 'selected' : '';
        echo '<a href="' . $all_link . '" class="' . $all_selected . '">📊 Всё</a>';
        
        // Пункты от 2 до 9
        for ($i = 2; $i <= 9; $i++) {
            $num_link = '?content=' . $i;
            if ($html_type !== 'TABLE') {
                $num_link .= '&html_type=' . $html_type;
            }
            $num_selected = ($content === $i) ? 'selected' : '';
            echo '<a href="' . $num_link . '" class="' . $num_selected . '">📐 Таблица умножения на ' . $i . '</a>';
        }
        ?>
    </div>
    
    <!-- Основной контент: таблица умножения -->
    <div class="content">
        <h1>📚 Таблица умножения</h1>
        
        <?php
        if ($content === null) {
            // Выводим всю таблицу умножения
            outFullTable($html_type);
        } else {
            // Выводим один столбец
            outSingleColumn($content, $html_type);
        }
        ?>
    </div>
</div>

<footer>
    <?php
    // Формирование информации о содержании страницы
    
    // Тип верстки
    if ($html_type === 'TABLE') {
        $info = 'Табличная верстка. ';
    } else {
        $info = 'Блочная верстка. ';
    }
    
    // Название таблицы умножения
    if ($content === null) {
        $info .= 'Таблица умножения полностью (2×2 до 9×9). ';
    } else {
        $info .= 'Столбец таблицы умножения на ' . $content . '. ';
    }
    
    // Дата и время
    $info .= 'Сформировано: ' . date('d.m.Y в H:i:s');
    
    echo $info;
    ?>
</footer>

</body>
</html>