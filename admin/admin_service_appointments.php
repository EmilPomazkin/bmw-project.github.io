<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Записи на сервис</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
    
</head>
<body>
<?php
session_start();
include("../header.php");
include("../db.php"); // Подключение к базе данных

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../loginform.php'); // Перенаправляем на страницу входа
    exit(); // Завершаем выполнение скрипта
}

// Обработка изменения статуса
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status'])) {
    $id = $_POST['appointment_id'];
    $status = $_POST['status'];
    $sql = "UPDATE service_appointments SET status='$status' WHERE id=$id";
    $conn->query($sql);
}

// Обработка удаления записи
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = $_POST['appointment_id'];
    $sql = "DELETE FROM service_appointments WHERE id=$id";
    $conn->query($sql);
}

// Получение записей на сервис
$sql = "SELECT sa.*, u.name AS user_name, u.patronymic AS user_patronymic, c.model AS car_model, s.service_name, d.dealership_name 
        FROM service_appointments sa
        JOIN users u ON sa.user_id = u.id
        JOIN cars c ON sa.car_id = c.id
        JOIN car_services s ON sa.service_id = s.id
        JOIN dealers d ON sa.dealer_id = d.id
        ORDER BY sa.appointment_date ASC, sa.appointment_time ASC";

$result = $conn->query($sql);
?>

<div class="container">
    <h1>Записи на сервис</h1>
    <a href="admin_panel.php" class='blue'>Вернуться в админ-панель</a>
    <div class="application_content">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='application'>
                <h3>Запись №: {$row['id']}</h3>
                <p><strong>Пользователь:</strong> <a href='admin_user_profile.php?id={$row['user_id']}' class='blue'>{$row['user_name']} {$row['user_patronymic']}</a></p>
                <p><strong>Модель автомобиля:</strong> <a href='edit_car.php?id={$row['car_id']}' class='blue'>{$row['car_model']}</a></p>
                <p><strong>Услуга:</strong> <a href='edit_service.php?id={$row['service_id']}' class='blue'>{$row['service_name']}</a></p>
                <p><strong>Дилер:</strong> <a href='edit_dealer.php?id={$row['dealer_id']}' class='blue'>{$row['dealership_name']}</a></p>
                <p><strong>VIN номер:</strong> {$row['vin_number']}</p>
                <p><strong>Год:</strong> {$row['year']}</p>
                <p><strong>Дата записи:</strong> {$row['appointment_date']}</p>
                <p><strong>Время записи:</strong> {$row['appointment_time']}</p>
                <p><strong>Статус:</strong> {$row['status']}</p>
                <form method='POST' action=''>
                    <input type='hidden' name='appointment_id' value='{$row['id']}'>
                    <label for='status'>Изменить статус:</label>
                    <select name='status' id='status'>
                        <option value='Ожидает'" . ($row['status'] == 'Ожидает' ? ' selected' : '') . ">Ожидает</option>
                        <option value='Подтверждена'" . ($row['status'] == 'Подтверждена' ? ' selected' : '') . ">Подтверждена</option>
                        <option value='Отклонена'" . ($row['status'] == 'Отклонена' ? ' selected' : '') . ">Отклонена</option>
                    </select>
                    <button type='submit' class='btn btn-primary'>Изменить</button>
                </form> 
                <form method='POST' action='' style='display:inline;'>
                    <input type='hidden' name='appointment_id' value='{$row['id']}'>
                    <input type='hidden' name='action' value='delete'>
                    <input type='submit' class='btn btn-danger' value='Удалить' onclick='return confirm(\"Вы уверены, что хотите удалить эту запись?\");'>
                </form>
              </div>";
        
            }
        } else {
            echo "<p>Нет записей на сервис</p>";
        }

        $conn->close();
        ?>
    </div>
</div>

<?php require_once("../footer.php"); ?>
</body>
</html>
