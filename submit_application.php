<?php
session_start();
include("db.php");

// Проверка, вошел ли пользователь
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = 'Для отправки заявки вам необходимо войти в аккаунт.';
    header("Location: available_cars_lp.php?id=" . $_POST['car_id']);
    exit();
}

// Получение идентификатора автомобиля из POST-запроса
$car_id = isset($_POST['car_id']) ? intval($_POST['car_id']) : 0;
$user_id = $_SESSION['user_id'];

// Вставка заявки в базу данных
$sql = "INSERT INTO applications (user_id, car_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $car_id);

if ($stmt->execute()) {
    $_SESSION['message'] = 'Заявка успешно отправлена. Для перехода в профиль нажмите на сообщение';
} else {
    $_SESSION['message'] = 'Ошибка при отправке заявки: ' . $conn->error;
}

$stmt->close();
$conn->close();
header("Location: available_cars_lp.php?id=" . $_POST['car_id']);
exit();
?>
