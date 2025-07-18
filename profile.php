<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя</title>
    <link rel="icon" href="img/BMW_logo_(gray).svg.png" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="wow fadeInDown">
        <?php require_once("header.php")?>
    </div>
    <div class="container">
        <?php
        session_start();
        include("db.php");

        // Отображение сообщения
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }

        // Получение ID пользователя
        $user_id = $_SESSION['user_id'];

        // Обработка удаления заявки
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_application'])) {
            $application_id = $_POST['application_id'];
            $delete_query = "DELETE FROM applications WHERE id = ?";
            $stmt = $conn->prepare($delete_query);
            $stmt->bind_param("i", $application_id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Заявка успешно отменена.</div>";
            } else {
                echo "<div class='alert alert-danger'>Ошибка при удалении заявки: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }

        // Обработка удаления заявки на тест-драйв
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_test_drive'])) {
            $test_drive_id = $_POST['test_drive_id'];
            $delete_test_drive_query = "DELETE FROM test_drive_requests WHERE id = ?";
            $stmt = $conn->prepare($delete_test_drive_query);
            $stmt->bind_param("i", $test_drive_id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Заявка на тест-драйв успешно отменена.</div>";
            } else {
                echo "<div class='alert alert-danger'>Ошибка при удалении заявки на тест-драйв: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }

        // Обработка удаления записи на ТО
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_service'])) {
            $service_id = $_POST['service_id'];
            $delete_service_query = "DELETE FROM service_appointments WHERE id = ?";
            $stmt = $conn->prepare($delete_service_query);
            $stmt->bind_param("i", $service_id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Запись на ТО успешно отменена.</div>";
            } else {
                echo "<div class='alert alert-danger'>Ошибка при удалении записи на ТО: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }

        // Запрос данных о пользователе
        $user_query = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($user_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_result = $stmt->get_result();
        
        if ($user_result->num_rows === 0) {
            die("Пользователь не найден.");
        }
        
        $user = $user_result->fetch_assoc();
        $stmt->close();

        // Запрос заявок на авто пользователя
        $applications_query = "
        SELECT a.id AS application_id, a.application_date, a.status, c.model AS car_model, c.id AS car_id, d.dealership_name, ci.name AS city_name 
        FROM applications a 
        JOIN available_cars ac ON a.car_id = ac.id 
        JOIN cars c ON ac.car_id = c.id 
        JOIN dealers d ON ac.dealer_id = d.id 
        JOIN cities ci ON d.city_id = ci.id 
        WHERE a.user_id = ?
        ORDER BY a.application_date DESC
        ";
        $stmt = $conn->prepare($applications_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $applications_result = $stmt->get_result();
        $stmt->close();

        // Запрос заявок на тест-драйв пользователя
        $test_drive_query = "
        SELECT t.id AS test_drive_id, t.test_drive_date, t.status, c.model AS car_model, c.id AS car_id, d.dealership_name, ci.name AS city_name 
        FROM test_drive_requests t 
        JOIN available_cars ac ON t.car_id = ac.id 
        JOIN cars c ON ac.car_id = c.id 
        JOIN dealers d ON ac.dealer_id = d.id 
        JOIN cities ci ON d.city_id = ci.id 
        WHERE t.user_id = ?
        ORDER BY t.test_drive_date DESC
        ";
        $stmt = $conn->prepare($test_drive_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $test_drive_result = $stmt->get_result();
        $stmt->close();

        // Запрос записей на ТО пользователя
        $service_query = "
        SELECT s.id AS service_id, s.appointment_date, s.appointment_time, s.status, c.model AS car_model, d.dealership_name, ci.name AS city_name 
        FROM service_appointments s 
        JOIN available_cars ac ON s.car_id = ac.id 
        JOIN cars c ON ac.car_id = c.id 
        JOIN dealers d ON ac.dealer_id = d.id 
        JOIN cities ci ON d.city_id = ci.id 
        WHERE s.user_id = ?
        ORDER BY s.appointment_date DESC
        ";
        $stmt = $conn->prepare($service_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $service_result = $stmt->get_result();
        $stmt->close();
        ?>

        <h2>Здравствуйте, <?php echo $user['name']; ?> <?php echo $user['patronymic']; ?></h2>
        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" style="border-radius: 0;" id="applications-tab" data-toggle="tab" href="#applications" role="tab" aria-controls="applications" aria-selected="true">Заявки на автомобиль</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" style="border-radius: 0;" id="test-drive-tab" data-toggle="tab" href="#test-drive" role="tab" aria-controls="test-drive" aria-selected="false">Заявки на тест-драйв</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" style="border-radius: 0;" id="service-tab" data-toggle="tab" href="#service" role="tab" aria-controls="service" aria-selected="false">Заявки на тех. обслуживание</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" style="border-radius: 0;" id="edit-tab" data-toggle="tab" href="#edit" role="tab" aria-controls="edit" aria-selected="false">Изменение данных</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="applications" role="tabpanel" aria-labelledby="applications-tab">
                <h3 class="mt-3">Ваши заявки на автомобиль</h3>
                <div class="applications-container">
                    <?php if ($applications_result->num_rows === 0): ?>
                        <p>У вас нет заявок.</p>
                    <?php else: ?>
                        <?php while ($application = $applications_result->fetch_assoc()): ?>
                            <div class="application-card">
                                <h4>Заявка №<?php echo $application['application_id']; ?></h4>
                                <p><strong>Автомобиль:</strong> <a href="available_cars_lp.php?id=<?php echo $application['car_id']; ?>" class="blue"><?php echo $application['car_model']; ?></a></p>
                                <p><strong>Дилер:</strong> <?php echo $application['dealership_name']; ?></p>
                                <p><strong>Город:</strong> <?php echo $application['city_name']; ?></p>
                                <p><strong>Дата подачи:</strong> <?php echo $application['application_date']; ?></p>
                                <p><strong>Статус:</strong> <?php echo $application['status']; ?></p>
                                <form method='POST' action='' style='display:inline;'>
                                    <input type='hidden' name='application_id' value='<?php echo $application['application_id']; ?>'>
                                    <input type='hidden' name='delete_application' value='1'>
                                    <input type='submit' value='Отменить' onclick='return confirm("Вы уверены, что хотите отменить эту заявку?");' class='btn btn-danger'>
                                </form>
                            </div>
                            <br>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="test-drive" role="tabpanel" aria-labelledby="test-drive-tab">
                <h2 class="mt-3">Ваши заявки на тест-драйв</h2>
                <div class="">
                    <?php if ($test_drive_result->num_rows === 0): ?>
                        <p style="text-align:left;">У вас нет заявок на тест-драйв</p>
                    <?php else: ?>
                        <?php while ($test_drive = $test_drive_result->fetch_assoc()): ?>
                            <div class="application-card">
                                <div class="application-header">
                                    <h4>Заявка №<?php echo $test_drive['test_drive_id']; ?></h4>
                                </div>
                                <p><strong>Автомобиль:</strong> <a href="cars.php?id=<?php echo $test_drive['car_id']; ?>"><?php echo $test_drive['car_model']; ?></a></p>
                                <p><strong>Дилер:</strong> <?php echo $test_drive['dealership_name']; ?></p>
                                <p><strong>Город:</strong> <?php echo $test_drive['city_name']; ?></p>
                                <p><strong>Дата тест-драйва:</strong> <?php echo $test_drive['test_drive_date']; ?></p>
                                <p><strong>Статус:</strong> <?php echo $test_drive['status']; ?></p>
                                <form method='POST' action='' style='display:inline;'>
                                    <input type='hidden' name='test_drive_id' value='<?php echo $test_drive['test_drive_id']; ?>'>
                                    <input type='hidden' name='delete_test_drive' value='1'>
                                    <input type='submit' value='Отменить запись' onclick='return confirm("Вы уверены, что хотите отменить этот тест-драйв?");' class='btn btn-danger'>
                                </form>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="service" role="tabpanel" aria-labelledby="service-tab">
                <h2 class="mt-3">Ваши записи на тех. обслуживание</h2>
                <div class="">
                    <?php if ($service_result->num_rows === 0): ?>
                        <p style="text-align:left;">У вас нет заявок на тех. обслуживание</p>
                    <?php else: ?>
                        <?php while ($service = $service_result->fetch_assoc()): ?>
                            <div class="application-card">
                                <h4>Заявка №<?php echo $service['service_id']; ?></h4>
                                <p><strong>Автомобиль:</strong> <?php echo $service['car_model']; ?></p>
                                <p><strong>Дилер:</strong> <?php echo $service['dealership_name']; ?></p>
                                <p><strong>Город:</strong> <?php echo $service['city_name']; ?></p>
                                <p><strong>Дата ТО:</strong> <?php echo $service['appointment_date']; ?></p>
                                <p><strong>Время ТО:</strong> <?php echo $service['appointment_time']; ?></p>
                                <p><strong>Статус:</strong> <?php echo $service['status']; ?></p>
                                <form method='POST' action='' style='display:inline;'>
                                    <input type='hidden' name='service_id' value='<?php echo $service['service_id']; ?>'>
                                    <input type='hidden' name='delete_service' value='1'>
                                    <input type='submit' value='Отменить запись' onclick='return confirm("Вы уверены, что хотите отменить эту запись на ТО?");' class='btn btn-danger'>
                                </form>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="edit" role="tabpanel" aria-labelledby="edit-tab">
                <h2 class="mt-3">Изменение данных</h2>
                <form action="update_profile.php" method="post" class="test_drive_form">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    <div class="form-group">
                        <label for="surname">Фамилия:</label>
                        <input type="text" class="form-control" name="surname" value="<?php echo $user['surname']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Имя:</label>
                        <input type="text" class="form-control" name="name" value="<?php echo $user['name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="patronymic">Отчество:</label>
                        <input type="text" class="form-control" name="patronymic" value="<?php echo $user['patronymic']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="login">Логин:</label>
                        <input type="text" class="form-control" name="login" value="<?php echo $user['login']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" name="email" value="<?php echo $user['email']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Телефон:</label>
                        <input type="text" class="form-control" name="phone" id="phone" value="<?php echo $user['phone']; ?>" required>
                    </div>
                    <button type="submit" class="btnauth mt-3">Сохранить изменения</button>
                </form>
            </div>
        </div>
    </div>
    <div class="wow fadeInUp">
        <?php require_once("footer.php");?>
    </div>
</body>
</html>
