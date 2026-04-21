<?php
// Кнопочный ввод цифр с передачей состояния через GET-параметры

// 1. Инициализация хранилища (результата)
if (!isset($_GET['store'])) {
    $_GET['store'] = '';
}

// 2. Обработка нажатия кнопки
if (isset($_GET['key'])) {
    $key = $_GET['key'];
    
    if ($key === 'reset') {
        $_GET['store'] = '';
    } else {
        $_GET['store'] .= $key;
    }
}

// 3. Подсчёт общего числа нажатий
if (!isset($_GET['click_count'])) {
    $_GET['click_count'] = 0;
}

if (isset($_GET['key'])) {
    $_GET['click_count']++;
}

// 4. Текущее значение для отображения
$display_value = $_GET['store'];
$is_empty = ($display_value === '');

// 5. Параметры для ссылок
$store_param = urlencode($_GET['store']);
$click_count = $_GET['click_count'];

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛР3 — Мельников Кирилл, группа 241-353</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">
        <img src="logo.png" alt="Логотип университета" onerror="this.style.display='none'">
    </div>
    <div class="header-text">
        Мельников Кирилл | Группа 241-353 | Лабораторная работа №3
    </div>
</header>

<main>
    <div class="calculator">
        <div class="result">
            <?php 
            if ($is_empty) {
                echo '&nbsp;'; 
            } else {
                echo htmlspecialchars($display_value);
            }
            ?>
        </div>

        <div class="buttons-row">
            <?php for ($digit = 1; $digit <= 5; $digit++): ?>
                <a href="?key=<?php echo $digit; ?>&store=<?php echo $store_param; ?>&click_count=<?php echo $click_count; ?>" class="btn"><?php echo $digit; ?></a>
            <?php endfor; ?>
        </div>

        <div class="buttons-row">
            <?php for ($digit = 6; $digit <= 9; $digit++): ?>
                <a href="?key=<?php echo $digit; ?>&store=<?php echo $store_param; ?>&click_count=<?php echo $click_count; ?>" class="btn"><?php echo $digit; ?></a>
            <?php endfor; ?>
            <a href="?key=0&store=<?php echo $store_param; ?>&click_count=<?php echo $click_count; ?>" class="btn">0</a>
        </div>

        <div class="reset-container">
            <a href="?key=reset&store=<?php echo $store_param; ?>&click_count=<?php echo $click_count; ?>" class="btn btn-reset">СБРОС</a>
        </div>
    </div>
</main>

<footer>
    Общее число нажатий кнопок: <?php echo $click_count; ?>
</footer>

</body>
</html>