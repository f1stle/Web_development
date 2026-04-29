<?php

require_once 'config.php';

$message = '';
$messageType = '';
$selectedId = null;
$currentRecord = null;

$conn = getDBConnection();

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int)$_POST['id'];
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
        $query = "UPDATE contacts SET 
                  lastname=?, firstname=?, middlename=?, gender=?, 
                  birth_date=?, phone=?, address=?, email=?, comment=? 
                  WHERE id=?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'sssssssssi', 
            $lastname, $firstname, $middlename, $gender, $birth_date, 
            $phone, $address, $email, $comment, $id
        );
        
        if (mysqli_stmt_execute($stmt)) {
            $message = '✅ Запись успешно изменена!';
            $messageType = 'success';
            $selectedId = $id;
        } else {
            $message = '❌ Ошибка: запись не изменена. ' . mysqli_error($conn);
            $messageType = 'error';
        }
        mysqli_stmt_close($stmt);
    }
}

// Определение выбранной записи (GET-параметр id или POST)
if (isset($_GET['id'])) {
    $selectedId = (int)$_GET['id'];
} elseif (isset($_POST['id']) && $selectedId === null) {
    $selectedId = (int)$_POST['id'];
}

// Получение всех записей для списка
$allRecords = [];
$query = "SELECT id, lastname, firstname FROM contacts ORDER BY lastname, firstname";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $allRecords[] = $row;
}

// Получение текущей записи (если выбран id)
if ($selectedId) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM contacts WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $selectedId);
    mysqli_stmt_execute($stmt);
    $currentRecord = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

// Если нет выбранной записи, но есть записи в БД — берём первую
if (!$currentRecord && count($allRecords) > 0) {
    $selectedId = $allRecords[0]['id'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM contacts WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $selectedId);
    mysqli_stmt_execute($stmt);
    $currentRecord = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

closeDBConnection($conn);

// Формирование HTML
$html = '<div class="edit-container">';
$html .= '<h2>✏️ Редактирование записи</h2>';

if ($message) {
    $html .= '<div class="message ' . $messageType . '">' . htmlspecialchars($message) . '</div>';
}

// Список записей
$html .= '<div class="records-list">';
$html .= '<h3>Выберите запись для редактирования:</h3>';
$html .= '<div class="list-items">';

foreach ($allRecords as $record) {
    $fullName = htmlspecialchars($record['lastname'] . ' ' . $record['firstname']);
    if ($record['id'] == $selectedId) {
        $html .= '<span class="list-item selected">👉 ' . $fullName . '</span>';
    } else {
        $html .= '<a href="?p=edit&id=' . $record['id'] . '" class="list-item">' . $fullName . '</a>';
    }
}

$html .= '</div></div>';

// Форма редактирования
if ($currentRecord) {
    $html .= '<div class="form-container">';
    $html .= '<h3>Редактирование записи</h3>';
    $html .= '<form method="post" class="contact-form">';
    $html .= '<input type="hidden" name="action" value="edit">';
    $html .= '<input type="hidden" name="id" value="' . $currentRecord['id'] . '">';
    
    $html .= '<div class="form-row">';
    $html .= '<div class="form-group"><label>Фамилия *</label><input type="text" name="lastname" value="' . htmlspecialchars($currentRecord['lastname']) . '" required></div>';
    $html .= '<div class="form-group"><label>Имя *</label><input type="text" name="firstname" value="' . htmlspecialchars($currentRecord['firstname']) . '" required></div>';
    $html .= '</div>';
    
    $html .= '<div class="form-row">';
    $html .= '<div class="form-group"><label>Отчество</label><input type="text" name="middlename" value="' . htmlspecialchars($currentRecord['middlename'] ?? '') . '"></div>';
    $html .= '<div class="form-group"><label>Пол</label>';
    $html .= '<select name="gender"><option value="male"' . ($currentRecord['gender'] == 'male' ? ' selected' : '') . '>Мужской</option>';
    $html .= '<option value="female"' . ($currentRecord['gender'] == 'female' ? ' selected' : '') . '>Женский</option></select>';
    $html .= '</div></div>';
    
    $html .= '<div class="form-row">';
    $html .= '<div class="form-group"><label>Дата рождения</label><input type="date" name="birth_date" value="' . $currentRecord['birth_date'] . '"></div>';
    $html .= '<div class="form-group"><label>Телефон</label><input type="text" name="phone" value="' . htmlspecialchars($currentRecord['phone'] ?? '') . '"></div>';
    $html .= '</div>';
    
    $html .= '<div class="form-group"><label>Адрес</label><textarea name="address" rows="2">' . htmlspecialchars($currentRecord['address'] ?? '') . '</textarea></div>';
    
    $html .= '<div class="form-row">';
    $html .= '<div class="form-group"><label>E-mail</label><input type="email" name="email" value="' . htmlspecialchars($currentRecord['email'] ?? '') . '"></div>';
    $html .= '<div class="form-group"><label>Комментарий</label><input type="text" name="comment" value="' . htmlspecialchars($currentRecord['comment'] ?? '') . '"></div>';
    $html .= '</div>';
    
    $html .= '<div class="form-buttons">';
    $html .= '<button type="submit" class="btn btn-primary">💾 Сохранить изменения</button>';
    $html .= '</div>';
    $html .= '</form>';
    $html .= '</div>';
} else {
    $html .= '<div class="empty-message">📭 В записной книжке пока нет контактов. Сначала добавьте запись.</div>';
}

$html .= '</div>';

echo $html;
?>