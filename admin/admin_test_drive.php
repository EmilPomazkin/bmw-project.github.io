<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявки на тест-драйв</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
</head>
<body>
<?php

require_once('../header.php');
?>
<div class="container">
<h1>Заявки на cпец. предложения</h1>
<a href="admin_panel.php" class='blue'>Вернуться в админ-панель</a>
<div class="application_content">
    <?php
    session_start();
    include("../db.php");
// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../loginform.php'); // Перенаправляем на страницу входа
    exit(); // Завершаем выполнение скрипта
}

    // Обработка изменения статуса
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status'])) {
        $id = $_POST['application_id'];
        $status = $_POST['status'];
        $sql = "UPDATE test_drive_requests SET status='$status' WHERE id=$id";
        $conn->query($sql);
    }

    // Обработка удаления заявки
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['application_id'];
        $sql = "DELETE FROM test_drive_requests WHERE id=$id";
        $conn->query($sql);
    }

    // Получение заявок на тест-драйв
    $sql = "SELECT tdr.*, u.name AS user_name, u.surname AS user_surname, u.patronymic AS user_patronymic, u.id AS user_id, 
                   c.name AS city_name, d.dealership_name, ca.model AS car_model, ca.id AS car_id 
            FROM test_drive_requests tdr
            JOIN users u ON tdr.user_id = u.id
            JOIN cities c ON tdr.city_id = c.id
            JOIN dealers d ON tdr.dealer_id = d.id
            JOIN cars ca ON tdr.car_id = ca.id
            ORDER BY (status = 'ожидает') DESC, submission_time ASC";
            
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='application'>
                    <h3>Заявка ID: {$row['id']}</h3>
                    <p><strong>Пользователь:</strong> <a href='admin_user_profile.php?id={$row['user_id']}' class='blue'>{$row['user_name']} {$row['user_patronymic']} </a></p> <!-- Ссылка на профиль пользователя -->
                    <p><strong>Город:</strong> {$row['city_name']}</p>
                    <p><strong>Дилер:</strong>  <a href='edit_dealer.php?id={$row['dealer_id']}' class='blue'>{$row['dealership_name']}</a> </p>
                    <p><strong>Модель автомобиля:</strong> <a href='edit_available_car.php?id={$row['car_id']}' class='blue'>{$row['car_model']}</a></p> <!-- Ссылка на автомобиль -->
                    <p><strong>Дата тест-драйва:</strong> {$row['test_drive_date']}</p>
                    <p><strong>Статус:</strong> {$row['status']}</p>
                    <form method='POST' action=''>
                        <input type='hidden' name='application_id' value='{$row['id']}'>
                        <label for='status'>Изменить статус:</label>
                        <select name='status' id='status'>
                            <option value='ожидает'" . ($row['status'] == 'ожидает' ? ' selected' : '') . ">ожидает</option>
                            <option value='подтверждена'" . ($row['status'] == 'подтверждена' ? ' selected' : '') . ">подтверждена</option>
                            <option value='отклонена'" . ($row['status'] == 'отклонена' ? ' selected' : '') . ">отклонена</option>
                        </select>
                        <button type='submit' class='btn btn-primary'>Изменить</button>
                    </form> 
                    <form method='POST' action='' style='display:inline;'>
                        <input type='hidden' name='application_id' value='{$row['id']}'>
                        <input type='hidden' name='action' value='delete'>
                        <input type='submit' class='btn btn-danger' value='Удалить' onclick='return confirm(\"Вы уверены, что хотите удалить эту заявку?\");'>
                    </form>
                  </div>";
        }
    } else {
        echo "<p>Нет заявок на тест-драйв</p>";
    }

    $conn->close();
    ?>
</div>
</div>
<?php require_once("../footer.php"); ?> </body>
</html>
