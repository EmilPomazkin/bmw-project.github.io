<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель специальных предложений</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
</head>
<body>
<style>
    .img-fluid {
        margin: 16px auto;
        width: 390px;
        height: 260px !important;
        object-fit: cover;
    }
</style>
<?php 
session_start();
include("../header.php"); 
include("../db.php"); // Подключение к базе данных

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../loginform.php'); 
    exit(); 
}

// Обработка удаления специального предложения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['offer_id'])) {
    $offer_id = intval($_POST['offer_id']);
    
    // Запрос на удаление специального предложения
    $sql = "DELETE FROM special_offer WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $offer_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Специальное предложение успешно удалено.";
        $_SESSION['msg_type'] = "success"; 
    } else {
        $_SESSION['message'] = "Ошибка при удалении специального предложения.";
        $_SESSION['msg_type'] = "danger"; 
    }

    $stmt->close();
}

// Обработка добавления нового специального предложения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = $_POST['title'];
    $short_description = $_POST['short_description'];
    $full_description = $_POST['full_description'];
    $conditions = $_POST['conditions'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];

    // Обработка загрузки изображения
    $image_path = null; // Инициализируем переменную
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../img/offer/'; // Папка для загрузки изображений
        $image_name = basename($_FILES['image']['name']); // Получаем только имя файла
        $image_path = $image_name; // Сохраняем только имя файла
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name)) {
            $_SESSION['message'] = "Ошибка при загрузке изображения.";
            $_SESSION['msg_type'] = "danger"; 
            return; // Прерываем выполнение, если загрузка не удалась
        }
    }

    // Получаем текущее время
    $current_time = date('Y-m-d H:i:s');

    // Запрос на добавление нового специального предложения
    $sql = "INSERT INTO special_offer (title, image, short_description, full_description, conditions, start_date, end_date, status, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Ошибка подготовки запроса: ' . $conn->error);
    }

    $stmt->bind_param("sssssssss", 
        $title, 
        $image_path, 
        $short_description, 
        $full_description, 
        $conditions, 
        $start_date, 
        $end_date, 
        $status,
        $current_time // Добавляем текущее время
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "Специальное предложение успешно добавлено.";
        $_SESSION['msg_type'] = "success"; 
    } else {
        $_SESSION['message'] = "Ошибка при добавлении специального предложения: " . $stmt->error;
        $_SESSION['msg_type'] = "danger"; 
    }

    $stmt->close();
}

// Получение списка специальных предложений
$sql = "SELECT * FROM special_offer";
$result = $conn->query($sql);
?>

<div class="container">
    <h1>Админ-панель специальных предложений</h1>
    <a href="admin_panel.php" class="blue">Вернуться в админ-панель</a>
    
    <?php
    // Вывод сообщения об успехе или ошибке
    if (isset($_SESSION['message'])) {
        echo "<div class='alert alert-{$_SESSION['msg_type']} mt-3'>{$_SESSION['message']}</div>";
        unset($_SESSION['message']);
        unset($_SESSION['msg_type']);
    }
    ?>

    <h2 class="mt-3">Добавить новое специальное предложение</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Название</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="image">Изображение</label>
            <input type="file" class="form-control" id="image" name="image" required>
        </div>
        <div class="form-group">
            <label for="short_description">Краткое описание</label>
            <textarea class="form-control" id="short_description" name="short_description" required></textarea>
        </div>
        <div class="form-group">
            <label for="full_description">Полное описание</label>
            <textarea class="form-control" id="full_description" name="full_description" required></textarea>
        </div>
        <div class="form-group">
            <label for="conditions">Условия</label>
            <textarea class="form-control" id="conditions" name="conditions" required></textarea>
        </div>
        <div class="form-group">
            <label for="start_date">Дата начала</label>
            <input type="date" class="form-control" id="start_date" name="start_date" required>
        </div>
        <div class="form-group">
            <label for="end_date">Дата окончания</label>
            <input type="date" class="form-control" id="end_date" name="end_date" required>
        </div>
        <div class="form-group">
            <label for="status">Статус</label>
            <select class="form-control" id="status" name="status">
                <option value="Показать">Показать</option>
                <option value="Скрыть">Скрыть</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Добавить</button>
    </form>

    <h2 class="mt-4">Список специальных предложений</h2>
    <div class="row">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='col-md-4 mb-3'>
                    <div class='card'>
                        <img src='../img/offer/{$row['image']}' alt='Изображение специального предложения' class='img-fluid'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$row['title']}</h5>
                            <p class='card-text'>Краткое описание: {$row['short_description']}</p>
                            <p class='card-text'>Статус: {$row['status']}</p>
                            <a href='edit_offer.php?id={$row['id']}' class='btn btn-primary'>Изменить</a>
                            <form method='POST' action='' style='display:inline;'>
                                <input type='hidden' name='offer_id' value='{$row['id']}'>
                                <input type='submit' value='Удалить' onclick='return confirm(\"Вы уверены, что хотите удалить это специальное предложение?\");' class='btn btn-danger'>
                            </form>
                        </div>
                    </div>
                  </div>";
        }
    } else {
        echo "<p>Нет специальных предложений</p>";
    }
    ?>
    </div>
</div>

<?php require_once("../footer.php"); ?>
</body>
</html>
