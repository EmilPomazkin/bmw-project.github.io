<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
    <style>
        .user-profile {
            margin-bottom: 20px;
        }
        .role-form {
            margin-top: 20px;
        }
    </style>
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

?>
<div class="container">
<?php
// Проверка наличия идентификатора пользователя в GET-запросе
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']); // Приведение к целому числу для безопасности

    // Обработка изменения роли
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role'])) {
        $new_role = $_POST['role'];

        // Обновление роли пользователя в базе данных
        $sql = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $new_role, $user_id);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success' role='alert'>
Роль пользователя успешно изменена.
</div>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>
Ошибка при изменении роли: " . $conn->error . "</div>";
        }

        $stmt->close();
    }

    // Получение информации о пользователе
    $sql = "SELECT * FROM users WHERE id = $user_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Получаем данные пользователя
        $user = $result->fetch_assoc();
        echo "<div class='user-profile'>
                <h1>Профиль пользователя: {$user['name']} {$user['surname']}</h1>
                  <a href='javascript:history.back();' class='blue'>Вернуться</a>
                <p><strong>ID:</strong> {$user['id']}</p>
                <p><strong>Фамилия:</strong> {$user['surname']}</p>
                <p><strong>Имя:</strong> {$user['name']}</p>
                <p><strong>Отчество:</strong> {$user['patronymic']}</p>
                <p><strong>Логин:</strong> {$user['login']}</p>
                <p><strong>Телефон:</strong> <a href='tel:{$user['phone']}' class='blue'>{$user['phone']}</a></p>
                <p><strong>Электронная почта:</strong> <a href='mailto:{$user['email']}' class='blue'>{$user['email']}</a></p>
                <p><strong>Роль:</strong> {$user['role']}</p>
                <p><strong>Дата создания:</strong> {$user['created_at']}</p>
              </div>";

        // Проверка роли для отображения формы изменения роли
        if ($user['role'] !== 'admin') {
            // Форма для изменения роли пользователя
            echo "<div class='role-form'>
                    <h2>Изменить роль пользователя</h2>
                    <form method='POST' action=''>
                        <input type='hidden' name='user_id' value='{$user['id']}'>
                        <label for='role'>Выберите роль:</label>
                        <select name='role' id='role'>
                            <option value='user'" . ($user['role'] == 'user' ? ' selected' : '') . ">Пользователь</option>
                            <option value='admin'" . ($user['role'] == 'admin' ? ' selected' : '') . ">Администратор</option>
                        </select>
                        <input type='submit' value='Изменить роль' class='btn btn-primary'>
                    </form>
                  </div>";
        } else {
            echo "<div class='role-form'>
                    <h2>Изменить роль пользователя</h2>
                    <p>Роль администратора изменить нельзя.</p>
                  </div>";
        }

        // Получение всех заявок пользователя из таблицы applications
        $applications_sql = "SELECT a.*, c.model AS car_model 
                             FROM applications a 
                             JOIN cars c ON a.car_id = c.id 
                             WHERE a.user_id = $user_id";
        $applications_result = $conn->query($applications_sql);

        // Вывод заявок из таблицы applications
        if ($applications_result->num_rows > 0) {
            echo "<a href='admin_application.php' class='blue'><h2>Заявки на автомобили</h2></a>";
            echo "<ul class='application_content'>";
            while ($application = $applications_result->fetch_assoc()) {
                echo "<li>
                        <strong>ID:</strong> {$application['id']}<br>
                        <strong>Модель автомобиля:</strong> {$application['car_model']}<br>
                        <strong>Дата заявки:</strong> {$application['application_date']}<br>
                        <strong>Статус:</strong> {$application['status']}
                      </li>";
            }
            echo "</ul>";
        } else {
            echo "<h2>Нет заявок на автомобили у пользователя.</h2>";
        }

        // Получение всех запросов пользователя из таблицы test_drive_requests
        $test_drive_sql = "SELECT t.*, c.model AS car_model, d.dealership_name 
                           FROM test_drive_requests t 
                           JOIN cars c ON t.car_id = c.id 
                           JOIN dealers d ON t.dealer_id = d.id 
                           WHERE t.user_id = $user_id";
        $test_drive_result = $conn->query($test_drive_sql);

        // Вывод запросов на тест-драйв
        if ($test_drive_result->num_rows > 0) {
            echo "<a href='admin_test_drive.php' class='blue'><h2>Запросы на тест-драйв</h2></a>";
            echo "<ul class='application_content'>";
            while ($test_drive = $test_drive_result->fetch_assoc()) {
                echo "<li>
                        <strong>ID:</strong> {$test_drive['id']}<br>
                        <strong>Модель автомобиля:</strong> {$test_drive['car_model']}<br>
                        <strong>Салон:</strong> {$test_drive['dealership_name']}<br>
                        <strong>Дата тест-драйва:</strong> {$test_drive['test_drive_date']}<br>
                        <strong>Статус:</strong> {$test_drive['status']}
                      </li>";
            }
            echo "</ul>";
        } else {
            echo "<h2>Нет запросов на тест-драйв у пользователя.</h2>";
        }

        // Получение всех записей пользователя из таблицы service_appointments
        $service_appointments_sql = "SELECT sa.*, c.model AS car_model, s.service_name, d.dealership_name 
                                     FROM service_appointments sa 
                                     JOIN cars c ON sa.car_id = c.id 
                                     JOIN car_services s ON sa.service_id = s.id 
                                     JOIN dealers d ON sa.dealer_id = d.id 
                                     WHERE sa.user_id = $user_id";
        $service_appointments_result = $conn->query($service_appointments_sql);

        // Вывод записей на ТО
        if ($service_appointments_result->num_rows > 0) {
            echo "<a href='admin_service_appointments.php' class='blue'><h2>Записи на техническое обслуживание</h2></a>";
            echo "<ul class='application_content'>";
            while ($appointment = $service_appointments_result->fetch_assoc()) {
                echo "<li>
                        <strong>ID:</strong> {$appointment['id']}<br>
                        <strong>Модель автомобиля:</strong> {$appointment['car_model']}<br>
                        <strong>Услуга:</strong> {$appointment['service_name']}<br>
                        <strong>Дилер:</strong> {$appointment['dealership_name']}<br>
                        <strong>Дата записи:</strong> {$appointment['appointment_date']}<br>
                        <strong>Время записи:</strong> {$appointment['appointment_time']}<br>
                        <strong>Статус:</strong> {$appointment['status']}
                      </li>";
            }
            echo "</ul>";
        } else {
            echo "<h2>Нет записей на техническое обслуживание у пользователя.</h2>";
        }

    } else {
        echo "<p>Пользователь не найден.</p>";
    }
} else {
    echo "<p>Идентификатор пользователя не указан.</p>";
}

$conn->close();
?>
</div>
<?php require_once("../footer.php"); ?> 
</body>
</html>
