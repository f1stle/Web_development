<?php
// Получаем текст из формы
$text = isset($_POST['text']) ? $_POST['text'] : '';
$text = trim($text);

// Функция для перекодировки из UTF-8 в CP1251 для анализа (чтобы правильно работали strlen и т.д.)
// и обратно для вывода
function toCp1251($str) {
    return iconv("UTF-8", "Windows-1251//IGNORE", $str);
}

function toUtf8($str) {
    return iconv("Windows-1251", "UTF-8//IGNORE", $str);
}

// Функция для проверки, является ли символ буквой (русской или английской)
function isLetter($char) {
    // Русские буквы в CP1251: А-Я = 192-223, а-п = 224-239, р-я = 240-255
    // Английские буквы: A-Z = 65-90, a-z = 97-122
    $code = ord($char);
    
    // Русские буквы
    if ($code >= 192 && $code <= 255) return true;
    // Английские буквы
    if (($code >= 65 && $code <= 90) || ($code >= 97 && $code <= 122)) return true;
    
    return false;
}

// Функция для проверки, является ли символ заглавной буквой
function isUpperCase($char) {
    $code = ord($char);
    
    // Русские заглавные (А-Я)
    if ($code >= 192 && $code <= 223) return true;
    // Английские заглавные (A-Z)
    if ($code >= 65 && $code <= 90) return true;
    
    return false;
}

// Функция для проверки, является ли символ строчной буквой
function isLowerCase($char) {
    $code = ord($char);
    
    // Русские строчные (а-я)
    if ($code >= 224 && $code <= 255) return true;
    // Английские строчные (a-z)
    if ($code >= 97 && $code <= 122) return true;
    
    return false;
}

// Функция для проверки, является ли символ знаком препинания
function isPunctuation($char) {
    $punctuation = array('.', ',', '!', '?', ';', ':', '-', '—', '(', ')', '[', ']', '{', '}', '"', "'", '«', '»', '…', '/', '\\', '|', '@', '#', '$', '%', '^', '&', '*', '+', '=', '<', '>');
    return in_array($char, $punctuation);
}

// Функция для проверки, является ли символ цифрой
function isDigit($char) {
    return ($char >= '0' && $char <= '9');
}

// Функция для подсчёта вхождений символов (без учёта регистра)
function countCharacters($text_cp) {
    $chars = array();
    $len = strlen($text_cp);
    
    for ($i = 0; $i < $len; $i++) {
        $char = $text_cp[$i];
        
        // Пропускаем пробелы и знаки переноса строки
        if ($char == ' ' || $char == "\n" || $char == "\r" || $char == "\t") {
            continue;
        }
        
        // Приводим к нижнему регистру для подсчёта без учёта регистра
        if (isLetter($char)) {
            // Для русских букв в CP1251: заглавные (192-223) -> строчные (224-255)
            $code = ord($char);
            if ($code >= 192 && $code <= 223) {
                $char = chr($code + 32);
            }
            // Для английских букв
            elseif ($code >= 65 && $code <= 90) {
                $char = chr($code + 32);
            }
        }
        
        if (isset($chars[$char])) {
            $chars[$char]++;
        } else {
            $chars[$char] = 1;
        }
    }
    
    // Сортируем по ключам (символам)
    ksort($chars);
    
    return $chars;
}

// Функция для подсчёта слов
function countWords($text_cp) {
    $words = array();
    $len = strlen($text_cp);
    $current_word = '';
    
    for ($i = 0; $i <= $len; $i++) {
        $char = ($i < $len) ? $text_cp[$i] : ' ';
        
        // Разделители слов: пробел, знаки препинания, конец строки
        if ($char == ' ' || $char == "\n" || $char == "\r" || $char == "\t" || isPunctuation($char) || $i == $len) {
            if ($current_word !== '') {
                // Приводим слово к нижнему регистру
                $word_lower = '';
                $word_len = strlen($current_word);
                for ($j = 0; $j < $word_len; $j++) {
                    $ch = $current_word[$j];
                    $code = ord($ch);
                    if ($code >= 192 && $code <= 223) {
                        $word_lower .= chr($code + 32);
                    } elseif ($code >= 65 && $code <= 90) {
                        $word_lower .= chr($code + 32);
                    } else {
                        $word_lower .= $ch;
                    }
                }
                
                if (isset($words[$word_lower])) {
                    $words[$word_lower]++;
                } else {
                    $words[$word_lower] = 1;
                }
                $current_word = '';
            }
        } else {
            $current_word .= $char;
        }
    }
    
    // Сортируем по ключам (словам) в алфавитном порядке
    ksort($words);
    
    return $words;
}

