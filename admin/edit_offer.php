<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование специального предложения</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
</head>
<?php
session_start();
include("../header.php"); 
include("../db.php"); // Подключение к базе данных

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../loginform.php'); 
    exit(); 
}

// Получение данных специального предложения для редактирования
$offer_data = null;
if (isset($_GET['id'])) {
    $offer_id = intval($_GET['id']);
    $sql = "SELECT * FROM special_offer WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $offer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $offer_data = $result->fetch_assoc();
    $stmt->close();
}

// Обработка изменения специального предложения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = $_POST['title'];
    $short_description = $_POST['short_description'];
    $full_description = $_POST['full_description'];
    $conditions = $_POST['conditions'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $image_path = $_POST['existing_image']; // Сохраняем существующее изображение

    // Обработка загрузки нового изображения
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../img/offer/'; // Папка для загрузки изображений
        $image_name = basename($_FILES['image']['name']); // Получаем только имя файла
        $image_path = $image_name; // Сохраняем только имя файла
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name); // Перемещаем файл в папку
    }

    // Запрос на обновление специального предложения
    $sql = "UPDATE special_offer SET 
                title = ?, 
                image = ?, 
                short_description = ?, 
                full_description = ?, 
                conditions = ?, 
                start_date = ?, 
                end_date = ?, 
                status = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", 
        $title, 
        $image_path, 
        $short_description, 
        $full_description, 
        $conditions, 
        $start_date, 
        $end_date, 
        $status, 
        $offer_id
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "Специальное предложение успешно обновлено.";
        $_SESSION['msg_type'] = "success"; 
   
    } else {
        $_SESSION['message'] = "Ошибка при обновлении специального предложения.";
        $_SESSION['msg_type'] = "danger"; 
    }

    $stmt->close();
}
?>


<body>
    <div class="container">
        <h1>Редактирование специального предложения</h1>
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
            <input type="hidden" name="existing_image" value="<?php echo $offer_data['image']; ?>">
            <div class="form-group">
                <label for="title">Название</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo $offer_data['title']; ?>" required>
            </div>   
            <label for="image">Изображение</label>
            <div class="form-group">
                <?php if ($offer_data['image']): ?>
                    <img src="../img/offer/<?php echo $offer_data['image']; ?>" alt="Изображение специального предложения" class="img-fluid mt-2" style="max-width: 100%; height: auto;">
                <?php endif; ?> 
                <input type="file" class="form-control mt-3" id="image" name="image">
            </div>
            <div class="form-group">
                <label for="short_description">Краткое описание</label>
                <textarea class="form-control" id="short_description" name="short_description" required><?php echo $offer_data['short_description']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="full_description">Полное описание</label>
                <textarea class="form-control" id="full_description" name="full_description" required><?php echo $offer_data['full_description']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="conditions">Условия</label>
                <textarea class="form-control" id="conditions" name="conditions" required><?php echo $offer_data['conditions']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="start_date">Дата начала</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $offer_data['start_date']; ?>" required>
            </div>
            <div class="form-group">
                <label for="end_date">Дата окончания</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $offer_data['end_date']; ?>" required>
            </div>
            <div class="form-group">
                <label for="status">Статус</label>
                <select class="form-control" id="status" name="status">
                    <option value="Показать" <?php echo $offer_data['status'] === 'Показать' ? 'selected' : ''; ?>>Показать</option>
                    <option value="Скрыть" <?php echo $offer_data['status'] === 'Скрыть' ? 'selected' : ''; ?>>Скрыть</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Сохранить изменения</button>
        </form>
    </div>

    <?php require_once("../footer.php"); ?>
</body>
</html>
