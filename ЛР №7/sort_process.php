<?php
// Лабораторная работа №7 - Обработчик сортировки

// Функция проверки, является ли строка числом
function isNumericValue($str) {
    $str = trim($str);
    if ($str === '') return false;
    return is_numeric(str_replace(',', '.', $str));
}

// Функция преобразования строки в число
function toNumber($str) {
    return (float)str_replace(',', '.', trim($str));
}

// Функция вывода состояния массива
function printArrayState($arr, $iteration, &$output) {
    $output .= '<div class="iteration">';
    $output .= '<span class="iteration-num">Итерация ' . $iteration . ':</span>';
    $output .= '<div class="array-state">';
    foreach ($arr as $index => $value) {
        $output .= '<div class="array-element">' . $index . ': ' . $value . '</div>';
    }
    $output .= '</div></div>';
}

// ==================== АЛГОРИТМЫ СОРТИРОВКИ ====================

// 1. Сортировка выбором
function choiceSort(&$arr, &$output) {
    $iterations = 0;
    $n = count($arr);
    
    for ($i = 0; $i < $n - 1; $i++) {
        $min = $i;
        for ($j = $i + 1; $j < $n; $j++) {
            $iterations++;
            if ($arr[$j] < $arr[$min]) {
                $min = $j;
            }
        }
        if ($min != $i) {
            $temp = $arr[$i];
            $arr[$i] = $arr[$min];
            $arr[$min] = $temp;
        }
        printArrayState($arr, $iterations, $output);
    }
    return $iterations;
}

// 2. Пузырьковая сортировка
function bubbleSort(&$arr, &$output) {
    $iterations = 0;
    $n = count($arr);
    
    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - $i - 1; $j++) {
            $iterations++;
            if ($arr[$j] > $arr[$j + 1]) {
                $temp = $arr[$j];
                $arr[$j] = $arr[$j + 1];
                $arr[$j + 1] = $temp;
            }
        }
        printArrayState($arr, $iterations, $output);
    }
    return $iterations;
}

// 3. Сортировка садового гнома
function gnomeSort(&$arr, &$output) {
    $iterations = 0;
    $i = 1;
    $n = count($arr);
    
    while ($i < $n) {
        $iterations++;
        if ($i == 0 || $arr[$i - 1] <= $arr[$i]) {
            $i++;
        } else {
            $temp = $arr[$i];
            $arr[$i] = $arr[$i - 1];
            $arr[$i - 1] = $temp;
            $i--;
        }
        printArrayState($arr, $iterations, $output);
    }
    return $iterations;
}

// 4. Сортировка Шелла
function shellSort(&$arr, &$output) {
    $iterations = 0;
    $n = count($arr);
    $gap = floor($n / 2);
    
    while ($gap >= 1) {
        for ($i = $gap; $i < $n; $i++) {
            $temp = $arr[$i];
            $j = $i;
            while ($j >= $gap && $arr[$j - $gap] > $temp) {
                $iterations++;
                $arr[$j] = $arr[$j - $gap];
                $j -= $gap;
            }
            $arr[$j] = $temp;
            $iterations++;
            printArrayState($arr, $iterations, $output);
        }
        $gap = floor($gap / 2);
    }
    return $iterations;
}

// 5. Быстрая сортировка
function quickSortRecursive(&$arr, $left, $right, &$iterations, &$output) {
    if ($left >= $right) return;
    
    $pivot = $arr[floor(($left + $right) / 2)];
    $i = $left;
    $j = $right;
    
    while ($i <= $j) {
        while ($arr[$i] < $pivot) $i++;
        while ($arr[$j] > $pivot) $j--;
        
        if ($i <= $j) {
            $temp = $arr[$i];
            $arr[$i] = $arr[$j];
            $arr[$j] = $temp;
            $i++;
            $j--;
            $iterations++;
            printArrayState($arr, $iterations, $output);
        }
    }
    
    quickSortRecursive($arr, $left, $j, $iterations, $output);
    quickSortRecursive($arr, $i, $right, $iterations, $output);
}

function quickSort(&$arr, &$output) {
    $iterations = 0;
    quickSortRecursive($arr, 0, count($arr) - 1, $iterations, $output);
    return $iterations;
}

// 7. Встроенная сортировка PHP (не считает итерации)
function builtinSort(&$arr, &$output) {
    sort($arr);
    $output .= '<div class="iteration">';
    $output .= '<span class="iteration-num">Встроенная сортировка sort():</span>';
    $output .= '<div class="array-state">';
    foreach ($arr as $index => $value) {
        $output .= '<div class="array-element">' . $index . ': ' . $value . '</div>';
    }
    $output .= '</div></div>';
    return "N/A (внутренние итерации не отображаются)";
}

