<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование автомобиля</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
</head>
<body>
<?php


require_once('../header.php');
include("../db.php");

session_start();
// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../loginform.php'); // Перенаправляем на страницу входа
    exit(); // Завершаем выполнение скрипта
}
// Проверка наличия идентификатора автомобиля
if (isset($_GET['id'])) {
    $car_id = intval($_GET['id']);

    // Получение информации об автомобиле
    $sql = "SELECT * FROM cars WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $car_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $car = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>Автомобиль не найден.</div>";
        exit;
    }

    // Обработка изменения автомобиля
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_car'])) {
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

        // Формирование имен файлов
        $model_slug = strtolower(str_replace(' ', '_', $model));
        $img_logo = $_FILES['img_logo']['name'] ? "{$car_id}_{$model_slug}_img_logo." . pathinfo($_FILES['img_logo']['name'], PATHINFO_EXTENSION) : $car['img_logo'];
        $img_drawing = $_FILES['img_drawing']['name'] ? "{$car_id}_{$model_slug}_img_drawing." . pathinfo($_FILES['img_drawing']['name'], PATHINFO_EXTENSION) : $car['img_drawing'];
        $img_slide1 = $_FILES['img_slide1']['name'] ? "{$car_id}_{$model_slug}_img_slide1." . pathinfo($_FILES['img_slide1']['name'], PATHINFO_EXTENSION) : $car['img_slide1'];
        $img_slide2 = $_FILES['img_slide2']['name'] ? "{$car_id}_{$model_slug}_img_slide2." . pathinfo($_FILES['img_slide2']['name'], PATHINFO_EXTENSION) : $car['img_slide2'];
        $img_slide3 = $_FILES['img_slide3']['name'] ? "{$car_id}_{$model_slug}_img_slide3." . pathinfo($_FILES['img_slide3']['name'], PATHINFO_EXTENSION) : $car['img_slide3'];
        $img_slide4 = $_FILES['img_slide4']['name'] ? "{$car_id}_{$model_slug}_img_slide4." . pathinfo($_FILES['img_slide4']['name'], PATHINFO_EXTENSION) : $car['img_slide4'];
        $img_inter1 = $_FILES['img_inter1']['name'] ? "{$car_id}_{$model_slug}_img_inter1." . pathinfo($_FILES['img_inter1']['name'], PATHINFO_EXTENSION) : $car['img_inter1'];
        $img_inter2 = $_FILES['img_inter2']['name'] ? "{$car_id}_{$model_slug}_img_inter2." . pathinfo($_FILES['img_inter2']['name'], PATHINFO_EXTENSION) : $car['img_inter2'];
        $img_inter3 = $_FILES['img_inter3']['name'] ? "{$car_id}_{$model_slug}_img_inter3." . pathinfo($_FILES['img_inter3']['name'], PATHINFO_EXTENSION) : $car['img_inter3'];

        // Папка для загрузки изображений
        $upload_dir = '../img/cars/';
        if ($_FILES['img_logo']['name']) move_uploaded_file($_FILES['img_logo']['tmp_name'], $upload_dir . $img_logo);
        if ($_FILES['img_drawing']['name']) move_uploaded_file($_FILES['img_drawing']['tmp_name'], $upload_dir . $img_drawing);
        if ($_FILES['img_slide1']['name']) move_uploaded_file($_FILES['img_slide1']['tmp_name'], $upload_dir . $img_slide1);
        if ($_FILES['img_slide2']['name']) move_uploaded_file($_FILES['img_slide2']['tmp_name'], $upload_dir . $img_slide2);
        if ($_FILES['img_slide3']['name']) move_uploaded_file($_FILES['img_slide3']['tmp_name'], $upload_dir . $img_slide3);
        if ($_FILES['img_slide4']['name']) move_uploaded_file($_FILES['img_slide4']['tmp_name'], $upload_dir . $img_slide4);
        if ($_FILES['img_inter1']['name']) move_uploaded_file($_FILES['img_inter1']['tmp_name'], $upload_dir . $img_inter1);
        if ($_FILES['img_inter2']['name']) move_uploaded_file($_FILES['img_inter2']['tmp_name'], $upload_dir . $img_inter2);
        if ($_FILES['img_inter3']['name']) move_uploaded_file($_FILES['img_inter3']['tmp_name'], $upload_dir . $img_inter3);

        // SQL-запрос для обновления автомобиля
        $update_sql = "UPDATE cars SET model = ?, body_type = ?, seats = ?, doors = ?, engine_types = ?, transmission = ?, dimensions = ?, suspension = ?, performance = ?, fuel_efficiency = ?, safety = ?, comfort_technology = ?, additional_options = ?, description = ?, img_logo = ?, img_drawing = ?, img_slide1 = ?, img_slide2 = ?, img_slide3 = ?, img_slide4 = ?, img_inter1 = ?, img_inter2 = ?, img_inter3 = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('sssssssssssssssssssssssi', $model, $body_type, $seats, $doors, $engine_types, $transmission, $dimensions, $suspension, $performance, $fuel_efficiency, $safety, $comfort_technology, $additional_options, $description, $img_logo, $img_drawing, $img_slide1, $img_slide2, $img_slide3, $img_slide4, $img_inter1, $img_inter2, $img_inter3, $car_id);

        if ($update_stmt->execute()) {
            echo "<div class='alert alert-success'>Автомобиль успешно изменен.</div>";
        } else {
            echo "<div class='alert alert-danger'>Ошибка при изменении автомобиля: " . $conn->error . "</div>";
        }
        $update_stmt->close();
    }
} else {
    echo "<div class='alert alert-danger'>Идентификатор автомобиля не указан.</div>";
    exit;
}
?>

