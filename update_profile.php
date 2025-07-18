<?php
session_start();
include("db.php"); // Подключение к базе данных

// Инициализация переменной для сообщений
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получаем данные из формы
    $user_id = $_POST['user_id'];
    $surname = $_POST['surname'];
    $name = $_POST['name'];
    $patronymic = $_POST['patronymic'];
    $login = $_POST['login'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Проверка на наличие необходимых данных
    if (empty($surname) || empty($name) || empty($login) || empty($email) || empty($phone)) {
        $_SESSION['message'] = "<div class='alert alert-danger'>Пожалуйста, заполните все поля.</div>";
    } else {
        // Подготовка запроса на обновление данных пользователя
        $update_query = "UPDATE users SET surname = ?, name = ?, patronymic = ?, login = ?, email = ?, phone = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);

        // Проверка на ошибки при подготовке запроса
        if (!$stmt) {
            $_SESSION['message'] = "<div class='alert alert-danger'>Ошибка подготовки запроса: " . $conn->error . "</div>";
        } else {
            // Привязываем параметры
            $stmt->bind_param("ssssssi", $surname, $name, $patronymic, $login, $email, $phone, $user_id);

            // Выполнение запроса
            if ($stmt->execute()) {
                // Закрытие подготовленного выражения
                $stmt->close();
                
                // Установка сообщения об успешном обновлении
                $_SESSION['message'] = "<div class='alert alert-success'>Данные успешно обновлены.</div>";
                
                // Перенаправление на страницу профиля с якорем #edit
                header("Location: profile.php#edit");
                exit();
            } else {
                $_SESSION['message'] = "<div class='alert alert-danger'>Ошибка при обновлении данных: " . $stmt->error . "</div>";
            }
        }
    }

    // Закрытие соединения
    $conn->close();
} else {
    $_SESSION['message'] = "<div class='alert alert-danger'>Некорректный запрос.</div>";
}

// Вывод сообщения
if (!empty($message)) {
    echo $message;
}
?>
