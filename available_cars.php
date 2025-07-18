<?php
session_start();
include("db.php");

// Получение фильтров из запроса
$city_ids = isset($_GET['city']) ? (array) $_GET['city'] : [];
$dealer_ids = isset($_GET['dealer']) ? (array) $_GET['dealer'] : [];
$model_ids = isset($_GET['model']) ? (array) $_GET['model'] : [];
$trim_ids = isset($_GET['trim']) ? (array) $_GET['trim'] : [];
$engine_types = isset($_GET['engine']) ? (array) $_GET['engine'] : [];
$mileage_from = isset($_GET['mileage_from']) ? intval($_GET['mileage_from']) : 0;
$mileage_to = isset($_GET['mileage_to']) ? intval($_GET['mileage_to']) : 0;
$price_from = isset($_GET['price_from']) ? floatval($_GET['price_from']) : 0;
$price_to = isset($_GET['price_to']) ? floatval($_GET['price_to']) : 0;
$owners_from = isset($_GET['owners_from']) ? intval($_GET['owners_from']) : 0;
$owners_to = isset($_GET['owners_to']) ? intval($_GET['owners_to']) : 0;

// Получение значения сортировки
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : 'price_asc'; // По умолчанию сортировка по возрастанию цены

// Основной SQL-запрос с фильтрацией
$sql = "SELECT ac.id, c.model, ac.owners_count, ac.year, ac.mileage, ac.transmission, ac.price, 
               d.dealership_name, t.name AS trim_name, ac.engine_type, 
               ac.img_slide_1, ac.img_slide_2, ac.img_slide_3, ac.img_slide_4 
        FROM available_cars ac 
        JOIN cars c ON ac.car_id = c.id 
        JOIN dealers d ON ac.dealer_id = d.id 
        JOIN trims t ON ac.trim_id = t.id 
        WHERE ac.status = 'В наличии'"; // Фильтрация по статусу


$params = [];
$types = '';

// Добавление условий фильтрации
if (!empty($city_ids) && is_array($city_ids)) {
    $sql .= " AND d.city_id IN (" . implode(',', array_fill(0, count($city_ids), '?')) . ")";
    $params = array_merge($params, $city_ids);
    $types .= str_repeat('i', count($city_ids));
}
if (!empty($dealer_ids) && is_array($dealer_ids)) {
    $sql .= " AND d.id IN (" . implode(',', array_fill(0, count($dealer_ids), '?')) . ")";
    $params = array_merge($params, $dealer_ids);
    $types .= str_repeat('i', count($dealer_ids));
}
if (!empty($model_ids) && is_array($model_ids)) {
    $sql .= " AND c.model IN (" . implode(',', array_fill(0, count($model_ids), '?')) . ")";
    $params = array_merge($params, $model_ids);
    $types .= str_repeat('s', count($model_ids));
}
if (!empty($trim_ids) && is_array($trim_ids)) {
    $sql .= " AND t.name IN (" . implode(',', array_fill(0, count($trim_ids), '?')) . ")";
    $params = array_merge($params, $trim_ids);
    $types .= str_repeat('s', count($trim_ids));
}
if (!empty($engine_types) && is_array($engine_types)) {
    $sql .= " AND ac.engine_type IN (" . implode(',', array_fill(0, count($engine_types), '?')) . ")";
    $params = array_merge($params, $engine_types);
    $types .= str_repeat('s', count($engine_types));
}
if ($mileage_from > 0) {
    $sql .= " AND ac.mileage >= ?";
    $params[] = $mileage_from;
    $types .= 'i';
}
if ($mileage_to > 0) {
    $sql .= " AND ac.mileage <= ?";
    $params[] = $mileage_to;
    $types .= 'i';
}
if ($price_from > 0) {
    $sql .= " AND ac.price >= ?";
    $params[] = $price_from;
    $types .= 'd';
}
if ($price_to > 0) {
    $sql .= " AND ac.price <= ?";
    $params[] = $price_to;
    $types .= 'd';
}
if ($owners_from > 0) {
    $sql .= " AND ac.owners_count >= ?";
    $params[] = $owners_from;
    $types .= 'i';
}
if ($owners_to > 0) {
    $sql .= " AND ac.owners_count <= ?";
    $params[] = $owners_to;
    $types .= 'i';
}

