<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление услугами автосервиса</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">

    <style>
        .img-fluid {
            width: 400px;
            height: 300px !important;
            object-fit: cover;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php 
    session_start();
    include("../header.php"); 
    include("../db.php"); // Подключение к базе данных

    // Проверяем, является ли пользователь администратором
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: ../loginform.php'); 
        exit(); 
    }

    // Обработка удаления услуги
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_id'])) {
        $service_id = intval($_POST['service_id']);
        
        // Запрос на удаление услуги
        $sql = "DELETE FROM car_services WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $service_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Услуга успешно удалена.";
            $_SESSION['msg_type'] = "success"; 
        } else {
            $_SESSION['message'] = "Ошибка при удалении услуги.";
            $_SESSION['msg_type'] = "danger"; 
        }

        $stmt->close();
    }

    // Обработка добавления новой услуги
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_name'])) {
        $service_name = $_POST['service_name'];
        $price = intval($_POST['price']);
        $description = $_POST['description'];
        $process_steps = [
            $_POST['process_step1'],
            $_POST['process_step2'],
            $_POST['process_step3'],
            $_POST['process_step4'],
            $_POST['process_step5'],
        ];
        $fault_detection = $_POST['fault_detection'];
        $diagnostic_steps_info = [
            $_POST['diagnostic_step1'],
            $_POST['diagnostic_step2'],
            $_POST['diagnostic_step3'],
            $_POST['diagnostic_step4'],
            $_POST['diagnostic_step5'],
        ];
        $diagnostic_steps_info_texts = [
            $_POST['diagnostic_step1_info'],
            $_POST['diagnostic_step2_info'],
            $_POST['diagnostic_step3_info'],
            $_POST['diagnostic_step4_info'],
            $_POST['diagnostic_step5_info'],
        ];
        $status = $_POST['status'];
        
        // Обработка загрузки изображения
        $image_path = null;
        if (isset($_FILES['image_serv']) && $_FILES['image_serv']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../img/service/'; // Папка для загрузки изображений
            $image_path = $upload_dir . basename($_FILES['image_serv']['name']);
            move_uploaded_file($_FILES['image_serv']['tmp_name'], $image_path);
        }

        // Запрос на добавление новой услуги
        $sql = "INSERT INTO car_services (service_name, price, description, process_step1, process_step2, process_step3, process_step4, process_step5, fault_detection, diagnostic_step1, diagnostic_step2, diagnostic_step3, diagnostic_step4, diagnostic_step5, diagnostic_step1_info, diagnostic_step2_info, diagnostic_step3_info, diagnostic_step4_info, diagnostic_step5_info, status, image_serv) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sissssssssssssssssss", 
            $service_name, 
            $price, 
            $description, 
            $process_steps[0], 
            $process_steps[1], 
            $process_steps[2], 
            $process_steps[3], 
            $process_steps[4], 
            $fault_detection,
            $diagnostic_steps_info[0],
            $diagnostic_steps_info[1],
            $diagnostic_steps_info[2],
            $diagnostic_steps_info[3],
            $diagnostic_steps_info[4],
            $diagnostic_steps_info_texts[0],
            $diagnostic_steps_info_texts[1],
            $diagnostic_steps_info_texts[2],
            $diagnostic_steps_info_texts[3],
            $diagnostic_steps_info_texts[4],
            $status,
            $image_path
        );

        if ($stmt->execute()) {
            $_SESSION['message'] = "Услуга успешно добавлена.";
            $_SESSION['msg_type'] = "success"; 
        } else {
            $_SESSION['message'] = "Ошибка при добавлении услуги.";
            $_SESSION['msg_type'] = "danger"; 
        }

        $stmt->close();
    }

    // Получение списка услуг
    $sql = "SELECT * FROM car_services";
    $result = $conn->query($sql);
    ?>

    <div class="container">
        <h1>Управление услугами автосервиса</h1>
        <a href="admin_panel.php" class="blue">Вернуться в админ-панель</a>
        
        <?php
        // Вывод сообщения об успехе или ошибке
        if (isset($_SESSION['message'])) {
            echo "<div class='alert alert-{$_SESSION['msg_type']} mt-3'>{$_SESSION['message']}</div>";
            unset($_SESSION['message']);
            unset($_SESSION['msg_type']);
        }
        ?>

        <h2 class="mt-3">Добавить новую услугу</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="service_name">Название услуги</label>
                <input type="text" class="form-control" id="service_name" name="service_name" required>
            </div>
            <div class="form-group">
                <label for="price">Цена</label>
                <input type="number" class="form-control" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="description">Описание</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
            </div>

            <!-- Процесс -->
            <h3>Процесс</h3>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="form-group">
                    <label for="process_step<?php echo $i; ?>">Шаг процесса <?php echo $i; ?></label>
                    <input type="text" class="form-control" id="process_step<?php echo $i; ?>" name="process_step<?php echo $i; ?>">
                </div>
            <?php endfor; ?>

            <div class="form-group">
                <label for="fault_detection">Обнаружение неисправностей (в блоке чата)</label>
                <textarea class="form-control" id="fault_detection" name="fault_detection"></textarea>
            </div>

            <!-- Диагностика -->
            <h3>Диагностика</h3>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="form-group">
                    <label for="diagnostic_step<?php echo $i; ?>">Шаг диагностики <?php echo $i; ?></label>
                    <input type="text" class="form-control" id="diagnostic_step<?php echo $i; ?>" name="diagnostic_step<?php echo $i; ?>">
                </div>
                <div class="form-group">
                    <label for="diagnostic_step<?php echo $i; ?>_info">Информация по шагу диагностики <?php echo $i; ?></label>
                    <textarea class="form-control" id="diagnostic_step<?php echo $i; ?>_info" name="diagnostic_step<?php echo $i; ?>_info"></textarea>
                </div>
            <?php endfor; ?>

            <div class="form-group">
                <label for="image_serv">Изображение услуги</label>
                <div class="custom-file">
                    <input type="file" class="form-control" id="image_serv" name="image_serv" required>
                    <label class="custom-file-label" for="image_serv">Выберите файл</label>
                </div>
            </div>

            <div class="form-group">
                <label for="status">Статус</label>
                <select class="form-control" id="status" name="status">
                    <option value="Показать">Показать</option>
                    <option value="Скрыть">Скрыть</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Добавить услугу</button>
        </form>

        <h2 class="mt-4">Список услуг</h2>
        <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='col-md-4 mb-3'>
                        <div class='card' >
                            <div class='card-body'>   
                            <img src='../img/service/{$row['image_serv']}' alt='Изображение услуги' class='img-fluid'>
                                <h5 class='card-title'>{$row['service_name']}</h5>
                                <p class='card-text'>Цена: {$row['price']}</p>
                                <p class='card-text'>Описание: {$row['description']}</p>
                                <p class='card-text'>Статус: {$row['status']}</p>";
             
                echo "  <a href='edit_service.php?id={$row['id']}' class='btn btn-primary'>Изменить</a>
                                <form method='POST' action='' style='display:inline;'>
                                    <input type='hidden' name='service_id' value='{$row['id']}'>
                                    <input type='submit' value='Удалить' onclick='return confirm(\"Вы уверены, что хотите удалить эту услугу?\");' class='btn btn-danger'>
                                </form>
                            </div>
                        </div>
                      </div>";
            }
        } else {
            echo "<p>Нет услуг</p>";
        }
        ?>
        </div>
    </div>

    <script>
        // Обновление имени файла в поле выбора файла
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
        });
    </script>

    <?php require_once("../footer.php"); ?>
</body>
</html>
