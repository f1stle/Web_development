<?php

require_once 'config.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $lastname = trim($_POST['lastname'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $middlename = trim($_POST['middlename'] ?? '');
    $gender = $_POST['gender'] ?? 'male';
    $birth_date = $_POST['birth_date'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    
    if (empty($lastname) || empty($firstname)) {
        $message = 'Ошибка: Фамилия и имя обязательны для заполнения';
        $messageType = 'error';
    } else {
        $conn = getDBConnection();
        
        $query = "INSERT INTO contacts (lastname, firstname, middlename, gender, birth_date, phone, address, email, comment) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'sssssssss', 
            $lastname, $firstname, $middlename, $gender, $birth_date, 
            $phone, $address, $email, $comment
        );
        
        if (mysqli_stmt_execute($stmt)) {
            $message = '✅ Запись успешно добавлена!';
            $messageType = 'success';
        } else {
            $message = '❌ Ошибка: запись не добавлена. ' . mysqli_error($conn);
            $messageType = 'error';
        }
        
        mysqli_stmt_close($stmt);
        closeDBConnection($conn);
    }
}

// Форма добавления
$formHtml = '<div class="form-container">';
$formHtml .= '<h2>➕ Добавление новой записи</h2>';

if ($message) {
    $formHtml .= '<div class="message ' . $messageType . '">' . htmlspecialchars($message) . '</div>';
}

$formHtml .= '<form method="post" class="contact-form">';
$formHtml .= '<input type="hidden" name="action" value="add">';

$formHtml .= '<div class="form-row">';
$formHtml .= '<div class="form-group"><label>Фамилия *</label><input type="text" name="lastname" required></div>';
$formHtml .= '<div class="form-group"><label>Имя *</label><input type="text" name="firstname" required></div>';
$formHtml .= '</div>';

$formHtml .= '<div class="form-row">';
$formHtml .= '<div class="form-group"><label>Отчество</label><input type="text" name="middlename"></div>';
$formHtml .= '<div class="form-group"><label>Пол</label>';
$formHtml .= '<select name="gender"><option value="male">Мужской</option><option value="female">Женский</option></select>';
$formHtml .= '</div></div>';

$formHtml .= '<div class="form-row">';
$formHtml .= '<div class="form-group"><label>Дата рождения</label><input type="date" name="birth_date"></div>';
$formHtml .= '<div class="form-group"><label>Телефон</label><input type="text" name="phone" placeholder="+7-xxx-xxx-xx-xx"></div>';
$formHtml .= '</div>';

$formHtml .= '<div class="form-group"><label>Адрес</label><textarea name="address" rows="2"></textarea></div>';

$formHtml .= '<div class="form-row">';
$formHtml .= '<div class="form-group"><label>E-mail</label><input type="email" name="email"></div>';
$formHtml .= '<div class="form-group"><label>Комментарий</label><input type="text" name="comment"></div>';
$formHtml .= '</div>';

$formHtml .= '<div class="form-buttons">';
$formHtml .= '<button type="submit" class="btn btn-primary">💾 Добавить запись</button>';
$formHtml .= '</div>';
$formHtml .= '</form>';
$formHtml .= '</div>';

echo $formHtml;
?>