// Добавление сортировки
switch ($sort_order) {
    case 'price_desc':
        $sql .= " ORDER BY ac.price DESC";
        break;
    case 'mileage_asc':
        $sql .= " ORDER BY ac.mileage ASC";
        break;
    case 'mileage_desc':
        $sql .= " ORDER BY ac.mileage DESC";
        break;
    case 'year_asc':
        $sql .= " ORDER BY ac.year ASC";
        break;
    case 'year_desc':
        $sql .= " ORDER BY ac.year DESC";
        break;
    case 'owners_asc':
        $sql .= " ORDER BY ac.owners_count ASC";
        break;
    case 'owners_desc':
        $sql .= " ORDER BY ac.owners_count DESC";
        break;
    case 'price_asc':
    default:
        $sql .= " ORDER BY ac.price ASC"; // По умолчанию сортировать по возрастанию цены
}

// Подготовка и выполнение запроса
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Получение списка городов
$city_sql = "SELECT id, name FROM cities";
$city_result = $conn->query($city_sql);
if (!$city_result) {
    die("Ошибка загрузки городов: " . $conn->error);
}

// Получение всех дилеров
$dealer_sql = "SELECT id, dealership_name, city_id FROM dealers";
$dealer_result = $conn->query($dealer_sql);
if (!$dealer_result) {
    die("Ошибка загрузки дилеров: " . $conn->error);
}

// Получение уникальных моделей
$model_sql = "SELECT DISTINCT model FROM cars";
$model_result = $conn->query($model_sql);
if (!$model_result) {
    die("Ошибка загрузки моделей: " . $conn->error);
}

// Получение уникальных комплектаций
$trim_sql = "SELECT DISTINCT name FROM trims";
$trim_result = $conn->query($trim_sql);
if (!$trim_result) {
    die("Ошибка загрузки комплектаций: " . $conn->error);
}

// Получение минимальной и максимальной цены, пробега и количества владельцев
$min_max_sql = "SELECT MIN(price) AS min_price, MAX(price) AS max_price, MIN(mileage) AS min_mileage, MAX(mileage) AS max_mileage, MIN(owners_count) AS min_owners, MAX(owners_count) AS max_owners FROM available_cars WHERE status = 'В наличии'";
$min_max_result = $conn->query($min_max_sql);
$min_max = $min_max_result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Автомобили в наличии</title>


    <script>
        function autoSubmit() {
            document.getElementById('filterForm').submit();
        }

        function clearFilters() {
            const form = document.getElementById('filterForm');
            
            // Сбрасываем значения всех чекбоксов
            const checkboxes = form.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            // Сбрасываем значения всех полей ввода
            const inputs = form.querySelectorAll('input[type="text"], input[type="number"], select');
            inputs.forEach(input => {
                input.value = '';
            });

            // Отправляем форму
            autoSubmit();
        }

        function updateDealers() {
            const selectedCities = Array.from(document.querySelectorAll('input[name="city[]"]:checked')).map(cb => cb.value);
            const dealerCheckboxes = document.querySelectorAll('input[name="dealer[]"]');

            dealerCheckboxes.forEach(checkbox => {
                const dealerCityId = checkbox.getAttribute('data-city-id');
                if (selectedCities.length === 0 || selectedCities.includes(dealerCityId)) {
                    checkbox.parentElement.style.display = 'block'; // Показываем дилера
                } else {
                    checkbox.parentElement.style.display = 'none'; // Скрываем дилера
                }
            });
        }
    </script>
         <link rel="icon" href="img/BMW_logo_(gray).svg.png" type="image/png">
