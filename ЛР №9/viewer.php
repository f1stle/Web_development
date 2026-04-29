<?php

require_once 'config.php';

function getContactsList($sort = 'id', $page = 0, $perPage = 10) {
    $conn = getDBConnection();
    
    $sortFields = [
        'id' => 'id',
        'lastname' => 'lastname, firstname',
        'birth_date' => 'birth_date'
    ];
    
    $orderBy = isset($sortFields[$sort]) ? $sortFields[$sort] : 'id';
    
    $countResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM contacts");
    $totalRow = mysqli_fetch_assoc($countResult);
    $total = $totalRow['total'];
    $totalPages = ceil($total / $perPage);
    
    if ($page >= $totalPages && $totalPages > 0) {
        $page = $totalPages - 1;
    }
    if ($page < 0) $page = 0;
    
    $offset = $page * $perPage;
    
    $query = "SELECT * FROM contacts ORDER BY $orderBy LIMIT $offset, $perPage";
    $result = mysqli_query($conn, $query);
    
    $contacts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $contacts[] = $row;
    }
    
    closeDBConnection($conn);
    
    return [
        'contacts' => $contacts,
        'total' => $total,
        'totalPages' => $totalPages,
        'currentPage' => $page
    ];
}

function renderViewer($sort = 'id', $page = 0) {
    $data = getContactsList($sort, $page, 10);
    
    if ($data['total'] == 0) {
        return '<div class="empty-message">📭 В записной книжке пока нет контактов.</div>';
    }
    
    // Формирование таблицы
    $html = '<div class="viewer-header">';
    $html .= '<h2>📇 Записная книжка</h2>';
    $html .= '<p class="total-count">Всего записей: ' . $data['total'] . '</p>';
    $html .= '</div>';
    
    $html .= '<table class="contacts-table">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>№</th>';
    $html .= '<th>Фамилия</th>';
    $html .= '<th>Имя</th>';
    $html .= '<th>Отчество</th>';
    $html .= '<th>Пол</th>';
    $html .= '<th>Дата рождения</th>';
    $html .= '<th>Телефон</th>';
    $html .= '<th>Адрес</th>';
    $html .= '<th>E-mail</th>';
    $html .= '<th>Комментарий</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    
    $startNum = $data['currentPage'] * 10 + 1;
    foreach ($data['contacts'] as $index => $contact) {
        $html .= '<tr>';
        $html .= '<td>' . ($startNum + $index) . '</td>';
        $html .= '<td>' . htmlspecialchars($contact['lastname']) . '</td>';
        $html .= '<td>' . htmlspecialchars($contact['firstname']) . '</td>';
        $html .= '<td>' . htmlspecialchars($contact['middlename'] ?? '') . '</td>';
        $html .= '<td>' . ($contact['gender'] == 'male' ? 'Мужской' : 'Женский') . '</td>';
        $html .= '<td>' . ($contact['birth_date'] ? date('d.m.Y', strtotime($contact['birth_date'])) : '—') . '</td>';
        $html .= '<td>' . htmlspecialchars($contact['phone'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($contact['address'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($contact['email'] ?? '') . '</td>';
        $html .= '<td>' . htmlspecialchars($contact['comment'] ?? '') . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody>';
    $html .= '</table>';
    
    if ($data['totalPages'] > 1) {
        $html .= '<div class="pagination">';
        $html .= '<span class="pagination-label">Страницы:</span>';
        
        for ($i = 0; $i < $data['totalPages']; $i++) {
            if ($i == $data['currentPage']) {
                $html .= '<span class="current-page">' . ($i + 1) . '</span>';
            } else {
                $html .= '<a href="?p=viewer&sort=' . urlencode($sort) . '&page=' . $i . '">' . ($i + 1) . '</a>';
            }
        }
        
        $html .= '</div>';
    }
    
    return $html;
}
?>