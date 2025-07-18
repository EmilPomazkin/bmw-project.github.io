<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление автомобилями</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
    <link rel="stylesheet" href="../css/style.css"> <!-- Подключите ваш CSS файл -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Подключите Bootstrap -->
</head>
<body>
<?php

require_once('../header.php');
include("../db.php");

session_start();

$message = '';

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../loginform.php'); // Перенаправляем на страницу входа
    exit(); // Завершаем выполнение скрипта
}

// Обработка добавления нового автомобиля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_car'])) {
    // Получение данных из формы
    $model = $_POST['model'];
    $body_type = $_POST['body_type'];
    $seats = $_POST['seats'];
    $doors = $_POST['doors'];
    $engine_types = $_POST['engine_types'];
    $transmission = $_POST['transmission'];
    $dimensions = $_POST['dimensions'];
    $suspension = $_POST['suspension'];
    $performance = $_POST['performance'];
    $fuel_efficiency = $_POST['fuel_efficiency'];
    $safety = $_POST['safety'];
    $comfort_technology = $_POST['comfort_technology'];
    $additional_options = $_POST['additional_options'];
    $description = $_POST['description'];

    // Обработка загрузки изображений
    $img_logo = $_FILES['img_logo']['name'];
    $img_drawing = $_FILES['img_drawing']['name'];
    $img_slide1 = $_FILES['img_slide1']['name'];
    $img_slide2 = $_FILES['img_slide2']['name'];
    $img_slide3 = $_FILES['img_slide3']['name'];
    $img_slide4 = $_FILES['img_slide4']['name'];
    $img_inter1 = $_FILES['img_inter1']['name'];
    $img_inter2 = $_FILES['img_inter2']['name'];
    $img_inter3 = $_FILES['img_inter3']['name'];

    // Загрузка файлов на сервер
    move_uploaded_file($_FILES['img_logo']['tmp_name'], "../img/cars/" . $img_logo);
    move_uploaded_file($_FILES['img_drawing']['tmp_name'], "../img/cars/" . $img_drawing);
    move_uploaded_file($_FILES['img_slide1']['tmp_name'], "../img/cars/" . $img_slide1);
    move_uploaded_file($_FILES['img_slide2']['tmp_name'], "../img/cars/" . $img_slide2);
    move_uploaded_file($_FILES['img_slide3']['tmp_name'], "../img/cars/" . $img_slide3);
    move_uploaded_file($_FILES['img_slide4']['tmp_name'], "../img/cars/" . $img_slide4);
    move_uploaded_file($_FILES['img_inter1']['tmp_name'], "../img/cars/" . $img_inter1);
    move_uploaded_file($_FILES['img_inter2']['tmp_name'], "../img/cars/" . $img_inter2);
    move_uploaded_file($_FILES['img_inter3']['tmp_name'], "../img/cars/" . $img_inter3);

    // Вставка данных в базу данных
    $sql = "INSERT INTO cars (model, body_type, seats, doors, engine_types, transmission, dimensions, suspension, performance, fuel_efficiency, safety, comfort_technology, additional_options, description, img_logo, img_drawing, img_slide1, img_slide2, img_slide3, img_slide4, img_inter1, img_inter2, img_inter3) 
            VALUES ('$model', '$body_type', '$seats', '$doors', '$engine_types', '$transmission', '$dimensions', '$suspension', '$performance', '$fuel_efficiency', '$safety', '$comfort_technology', '$additional_options', '$description', '$img_logo', '$img_drawing', '$img_slide1', '$img_slide2', '$img_slide3', '$img_slide4', '$img_inter1', '$img_inter2', '$img_inter3')";

    if ($conn->query($sql) === TRUE) {
        $message = '<div class="alert alert-success" role="alert">Автомобиль успешно добавлен!</div>';
    } else {
        $message = '<div class="alert alert-danger" role="alert">Ошибка: ' . $conn->error . '</div>';
    }
}

// Обработка удаления автомобиля
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM cars WHERE id = $delete_id");
    $message = '<div class="alert alert-success" role="alert">Автомобиль успешно удалён!</div>';
}

// Получение списка автомобилей
$sql = "SELECT * FROM cars";
$cars_result = $conn->query($sql);
?>

