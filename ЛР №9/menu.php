<?php

function renderMenu() {
    $active_page = isset($_GET['p']) ? $_GET['p'] : 'viewer';
    
    $allowed = ['viewer', 'add', 'edit', 'delete'];
    if (!in_array($active_page, $allowed)) {
        $active_page = 'viewer';
    }
    
    $active_sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
    $allowed_sorts = ['id', 'lastname', 'birth_date'];
    if (!in_array($active_sort, $allowed_sorts)) {
        $active_sort = 'id';
    }
    
    // Номер страницы пагинации
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
    if ($page < 0) $page = 0;
    
    $html = '<div id="main-menu">';
    
    $html .= '<a href="?p=viewer&sort=' . $active_sort . '&page=' . $page . '"';
    if ($active_page == 'viewer') $html .= ' class="active"';
    $html .= '>📋 Просмотр</a>';
    
    $html .= '<a href="?p=add"';
    if ($active_page == 'add') $html .= ' class="active"';
    $html .= '>➕ Добавление записи</a>';
    
    $html .= '<a href="?p=edit"';
    if ($active_page == 'edit') $html .= ' class="active"';
    $html .= '>✏️ Редактирование записи</a>';
    
    $html .= '<a href="?p=delete"';
    if ($active_page == 'delete') $html .= ' class="active"';
    $html .= '>🗑️ Удаление записи</a>';
    
    $html .= '</div>';
    
    if ($active_page == 'viewer') {
        $html .= '<div id="submenu">';
        $html .= '<span class="submenu-label">Сортировка:</span>';
        
        $html .= '<a href="?p=viewer&sort=id&page=' . $page . '"';
        if ($active_sort == 'id') $html .= ' class="active-sub"';
        $html .= '>По умолчанию (ID)</a>';
        
        $html .= '<a href="?p=viewer&sort=lastname&page=' . $page . '"';
        if ($active_sort == 'lastname') $html .= ' class="active-sub"';
        $html .= '>По фамилии</a>';
        
        $html .= '<a href="?p=viewer&sort=birth_date&page=' . $page . '"';
        if ($active_sort == 'birth_date') $html .= ' class="active-sub"';
        $html .= '>По дате рождения</a>';
        
        $html .= '</div>';
    }
    
    return $html;
}
?>