// Анализ текста
function analyzeText($text_utf8) {
    $result = array();
    
    // Перекодируем в CP1251 для анализа
    $text_cp = toCp1251($text_utf8);
    
    if (empty($text_cp)) {
        return null;
    }
    
    $len = strlen($text_cp);
    
    // 1. Количество символов (включая пробелы)
    $result['total_chars'] = $len;
    
    // 2. Количество букв, строчных и заглавных
    $result['total_letters'] = 0;
    $result['total_uppercase'] = 0;
    $result['total_lowercase'] = 0;
    $result['total_punctuation'] = 0;
    $result['total_digits'] = 0;
    
    for ($i = 0; $i < $len; $i++) {
        $char = $text_cp[$i];
        
        if (isLetter($char)) {
            $result['total_letters']++;
            if (isUpperCase($char)) {
                $result['total_uppercase']++;
            } elseif (isLowerCase($char)) {
                $result['total_lowercase']++;
            }
        } elseif (isPunctuation($char)) {
            $result['total_punctuation']++;
        } elseif (isDigit($char)) {
            $result['total_digits']++;
        }
    }
    
    // 3. Количество слов
    $words = countWords($text_cp);
    $result['total_words'] = array_sum($words);
    $result['words_list'] = $words;
    
    // 4. Количество вхождений символов
    $result['char_counts'] = countCharacters($text_cp);
    
    return $result;
}

// Получаем результаты анализа
$analysis = analyzeText($text);
$has_text = !empty($text) && $analysis !== null;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>ЛР8 — Результат анализа текста</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h2>Лабораторная работа №8 — Результат анализа</h2>
    <p>Мельников Кирилл | Группа 241-353</p>
</header>

<main>
    <div class="result-container">
        <h1>Результаты анализа текста</h1>
        
        <?php if (!$has_text): ?>
            <div class="error-message">
                ❌ Нет текста для анализа
            </div>
        <?php else: ?>
            <!-- Исходный текст -->
            <div class="source-text">
                <h3>Исходный текст:</h3>
                <div class="original-text">
                    <?php echo nl2br(htmlspecialchars($text)); ?>
                </div>
            </div>
            
            <!-- Информация о тексте в виде таблицы -->
            <div class="info-table">
                <h3>Информация о тексте:</h3>
                <table class="analysis-table" border="1">
                    <tr>
                        <th>Параметр</th>
                        <th>Значение</th>
                    </tr>
                    <tr>
                        <td>Количество символов (включая пробелы)</td>
                        <td><?php echo $analysis['total_chars']; ?></td>
                    </tr>
                    <tr>
                        <td>Количество букв</td>
                        <td><?php echo $analysis['total_letters']; ?></td>
                    </tr>
                    <tr>
                        <td>Количество заглавных букв</td>
                        <td><?php echo $analysis['total_uppercase']; ?></td>
                    </tr>
                    <tr>
                        <td>Количество строчных букв</td>
                        <td><?php echo $analysis['total_lowercase']; ?></td>
                    </tr>
                    <tr>
                        <td>Количество знаков препинания</td>
                        <td><?php echo $analysis['total_punctuation']; ?></td>
                    </tr>
                    <tr>
                        <td>Количество цифр</td>
                        <td><?php echo $analysis['total_digits']; ?></td>
                    </tr>
                    <tr>
                        <td>Количество слов</td>
                        <td><?php echo $analysis['total_words']; ?></td>
                    </tr>
                </table>
            </div>
            
            <!-- Количество вхождений символов -->
            <?php if (!empty($analysis['char_counts'])): ?>
                <div class="char-counts">
                    <h3>Количество вхождений каждого символа (без учёта регистра):</h3>
                    <table class="analysis-table" border="1">
                        <tr>
                            <th>Символ</th>
                            <th>Количество</th>
                        </tr>
                        <?php foreach ($analysis['char_counts'] as $char => $count): 
                            $display_char = ($char == ' ') ? '[пробел]' : (($char == "\n") ? '[перенос строки]' : toUtf8($char));
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($display_char); ?></td>
                                <td><?php echo $count; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif; ?>
            
            <!-- Список слов с количеством вхождений -->
            <?php if (!empty($analysis['words_list'])): ?>
                <div class="word-counts">
                    <h3>Список слов и количество их вхождений (отсортировано по алфавиту):</h3>
                    <table class="analysis-table" border="1">
                        <tr>
                            <th>Слово</th>
                            <th>Количество</th>
                        </tr>
                        <?php foreach ($analysis['words_list'] as $word => $count): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(toUtf8($word)); ?></td>
                                <td><?php echo $count; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <!-- Кнопка "Другой анализ" -->
        <div class="another-analysis">
            <a href="index.html" class="another-btn">🔄 Другой анализ</a>
        </div>
    </div>
</main>

<footer>
    Лабораторная работа №8 | Анализ текста
</footer>

</body>
</html>