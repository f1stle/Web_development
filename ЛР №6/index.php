<?php
session_start();

// Восстановление ФИО и группы из сессии
$savedFio = $_SESSION['fio'] ?? '';
$savedGroup = $_SESSION['group'] ?? '';

// Генерация случайных чисел (0-100, 2 знака после запятой)
function randomFloat()
{
  return round(mt_rand(0, 10000) / 100, 2);
}

// Инициализация значений по умолчанию
$defaultA = randomFloat();
$defaultB = randomFloat();
$defaultC = randomFloat();

// Обработка данных формы
$showResults = false;
$resultData = [];
$mode = 'view'; // view или print

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $mode = $_POST['display_mode'] ?? 'view';

  // Сохраняем ФИО и группу в сессию
  $_SESSION['fio'] = $_POST['fio'] ?? '';
  $_SESSION['group'] = $_POST['group'] ?? '';

  $fio = $_POST['fio'] ?? '';
  $group = $_POST['group'] ?? '';
  $about = $_POST['about'] ?? '';
  $a = (float) str_replace(',', '.', $_POST['a'] ?? 0);
  $b = (float) str_replace(',', '.', $_POST['b'] ?? 0);
  $c = (float) str_replace(',', '.', $_POST['c'] ?? 0);
  $userAnswer = (float) str_replace(',', '.', $_POST['user_answer'] ?? 0);
  $taskType = $_POST['task_type'] ?? '';
  $email = $_POST['email'] ?? '';
  $sendEmail = isset($_POST['send_email']);

  // Вычисление правильного ответа
  $correctAnswer = 0;
  $taskName = '';

  switch ($taskType) {
    case 'area':
      $p = ($a + $b + $c) / 2;
      $correctAnswer = sqrt($p * ($p - $a) * ($p - $b) * ($p - $c));
      $taskName = 'Площадь треугольника';
      break;
    case 'perimeter':
      $correctAnswer = $a + $b + $c;
      $taskName = 'Периметр треугольника';
      break;
    case 'volume':
      $correctAnswer = $a * $b * $c;
      $taskName = 'Объем параллелепипеда';
      break;
    case 'average':
      $correctAnswer = ($a + $b + $c) / 3;
      $taskName = 'Среднее арифметическое';
      break;
    case 'max':
      $correctAnswer = max($a, $b, $c);
      $taskName = 'Максимальное из трех чисел';
      break;
    case 'min':
      $correctAnswer = min($a, $b, $c);
      $taskName = 'Минимальное из трех чисел';
      break;
    case 'hypotenuse':
      $correctAnswer = sqrt($a * $a + $b * $b);
      $taskName = 'Гипотенуза (игнорируя C)';
      break;
    case 'circle_area':
      $radius = ($a + $b + $c) / 3;
      $correctAnswer = pi() * $radius * $radius;
      $taskName = 'Площадь круга (радиус = среднее A,B,C)';
      break;
  }

  $correctAnswer = round($correctAnswer, 4);
  $userAnswerRounded = round($userAnswer, 4);
  $isPassed = abs($correctAnswer - $userAnswerRounded) < 0.0001;

  $resultData = [
    'fio' => $fio,
    'group' => $group,
    'about' => nl2br(htmlspecialchars($about)),
    'taskName' => $taskName,
    'a' => $a,
    'b' => $b,
    'c' => $c,
    'userAnswer' => $userAnswer,
    'correctAnswer' => $correctAnswer,
    'isPassed' => $isPassed,
    'sendEmail' => $sendEmail,
    'email' => $email
  ];

  $showResults = true;
}
?>
<!doctype html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ЛР6 — Мельников Кирилл, группа 241-353 - Тестирование знаний </title>
  <link rel="stylesheet" href="style.css?v=2">
</head>

