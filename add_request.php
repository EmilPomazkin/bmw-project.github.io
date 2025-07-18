<?php
session_start();
include("db.php");


// Массив для хранения ошибок
$errors = [];

// Проверка, была ли отправлена форма
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из формы
    $name = $_POST['name'];
    $phone = $_POST['phone'];

    // Подготовка SQL-запроса
    $sql = "INSERT INTO requests_for_special (name, phone) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    // Проверка на успешность подготовки запроса
    if ($stmt === false) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }

    // Привязка параметров
    $stmt->bind_param("ss", $name, $phone);

    // Выполнение запроса
    if ($stmt->execute()) {
        $_SESSION['registration_success'] = "Заявка успешно добавлена!";
        header("Location: index.php"); // Перенаправление на страницу успеха
        exit;
    } else {
        $errors[] = "Ошибка выполнения запроса: " . $stmt->error;
    }

    // Закрыть подготовленный запрос
    $stmt->close();
}

// Закрыть соединение с базой данных
$conn->close();
?>