<?php // Подключение к базе данных
$servername = "localhost";
$username = "root"; // замените на ваше имя пользователя
$password = ""; // замените на ваш пароль
$dbname = "bmw"; // замените на название вашей базы данных

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}
?>