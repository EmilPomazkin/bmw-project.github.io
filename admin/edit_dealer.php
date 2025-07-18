<?php
session_start();
include("../db.php"); // Подключение к базе данных
// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../loginform.php'); // Перенаправляем на страницу входа
    exit(); // Завершаем выполнение скрипта
}

// Проверка, передан ли ID дилера
if (!isset($_GET['id'])) {
    header("Location: admin_dealers.php");
    exit();
}

$dealer_id = intval($_GET['id']);

// Получение информации о дилере
$sql = "SELECT * FROM dealers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dealer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dealers.php");
    exit();
}

$dealer = $result->fetch_assoc();
$message = ''; // Переменная для сообщения

// Обработка изменения дилера
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dealership_name = $_POST['dealership_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $city_id = $_POST['city_id'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Запрос на обновление дилера
    $sql = "UPDATE dealers SET dealership_name = ?, phone = ?, email = ?, address = ?, city_id = ?, latitude = ?, longitude = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssdddi", $dealership_name, $phone, $email, $address, $city_id, $latitude, $longitude, $dealer_id);

    if ($stmt->execute()) {
        $message = "Дилер успешно изменён."; // Успешное сообщение
    } else {
        $message = "Ошибка при изменении дилера."; // Ошибка
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование дилера</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
</head>
<body>
<?php
require_once('../header.php');
?>
<div class="container">
<h1>Изменение дилера</h1>
<a href="javascript:history.back();" class="blue">Вернуться</a>
    <?php
    // Вывод сообщения об успехе или ошибке
    if ($message) {
        echo "<div class='alert alert-success mt-3'>$message</div>";
    }
    ?>

    <form method="POST" action="" class="mt-3">
        <div class="form-group">
            <label for="dealership_name">Название дилера</label>
            <input type="text" class="form-control" id="dealership_name" name="dealership_name" value="<?php echo $dealer['dealership_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Телефон</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $dealer['phone']; ?>">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $dealer['email']; ?>">
        </div>
        <div class="form-group">
            <label for="address">Адрес</label>
            <input type="text" class="form-control" id="address" name="address" value="<?php echo $dealer['address']; ?>">
        </div>
        <div class="form-group">
            <label for="city_id">Город</label>
            <select class="form-control" id="city_id" name="city_id">
                <?php
                // Получение списка городов для выпадающего списка
                $cities = $conn->query("SELECT id, name FROM cities");
                while ($city = $cities->fetch_assoc()) {
                    $selected = ($city['id'] == $dealer['city_id']) ? 'selected' : '';
                    echo "<option value='{$city['id']}' $selected>{$city['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="latitude">Широта</label>
            <input type="text" class="form-control" id="latitude" name="latitude" value="<?php echo $dealer['latitude']; ?>">
        </div>
        <div class="form-group">
            <label for="longitude">Долгота</label>
            <input type="text" class="form-control" id="longitude" name="longitude" value="<?php echo $dealer['longitude']; ?>">
        </div>
        <button type="submit" class="btn btn-primary  mt-3">Сохранить изменения</button>
    </form>
</div>


<?php require_once("../footer.php"); ?> </body>
</html>