<div class="container">
    <h1>Редактировать автомобиль</h1>
    <a href="javascript:history.back();" class="blue">Вернуться</a>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="edit_car" value="1">
        <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">

        <div class="form-group">
            <label for="model">Модель:</label>
            <input type="text" class="form-control" name="model" value="<?php echo htmlspecialchars($car['model']); ?>" required>
        </div>
        <div class="form-group">
            <label for="body_type">Тип кузова:</label>
            <input type="text" class="form-control" name="body_type" value="<?php echo htmlspecialchars($car['body_type']); ?>">
        </div>
        <div class="form-group">
            <label for="seats">Количество мест:</label>
            <input type="text" class="form-control" name="seats" value="<?php echo htmlspecialchars($car['seats']); ?>">
        </div>
        <div class="form-group">
            <label for="doors">Количество дверей:</label>
            <input type="number" class="form-control" name="doors" value="<?php echo htmlspecialchars($car['doors']); ?>">
        </div>
        <div class="form-group">
            <label for="engine_types">Типы двигателей:</label>
            <textarea class="form-control" name="engine_types"><?php echo htmlspecialchars($car['engine_types']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="transmission">Трансмиссия:</label>
            <input type="text" class="form-control" name="transmission" value="<?php echo htmlspecialchars($car['transmission']); ?>">
        </div>
        <div class="form-group">
            <label for="dimensions">Габариты:</label>
            <textarea class="form-control" name="dimensions"><?php echo htmlspecialchars($car['dimensions']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="suspension">Подвеска:</label>
            <textarea class="form-control" name="suspension"><?php echo htmlspecialchars($car['suspension']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="performance">Производительность:</label>
            <textarea class="form-control" name="performance"><?php echo htmlspecialchars($car['performance']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="fuel_efficiency">Экономия топлива:</label>
            <textarea class="form-control" name="fuel_efficiency"><?php echo htmlspecialchars($car['fuel_efficiency']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="safety">Безопасность:</label>
            <textarea class="form-control" name="safety"><?php echo htmlspecialchars($car['safety']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="comfort_technology">Комфорт и технологии:</label>
            <textarea class="form-control" name="comfort_technology"><?php echo htmlspecialchars($car['comfort_technology']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="additional_options">Дополнительные опции:</label>
            <textarea class="form-control" name="additional_options"><?php echo htmlspecialchars($car['additional_options']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="description">Описание:</label>
            <textarea class="form-control" name="description"><?php echo htmlspecialchars($car['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="img_logo">Логотип:</label><br>
            <img src="../img/cars/<?php echo htmlspecialchars($car['img_logo']); ?>" class="img-preview" alt="Логотип">
            <input type="file"  class="form-control" id="formFile" name="img_logo">
        </div>
        <div class="form-group">
            <label for="img_drawing">Рисунок:</label><br>
            <img src="../img/cars/<?php echo htmlspecialchars($car['img_drawing']); ?>" class="img-preview" alt="Рисунок">
            <input type="file"  class="form-control" id="formFile" name="img_drawing">
        </div>
        <div class="form-group">
            <label for="img_slide1">Слайд 1:</label><br>
            <img src="../img/cars/<?php echo htmlspecialchars($car['img_slide1']); ?>" class="img-preview" alt="Слайд 1">
            <input type="file"  class="form-control" id="formFile" name="img_slide1">
        </div>
        <div class="form-group">
            <label for="img_slide2">Слайд 2:</label><br>
            <img src="../img/cars/<?php echo htmlspecialchars($car['img_slide2']); ?>" class="img-preview" alt="Слайд 2">
            <input type="file"  class="form-control" id="formFile" name="img_slide2">
        </div>
        <div class="form-group">
            <label for="img_slide3">Слайд 3:</label><br>
            <img src="../img/cars/<?php echo htmlspecialchars($car['img_slide3']); ?>" class="img-preview" alt="Слайд 3">
            <input type="file"  class="form-control" id="formFile" name="img_slide3">
        </div>
        <div class="form-group">
            <label for="img_slide4">Слайд 4:</label><br>
            <img src="../img/cars/<?php echo htmlspecialchars($car['img_slide4']); ?>" class="img-preview" alt="Слайд 4">
            <input type="file"  class="form-control" id="formFile" name="img_slide4">
        </div>
        <div class="form-group">
            <label for="img_inter1">Интерьер 1:</label><br>
            <img src="../img/cars/<?php echo htmlspecialchars($car['img_inter1']); ?>" class="img-preview" alt="Интерьер 1">
            <input type="file"  class="form-control" id="formFile" name="img_inter1">
        </div>
        <div class="form-group">
            <label for="img_inter2">Интерьер 2:</label><br>
            <img src="../img/cars/<?php echo htmlspecialchars($car['img_inter2']); ?>" class="img-preview" alt="Интерьер 2">
            <input type="file"  class="form-control" id="formFile" name="img_inter2">
        </div>
        <div class="form-group">
            <label for="img_inter3">Интерьер 3:</label><br>
            <img src="../img/cars/<?php echo htmlspecialchars($car['img_inter3']); ?>" class="img-preview" alt="Интерьер 3">
            <input type="file"  class="form-control" id="formFile" name="img_inter3">
        </div>

        <button type="submit" class="btn btn-primary mt-3 mb-3">Сохранить изменения</button>
    </form>
</div>

<?php require_once("../footer.php"); ?> </body>
</html>
