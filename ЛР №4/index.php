<?php

// 1. ИНИЦИАЛИЗАЦИЯ ПЕРЕМЕННЫХ

$columns_count = 3;

$structures = [
    'Яблоко*Красный*Сладкий#Груша*Зеленый*Сочный#Апельсин*Оранжевый*Кислый',
    'Иванов*25*Москва#Петров*30*СПб#Сидоров*28*Казань#Козлов*35*Нск',
    'PHP*1995*Раммус#Python*1991*ВанРоссум#Java*1995*Гослинг#C++*1985*Страуструп',
    'Меркурий*4879*0#Венера*12104*0#Земля*12756*1#Марс*6792*2',
    'Волга*3530*Каспийское#Лена*4400*Лаптевых#Обь*3650*Карское#Енисей*3487*Карское',
    'Байкал*1642*Россия#Виктория*83*Африка#Гурон*206*США#Мичиган*281*США',
    'Эверест*8848*Непал#К2*8611*Пакистан#Канченджанга*8586*Непал#Лхоцзе*8516*Непал',
    'Футбол*11*Ворота#Баскетбол*5*Кольцо#Теннис*1*Ракетка#Хоккей*6*Шайба',
    'Windows*1985*Microsoft#Linux*1991*Linus#macOS*2001*Apple#Android*2008*Google',
    'Красный*FF0000*Стоп#Зеленый*00FF00*Старт#Синий*0000FF*Море#Желтый*FFFF00*Солнце',
    'Пушкин*1799*Стихи#Лермонтов*1814*Поэмы#Толстой*1828*Романы#Достоевский*1821*Романы',
    'Кошка*Мурка*4#Собака*Бобик*5#Попугай*Кеша*2#Хомяк*Хома*1'
];



function getTR($row_data, $columns) {
    // Разбиваем строку на ячейки
    $cells = explode('*', $row_data);
    
    // Если нет ячеек — возвращаем пустую строку
    if (count($cells) == 0 || (count($cells) == 1 && $cells[0] === '')) {
        return '';
    }
    
    // Формируем строку таблицы
    $html = '<tr>';
    
    // Выводим существующие ячейки
    for ($i = 0; $i < count($cells); $i++) {
        $cell_content = htmlspecialchars(trim($cells[$i]));
        $html .= '<td>' . $cell_content . '</td>';
    }
    
    // Добавляем пустые ячейки, если нужно достичь нужного количества колонок
    for ($i = count($cells); $i < $columns; $i++) {
        $html .= '<td>&nbsp;</td>';
    }
    
    $html .= '</tr>';
    return $html;
}


function outTable($structure, $columns, $table_num) {
    // Проверка на нулевое количество колонок
    if ($columns <= 0) {
        echo '<h2>Таблица №' . $table_num . '</h2>';
        echo '<p style="color: red;">Неправильное число колонок</p>';
        return;
    }
    
    // Разбиваем структуру на строки (разделитель #)
    $rows = explode('#', $structure);
    
    // Проверка: есть ли строки
    if (count($rows) == 0 || (count($rows) == 1 && $rows[0] === '')) {
        echo '<h2>Таблица №' . $table_num . '</h2>';
        echo '<p style="color: red;">В таблице нет строк</p>';
        return;
    }
    
    // Формируем HTML-код всех строк таблицы
    $rows_html = '';
    $has_rows_with_cells = false;
    
    foreach ($rows as $row) {
        $tr_html = getTR($row, $columns);
        if ($tr_html !== '') {
            $rows_html .= $tr_html;
            $has_rows_with_cells = true;
        }
    }
    
    // Проверка: есть ли строки с ячейками
    if (!$has_rows_with_cells) {
        echo '<h2>Таблица №' . $table_num . '</h2>';
        echo '<p style="color: red;">В таблице нет строк с ячейками</p>';
        return;
    }
    
    // Выводим таблицу
    echo '<h2>Таблица №' . $table_num . '</h2>';
    echo '<table class="lab4-table">' . "\n";
    echo $rows_html;
    echo '</table>' . "\n";
}

// 3. ВЫВОД ВСЕХ ТАБЛИЦ

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛР4 — Мельников Кирилл, группа 241-353</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">
        <img src="logo.png" alt="Логотип университета" onerror="this.style.display='none'">
    </div>
    <div class="header-text">
        Мельников Кирилл | Группа 241-353 | Лабораторная работа №4
    </div>
</header>

<main>
    <div class="container">
        <h1>Вывод таблиц из структурированных строк</h1>
        <p><strong>Количество колонок:</strong> <?php echo $columns_count; ?></p>
        
        <?php
        // Выводим все таблицы
        for ($i = 0; $i < count($structures); $i++) {
            outTable($structures[$i], $columns_count, $i + 1);
        }
        ?>
    </div>
</main>

<footer>
    Лабораторная работа №4 | Вывод таблиц | Всего таблиц: <?php echo count($structures); ?>
</footer>

</body>
</html>