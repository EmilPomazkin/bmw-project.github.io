<?php
session_start();
include("db.php");

// Проверка, вошел ли пользователь в систему
if (!isset($_SESSION['user_id'])) {
    $user_logged_in = false; // Пользователь не авторизован
} else {
    $user_logged_in = true; // Пользователь авторизован

    // Инициализация переменных
    $cars = $conn->query("SELECT id, model FROM cars");
    $services = $conn->query("SELECT id, service_name FROM car_services");
    $cities = $conn->query("SELECT id, name FROM cities");
    $dealers = $conn->query("SELECT id, dealership_name, city_id FROM dealers");

    // Проверка на ошибки при запросах
    if ($cars === false || $services === false || $cities === false || $dealers === false) {
        error_log("Ошибка запроса к базе данных: " . $conn->error);
        die("Произошла ошибка при получении данных.");
    }

    // Сброс данных формы при обновлении страницы
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && !isset($_GET['step'])) {
        unset($_SESSION['form_data']);
        unset($_SESSION['selected_city_id']);
    }

    // Обработка формы
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['step1'])) {
            // Валидация VIN
            $vin = trim($_POST['vin_number']);
            if (strlen($vin) != 17) {
                $_SESSION['error_message'] = 'VIN-номер должен содержать ровно 17 символов';
                header("Location: ".$_SERVER['PHP_SELF']."?step=1");
                exit();
            }
            
            // Сохранение данных в сессии
            $_SESSION['form_data'] = [
                'car_id' => intval($_POST['car_id']),
                'service_id' => intval($_POST['service_id']),
                'vin_number' => $conn->real_escape_string($vin),
                'year' => intval($_POST['year'])
            ];
            header("Location: ".$_SERVER['PHP_SELF']."?step=2");
            exit();
        }
        
        if (isset($_POST['submit_maintenance'])) {
            $selected_city_id = intval($_POST['city_id']);
            $selected_dealer_id = intval($_POST['dealer_id']);
            $appointment_date = $_POST['appointment_date'];
            $appointment_time = $_POST['appointment_time'];

            // Проверка занятости дилера
            $sql_check = "SELECT COUNT(*) FROM service_appointments 
                          WHERE dealer_id = ? AND appointment_date = ? AND appointment_time = ?";
            $stmt_check = $conn->prepare($sql_check);
            
            if ($stmt_check === false) {
                die('Ошибка подготовки запроса: ' . htmlspecialchars($conn->error));
            }
            
            $stmt_check->bind_param("iss", $selected_dealer_id, $appointment_date, $appointment_time);
            $stmt_check->execute();
            $stmt_check->bind_result($count);
            $stmt_check->fetch();
            $stmt_check->close();

            if ($count > 0) {
                $_SESSION['error_message'] = 'Этот дилер уже занят в указанное время. Пожалуйста, выберите другое время или другого дилера.';
                header("Location: ".$_SERVER['PHP_SELF']."?step=2");
                exit();
            }

            // Запись в базу данных
            $sql = "INSERT INTO service_appointments 
                    (user_id, car_id, service_id, dealer_id, vin_number, year, status, appointment_date, appointment_time) 
                    VALUES (?, ?, ?, ?, ?, ?, 'Ожидает', ?, ?)";
            $stmt = $conn->prepare($sql);
            
            if ($stmt === false) {
                die('Ошибка подготовки запроса: ' . htmlspecialchars($conn->error));
            }
            
            $stmt->bind_param("iiississ", 
                $_SESSION['user_id'], 
                $_SESSION['form_data']['car_id'], 
                $_SESSION['form_data']['service_id'], 
                $selected_dealer_id, 
                $_SESSION['form_data']['vin_number'],
                $_SESSION['form_data']['year'],
                $appointment_date,
                $appointment_time);
            
            if ($stmt->execute()) {
                unset($_SESSION['form_data']);
                unset($_SESSION['selected_city_id']); // Очистка выбранного города
                $_SESSION['success_message'] = 'Заявка на ТО отправлена!';
                header("Location: maintenance.php");
                exit();
            } else {
                die('Ошибка выполнения запроса: ' . htmlspecialchars($stmt->error));
            }
            $stmt->close();
        }
    }

    // Фильтрация дилеров
    $filtered_dealers = [];
    if (isset($_GET['step']) && $_GET['step'] == 2) {
        if (!isset($_SESSION['form_data'])) {
            header("Location: ".$_SERVER['PHP_SELF']."?step=1");
            exit();
        }
        
        if (isset($_POST['city_id'])) {
            $selected_city_id = intval($_POST['city_id']);
            $_SESSION['selected_city_id'] = $selected_city_id; // Сохраняем выбранный город в сессии
        } else {
            $selected_city_id = isset($_SESSION['selected_city_id']) ? $_SESSION['selected_city_id'] : null;
        }

        while ($dealer = $dealers->fetch_assoc()) {
            if ($dealer['city_id'] == $selected_city_id) {
                $filtered_dealers[] = $dealer;
            }
        }
        $dealers->data_seek(0);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Запись на ТО</title>
    <link rel="icon" href="img/BMW_logo_(gray).svg.png" type="image/png">
    <style>
        .form-columns {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-column {
            flex: 1;
            min-width: 300px;
        }
        .nav-tabs-custom {
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
        .nav-tabs-custom .nav-link {
            border: none;
            color: #495057;
            font-weight: 500;
            padding: 10px 20px;
            position: relative;
        }
        .nav-tabs-custom .nav-link.active {
            color: #0166b1;
        }
        .nav-tabs-custom .nav-link.active:after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #0166b1;
        }
        .nav-tabs-custom .nav-link.disabled {
            color: #6c757d;
        }
        .d-none {
            display: none;
        }
        .mb-3 {
            margin-bottom: 1rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
        }
        .form-control {
            width: 100%;
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
        }
        .alert-danger {
            color: #842029;
            background-color: #f8d7da;
            border-color: #f5c2c7;
        }
        .alert-success {
            color: #0f5132;
            background-color: #d1e7dd;
            border-color: #badbcc;
        }
        .alert-warning {
            color: #664d03;
            background-color: #fff3cd;
            border-color: #ffecb5;
        }
        .text-end {
            text-align: end;
        }
        .d-flex {
            display: flex;
        }
        .justify-content-between {
            justify-content: space-between;
        }
    </style>
</head>
<body>
<?php require_once("header.php")?>

<div class="container mt-4">
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (!$user_logged_in): ?>
        <h1 style="text-align:center;">Запись на тех. обслуживание</h1>
        <p style="text-align:center;">Для того чтобы оставить заявку, войдите <a href="loginform.php" class="blue" style="text-decoration:none;">в личный кабинет</a></p>
    <?php else: ?>
        <h1 style="text-align:center;">Запись на тех. обслуживание</h1>
        <ul class="nav nav-tabs-custom">
            <li class="nav-item">
                <a class="nav-link <?= (!isset($_GET['step']) || $_GET['step'] == 1) ? 'active' : '' ?>" 
                   href="?step=1">Шаг 1: Данные автомобиля</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (isset($_GET['step']) && $_GET['step'] == 2) ? 'active' : '' ?>" 
                   href="?step=2">Шаг 2: Выбор сервиса</a>
            </li>
        </ul>
        
        <div>
            <!-- Шаг 1 -->
            <div class="<?= (!isset($_GET['step']) || $_GET['step'] == 1) ? '' : 'd-none' ?>">
                <form method="post" id="step1-form" class="mt-3">
                    <div class="form-columns">
                        <div class="form-column">
                            <div class="mb-3">
                                <label for="car_id" class="form-label">Автомобиль</label>
                                <select class="form-control" id="car_id" name="car_id" required>
                                    <option value="" selected disabled>Выберите модель</option>
                                    <?php while ($row = $cars->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>" <?= isset($_SESSION['form_data']['car_id']) && $_SESSION['form_data']['car_id'] == $row['id'] ? 'selected' : '' ?>>
                                            <?= $row['model'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="vin_number" class="form-label">VIN-номер</label>
                                <input type="text" class="form-control" id="vin_number" name="vin_number" 
                                       value="<?= isset($_SESSION['form_data']['vin_number']) ? htmlspecialchars($_SESSION['form_data']['vin_number']) : '' ?>" 
                                       required maxlength="17"
                                       oninput="this.value = this.value.slice(0, 17)"
                                       title="VIN-номер должен содержать ровно 17 символов">
                                <div class="form-text">Должен содержать ровно 17 символов</div>
                            </div>
                        </div>
                        
                        <div class="form-column">
                            <div class="mb-3">
                                <label for="service_id" class="form-label">Тип обслуживания</label>
                                <select class="form-control" id="service_id" name="service_id" required>
                                    <option value="" selected disabled>Выберите услугу</option>
                                    <?php $services->data_seek(0); while ($row = $services->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>" <?= isset($_SESSION['form_data']['service_id']) && $_SESSION['form_data']['service_id'] == $row['id'] ? 'selected' : '' ?>>
                                            <?= $row['service_name'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="year" class="form-label">Год автомобиля</label>
                                <select class="form-control" id="year" name="year" required>
                                    <option value="" selected disabled>Выберите год</option>
                                    <?php 
                                    $current_year = date('Y');
                                    for ($year = $current_year; $year >= 2015; $year--) {
                                        $selected = isset($_SESSION['form_data']['year']) && $_SESSION['form_data']['year'] == $year ? 'selected' : '';
                                        echo "<option value='$year' $selected>$year</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" name="step1" class="btnauth">Далее</button>
                    </div>
                </form>
            </div>
            
            <!-- Шаг 2 -->
            <div class="<?= (isset($_GET['step']) && $_GET['step'] == 2) ? '' : 'd-none' ?>">
                <form method="post" id="step2-form" class="mt-3">
                    <div class="form-columns">
                        <div class="form-column">
                            <div class="mb-3">
                                <label for="city_id" class="form-label">Город</label>
                                <select class="form-control" id="city_id" name="city_id" required onchange="this.form.submit()">
                                    <option value="" selected disabled>Выберите город</option>
                                    <?php $cities->data_seek(0); while ($row = $cities->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>" <?= (isset($_SESSION['selected_city_id']) && $_SESSION['selected_city_id'] == $row['id']) ? 'selected' : '' ?>>
                                            <?= $row['name'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-column">
                            <div class="mb-3">
                                <label for="dealer_id" class="form-label">Сервисный центр</label>
                                <select class="form-control" id="dealer_id" name="dealer_id" <?= empty($filtered_dealers) ? 'disabled' : '' ?> required>
                                    <option value="" selected disabled>
                                        <?= empty($filtered_dealers) ? 'Сначала выберите город' : 'Выберите сервис' ?>
                                    </option>
                                    <?php foreach ($filtered_dealers as $dealer): ?>
                                        <option value="<?= $dealer['id'] ?>">
                                            <?= $dealer['dealership_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-columns">
                        <div class="form-column">
                            <div class="mb-3">
                                <label for="appointment_date" class="form-label">Дата записи</label>
                                <input type="date" class="form-control" id="appointment_date" name="appointment_date" required 
                                       min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="form-column">
                            <div class="mb-3">
                                <label for="appointment_time" class="form-label">Время записи</label>
                                <select class="form-control" id="appointment_time" name="appointment_time" required>
                                    <option value="" selected disabled>Выберите время</option>
                                    <option value="09:00">09:00</option>
                                    <option value="10:00">10:00</option>
                                    <option value="11:00">11:00</option>
                                    <option value="12:00">12:00</option>
                                    <option value="13:00">13:00</option>
                                    <option value="14:00">14:00</option>
                                    <option value="15:00">15:00</option>
                                    <option value="16:00">16:00</option>
                                    <option value="17:00">17:00</option>
                                    <option value="18:00">18:00</option>
                                    <option value="19:00">19:00</option>
                                    <option value="20:00">20:00</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" onclick="window.location.href='?step=1'" class="btn btn-secondary">Назад</button>
                        <button type="submit" name="submit_maintenance" class="btn btn-primary">Записаться</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once("footer.php"); ?>
</body>
</html>