<link rel="stylesheet" href="custom.css">
</head>
<body>
<?php
  require_once("header.php")?> 
    <main class="container">       
         <h1>Автомобили в наличии</h1>

        <form method="GET"  id="filterForm">
     <div class="mainblock">
        <div class="filtersection">
     <button type="button" id="toggleButton" class="btnauth w-100">Показать содержимое</button>
     <div id="content" class="mt-3">
<div class="accordion accordion-flush" id="accordionPanelsStayOpenExample">
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="false" aria-controls="panelsStayOpen-collapseOne">
        Города
      </button>
    </h2>
    <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse ">
      <div class="accordion-body">
        <p>При выборе города - меняется список дилеров</p>
   <?php
                if ($city_result) {
                    while ($row = $city_result->fetch_assoc()) {
                        $checked = in_array($row['id'], $city_ids) ? 'checked' : '';
                        echo "<label><input type='checkbox' class='form-check-input' name='city[]' value='" . htmlspecialchars($row['id']) . "' $checked onchange='updateDealers();'> " . htmlspecialchars($row['name']) . "</label><br>";
                    }
                } else {
                    echo "<option value=''>Ошибка загрузки городов</option>";
                }
                ?> </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
        Дилеры
      </button>
    </h2>
    <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse">
      <div class="accordion-body">
        <div class="filtercheckbox">
        <?php
                if ($dealer_result) {
                    while ($row = $dealer_result->fetch_assoc()) {
                        $checked = in_array($row['id'], $dealer_ids) ? 'checked' : '';
                        echo "<label><input type='checkbox' class='form-check-input' name='dealer[]' value='" . htmlspecialchars($row['id']) . "' data-city-id='" . htmlspecialchars($row['city_id']) . "' $checked onchange='autoSubmit();'> " . htmlspecialchars($row['dealership_name']) . "</label>";
                    }
                } else {
                    echo "<li>Ошибка загрузки дилеров.</li>";
                }
                ?>   </div></div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
      Модели
      </button>
    </h2>
    <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse">
      <div class="accordion-body">
       <?php
                if ($model_result) {
                    while ($row = $model_result->fetch_assoc()) {
                        $checked = in_array($row['model'], $model_ids) ? 'checked' : '';
                        echo "<label><input type='checkbox' class='form-check-input' name='model[]' value='" . htmlspecialchars($row['model']) . "' $checked onchange='autoSubmit();'> " . htmlspecialchars($row['model']) . "</label><br>";
                    }
                } else {
                    echo "<li>Ошибка загрузки моделей.</li>";
                }
                ?> </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">
      Комплектации
      </button>
    </h2>
    <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse">
      <div class="accordion-body">
      <?php
                if ($trim_result) {
                    while ($row = $trim_result->fetch_assoc()) {
                        $checked = in_array($row['name'], $trim_ids) ? 'checked' : '';
                        echo "<label><input type='checkbox' class='form-check-input' name='trim[]' value='" . htmlspecialchars($row['name']) . "' $checked onchange='autoSubmit();'> " . htmlspecialchars($row['name']) . "</label><br>";
                    }
                } else {
                    echo "<li>Ошибка загрузки комплектаций.</li>";
                }
                ?> </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFive" aria-expanded="false" aria-controls="panelsStayOpen-collapseFive">
      Тип двигателя
      </button>
    </h2>
    <div id="panelsStayOpen-collapseFive" class="accordion-collapse collapse">
      <div class="accordion-body">
      <label><input type='checkbox' class='form-check-input' name='engine[]' value='Электропривод' <?php echo in_array('Электропривод', $engine_types) ? 'checked' : ''; ?> onchange='autoSubmit();'> Электропривод</label><br>
                <label><input type='checkbox' class='form-check-input' name='engine[]' value='Бензин' <?php echo in_array('Бензин', $engine_types) ? 'checked' : ''; ?> onchange='autoSubmit();'> Бензин</label><br>
                <label><input type='checkbox' class='form-check-input' name='engine[]' value='Дизель' <?php echo in_array('Дизель', $engine_types) ? 'checked' : ''; ?> onchange='autoSubmit();'> Дизель</label><br>
           </div>
    </div>
  </div>
  <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseSix" aria-expanded="false" aria-controls="panelsStayOpen-collapseSix">
                Пробег
            </button>
        </h2>
        <div id="panelsStayOpen-collapseSix" class="accordion-collapse collapse">
            <div class="accordion-body">
                <div class="inputfilterbox">
                  <label for="">От</label>  <input type="number" name="mileage_from" id="mileage_from" min="<?php echo htmlspecialchars($min_max['min_mileage']); ?>" max="<?php echo htmlspecialchars($min_max['max_mileage']); ?>" placeholder="<?php echo htmlspecialchars($min_max['min_mileage']); ?>" onchange="autoSubmit();"> 
                  <label for="">До</label>  <input type="number" name="mileage_to" id="mileage_to" min="<?php echo htmlspecialchars($min_max['min_mileage']); ?>" max="<?php echo htmlspecialchars($min_max['max_mileage']); ?>" placeholder="<?php echo htmlspecialchars($min_max['max_mileage']); ?>" onchange="autoSubmit();">
                </div>
            </div>
        </div>
        
    </div>
    
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseSeven" aria-expanded="false" aria-controls="panelsStayOpen-collapseSeven">
                Цена
            </button>
        </h2>
        <div id="panelsStayOpen-collapseSeven" class="accordion-collapse collapse">
            <div class="accordion-body">
                <div class="inputfilterbox">
                  <label for="">От</label> <input type="number" name="price_from" id="price_from" min="<?php echo htmlspecialchars($min_max['min_price']); ?>" max="<?php echo htmlspecialchars($min_max['max_price']); ?>" placeholder="<?php echo htmlspecialchars($min_max['min_price']); ?>" onchange="autoSubmit();">
                  <label for="">До</label> <input type="number" name="price_to" id="price_to" min="<?php echo htmlspecialchars($min_max['min_price']); ?>" max="<?php echo htmlspecialchars($min_max['max_price']); ?>" placeholder="<?php echo htmlspecialchars($min_max['max_price']); ?>" onchange="autoSubmit();">
                </div>
            </div>
        </div>
 </div>
 <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseEight" aria-expanded="false" aria-controls="panelsStayOpen-collapseEight">
               Владельцы
            </button>
        </h2>
        <div id="panelsStayOpen-collapseEight" class="accordion-collapse collapse">
            <div class="accordion-body">
                <div class="inputfilterbox">
                  <label for="">От</label>  <input type="number" name="owners_from" id="owners_from" min="<?php echo htmlspecialchars($min_max['min_owners']); ?>" max="<?php echo htmlspecialchars($min_max['max_owners']); ?>" placeholder="<?php echo htmlspecialchars($min_max['min_owners']); ?>" onchange="autoSubmit();">
                  <label for="">До</label> <input type="number" name="owners_to" id="owners_to"  min="<?php echo htmlspecialchars($min_max['min_owners']); ?>" max="<?php echo htmlspecialchars($min_max['max_owners']); ?>" placeholder="<?php echo htmlspecialchars($min_max['max_owners']); ?>" onchange="autoSubmit();">
                  </div>
            </div>
        </div>
   
            </div>
            <button type="button" class="btnauth" onclick="clearFilters()" style="width:100%">Очистить фильтры</button>
    </div>
            </div>
            </div>
            
    <script>
        const toggleButton = document.getElementById('toggleButton');
