<?php
session_start();
include("../db.php");
// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../loginform.php'); // Перенаправляем на страницу входа
    exit(); // Завершаем выполнение скрипта
}

// Проверка, передан ли идентификатор комплектации
if (!isset($_GET['id'])) {
    die("Ошибка: идентификатор комплектации не передан.");
}

$trim_id = intval($_GET['id']);
$message = ''; // Переменная для хранения сообщений

// Обработка обновления комплектации
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $engine = $_POST['engine'];
    $features = $_POST['features'];

    // Подготовка и выполнение запроса на обновление комплектации
    $sql = "UPDATE trims SET name = ?, engine = ?, features = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $engine, $features, $trim_id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Комплектация успешно обновлена.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Ошибка обновления комплектации: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Выполнение запроса для получения данных комплектации
$sql = "SELECT * FROM trims WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trim_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Ошибка: комплектация не найдена.");
}

$trim = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование комплектации</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php require_once("../header.php")?>
    <div class="container">
        <h1>Редактирование комплектации</h1>
        <a href="javascript:history.back();" class="blue">Вернуться</a>

        <?php if ($message): ?>
            <?php echo $message; ?>
        <?php endif; ?>

        <form method="POST" action="" class="mt-3">
            <div class="form-group">
                <label for="name">Название:</label>
                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($trim['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="engine">Двигатель:</label>
                <input type="text" class="form-control" name="engine" value="<?php echo htmlspecialchars($trim['engine']); ?>" required>
            </div>
            <div class="form-group">
                <label for="features">Особенности:</label>
                <textarea class="form-control" name="features" rows="3" required><?php echo htmlspecialchars($trim['features']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        </form>
    </div>
<?php require_once("../footer.php"); ?> </body>
</html>
