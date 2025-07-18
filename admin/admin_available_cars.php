<?php
session_start();
include("../db.php"); // Подключение к базе данных
// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../loginform.php'); // Перенаправляем на страницу входа
    exit(); // Завершаем выполнение скрипта
}

// Получение списков автомобилей, дилеров и комплектаций
$cars = $conn->query("SELECT id, model FROM cars");
$dealers = $conn->query("SELECT id, dealership_name FROM dealers");
$trims = $conn->query("SELECT id, name FROM trims");

// Обработка удаления автомобиля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_car_id'])) {
    $car_id_to_delete = $_POST['delete_car_id'];

    // Запрос на удаление автомобиля
    $delete_sql = "DELETE FROM available_cars WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $car_id_to_delete);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Автомобиль успешно удален.";
        $_SESSION['msg_type'] = "success"; // Успешное сообщение
    } else {
        $_SESSION['message'] = "Ошибка при удалении автомобиля.";
        $_SESSION['msg_type'] = "danger"; // Ошибка
    }

    $stmt->close();
}

// Обработка добавления нового автомобиля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_car'])) {
    $car_id = $_POST['car_id'];
    $dealer_id = $_POST['dealer_id'];
    $trim_id = $_POST['trim_id'];
    $owners_count = $_POST['owners_count'];
    $year = $_POST['year'];
    $mileage = $_POST['mileage'];
    $transmission = $_POST['transmission'];
    $price = $_POST['price'];
    $engine_type = $_POST['engine_type'];
    $status = $_POST['status'];

    // Получение модели автомобиля
    $car_model_query = $conn->query("SELECT model FROM cars WHERE id = $car_id");
    if ($car_model_query) {
        $car_model = $car_model_query->fetch_assoc()['model'];
    } else {
        $car_model = 'неизвестно'; // Установите значение по умолчанию в случае ошибки
    }

    // Обработка загрузки изображений
    $img_slide_1 = uploadImage($_FILES['img_slide_1'], $car_id, $mileage, $year, 'img_slide_1', $car_model);
    $img_slide_2 = uploadImage($_FILES['img_slide_2'], $car_id, $mileage, $year, 'img_slide_2', $car_model);
    $img_slide_3 = uploadImage($_FILES['img_slide_3'], $car_id, $mileage, $year, 'img_slide_3', $car_model);
    $img_slide_4 = uploadImage($_FILES['img_slide_4'], $car_id, $mileage, $year, 'img_slide_4', $car_model);

    // Запрос на добавление нового автомобиля
    $sql = "INSERT INTO available_cars (car_id, dealer_id, trim_id, owners_count, year, mileage, transmission, price, engine_type, img_slide_1, img_slide_2, img_slide_3, img_slide_4, status, timestap) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiississssssss", $car_id, $dealer_id, $trim_id, $owners_count, $year, $mileage, $transmission, $price, $engine_type, $img_slide_1, $img_slide_2, $img_slide_3, $img_slide_4, $status);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Автомобиль успешно добавлен.";
        $_SESSION['msg_type'] = "success"; // Успешное сообщение
    } else {
        $_SESSION['message'] = "Ошибка при добавлении автомобиля.";
        $_SESSION['msg_type'] = "danger"; // Ошибка
    }

    $stmt->close();
}

// Функция для загрузки изображения
function uploadImage($file, $car_id, $mileage, $year, $field_name, $car_model) {
    $targetDir = "../img/av_cars/";
    $fileType = pathinfo(basename($file["name"]), PATHINFO_EXTENSION);
    $newFileName = "{$car_id}_{$car_model}_{$field_name}_{$mileage}_{$year}_" . time() . ".{$fileType}";
    $targetFilePath = $targetDir . $newFileName;

    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        return $newFileName; // Возвращаем имя файла, если загрузка успешна
    } else {
        return null; // Возвращаем null, если произошла ошибка
    }
}

// Получение списка доступных автомобилей
$sql = "SELECT ac.id, ac.car_id, ac.img_slide_1, ac.dealer_id, ac.trim_id, ac.owners_count, ac.year, ac.mileage, ac.transmission, ac.price, ac.engine_type, ac.status, c.model AS car_model, d.dealership_name AS dealer_name, t.name AS trim_name FROM available_cars ac LEFT JOIN cars c ON ac.car_id = c.id LEFT JOIN dealers d ON ac.dealer_id = d.id LEFT JOIN trims t ON ac.trim_id = t.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление доступными автомобилями</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
 
</head>
<body>
    <style>
        .img-fluid{
           
            width: 390px;
            height: 260px !important;
            object-fit:cover;
        }
    </style>
    <?php require_once('../header.php');?>