<div class="container">
    <h1>Управление автомобилями</h1>
    <a href="admin_panel.php" class="blue">Вернуться в админ-панель</a>

    <!-- Сообщения об успехе или ошибках -->
    <?php if ($message): ?>
        <div class="mb-3">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Форма для добавления нового автомобиля -->
    <div class="mb-4">
        <h2>Добавить новый автомобиль</h2>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="add_car" value="1">
                <div class="form-group">
                    <label for="model">Модель:</label>
                    <input type="text" class="form-control" name="model" required>
                </div>
                <div class="form-group">
                    <label for="body_type">Тип кузова:</label>
                    <input type="text" class="form-control" name="body_type">
                </div>
                <div class="form-group">
                    <label for="seats">Количество мест:</label>
                    <input type="text" class="form-control" name="seats">
                </div>
                <div class="form-group">
                    <label for="doors">Количество дверей:</label>
                    <input type="number" class="form-control" name="doors">
                </div>
                <div class="form-group">
                    <label for="engine_types">Типы двигателей:</label>
                    <textarea class="form-control" name="engine_types"></textarea>
                </div>
                <div class="form-group">
                    <label for="transmission">Трансмиссия:</label>
                    <input type="text" class="form-control" name="transmission">
                </div>
                <div class="form-group">
                    <label for="dimensions">Габариты:</label>
                    <textarea class="form-control" name="dimensions"></textarea>
                </div>
                <div class="form-group">
                    <label for="suspension">Подвеска:</label>
                    <textarea class="form-control" name="suspension"></textarea>
                </div>
                <div class="form-group">
                    <label for="performance">Производительность:</label>
                    <textarea class="form-control" name="performance"></textarea>
                </div>
                <div class="form-group">
                    <label for="fuel_efficiency">Экономия топлива:</label>
                    <textarea class="form-control" name="fuel_efficiency"></textarea>
                </div>
                <div class="form-group">
                    <label for="safety">Безопасность:</label>
                    <textarea class="form-control" name="safety"></textarea>
                </div>
                <div class="form-group">
                    <label for="comfort_technology">Комфорт и технологии:</label>
                    <textarea class="form-control" name="comfort_technology"></textarea>
                </div>
                <div class="form-group">
                    <label for="additional_options">Дополнительные опции:</label>
                    <textarea class="form-control" name="additional_options"></textarea>
                </div>
                <div class="form-group">
                    <label for="description">Описание:</label>
                    <textarea class="form-control" name="description"></textarea>
                </div>
                <div class="form-group">
                    <label for="img_logo">Логотип:</label>
                    <input type="file" class="form-control" name="img_logo" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="img_drawing">Рисунок:</label>
                    <input type="file" class="form-control" name="img_drawing" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="img_slide1">Слайд 1:</label>
                    <input type="file" class="form-control" name="img_slide1" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="img_slide2">Слайд 2:</label>
                    <input type="file" class="form-control" name="img_slide2" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="img_slide3">Слайд 3:</label>
                    <input type="file" class="form-control" name="img_slide3" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="img_slide4">Слайд 4:</label>
                    <input type="file" class="form-control" name="img_slide4" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="img_inter1">Интерьер 1:</label>
                    <input type="file" class="form-control" name="img_inter1" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="img_inter2">Интерьер 2:</label>
                    <input type="file" class="form-control" name="img_inter2" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="img_inter3">Интерьер 3:</label>
                    <input type="file" class="form-control" name="img_inter3" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary mt-3 mb-3">Добавить автомобиль</button>
            </form>
        </div>
    </div>

    <!-- Список автомобилей -->
    <div class="card">
        <div class="card-header">
            <h2>Существующие автомобили</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <?php while ($car = $cars_result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="car-card card mb-4">
                        <img src="../img/cars/<?php echo $car['img_logo']; ?>" class="card-img-top" alt="Логотип <?php echo $car['model']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $car['model']; ?></h5>
                            <p class="card-text"><strong>Тип кузова:</strong> <?php echo $car['body_type']; ?></p>
                            <p class="card-text"><strong>Количество мест:</strong> <?php echo $car['seats']; ?></p>
                            <p class="card-text"><strong>Количество дверей:</strong> <?php echo $car['doors']; ?></p>
                            <a href="edit_car.php?id=<?php echo $car['id']; ?>" class="btn btn-primary">Редактировать</a>
                            <a href="?delete_id=<?php echo $car['id']; ?>" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить этот автомобиль?');">Удалить</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once("../footer.php"); ?> </body>
</html>
