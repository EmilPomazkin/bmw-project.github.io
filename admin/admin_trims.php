<?php
session_start();
include("../db.php");
// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../loginform.php'); // Перенаправляем на страницу входа
    exit(); // Завершаем выполнение скрипта
}

$message = ''; // Переменная для хранения сообщений

// Обработка добавления новой комплектации
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_trim'])) {
    $name = $_POST['name'];
    $engine = $_POST['engine'];
    $features = $_POST['features'];

    // Подготовка и выполнение запроса на добавление комплектации
    $sql = "INSERT INTO trims (name, engine, features) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $engine, $features);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Комплектация успешно добавлена.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Ошибка добавления комплектации: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Обработка удаления комплектации
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    // Подготовка и выполнение запроса на удаление комплектации
    $sql = "DELETE FROM trims WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Комплектация успешно удалена.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Ошибка удаления комплектации: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Выполнение запроса для получения комплектаций
$sql = "SELECT * FROM trims";
$result = $conn->query($sql);

// Проверка на ошибки выполнения запроса
if (!$result) {
    die("Ошибка выполнения запроса: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление комплектациями</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php require_once("../header.php")?>
    <div class="container">
        <h1>Управление комплектациями</h1>
        <a href="admin_panel.php" class="blue">Вернуться в админ-панель</a>

        <?php if ($message): ?>
            <?php echo $message; ?>
        <?php endif; ?>

        <h2 class="mt-3">Добавить новую комплектацию</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Название:</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="form-group">
                <label for="engine">Двигатель:</label>
                <input type="text" class="form-control" name="engine" required>
            </div>
            <div class="form-group">
                <label for="features">Особенности:</label>
                <textarea class="form-control" name="features" rows="3" required></textarea>
            </div>
            <button type="submit" name="add_trim" class="btn btn-primary">Добавить комплектацию</button>
        </form>

        <h2 class="mt-5">Существующие комплектации</h2>
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='col-md-4 mb-4'>
                            <div class='card'>
                                <div class='card-body'>
                                    <h5 class='card-title'>{$row['name']}</h5>
                                    <p class='card-text'><strong>Двигатель:</strong> {$row['engine']}</p>
                                    <p class='card-text'><strong>Особенности:</strong> {$row['features']}</p>
                                    <a href='edit_trim.php?id={$row['id']}' class='btn btn-primary'>Изменить</a>
                                    <a href='admin_trims.php?delete_id={$row['id']}' class='btn btn-danger' onclick='return confirm(\"Вы уверены, что хотите удалить эту комплектацию?\");'>Удалить</a>
                                </div>
                            </div>
                          </div>";
                }
            } else {
                echo "<p>Нет доступных комплектаций.</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>

    
<?php require_once("../footer.php"); ?> </body>
</html>