const content = document.getElementById('content');

toggleButton.addEventListener('click', function() {
    // Переключаем видимость содержимого
    if (content.style.display === 'none' || content.style.display === '') {
        content.style.display = 'block';
        setTimeout(() => {
            content.style.opacity = 1; // Устанавливаем полную непрозрачность
        }, 10); // Небольшая задержка для применения стиля
        toggleButton.textContent = 'Скрыть содержимое';
    } else {
        content.style.opacity = 0; // Уменьшаем непрозрачность
        setTimeout(() => {
            content.style.display = 'none'; // Скрываем содержимое после анимации
        }, 500); // Соответствует времени анимации
        toggleButton.textContent = 'Показать содержимое';
    }
});

    </script>
<script>
    function autoSubmit() {
        document.getElementById('filterForm').submit();
    }
</script>

     
            
        </form>
        <div class="availablecarblock">      
        <form method="GET" class="filterselect" action="available_cars.php">
            <label for="sort">Сортировать по:</label>
            <select name="sort" id="sort"  onchange="autoSubmit2();">
                <option value="price_asc" <?php echo $sort_order == 'price_asc' ? 'selected' : ''; ?>>Цена по возрастанию</option>
                <option value="price_desc" <?php echo $sort_order == 'price_desc' ? 'selected' : ''; ?>>Цена по убыванию</option>
                <option value="mileage_asc" <?php echo $sort_order == 'mileage_asc' ? 'selected' : ''; ?>>Пробег по возрастанию</option>
                <option value="mileage_desc" <?php echo $sort_order == 'mileage_desc' ? 'selected' : ''; ?>>Пробег по убыванию</option>
                <option value="year_asc" <?php echo $sort_order == 'year_asc' ? 'selected' : ''; ?>>Год по возрастанию</option>
                <option value="year_desc" <?php echo $sort_order == 'year_desc' ? 'selected' : ''; ?>>Год по убыванию</option>
                <option value="owners_asc" <?php echo $sort_order == 'owners_asc' ? 'selected' : ''; ?>>Владельцы по возрастанию</option>
                <option value="owners_desc" <?php echo $sort_order == 'owners_desc' ? 'selected' : ''; ?>>Владельцы по убыванию</option>
            </select>
        </form>
        <script>
    function autoSubmit2() {
        document.querySelector('.filterselect').submit();
    }
