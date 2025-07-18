<?php
session_start();
include("db.php");

$message = ""; // Сообщение для отображения
$errors = []; // Массив для хранения ошибок

// Проверка на наличие сообщения об успешной регистрации
if (isset($_SESSION['registration_success'])) {
    $message = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']); // Удаляем сообщение после отображения
}

// Обработка данных формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Проверка логина и пароля
    $sql = "SELECT * FROM users WHERE login = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role']; 

            // Проверка на роль администратора
            if ($user['role'] === 'admin') {
                header("Location: admin/admin_panel.php"); // Переход на админ-панель
            } else {
                header("Location: index.php"); // Переход на главную страницу
            }
            exit;
        } else {
            $errors[] = "Неверный пароль.";
        }
    } else {
        $errors[] = "Логин не найден.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Войти</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style/custom.css">
    <link rel="stylesheet" href="style/animate.css">
    <link rel="icon" href="img/BMW_logo_(gray).svg.png" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.2.1/dist/jquery.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js" type="text/javascript"></script>
    <script src="scripts/wow.min.js"></script>
    <script>
        new WOW().init();
    </script>
</head>

<body>
   <main>
<section>
  <div class="page">
    <div class="left_side wow fadeInLeft">  
      <h2>Авторизация</h2>
      
      <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <?php echo $message; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
      <?php endif; ?>

      <form class="loginform" method="post" autocomplete="off">
        <label for="login">Логин</label>
        <input type="text" id="login" name="login" required>
        
        <label for="password">Пароль</label>
        <input type="password" id="password" name="password" autocomplete="new-password" required>
        
        <button class="btnauth">Войти</button>
      </form>
      <p>У вас нет аккаунта? <a href="regform.php"><span class="blue">Зарегистрироваться</span></a></p>
      <a href="index.php"><span class="blue">Вернуться на главную</span></a>
    </div>
    <div class="right_side wow fadeInRight">
      <!-- Дополнительный контент может быть здесь -->
    </div>
  </div>
</section>
   </main>
</body>
</html>
