<?php
session_start();
include("../db.php"); // Подключение к базе данных

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../loginform.php'); // Перенаправляем на страницу входа
    exit(); // Завершаем выполнение скрипта
}

// Проверка, передан ли ID автомобиля
if (!isset($_GET['id'])) {
    header("Location: admin_available_cars.php");
    exit();
}

$car_id = intval($_GET['id']);

// Получение информации об автомобиле
$sql = "SELECT * FROM available_cars WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin_available_cars.php");
    exit();
}

$car = $result->fetch_assoc();
$message = '';

// Получение списков автомобилей, дилеров и комплектаций
$cars = $conn->query("SELECT id, model FROM cars");
$dealers = $conn->query("SELECT id, dealership_name FROM dealers");
$trims = $conn->query("SELECT id, name FROM trims");

// Обработка изменения автомобиля
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_id = $_POST['car_id']; // Получаем ID автомобиля
    $car_model_id = $_POST['car_model_id']; // Получаем ID новой модели
    $dealer_id = $_POST['dealer_id'];
    $trim_id = $_POST['trim_id'];
    $owners_count = $_POST['owners_count'];
    $year = $_POST['year'];
    $mileage = $_POST['mileage'];
    $transmission = $_POST['transmission']; // Получаем значение типа трансмиссии
    $price = $_POST['price'];
    $engine_type = $_POST['engine_type'];
    $status = $_POST['status'];



    // Обработка загрузки изображений
    $img_slide_1 = !empty($_FILES['img_slide_1']['name']) ? 
                    uploadImage($_FILES['img_slide_1'], $car_id, $mileage, $year, 'img_slide_1', $car_model_id) : 
                    $car['img_slide_1'];

    $img_slide_2 = !empty($_FILES['img_slide_2']['name']) ? 
                    uploadImage($_FILES['img_slide_2'], $car_id, $mileage, $year, 'img_slide_2', $car_model_id) : 
                    $car['img_slide_2'];

    $img_slide_3 = !empty($_FILES['img_slide_3']['name']) ? 
                    uploadImage($_FILES['img_slide_3'], $car_id, $mileage, $year, 'img_slide_3', $car_model_id) : 
                    $car['img_slide_3'];

    $img_slide_4 = !empty($_FILES['img_slide_4']['name']) ? 
                    uploadImage($_FILES['img_slide_4'], $car_id, $mileage, $year, 'img_slide_4', $car_model_id) : 
                    $car['img_slide_4'];

    // Запрос на обновление автомобиля
    $sql = "UPDATE available_cars SET 
                dealer_id = ?, 
                trim_id = ?, 
                owners_count = ?, 
                year = ?, 
                mileage = ?, 
                transmission = ?, 
                price = ?, 
                engine_type = ?, 
                img_slide_1 = ?, 
                img_slide_2 = ?, 
                img_slide_3 = ?, 
                img_slide_4 = ?, 
                status = ? 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    // Проверка успешности подготовки запроса
    if ($stmt === false) {
        $message = "Ошибка подготовки запроса: " . $conn->error . "\nSQL-запрос: " . $sql;
    } else {
        // Привязка переменных к параметрам запроса
        $stmt->bind_param("iiiiissssssssi", 
            $dealer_id,     
            $trim_id,        
            $owners_count,
            $year,          
            $mileage,       
            $transmission,  // Убедитесь, что это строка
            $price,         
            $engine_type,   
            $img_slide_1,   
            $img_slide_2,  
            $img_slide_3,   
            $img_slide_4,   
            $status,        
            $car_id        
        );

        // Выполнение запроса
        if ($stmt->execute()) {
            $message = "Автомобиль успешно обновлён.";
        } else {
            $message = "Ошибка обновления автомобиля: " . $stmt->error;
        }

        // Закрытие подготовленного запроса
        $stmt->close();
    }
}