</script>
<div class="product_card_group">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='col'>";
            echo "<div class='card h-100'>";

            // Ссылка на товар
            echo "<a href='available_cars_lp.php?id=" . htmlspecialchars($row['id']) . "' class='product-link'>";
            
            // Выводим одно изображение
            if (!empty($row['img_slide_1'])) {
                echo "<img src='img/av_cars/" . htmlspecialchars($row['img_slide_1']) . "' class='card-img-top' alt='" . htmlspecialchars($row['model']) . "'>";
            }

            echo "<div class='card-body'>";
            echo "<h4 class='card-title'>" . htmlspecialchars($row['model']) . "</h4>";
            echo "<p class='card-text'>Комплектация: " . htmlspecialchars($row['trim_name']) . "</p>";
            echo "<p class='card-text'>Тип двигателя: " . htmlspecialchars($row['engine_type']) . "</p>";
            echo "<p class='card-text'>Количество владельцев: " . htmlspecialchars($row['owners_count']) . "</p>";
            echo "<p class='card-text'>Год выпуска: " . htmlspecialchars($row['year']) . "</p>";
            echo "<p class='card-text'>Пробег: " . htmlspecialchars($row['mileage']) . " км</p>";
            echo "<p class='card-text'>Трансмиссия: " . htmlspecialchars($row['transmission']) . "</p>";
            echo "<h5 class='blue card-price'>" . number_format($row['price'], 0, ' ', ' ') . " руб.</h5>";

            echo "</div>";
            echo "</a>"; 
            echo "</div>";
            echo "</div>"; 
        }
    } else {
        echo "<p>Нет доступных автомобилей. Очистите настройки фильтрации</p>";
    }
    ?>
</div>


<!-- Не забудьте подключить Bootstrap JS и CSS в вашем HTML -->


        </div>
        </div>
    </main>
    <?php
  require_once("footer.php")?> 
</body>  
</html>

<?php
$conn->close();
?>

