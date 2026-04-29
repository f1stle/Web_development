<?php

require_once 'config.php';

$message = '';
$messageType = '';
$deletedLastname = '';

$conn = getDBConnection();

// Обработка удаления
if (isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    
    // Получаем фамилию перед удалением
    $stmt = mysqli_prepare($conn, "SELECT lastname, firstname FROM contacts WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $deleteId);
    mysqli_stmt_execute($stmt);
    $record = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    
    if ($record) {
        $deletedLastname = htmlspecialchars($record['lastname'] . ' ' . $record['firstname']);
        
        $stmt = mysqli_prepare($conn, "DELETE FROM contacts WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $deleteId);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = '✅ Запись с фамилией "' . $deletedLastname . '" успешно удалена!';
            $messageType = 'success';
        } else {
            $message = '❌ Ошибка: запись не удалена. ' . mysqli_error($conn);
            $messageType = 'error';
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = '❌ Запись не найдена.';
        $messageType = 'error';
    }
}

// Получение всех записей для списка
$allRecords = [];
$query = "SELECT id, lastname, firstname, middlename FROM contacts ORDER BY lastname, firstname";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $allRecords[] = $row;
}

closeDBConnection($conn);

// Формирование HTML
$html = '<div class="delete-container">';
$html .= '<h2>🗑️ Удаление записи</h2>';

if ($message) {
    $html .= '<div class="message ' . $messageType . '">' . $message . '</div>';
}

if (count($allRecords) == 0) {
    $html .= '<div class="empty-message">📭 В записной книжке пока нет контактов. Удалять нечего.</div>';
} else {
    $html .= '<div class="records-list delete-list">';
    $html .= '<h3>Выберите запись для удаления:</h3>';
    $html .= '<div class="list-items delete-items">';
    
    foreach ($allRecords as $record) {
        $initials = '';
        if ($record['firstname']) {
            $initials .= mb_substr($record['firstname'], 0, 1) . '.';
        }
        if ($record['middlename']) {
            $initials .= mb_substr($record['middlename'], 0, 1) . '.';
        }
        $displayName = htmlspecialchars($record['lastname'] . ' ' . $initials);
        
        $html .= '<a href="?p=delete&delete_id=' . $record['id'] . '" class="delete-item" onclick="return confirm(\'Вы уверены, что хотите удалить запись "' . $displayName . '"?\')">';
        $html .= '🗑️ ' . $displayName;
        $html .= '</a>';
    }
    
    $html .= '</div></div>';
}

$html .= '</div>';

echo $html;
?>