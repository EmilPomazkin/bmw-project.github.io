<?php
session_start();

// Подключение к базе данных
include("db.php");

// Проверка, вошел ли пользователь в систему
if (!isset($_SESSION['user_id'])) {
    $user_logged_in = false;
} else {
    $user_logged_in = true;

    // Инициализация переменных
    $cities = $conn->query("SELECT id, name FROM cities");
    if ($cities === false) {
        error_log("Ошибка запроса к базе данных: " . $conn->error);
        die("Произошла ошибка при получении городов.");
    }

    $dealers = [];
    $cars = [];
    $selected_dealer_id = null;
    $selected_city_id = null;

    // Переменные для сообщений
    $message = '';
    $message_type = '';

    // Обработка формы для выбора дилера и автомобилей
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['city'])) {
            $selected_city_id = intval($_POST['city']);
            $dealers_result = $conn->query("SELECT id, dealership_name FROM dealers WHERE city_id = $selected_city_id");

            if ($dealers_result === false) {
                error_log("Ошибка запроса к базе данных: " . $conn->error);
                die("Произошла ошибка при получении дилеров.");
            }

            $dealers = $dealers_result->fetch_all(MYSQLI_ASSOC);
        }

        if (isset($_POST['dealer'])) {
            $selected_dealer_id = intval($_POST['dealer']);
            $cars_result = $conn->query("
                SELECT MIN(ac.id) AS id, c.model AS car_model 
                FROM available_cars ac
                JOIN cars c ON ac.car_id = c.id
                WHERE ac.dealer_id = $selected_dealer_id
                GROUP BY c.model
            ");

            if ($cars_result === false) {
                error_log("Ошибка запроса к базе данных: " . $conn->error);
                die("Произошла ошибка при получении автомобилей: " . $conn->error);
            }

            $cars = $cars_result->fetch_all(MYSQLI_ASSOC);
        }

        if (isset($_POST['car'])) {
            // Обработка отправки заявки на тест-драйв
            $user_id = $_SESSION['user_id']; // Предполагается, что user_id хранится в сессии
            $dealer_id = $_POST['dealer'];
            $car_id = $_POST['car'];
            $test_drive_date = $_POST['test_drive_date'];
            $status = 'ожидает'; // Начальный статус заявки

            // Подготовка SQL-запроса для вставки данных
            $sql = "INSERT INTO test_drive_requests (user_id, city_id, dealer_id, car_id, test_drive_date, status, submission_time) VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                error_log("Ошибка подготовки запроса: " . $conn->error);
                die("Произошла ошибка при подготовке запроса.");
            }

            $stmt->bind_param("iiisss", $user_id, $selected_city_id, $dealer_id, $car_id, $test_drive_date, $status);

            if ($stmt->execute()) {
                $message = 'Заявка на тест-драйв успешно отправлена! Заявку вы сможете отследить <a href="profile.php" class="blue" style="text-decoration:none;">в личном кабинете</a>';
                $message_type = 'success'; // Успешное сообщение
            } else {
                error_log("Ошибка выполнения запроса: " . $stmt->error);
                $message = 'Ошибка: ' . $stmt->error;
                $message_type = 'danger'; // Ошибка
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Запись на тест-драйв</title>

    <link rel="icon" href="img/BMW_logo_(gray).svg.png" type="image/png">
</head>
<body>

<?php require_once("header.php")?>
<div class="container">
    <div class="test-drive-container">
    <h1>Запись на тест-драйв</h1>
    
    <?php if ($user_logged_in): ?>
        <?php if ($message): ?>
            <div class="alert alert-<?= $message_type; ?>" role="alert">
                <?= $message; ?>
            </div>
        <?php endif; ?>

        <form action="test_drive.php" method="POST" class="test_drive_form">
            <label for="city">Выберите город</label>
            <select id="city" name="city" onchange="this.form.submit()">
                <option value="" disabled selected>Выберите город</option>
                <?php while ($row = $cities->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>" <?= $selected_city_id == $row['id'] ? 'selected' : '' ?>><?= $row['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </form>

        <form action="test_drive.php" method="POST" class="test_drive_form">
            <input type="hidden" name="city" value="<?= $selected_city_id ?>">
            <label for="dealer">Выберите дилера</label>
            <select id="dealer" name="dealer" onchange="this.form.submit()" <?= $selected_city_id === null ? 'disabled' : '' ?>>
                <option value="" disabled selected>Сначала выберите город</option>
                <?php if (!empty($dealers)): ?>
                    <?php foreach ($dealers as $dealer): ?>
                        <option value="<?= $dealer['id'] ?>" <?= $selected_dealer_id == $dealer['id'] ? 'selected' : '' ?>><?= $dealer['dealership_name'] ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </form>

        <form action="test_drive.php" method="POST" class="test_drive_form">
            <input type="hidden" name="city" value="<?= $selected_city_id ?>">
            <input type="hidden" name="dealer" value="<?= $selected_dealer_id ?>">
            <label for="car">Выберите автомобиль</label>
            <select id="car" name="car" <?= $selected_dealer_id === null ? 'disabled' : '' ?>>
                <option value="" disabled selected>Сначала выберите дилера</option>
                <?php if ($selected_dealer_id !== null && empty($cars)): ?>
                    <option value="" disabled selected>У дилера нет автомобилей</option>
                <?php else: ?>
                    <?php foreach ($cars as $car): ?>
                        <option value="<?= $car['id'] ?>"><?= $car['car_model'] ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="test_drive_date">Дата тест-драйва:</label>
            <input type="date" id="test_drive_date" name="test_drive_date" required>

            <button type="submit" class="btnauth">Отправить заявку</button>

        </form>
        
    <?php else: ?>
        <p>Для того чтобы оставить заявку на тест-драйв войдите <a href="loginform.php" class="blue" style="text-decoration:none;"> в личный кабинет </a></p>
    <?php endif; ?>
</div>
</div>
<?php require_once("footer.php");?>
</body>
</html>
