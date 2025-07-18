<?php
session_start();
include("db.php");

$errors = []; // Массив для хранения ошибок

// Получение данных из формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $surname = $_POST['surname'];
    $name = $_POST['name'];
    $patronymic = $_POST['patronymic'];
    $login = $_POST['login'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Хеширование пароля
    $role = 'user'; // По умолчанию роль 'user'

    // Проверка уникальности логина
    $sql = "SELECT * FROM users WHERE login = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors[] = "Логин уже существует.";
    }

    // Проверка других полей (пример)
    if (empty($surname)) {
        $errors[] = "Фамилия обязательна.";
    }
    if (empty($name)) {
        $errors[] = "Имя обязательно.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Некорректный email.";
    }
    if (empty($phone)) {
        $errors[] = "Телефон обязателен.";
    }
    if (empty($_POST['password'])) {
        $errors[] = "Пароль обязателен.";
    }
    if ($_POST['password'] !== $_POST['confirmPassword']) {
        $errors[] = "Пароли не совпадают.";
    }

    // Если нет ошибок, вставляем нового пользователя
    if (empty($errors)) {
        $sql = "INSERT INTO users (surname, name, patronymic, login, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $surname, $name, $patronymic, $login, $email, $phone, $password, $role);

        if ($stmt->execute()) {
          $_SESSION['registration_success'] = "Регистрация прошла успешно!";
            header("Location: loginform.php");
            exit;
        } else {
            $errors[] = "Ошибка: " . $stmt->error;
        }
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
    <title>Регистрация</title>
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
    <div class="left_side left_reg wow fadeInLeft">  
      <h2>Регистрация</h2>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
      <?php endif; ?>

      <form class="loginform" method="post" autocomplete="off" onsubmit="return validateForm()">
        <label for="surname">Фамилия:</label>
        <input type="text" id="surname" name="surname" required>

        <label for="name">Имя:</label>
        <input type="text" id="name" name="name" required>

        <label for="patronymic">Отчество:</label>
        <input type="text" id="patronymic" name="patronymic" required>

        <label for="login">Логин:</label>
        <input type="text" id="login" name="login" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}"        oninvalid="this.setCustomValidity('Введите адрес электронной почты, содержащий только латиницу.')"
        oninput="this.setCustomValidity('')">

        <label for="phone">Телефон:</label>
        <input type="tel" id="phone" name="phone" required>

        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" minlength="5" required>
        
        <label for="confirmPassword">Повторите пароль:</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required>

        <button class="btnauth">Зарегистрироваться</button>
      </form>
      <p>У вас есть аккаунт? <a href="loginform.php"><span class="blue">Войти</span></a></p>
      <a href="index.php"><span class="blue">Вернуться на главную</span></a>
    </div>
    <div class="right_side right_reg wow fadeInRight">
      <!-- Дополнительный контент может быть здесь -->
    </div>
  </div>
</section>
   </main>

 <script>
  $(document).ready(function() {
      $('#phone').mask('+7 (999) 999-99-99');
  });
</script>

</body>
</html>