// Функция для загрузки изображения
function uploadImage($file, $car_id, $mileage, $year, $field_name, $car_model_id) {
    if (!empty($file['name'])) { // Проверяем, загружено ли изображение
        $targetDir = "../img/av_cars/";
        $fileType = pathinfo(basename($file["name"]), PATHINFO_EXTENSION);
        $newFileName = "{$car_id}_{$car_model_id}_{$field_name}_{$mileage}_{$year}_" . time() . ".{$fileType}";
        $targetFilePath = $targetDir . $newFileName;

        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            return $newFileName; // Возвращаем имя файла, если загрузка успешна
        } else {
            return null; // Возвращаем null, если произошла ошибка
        }
    }
    return null; // Если файл не загружен, возвращаем null
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Изменение автомобиля</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
</head>
<body>
    <?php require_once('../header.php'); ?>
    <div class="container ">
        <h1>Изменение автомобиля</h1>
        <a href="javascript:history.back();" class="blue">Вернуться</a>

        <?php
        // Вывод сообщения об успехе или ошибке
        if ($message) {
            echo "<div class='alert alert-success mt-3'>$message</div>";
        }
        ?>

        <form method="POST" action="" enctype="multipart/form-data" class="mt-3">
            <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
            <div class="form-group">
                <label for="car_model_id">Модель автомобиля</label>
                <select class="form-control" id="car_model_id" name="car_model_id">
                    <?php
                    // Выводим список моделей автомобилей
                    while ($row = $cars->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $car['car_id']) ? 'selected' : ''; ?>><?php echo $row['model']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="dealer_id">Дилер</label>
                <select class="form-control" id="dealer_id" name="dealer_id">
                    <?php while ($row = $dealers->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $car['dealer_id']) ? 'selected' : ''; ?>><?php echo $row['dealership_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="trim_id">Комплектация</label>
                <select class="form-control" id="trim_id" name="trim_id">
                    <?php while ($row = $trims->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $car['trim_id']) ? 'selected' : ''; ?>><?php echo $row['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="owners_count">Количество владельцев</label>
                <input type="number" class="form-control" id="owners_count" name="owners_count" value="<?php echo $car['owners_count']; ?>">
            </div>
            <div class="form-group">
                <label for="year">Год</label>
                <input type="number" class="form-control" id="year" name="year" value="<?php echo $car['year']; ?>">
            </div>
            <div class="form-group">
                <label for="mileage">Пробег</label>
                <input type="number" class="form-control" id="mileage" name="mileage" value="<?php echo $car['mileage']; ?>">
            </div>
            <div class="form-group">
                <label for="transmission">Тип трансмиссии</label>
                <select class="form-control" id="transmission" name="transmission">
                    <option value="Автоматическая" <?php echo ($car['transmission'] == 'Автоматическая') ? 'selected' : ''; ?>>Автоматическая</option>
                    <option value="Механическая" <?php echo ($car['transmission'] == 'Механическая') ? 'selected' : ''; ?>>Механическая</option>
                    <option value="Вариатор" <?php echo ($car['transmission'] == 'Вариатор') ? 'selected' : ''; ?>>Вариатор</option>
                </select>
            </div>
            <div class="form-group">
                <label for="price">Цена</label>
                <input type="number" class="form-control" id="price" name="price" value="<?php echo $car['price']; ?>">
            </div>
            <div class="form-group">
                <label for="engine_type">Тип двигателя</label>
                <select class="form-control" id="engine_type" name="engine_type">
                    <option value="Электроэнергия" <?php echo ($car['engine_type'] == 'Электроэнергия') ? 'selected' : ''; ?>>Электроэнергия</option>
                    <option value="Бензин" <?php echo ($car['engine_type'] == 'Бензин') ? 'selected' : ''; ?>>Бензин</option>
                    <option value="Дизель" <?php echo ($car['engine_type'] == 'Дизель') ? 'selected' : ''; ?>>Дизель</option>
                </select>
            </div>
            <div class="form-group">
                <label for="img_slide_1">Изображение 1</label>
                <input type="file" class="form-control" id="img_slide_1" name="img_slide_1">
            </div>
            <div class="form-group">
                <label for="img_slide_2">Изображение 2</label>
                <input type="file" class="form-control" id="img_slide_2" name="img_slide_2">
            </div>
            <div class="form-group">
                <label for="img_slide_3">Изображение 3</label>
                <input type="file" class="form-control" id="img_slide_3" name="img_slide_3">
            </div>
            <div class="form-group">
                <label for="img_slide_4">Изображение 4</label>
                <input type="file" class="form-control" id="img_slide_4" name="img_slide_4">
            </div>
            <div class="form-group">
                <label for="status">Статус</label>
                <select class="form-control" id="status" name="status">
                    <option value="В наличии" <?php echo ($car['status'] == 'В наличии') ? 'selected' : ''; ?>>В наличии</option>
                    <option value="Забронировано" <?php echo ($car['status'] == 'Забронировано') ? 'selected' : ''; ?>>Забронировано</option>
                    <option value="Куплено" <?php echo ($car['status'] == 'Куплено') ? 'selected' : ''; ?>>Куплено</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Сохранить изменения</button>
        </form>
    </div>
<?php require_once("../footer.php"); ?> 
</body>
</html>
