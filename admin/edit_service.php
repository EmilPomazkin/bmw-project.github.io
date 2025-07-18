<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование услуги</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
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

    // Получение данных услуги для редактирования
    $service_data = null;
    if (isset($_GET['id'])) {
        $service_id = intval($_GET['id']);
        $sql = "SELECT * FROM car_services WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $service_data = $result->fetch_assoc();
        $stmt->close();
    }

    // Обработка изменения услуги
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
        $image_path = $_POST['existing_image']; // Сохраняем существующее изображение

        // Обработка загрузки нового изображения
        if (isset($_FILES['image_serv']) && $_FILES['image_serv']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../img/service/'; // Изменённый путь
            $image_path = basename($_FILES['image_serv']['name']);
            move_uploaded_file($_FILES['image_serv']['tmp_name'], $upload_dir . $image_path);
        }

        // Запрос на обновление услуги
        $sql = "UPDATE car_services SET 
                    service_name = ?, 
                    price = ?, 
                    description = ?, 
                    process_step1 = ?, 
                    process_step2 = ?, 
                    process_step3 = ?, 
                    process_step4 = ?, 
                    process_step5 = ?, 
                    fault_detection = ?, 
                    diagnostic_step1 = ?, 
                    diagnostic_step2 = ?, 
                    diagnostic_step3 = ?, 
                    diagnostic_step4 = ?, 
                    diagnostic_step5 = ?, 
                    diagnostic_step1_info = ?, 
                    diagnostic_step2_info = ?, 
                    diagnostic_step3_info = ?, 
                    diagnostic_step4_info = ?, 
                    diagnostic_step5_info = ?, 
                    image_serv = ?, 
                    status = ? 
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        
        // Обратите внимание на строку типа

$stmt->bind_param("sisssssssssssssssssssi", 
    $service_name,    // s
    $price,          // i (так как это integer)
    $description,    // s
    $process_steps[0], // s
    $process_steps[1], // s
    $process_steps[2], // s
    $process_steps[3], // s
    $process_steps[4], // s
    $fault_detection, // s
    $diagnostic_steps_info[0],      // s
    $diagnostic_steps_info[1],      // s
    $diagnostic_steps_info[2],      // s
    $diagnostic_steps_info[3],      // s
    $diagnostic_steps_info[4],      // s
    $diagnostic_steps_info_texts[0], // s
    $diagnostic_steps_info_texts[1], // s
    $diagnostic_steps_info_texts[2], // s
    $diagnostic_steps_info_texts[3], // s
    $diagnostic_steps_info_texts[4], // s
    $image_path,      // s
    $status,         // s (добавленный тип)
    $service_id      // i
);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Услуга успешно обновлена.";
            $_SESSION['msg_type'] = "success"; 
    
        } else {
            $_SESSION['message'] = "Ошибка при сохранении услуги.";
            $_SESSION['msg_type'] = "danger"; 
        }

        $stmt->close();
    }
    ?>

    <div class="container">
        <h1>Редактирование услуги</h1>
        <a href="javascript:history.back();" class="blue">Вернуться</a>
        <?php
        // Вывод сообщения об успехе или ошибке
        if (isset($_SESSION['message'])) {
            echo "<div class='alert alert-{$_SESSION['msg_type']} mt-3'>{$_SESSION['message']}</div>";
            unset($_SESSION['message']);
            unset($_SESSION['msg_type']);
        }
        ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $service_data['id']; ?>">
            <input type="hidden" name="existing_image" value="<?php echo $service_data['image_serv']; ?>">
            
            <div class="form-group">
                <label for="service_name">Название услуги</label>
                <input type="text" class="form-control" id="service_name" name="service_name" value="<?php echo $service_data['service_name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Цена</label>
                <input type="number" class="form-control" id="price" name="price" value="<?php echo $service_data['price']; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Описание</label>
                <textarea class="form-control" id="description" name="description" required><?php echo $service_data['description']; ?></textarea>
            </div>

            <!-- Процесс -->
            <h3>Процесс</h3>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="form-group">
                    <label for="process_step<?php echo $i; ?>">Шаг процесса <?php echo $i; ?></label>
                    <input type="text" class="form-control" id="process_step<?php echo $i; ?>" name="process_step<?php echo $i; ?>" value="<?php echo $service_data["process_step$i"]; ?>">
                </div>
            <?php endfor; ?>

            <div class="form-group">
                <label for="fault_detection">Обнаружение неисправностей (в блоке чата)</label>
                <textarea class="form-control" id="fault_detection" name="fault_detection"><?php echo $service_data['fault_detection']; ?></textarea>
            </div>

            <!-- Диагностика -->
            <h3>Диагностика</h3>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="form-group">
                    <label for="diagnostic_step<?php echo $i; ?>">Шаг диагностики <?php echo $i; ?></label>
                    <input type="text" class="form-control" id="diagnostic_step<?php echo $i; ?>" name="diagnostic_step<?php echo $i; ?>" value="<?php echo $service_data["diagnostic_step$i"]; ?>">
                </div>
                <div class="form-group">
                    <label for="diagnostic_step<?php echo $i; ?>_info">Информация по шагу диагностики <?php echo $i; ?></label>
                    <textarea class="form-control" id="diagnostic_step<?php echo $i; ?>_info" name="diagnostic_step<?php echo $i; ?>_info"><?php echo $service_data["diagnostic_step{$i}_info"]; ?></textarea>
                </div>
            <?php endfor; ?>

            <label for="image_serv">Изображение услуги</label>
            <div class="form-group">
                <?php if ($service_data['image_serv']): ?>
                    <img src="../img/service/<?php echo $service_data['image_serv']; ?>" alt="Изображение услуги" class="img-fluid mt-2" style="max-width: 100%; height: auto;">
                <?php endif; ?>             
                <input type="file" class="form-control mt-3" id="image_serv" name="image_serv">
            </div>

            <div class="form-group">
                <label for="status">Статус</label>
                <select class="form-control" id="status" name="status">
                    <option value="Показать" <?php echo $service_data['status'] === 'Показать' ? 'selected' : ''; ?>>Показать</option>
                    <option value="Скрыть" <?php echo $service_data['status'] === 'Скрыть' ? 'selected' : ''; ?>>Скрыть</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary mt-3">Сохранить изменения</button>
        </form>
    </div>

    <?php require_once("../footer.php"); ?>
</body>
</html>
