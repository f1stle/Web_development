<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛР7 — Мельников Кирилл, группа 241-353 — Сортировка массивов</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <img src="./logo.png" alt="Логотип" width="60">
        <span>Мельников Кирилл | Группа 241-353 | Лабораторная работа №7</span>
    </header>

    <main>
        <div class="form-container">
            <h1>📊 Визуализация сортировки массивов</h1>
            
            <form action="sort_process.php" method="post" target="_blank" id="sortForm">
                <div class="array-input-section">
                    <h3>Введите элементы массива:</h3>
                    <table id="elements-table">
                        <tbody>
                            <tr>
                                <td class="element-index">0</td>
                                <td class="element-input">
                                    <input type="text" name="element0" placeholder="Число" required>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="button-group">
                        <button type="button" class="btn btn-add" onclick="addElement()">➕ Добавить еще один элемент</button>
                    </div>
                </div>

                <div class="algorithm-section">
                    <h3>Выберите алгоритм сортировки:</h3>
                    <div class="algorithm-options">
                        <label class="radio-label">
                            <input type="radio" name="algorithm" value="choice" required> Сортировка выбором
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="algorithm" value="bubble"> Пузырьковая сортировка
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="algorithm" value="gnome"> Сортировка садового гнома
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="algorithm" value="shell"> Сортировка Шелла
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="algorithm" value="quick"> Быстрая сортировка
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="algorithm" value="builtin"> Встроенная sort()
                        </label>
                    </div>
                </div>

                <input type="hidden" name="arrLength" id="arrLength" value="1">
                
                <div class="submit-section">
                    <button type="submit" class="btn btn-sort">🚀 Сортировать массив</button>
                </div>
            </form>
        </div>
    </main>

    <footer>
        Лабораторная работа №7 | Сортировка массивов
    </footer>

    <script>
        let elementCounter = 1;
        const table = document.getElementById('elements-table').getElementsByTagName('tbody')[0];

        function addElement() {
            const row = table.insertRow();
            const cellIndex = row.insertCell(0);
            const cellInput = row.insertCell(1);
            
            cellIndex.className = 'element-index';
            cellIndex.textContent = elementCounter;
            
            cellInput.className = 'element-input';
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'element' + elementCounter;
            input.placeholder = 'Число';
            input.required = true;
            cellInput.appendChild(input);
            
            elementCounter++;
            document.getElementById('arrLength').value = elementCounter;
        }
    </script>
</body>
</html>