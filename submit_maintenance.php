<?php
session_start();
include("db.php"); 

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получение данных из формы
$user_id = $_POST['user_id'];
$car_id = $_POST['car_id'];
$service_id = $_POST['service_id'];
$city_id = $_POST['city_id'];
$dealer_id = $_POST['dealer_id'];
$vin_number = $_POST['vin_number'];
$appointment_datetime = $_POST['appointment_datetime'];

// SQL-запрос для вставки данных
$sql = "INSERT INTO service_appointments (user_id, car_id, service_id, city_id, dealer_id, vin_number, appointment_datetime) VALUES (?, ?, ?, ?, ?, ?, ?)";

// Подготовка запроса
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Ошибка подготовки запроса: " . $conn->error);
}

// Исправленная строка: "iiissss" вместо "iiisssss" (7 параметров вместо 8)
// i - integer, s - string
$stmt->bind_param("iiissss", $user_id, $car_id, $service_id, $city_id, $dealer_id, $vin_number, $appointment_datetime);

if ($stmt->execute()) {
    echo "Запись успешно создана!";
} else {
    echo "Ошибка: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