<div class="container">
    <h1>Управление доступными автомобилями</h1>
    <a href="admin_panel.php" class='blue'>Вернуться в админ-панель</a>
    <?php
    // Вывод сообщения об успехе или ошибке
    if (isset($_SESSION['message'])) {
        echo "<div class='alert alert-{$_SESSION['msg_type']} mt-3'>{$_SESSION['message']}</div>";
        unset($_SESSION['message']); // Удаление сообщения после отображения
        unset($_SESSION['msg_type']); // Удаление типа сообщения после отображения
    }
    ?>

    <h2 class="mt-3">Добавить новый автомобиль</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="car_id">Автомобиль</label>
            <select class="form-control" id="car_id" name="car_id" required>
                <?php while ($row = $cars->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['model']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="dealer_id">Дилер</label>
            <select class="form-control" id="dealer_id" name="dealer_id" required>
                <?php while ($row = $dealers->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['dealership_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="trim_id">Комплектация</label>
            <select class="form-control" id="trim_id" name="trim_id" required>
                <?php while ($row = $trims->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="owners_count">Количество владельцев</label>
            <input type="number" class="form-control" id="owners_count" name="owners_count">
        </div>
        <div class="form-group">
            <label for="year">Год</label>
            <input type="number" class="form-control" id="year" name="year">
        </div>
        <div class="form-group">
            <label for="mileage">Пробег</label>
            <input type="number" class="form-control" id="mileage" name="mileage">
        </div>
        <div class="form-group">
            <label for="transmission">Тип трансмиссии</label>
            <input type="text" class="form-control" id="transmission" name="transmission">
        </div>
        <div class="form-group">
            <label for="price">Цена</label>
            <input type="number" class="form-control" id="price" name="price">
        </div>
        <div class="form-group">
            <label for="engine_type">Тип двигателя</label>
            <select class="form-control" id="engine_type" name="engine_type">
                <option value="Электроэнергия">Электроэнергия</option>
                <option value="Бензин">Бензин</option>
                <option value="Дизель">Дизель</option>
            </select>
        </div>
        <div class="form-group">
            <label for="img_slide_1">Изображение 1</label>
            <input type="file" class="form-control" id="img_slide_1" name="img_slide_1" >
        </div>
        <div class="form-group">
            <label for="img_slide_2">Изображение 2</label>
            <input type="file" class="form-control" id="img_slide_2" name="img_slide_2" >
        </div>
        <div class="form-group">
            <label for="img_slide_3">Изображение 3</label>
            <input type="file" class="form-control" id="img_slide_3" name="img_slide_3" >
        </div>
        <div class="form-group">
            <label for="img_slide_4">Изображение 4</label>
            <input type="file" class="form-control" id="img_slide_4" name="img_slide_4" >
        </div>
        <div class="form-group">
            <label for="status">Статус</label>
            <select class="form-control" id="status" name="status">
                <option value="В наличии">В наличии</option>
                <option value="Забронировано">Забронировано</option>
                <option value="Куплено">Куплено</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-3" name="new_car">Добавить автомобиль</button>
    </form>

    <h2 class="mt-4">Список доступных автомобилей</h2>
    <div class="row">
    <?php
    if ($result->num_rows > 0) {
        // Вывод данных автомобилей в виде карточек
        while ($row = $result->fetch_assoc()) {
            echo "<div class='col-md-4 mb-3'>
                    <div class='card'>
                        <div class='card-body'>
                        <img src='../img/av_cars/{$row['img_slide_1']}' alt='Изображение автомобиля' class='img-fluid'>
                            <h5 class='card-title mt-3'><a href='edit_car.php?id={$row['car_id']}' class='blue'>{$row['car_model']}</a></h5>
                            <p class='card-text'>Дилер: <a href='edit_dealer.php?id={$row['dealer_id']}' class='blue'>{$row['dealer_name']}</a></p>
                            <p class='card-text'>Комплектация: <a href='edit_trim.php?id={$row['trim_id']}' class='blue'>{$row['trim_name']}</a></p>
                            <p class='card-text'>Пробег: {$row['mileage']} км</p>
                            <p class='card-text'>Цена: {$row['price']} руб.</p>
                            <p class='card-text'>Статус: {$row['status']}</p>
                            <a href='edit_available_car.php?id={$row['id']}' class='btn btn-primary'>Изменить</a>
                            <form method='POST' action='' style='display:inline;'>
                                <input type='hidden' name='delete_car_id' value='{$row['id']}'>
                                <input type='submit' value='Удалить' onclick='return confirm(\"Вы уверены, что хотите удалить этот автомобиль?\");' class='btn btn-danger'>
                            </form>
                            
                        </div>
                    </div>
                  </div>";
        }
    } else {
        echo "<p>Нет доступных автомобилей</p>";
    }
    ?>
    </div>
</div>
<?php require_once("../footer.php"); ?> </body>
</html>
