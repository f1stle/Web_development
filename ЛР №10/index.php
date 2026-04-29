<?php
session_start();

if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = array();
    $_SESSION['iteration'] = 0;
}
$_SESSION['iteration']++;

function isnum($x) {
    if ($x === '' || $x === null) return false;
    $x = (string)$x;
    if ($x[0] == '.' || $x[0] == '0') return false;
    if ($x[strlen($x) - 1] == '.') return false;
    
    $point_count = false;
    for ($i = 0; $i < strlen($x); $i++) {
        if ($x[$i] != '0' && $x[$i] != '1' && $x[$i] != '2' && $x[$i] != '3' &&
            $x[$i] != '4' && $x[$i] != '5' && $x[$i] != '6' && $x[$i] != '7' &&
            $x[$i] != '8' && $x[$i] != '9' && $x[$i] != '.') {
            return false;
        }
        if ($x[$i] == '.') {
            if ($point_count) return false;
            $point_count = true;
        }
    }
    return true;
}

function calculate($val) {
    if ($val === '' || $val === null) return 'Выражение не задано!';
    
    $val = str_replace(' ', '', $val);
    
    if (isnum($val)) return (float)$val;
    
    // Сложение
    $args = explode('+', $val);
    if (count($args) > 1) {
        $sum = 0;
        for ($i = 0; $i < count($args); $i++) {
            $arg = calculate($args[$i]);
            if (!isnum($arg)) return $arg;
            $sum += (float)$arg;
        }
        return $sum;
    }
    
    // Вычитание
    $args = explode('-', $val);
    if (count($args) > 1) {
        $result = calculate($args[0]);
        if (!isnum($result)) return $result;
        for ($i = 1; $i < count($args); $i++) {
            $arg = calculate($args[$i]);
            if (!isnum($arg)) return $arg;
            $result -= (float)$arg;
        }
        return $result;
    }
    
    // Умножение
    $args = explode('*', $val);
    if (count($args) > 1) {
        $product = 1;
        for ($i = 0; $i < count($args); $i++) {
            $arg = $args[$i];
            if (!isnum($arg)) return 'Неправильная форма числа!';
            $product *= (float)$arg;
        }
        return $product;
    }
    
    // Деление
    $args = preg_split('/[\/:]/', $val);
    if (count($args) > 1) {
        $result = calculate($args[0]);
        if (!isnum($result)) return $result;
        for ($i = 1; $i < count($args); $i++) {
            $arg = calculate($args[$i]);
            if (!isnum($arg)) return $arg;
            if ((float)$arg == 0) return 'Деление на ноль!';
            $result /= (float)$arg;
        }
        return $result;
    }
    
    return 'Недопустимые символы в выражении';
}

function SqValidator($val) {
    $open = 0;
    for ($i = 0; $i < strlen($val); $i++) {
        if ($val[$i] == '(') {
            $open++;
        } elseif ($val[$i] == ')') {
            $open--;
            if ($open < 0) return false;
        }
    }
    return $open == 0;
}

function calculateSq($val) {
    $val = str_replace(' ', '', $val);
    
    if (!SqValidator($val)) {
        return 'Неправильная расстановка скобок';
    }
    
    $start = strpos($val, '(');
    if ($start === false) {
        return calculate($val);
    }
    
    $end = $start + 1;
    $open = 1;
    while ($open > 0 && $end < strlen($val)) {
        if ($val[$end] == '(') $open++;
        if ($val[$end] == ')') $open--;
        $end++;
    }
    
    $left = substr($val, 0, $start);
    $inner = substr($val, $start + 1, $end - $start - 2);
    $right = substr($val, $end);
    
    $inner_result = calculateSq($inner);
    if (!isnum($inner_result)) {
        return $inner_result;
    }
    
    $new_val = $left . $inner_result . $right;
    return calculateSq($new_val);
}

$res = null;
$is_submitted = false;
$expression = '';

if (isset($_POST['val']) && isset($_POST['iteration'])) {
    $submitted_iteration = (int)$_POST['iteration'];
    $current_iteration = $_SESSION['iteration'];
    
    if ($submitted_iteration + 1 == $current_iteration) {
        $is_submitted = true;
        $expression = trim($_POST['val']);
        $res = calculateSq($expression);
        
        $_SESSION['history'][] = $expression . ' = ' . $res;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Арифметический калькулятор — Мельников Кирилл, группа 241-353, ЛР10</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <img src="./logo.png" alt="Логотип" width="60">
        <span>Мельников Кирилл | Группа 241-353 | Лабораторная работа №10</span>
    </header>

    <div class="container">
        <main>
            <h1>Арифметический калькулятор</h1>
            <p>Поддерживаются операции: +, -, *, /, : и скобки ()</p>
            
            <?php if ($is_submitted && $res !== null): ?>
                <div class="result <?php echo isnum($res) ? 'success' : 'error'; ?>">
                    <strong>Результат:</strong> 
                    <span class="result-expression"><?php echo htmlspecialchars($expression); ?></span> = 
                    <span class="result-value"><?php echo htmlspecialchars($res); ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="calculator-form">
                <div class="form-group">
                    <label for="val">Выражение:</label>
                    <input type="text" name="val" id="val" 
                           value="<?php echo isset($_POST['val']) ? htmlspecialchars($_POST['val']) : ''; ?>"
                           placeholder="Например: 2+3*4 или (2+3)*4" 
                           autocomplete="off"
                           required>
                </div>
                <input type="hidden" name="iteration" value="<?php echo $_SESSION['iteration']; ?>">
                <button type="submit" class="btn-calc">Вычислить</button>
            </form>
        </main>
        
        <footer>
            <h3>История вычислений</h3>
            <div class="history">
                <?php if (count($_SESSION['history']) > 0): ?>
                    <?php foreach ($_SESSION['history'] as $item): ?>
                        <div class="history-item"><?php echo htmlspecialchars($item); ?></div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="history-empty">История пуста. Выполните вычисления.</div>
                <?php endif; ?>
            </div>
        </footer>
    </div>

    <footer class="main-footer">
        Арифметический калькулятор | © <?php echo date('Y'); ?>
    </footer>
</body>
</html>