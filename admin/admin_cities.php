<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление городами</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
    <link rel="stylesheet" href="../css/styles.css"> <!-- Подключите ваш CSS файл -->
</head>
<body>
    <?php 
    require_once('../header.php');
    session_start();
    include("../db.php"); // Подключение к базе данных

    // Проверка на администраторские права
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: ../loginform.php');
        exit();
    }

    // Обработка удаления города
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['city_id'])) {
        $city_id = intval($_POST['city_id']);
        
        // Запрос на удаление города
        $sql = "DELETE FROM cities WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $city_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Город успешно удалён.";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['message'] = "Ошибка при удалении города.";
            $_SESSION['msg_type'] = "danger";
        }

        $stmt->close();
    }

    // Обработка добавления нового города
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['city_name'])) {
        $city_name = trim($_POST['city_name']);
        $latitude = trim($_POST['latitude']);
        $longitude = trim($_POST['longitude']);

        // Запрос на добавление нового города
        $sql = "INSERT INTO cities (name, latitude, longitude) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdd", $city_name, $latitude, $longitude);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Город успешно добавлен.";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['message'] = "Ошибка при добавлении города.";
            $_SESSION['msg_type'] = "danger";
        }

        $stmt->close();
    }

    // Получение списка городов
    $sql = "SELECT id, name, latitude, longitude FROM cities";
    $result = $conn->query($sql);
    ?>

    <div class="container">
        <h1>Управление городами</h1>
        <a href="admin_panel.php" class="blue">Вернуться в админ-панель</a>
        <?php
        // Вывод сообщения об успехе или ошибке
        if (isset($_SESSION['message'])) {
            echo "<div class='alert alert-{$_SESSION['msg_type']} mt-3'>{$_SESSION['message']}</div>";
            unset($_SESSION['message'], $_SESSION['msg_type']); // Удаление сообщения после отображения
        }
        ?>

        <h2 class="mt-3">Добавить новый город</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="city_name">Имя города</label>
                <input type="text" class="form-control" id="city_name" name="city_name" required>
            </div>
            <div class="form-group">
                <label for="latitude">Широта</label>
                <input type="number" step="0.000001" class="form-control" id="latitude" name="latitude" required>
            </div>
            <div class="form-group">
                <label for="longitude">Долгота</label>
                <input type="number" step="0.000001" class="form-control" id="longitude" name="longitude" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Добавить город</button>
        </form>

        <h2 class="mt-4">Список городов</h2>
        <div class="row">
        <?php
        if ($result->num_rows > 0) {
            // Вывод данных городов в виде карточек
            while ($row = $result->fetch_assoc()) {
                echo "<div class='col-md-4 mb-3'>
                        <div class='card'>
                            <div class='card-body'>
                                <h5 class='card-title'>{$row['name']}</h5>
                                <p class='card-text'>ID: {$row['id']}</p>
                                <p class='card-text'>Широта: {$row['latitude']}</p>
                                <p class='card-text'>Долгота: {$row['longitude']}</p>
                                <form method='POST' action='' style='display:inline;'>
                                    <input type='hidden' name='city_id' value='{$row['id']}'>
                                    <input type='submit' value='Удалить' onclick='return confirm(\"Вы уверены, что хотите удалить этот город?\");' class='btn btn-danger'>
                                </form>
                            </div>
                        </div>
                      </div>";
            }
        } else {
            echo "<p>Нет городов</p>";
        }
        ?>
        </div>
    </div>

    <?php require_once("../footer.php"); ?>
</body>
</html>
