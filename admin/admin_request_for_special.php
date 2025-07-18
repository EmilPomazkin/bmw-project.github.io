<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявки на cпец. предложения</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
</head>
<body>
<?php


require_once('../header.php');
?>

<div class="container">
<h1>Заявки на cпец. предложения</h1>
<a href="admin_panel.php" class='blue'>Вернуться в админ-панель</a>
<div class="application_content">
    <?php
session_start();
include("../db.php");
// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../loginform.php'); // Перенаправляем на страницу входа
    exit(); // Завершаем выполнение скрипта
}
    // Обработка изменения статуса
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status'])) {
        $id = $_POST['application_id'];
        $status = $_POST['status'];
        $sql = "UPDATE requests_for_special SET status='$status' WHERE id=$id";
        $conn->query($sql);
    }

    // Обработка удаления заявки
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['application_id'];
        $sql = "DELETE FROM requests_for_special WHERE id=$id";
        $conn->query($sql);
    }

    // Получение заявок
    $sql = "SELECT * FROM requests_for_special ORDER BY (status = 'Новая') DESC, created_at ASC";;
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='application'>
                    <h3>Заявка ID: {$row['id']}</h3>
                    <p><strong>Имя:</strong> {$row['name']}</p>
                    <p><strong>Телефон:</strong> <a href='tel:{$row['phone']}' class='blue'>{$row['phone']}</a></p>
                               <p><strong>Дата заявки:</strong> {$row['created_at']}</p>
                    <p><strong>Статус:</strong> {$row['status']}</p>
                    <form method='POST' action=''>
                        <input type='hidden' name='application_id' value='{$row['id']}'>
                        <label for='status'>Изменить статус:</label>
                        <select name='status' id='status'>
                            <option value='Новая'" . ($row['status'] == 'Новая' ? ' selected' : '') . ">Новая</option>
                            <option value='В обработке'" . ($row['status'] == 'В обработке' ? ' selected' : '') . ">В обработке</option>
                            <option value='Завершена'" . ($row['status'] == 'Завершена' ? ' selected' : '') . ">Завершена</option>
                        </select>
                        <button type='submit' class='btn btn-primary'>Изменить</button>
                    </form> 
                    <form method='POST' action='' style='display:inline;'>
                        <input type='hidden' name='application_id' value='{$row['id']}'>
                        <input type='hidden' name='action' value='delete'>
                        <input type='submit' class='btn btn-danger' value='Удалить' onclick='return confirm(\"Вы уверены, что хотите удалить эту заявку?\");'>
                    </form>
                  </div>";
        }
    } else {
        echo "<p>Нет заявок</p>";
    }

    $conn->close();
    ?>
</div>
</div>
<?php require_once("../footer.php"); ?> </body>
</html>