// ==================== ОСНОВНАЯ ОБРАБОТКА ====================

$output = '';
$validation_error = false;

// Проверка наличия данных
if (!isset($_POST['element0']) || !isset($_POST['arrLength'])) {
    die('<div class="error-message">❌ Массив не задан, сортировка невозможна</div>');
}

$length = (int)$_POST['arrLength'];
$algorithm = $_POST['algorithm'] ?? 'bubble';

// Сбор и валидация элементов
$rawArray = [];
for ($i = 0; $i < $length; $i++) {
    $fieldName = 'element' . $i;
    if (!isset($_POST[$fieldName]) || $_POST[$fieldName] === '') {
        $validation_error = true;
        $output = '<div class="error-message">❌ Элемент с индексом ' . $i . ' пуст. Сортировка невозможна.</div>';
        break;
    }
    if (!isNumericValue($_POST[$fieldName])) {
        $validation_error = true;
        $output = '<div class="error-message">❌ Элемент "' . htmlspecialchars($_POST[$fieldName]) . '" не является числом. Сортировка невозможна.</div>';
        break;
    }
    $rawArray[] = toNumber($_POST[$fieldName]);
}

// Названия алгоритмов
$algorithmNames = [
    'choice' => 'Сортировка выбором (Selection Sort)',
    'bubble' => 'Пузырьковая сортировка (Bubble Sort)',
    'gnome' => 'Сортировка садового гнома (Gnome Sort)',
    'shell' => 'Сортировка Шелла (Shell Sort)',
    'quick' => 'Быстрая сортировка (Quick Sort)',
    'builtin' => 'Встроенная функция PHP sort()'
];

$algorithmName = $algorithmNames[$algorithm] ?? 'Неизвестный алгоритм';

// Вывод начальной информации
$output .= '<div class="algorithm-info">';
$output .= '<h2>📌 ' . htmlspecialchars($algorithmName) . '</h2>';
$output .= '<div class="input-data">';
$output .= '<strong>Входные данные:</strong><br>';
foreach ($rawArray as $index => $value) {
    $output .= '<span class="input-element">' . $index . ': ' . $value . '</span> ';
}
$output .= '</div>';

if (!$validation_error) {
    $output .= '<div class="validation-success">✅ Все элементы массива валидны. Начинаем сортировку...</div>';
    $output .= '<div class="sorting-process">';
    $output .= '<h3>🔄 Процесс сортировки:</h3>';
    
    $sortedArray = $rawArray;
    $startTime = microtime(true);
    
    // Запуск выбранного алгоритма
    switch ($algorithm) {
        case 'choice':
            $iterations = choiceSort($sortedArray, $output);
            break;
        case 'bubble':
            $iterations = bubbleSort($sortedArray, $output);
            break;
        case 'gnome':
            $iterations = gnomeSort($sortedArray, $output);
            break;
        case 'shell':
            $iterations = shellSort($sortedArray, $output);
            break;
        case 'quick':
            $iterations = quickSort($sortedArray, $output);
            break;
        case 'builtin':
            $iterations = builtinSort($sortedArray, $output);
            break;
        default:
            $iterations = bubbleSort($sortedArray, $output);
    }
    
    $endTime = microtime(true);
    $duration = round(($endTime - $startTime) * 1000000, 2); // в микросекундах
    
    $output .= '</div>'; // закрываем sorting-process
    $output .= '<div class="completion-message">';
    $output .= '✅ Сортировка завершена!<br>';
    $output .= '📊 Проведено итераций: <strong>' . $iterations . '</strong><br>';
    $output .= '⏱️ Время выполнения: <strong>' . $duration . ' мкс</strong> (' . round($duration / 1000, 4) . ' мс)';
    $output .= '</div>';
    
    $output .= '<div class="final-result">';
    $output .= '<h3>📋 Отсортированный массив:</h3>';
    $output .= '<div class="array-state final">';
    foreach ($sortedArray as $index => $value) {
        $output .= '<div class="array-element">' . $index . ': ' . $value . '</div>';
    }
    $output .= '</div></div>';
} else {
    $output .= '<div class="validation-failed">❌ Валидация не пройдена. Сортировка не выполнена.</div>';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результат сортировки — Мельников Кирилл, группа 241-353</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <img src="./logo.png" alt="Логотип" width="60">
        <span>Мельников Кирилл | Группа 241-353 | Результат сортировки</span>
    </header>

    <main>
        <div class="result-container">
            <?php echo $output; ?>
            <div class="back-link">
                <a href="index.php" class="btn btn-back">🔙 Назад к форме</a>
            </div>
        </div>
    </main>

    <footer>
        Лабораторная работа №7 | Сортировка массивов | <?php echo date('d.m.Y H:i:s'); ?>
    </footer>
</body>
</html>