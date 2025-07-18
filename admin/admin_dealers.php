<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление дилерами</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
</head>
<body>
    <?php 
require_once('../header.php'); ?>
<?php
session_start();
include("../db.php"); // Подключение к базе данных
// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../loginform.php'); // Перенаправляем на страницу входа
    exit(); // Завершаем выполнение скрипта
}
// Обработка удаления дилера
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dealer_id'])) {
    $dealer_id = intval($_POST['dealer_id']);
    
    // Запрос на удаление дилера
    $sql = "DELETE FROM dealers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $dealer_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Дилер успешно удалён.";
        $_SESSION['msg_type'] = "success"; // Успешное сообщение
    } else {
        $_SESSION['message'] = "Ошибка при удалении дилера.";
        $_SESSION['msg_type'] = "danger"; // Ошибка
    }

    $stmt->close();
}

// Обработка добавления нового дилера
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dealership_name'])) {
    $dealership_name = $_POST['dealership_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $city_id = $_POST['city_id'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Запрос на добавление нового дилера
    $sql = "INSERT INTO dealers (dealership_name, phone, email, address, city_id, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssdds", $dealership_name, $phone, $email, $address, $city_id, $latitude, $longitude);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Дилер успешно добавлен.";
        $_SESSION['msg_type'] = "success"; // Успешное сообщение
    } else {
        $_SESSION['message'] = "Ошибка при добавлении дилера.";
        $_SESSION['msg_type'] = "danger"; // Ошибка
    }

    $stmt->close();
}

// Получение списка дилеров
$sql = "SELECT d.id, d.dealership_name, d.phone, d.email, d.address, c.name AS city FROM dealers d LEFT JOIN cities c ON d.city_id = c.id";
$result = $conn->query($sql);
?>

<div class="container">
    <h1>Управление дилерами</h1>
    <a href="admin_panel.php" class="blue">Вернуться в админ-панель</a>
    <?php
    // Вывод сообщения об успехе или ошибке
    if (isset($_SESSION['message'])) {
        echo "<div class='alert alert-{$_SESSION['msg_type']} mt-3'>{$_SESSION['message']}</div>";
        unset($_SESSION['message']); // Удаление сообщения после отображения
        unset($_SESSION['msg_type']); // Удаление типа сообщения после отображения
    }
    ?>

    <h2 class="mt-3">Добавить нового дилера</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="dealership_name">Название дилера</label>
            <input type="text" class="form-control" id="dealership_name" name="dealership_name" required>
        </div>
        <div class="form-group">
            <label for="phone">Телефон</label>
            <input type="text" class="form-control" id="phone" name="phone">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email">
        </div>
        <div class="form-group">
            <label for="address">Адрес</label>
            <input type="text" class="form-control" id="address" name="address">
        </div>
        <div class="form-group">
            <label for="city_id">Город</label>
            <select class="form-control" id="city_id" name="city_id">
                <?php
                // Получение списка городов для выпадающего списка
                $cities = $conn->query("SELECT id, name FROM cities");
                while ($city = $cities->fetch_assoc()) {
                    echo "<option value='{$city['id']}'>{$city['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="latitude">Широта</label>
            <input type="text" class="form-control" id="latitude" name="latitude">
        </div>
        <div class="form-group">
            <label for="longitude">Долгота</label>
            <input type="text" class="form-control" id="longitude" name="longitude">
        </div>
        <button type="submit" class="btn btn-primary mt-3">Добавить дилера</button>
    </form>

    <h2 class="mt-4">Список дилеров</h2>
    <div class="row">
    <?php
    if ($result->num_rows > 0) {
        // Вывод данных дилеров в виде карточек
        while ($row = $result->fetch_assoc()) {
            echo "<div class='col-md-4 mb-3'>
                    <div class='card'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$row['dealership_name']}</h5>
                            <p class='card-text'>Телефон: {$row['phone']}</p>
                            <p class='card-text'>Email: {$row['email']}</p>
                            <p class='card-text'>Адрес: {$row['address']}</p>
                            <p class='card-text'>Город: {$row['city']}</p>    
                            <a href='edit_dealer.php?id={$row['id']}' class='btn btn-primary'>Изменить</a>
                            <form method='POST' action='' style='display:inline;'>
                                <input type='hidden' name='dealer_id' value='{$row['id']}'>
                                <input type='submit' value='Удалить' onclick='return confirm(\"Вы уверены, что хотите удалить этого дилера?\");' class='btn btn-danger'>
                            </form>
                        
                        </div>
                    </div>
                  </div>";
        }
    } else {
        echo "<p>Нет дилеров</p>";
    }
    ?>
    </div>
</div>

<?php require_once("../footer.php"); ?> </body>
</html>