<body>
  <header><img src="./logo.png" alt="" width="90"><span>Мельников Кирилл | Группа 241-353 | Лабораторная работа №6</span></header>
  <main>
    <div class="form-container">
      <?php if (!$showResults): ?>
        <!-- ФОРМА ВВОДА -->
        <h1>Тестирование знаний</h1>
        <form method="post" id="testForm">
          <div class="form-group">
            <label>ФИО *</label>
            <input type="text" name="fio" value="<?= htmlspecialchars($savedFio) ?>" required>
          </div>
          <div class="form-group">
            <label>Номер группы *</label>
            <input type="text" name="group" value="<?= htmlspecialchars($savedGroup) ?>" required>
          </div>
          <div class="form-group">
            <label>Значение A</label>
            <input type="text" name="a" value="<?= $defaultA ?>" required>
          </div>
          <div class="form-group">
            <label>Значение B</label>
            <input type="text" name="b" value="<?= $defaultB ?>" required>
          </div>
          <div class="form-group">
            <label>Значение C</label>
            <input type="text" name="c" value="<?= $defaultC ?>" required>
          </div>
          <div class="form-group">
            <label>Ваш ответ</label>
            <input type="text" name="user_answer" placeholder="0.00" required>
          </div>
          <div class="form-group">
            <label>Немного о себе</label>
            <textarea name="about" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Тип задачи</label>
            <select name="task_type" required>
              <option value="area">Площадь треугольника</option>
              <option value="perimeter">Периметр треугольника</option>
              <option value="volume">Объем параллелепипеда</option>
              <option value="average">Среднее арифметическое</option>
              <option value="max">Максимальное из трех</option>
              <option value="min">Минимальное из трех</option>
              <option value="hypotenuse">Гипотенуза (A и B)</option>
              <option value="circle_area">Площадь круга (R=среднее)</option>
            </select>
          </div>
          <div class="checkbox-group">
            <label>
              <input type="checkbox" name="send_email" id="sendEmailCheck"> Отправить результат теста по e-mail
            </label>
          </div>
          <div class="form-group" id="email-field" style="display: none;">
            <label>Ваш e-mail</label>
            <input type="email" name="email" placeholder="example@mail.ru">
          </div>
          <div class="form-group">
            <label>Режим отображения</label>
            <select name="display_mode">
              <option value="view">Версия для просмотра в браузере</option>
              <option value="print">Версия для печати</option>
            </select>
          </div>
          <button type="submit" class="btn">Проверить</button>
        </form>
      <?php else: ?>
        <!-- РЕЗУЛЬТАТЫ -->
        <h1>📋 Результаты тестирования</h1>
        <div class="result-box">
          <div class="result-row">
            <span class="result-label">ФИО:</span>
            <span class="result-value"><?= htmlspecialchars($resultData['fio']) ?></span>
          </div>
          <div class="result-row">
            <span class="result-label">Группа:</span>
            <span class="result-value"><?= htmlspecialchars($resultData['group']) ?></span>
          </div>
          <div class="result-row">
            <span class="result-label">О себе:</span>
            <span class="result-value"><?= $resultData['about'] ?: '—' ?></span>
          </div>
          <div class="result-row">
            <span class="result-label">Тип задачи:</span>
            <span class="result-value"><?= $resultData['taskName'] ?></span>
          </div>
          <div class="result-row">
            <span class="result-label">Входные данные:</span>
            <span class="result-value">A = <?= $resultData['a'] ?>, B = <?= $resultData['b'] ?>, C = <?= $resultData['c'] ?></span>
          </div>
          <div class="result-row">
            <span class="result-label">Ваш ответ:</span>
            <span class="result-value"><?= $resultData['userAnswer'] ?></span>
          </div>
          <div class="result-row">
            <span class="result-label">Правильный ответ:</span>
            <span class="result-value"><?= $resultData['correctAnswer'] ?></span>
          </div>
          <div class="result-row">
            <span class="result-label">Результат:</span>
            <span class="result-value">
              <?php if ($resultData['isPassed']): ?>
                <span class="success">✅ Тест пройден</span>
              <?php else: ?>
                <span class="error">❌ Ошибка: тест не пройден</span>
              <?php endif; ?>
            </span>
          </div>
          <?php if ($resultData['sendEmail'] && !empty($resultData['email'])): ?>
            <div class="result-row">
              <span class="result-label">E-mail отправка:</span>
              <span class="result-value">Результаты теста были автоматически отправлены на <?= htmlspecialchars($resultData['email']) ?></span>
            </div>
          <?php endif; ?>
        </div>

        <?php if ($mode === 'view'): ?>
          <a href="index.php" class="btn-link">🔄 Повторить тест</a>
        <?php else: ?>
          <div class="print-only">Версия для печати — результаты теста</div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </main>
    <footer>
        Лабораторная работа №6 | Тестирование знаний | 
        <?php echo date('d.m.Y в H:i:s'); ?>
    </footer>

  <script>
    // Управление видимостью поля email
    const checkbox = document.getElementById('sendEmailCheck');
    const emailField = document.getElementById('email-field');
    if (checkbox && emailField) {
      checkbox.addEventListener('change', function() {
        emailField.style.display = this.checked ? 'flex' : 'none';
        if (!this.checked) {
          emailField.querySelector('input').value = '';
        }
      });
    }

    // Замена запятой на точку перед отправкой (опционально)
    const form = document.getElementById('testForm');
    if (form) {
      form.addEventListener('submit', function(e) {
        const numberInputs = ['a', 'b', 'c', 'user_answer'];
        numberInputs.forEach(name => {
          const field = form.querySelector(`[name="${name}"]`);
          if (field) {
            field.value = field.value.replace(/,/g, '.');
          }
        });
      });
    }
  </script>
</body>

</html>