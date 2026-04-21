<?php
date_default_timezone_set('Europe/Moscow');

$start_value = 5;      
$count = 15;            
$step = 2;              
$type = 'D';             

$min_limit = null;       
$max_limit = null;


// 2. ФУНКЦИЯ ДЛЯ ВАРИАНТА 4
// f(x) = (5 - x)/(1 - x/5), при x ≤ 10
// f(x) = x^2/4 + 7, при 10 < x < 20
// f(x) = 2*x - 21, при x ≥ 20


function calculate_f($x) {
    if ($x <= 10) {
        // (5 - x) / (1 - x/5)
        $denominator = 1 - $x/5;
        if ($denominator == 0) {
            return 'error';  // деление на ноль при x = 5
        }
        return (5 - $x) / $denominator;
    } 
    elseif ($x < 20) {
        // x^2/4 + 7
        return ($x * $x) / 4 + 7;
    } 
    else {
        // 2*x - 21
        return 2 * $x - 21;
    }
}


$results = [];       
$sum = 0;           
$valid_count = 0;    
$min_val = null;     
$max_val = null;    

$x = $start_value;

for ($i = 0; $i < $count; $i++) {
    $fx = calculate_f($x);
    
    if (is_numeric($fx)) {
        $fx = round($fx, 3);
    }
    
    // Проверка на остановку по min/max
    if (is_numeric($fx)) {
        if (($min_limit !== null && $fx < $min_limit) || 
            ($max_limit !== null && $fx > $max_limit)) {
            break; 
        }
    }
    
    $results[] = ['x' => $x, 'fx' => $fx];
    
    if (is_numeric($fx)) {
        $sum += $fx;
        $valid_count++;
        
        if ($min_val === null || $fx < $min_val) {
            $min_val = $fx;
        }
        if ($max_val === null || $fx > $max_val) {
            $max_val = $fx;
        }
    }
    
    $x += $step;
}

$average = ($valid_count > 0) ? $sum / $valid_count : null;
if ($average !== null) {
    $average = round($average, 3);
}




$output_html = ''; 

switch ($type) {
    case 'A':
        // Простая верстка текстом, разделитель <br>
        foreach ($results as $row) {
            $output_html .= "f({$row['x']}) = {$row['fx']}<br>\n";
        }
        break;
        
    case 'B':
        // Маркированный список
        $output_html .= "<ul>\n";
        foreach ($results as $row) {
            $output_html .= "<li>f({$row['x']}) = {$row['fx']}</li>\n";
        }
        $output_html .= "</ul>\n";
        break;
        
    case 'C':
        // Нумерованный список
        $output_html .= "<ol>\n";
        foreach ($results as $row) {
            $output_html .= "<li>f({$row['x']}) = {$row['fx']}</li>\n";
        }
        $output_html .= "</ol>\n";
        break;
        
    case 'D':
        // Табличная верстка
        $output_html .= "<table>\n";
        $output_html .= "<tr><th>№</th><th>Аргумент (x)</th><th>Значение f(x)</th></tr>\n";
        $i = 1;
        foreach ($results as $row) {
            $output_html .= "<tr>";
            $output_html .= "<td>{$i}</td>";
            $output_html .= "<td>{$row['x']}</td>";
            $output_html .= "<td>{$row['fx']}</td>";
            $output_html .= "</tr>\n";
            $i++;
        }
        $output_html .= "</table>\n";
        break;
        
    case 'E':
        // Блочная верстка
        $output_html .= "<div class='block-container'>\n";
        foreach ($results as $row) {
            $output_html .= "<div class='block'>f({$row['x']}) = {$row['fx']}</div>\n";
        }
        $output_html .= "</div>\n";
        break;
        
    default:
        $output_html .= "<p>Неизвестный тип верстки: {$type}</p>\n";
        break;
}


$stats_html = '';
if ($valid_count > 0) {
    $stats_html .= "<div class='statistics'>\n";
    $stats_html .= "<h2>Статистика значений функции</h2>\n";
    $stats_html .= "<p><strong>Максимальное значение:</strong> " . round($max_val, 3) . "</p>\n";
    $stats_html .= "<p><strong>Минимальное значение:</strong> " . round($min_val, 3) . "</p>\n";
    $stats_html .= "<p><strong>Сумма значений:</strong> " . round($sum, 3) . "</p>\n";
    $stats_html .= "<p><strong>Среднее арифметическое:</strong> " . $average . "</p>\n";
    $stats_html .= "<p><strong>Количество корректных значений:</strong> {$valid_count}</p>\n";
    $stats_html .= "</div>\n";
} else {
    $stats_html .= "<div class='statistics'>\n";
    $stats_html .= "<p>Нет корректных числовых значений для подсчёта статистики.</p>\n";
    $stats_html .= "</div>\n";
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛР2 — Мельников Кирилл, группа 241-353, вариант 4</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">
        <img src="logo.png" alt="Логотип университета" onerror="this.style.display='none'">
    </div>
    <div class="header-text">
        Мельников Кирилл | Группа 241-353 | Лабораторная работа №2 | Вариант 4
    </div>
</header>

<main>
    <h1>Табулирование функции</h1>
    
    <p><strong>Исходные данные:</strong> 
        x<sub>нач</sub> = <?php echo $start_value; ?>, 
        шаг = <?php echo $step; ?>, 
        количество = <?php echo $count; ?>,
        тип верстки = '<?php echo $type; ?>'
    </p>
    
    <h2>Результаты вычислений</h2>
    
    <?php echo $output_html; ?>
    
    <?php echo $stats_html; ?>
    
</main>

<footer>
    Тип верстки: <?php echo $type; ?> | Сформировано <?php echo date('d.m.Y в H:i:s'); ?>
</footer>

</body>
</html>