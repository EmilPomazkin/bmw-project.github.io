<?php
session_start();
include("../db.php");
// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../loginform.php'); // Перенаправляем на страницу входа
    exit(); // Завершаем выполнение скрипта
}

// Обработка изменения статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Изменение статуса заявки
    if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
        $application_id = $_POST['application_id'];
        $status = $_POST['status'];

        // Подготовка и выполнение запроса на обновление статуса
        $sql = "UPDATE applications SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $application_id);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Статус успешно обновлён.</p>";
        } else {
            echo "<p style='color: red;'>Ошибка обновления статуса: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }

    // Удаление заявки
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $application_id = $_POST['application_id'];

        // Подготовка и выполнение запроса на удаление заявки
        $sql = "DELETE FROM applications WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $application_id);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Заявка успешно удалена.</p>";
        } else {
            echo "<p style='color: red;'>Ошибка удаления заявки: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

// Выполнение запроса для получения заявок (новые заявки в первой очереди)
$sql = "SELECT a.id AS application_id, 
               a.application_date, 
               a.status, 
               u.id AS user_id, 
               u.name AS user_name, 
               u.patronymic AS user_patronymic, 
               u.phone AS user_phone, 
               u.email AS user_email, 
               c.model AS car_model,
               c.id AS car_id
        FROM applications a
        JOIN users u ON a.user_id = u.id
        JOIN available_cars ac ON a.car_id = ac.id
        JOIN cars c ON ac.car_id = c.id
        ORDER BY (a.status = 'ожидает') DESC, a.application_date ASC"; // Сортировка по дате заявки

$result = $conn->query($sql);

// Проверка на ошибки выполнения запроса
if (!$result) {
    die("Ошибка выполнения запроса: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление заявками</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
</head>
<body>
    <?php require_once("../header.php")?>
    <div class="container">
        <h1>Заявки на автомобили</h1>
        <a href="admin_panel.php" class='blue'>Вернуться в админ-панель</a>
        <div class="application_content">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='application'>
                            <h3>Заявка ID: {$row['application_id']}</h3>
                            <p><strong>Пользователь:</strong> <a href='admin_user_profile.php?id={$row['user_id']}' class='blue'>{$row['user_name']} {$row['user_patronymic']}</a></p> <!-- Ссылка на профиль пользователя -->
                            <p><strong>Телефон:</strong> <a href='tel:{$row['user_phone']}' class='blue'>{$row['user_phone']}</a></p>
                            <p><strong>Электронная почта:</strong> <a href='mailto:{$row['user_email']}' class='blue'>{$row['user_email']}</a></p>
                            <p><strong>Автомобиль:</strong> <a href='edit_car.php?id={$row['car_id']}' class='blue'>{$row['car_model']}</a></p> <!-- Ссылка на редактирование автомобиля -->
                            <p><strong>Дата заявки:</strong> {$row['application_date']}</p>
                            <p><strong>Статус:</strong> {$row['status']}</p>
                            <form method='POST' action=''>
                                <input type='hidden' name='application_id' value='{$row['application_id']}'>
                                <label for='status'>Изменить статус:</label>
                                <select name='status' id='status'>
                                    <option value='ожидает' " . ($row['status'] == 'ожидает' ? 'selected' : '') . ">ожидает</option>
                                    <option value='одобрена' " . ($row['status'] == 'одобрена' ? 'selected' : '') . ">одобрена</option>
                                    <option value='отклонена' " . ($row['status'] == 'отклонена' ? 'selected' : '') . ">отклонена</option>
                                </select>
                                <input type='hidden' name='action' value='update_status'>
                                <button type='submit' class='btn btn-primary'>Изменить</button>
                            </form> 
                            <form method='POST' action='' style='display:inline;'>
                                <input type='hidden' name='application_id' value='{$row['application_id']}'>
                                <input type='hidden' name='action' value='delete'>
                                <input type='submit' class='btn btn-danger' value='Удалить' onclick='return confirm(\"Вы уверены, что хотите удалить эту заявку?\");'>
                            </form>
                          </div>";
                }
            } else {
                echo "<p>Нет заявок</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>
<?php require_once("../footer.php"); ?> </body>
</html>
