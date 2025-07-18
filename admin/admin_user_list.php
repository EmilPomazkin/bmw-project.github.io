<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список пользователей</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
</head>
<body>
<?php

require_once('../header.php'); // Подключение заголовка
session_start();
include("../db.php"); // Подключение к базе данных
// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../loginform.php'); // Перенаправляем на страницу входа
    exit(); // Завершаем выполнение скрипта
}

// Обработка удаления пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    
    // Запрос на удаление пользователя
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Пользователь успешно удалён.";
        $_SESSION['msg_type'] = "success"; // Успешное сообщение
    } else {
        $_SESSION['message'] = "Ошибка при удалении пользователя.";
        $_SESSION['msg_type'] = "danger"; // Ошибка
    }

    $stmt->close();
}

// Получение списка пользователей
$sql = "SELECT id, surname, name, patronymic, login, email, phone, role FROM users";
$result = $conn->query($sql);

?>

<div class="container mt-4">
    <h1>Пользователи</h1>
    <a href='admin_panel.php' class='blue'>Вернуться в админ-панель</a>

    <?php
    // Вывод сообщения об успехе или ошибке
    if (isset($_SESSION['message'])) {
        echo "<div class='alert alert-{$_SESSION['msg_type']} mt-3'>{$_SESSION['message']}</div>";
        unset($_SESSION['message']); // Удаление сообщения после отображения
        unset($_SESSION['msg_type']); // Удаление типа сообщения после отображения
    }
    ?>

    <div class='application_content'>
    <?php
    if ($result->num_rows > 0) {
        // Вывод данных пользователей
        while ($row = $result->fetch_assoc()) {
            echo "<div class='user-card border mb-3 mt-3'>
                    <h3>{$row['surname']} {$row['name']} {$row['patronymic']}</h3>
                    <p><strong>Логин:</strong> {$row['login']}</p>
                    <p><strong>Email:</strong> {$row['email']}</p>
                    <p><strong>Телефон:</strong> {$row['phone']}</p>
                    <p><strong>Роль:</strong> {$row['role']}</p>
                    <div class='actions'>
                        <a href='admin_user_profile.php?id={$row['id']}' class='btn btn-primary'>Профиль</a>";

            // Условие для проверки роли перед отображением кнопки удаления
            if ($row['role'] !== 'admin') {
                echo "<form method='POST' action='' style='display:inline;'>
                        <input type='hidden' name='user_id' value='{$row['id']}'>
                        <input type='submit' value='Удалить' onclick='return confirm(\"Вы уверены, что хотите удалить этого пользователя?\");' class='btn btn-danger'>
                      </form>";
            } else {
                echo "<span class='btn btn-danger' style='pointer-events: none; opacity: 0.5; margin-left:5px'>Удалить</span>";
            }

            echo "    </div>
                  </div>";
        }
    } else {
        echo "<p>Нет пользователей</p>";
    }

    $conn->close();
    ?>
    </div>
</div>

<?php require_once("../footer.php"); ?> </body>
</html>
