<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("db.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../style/custom.css">
    <link rel="stylesheet" href="../style/animate.css">
    <link rel="icon" href="img/BMW_logo_(gray).svg.png" type="image/png">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.2.1/dist/jquery.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js" type="text/javascript"></script>
    <script src="scripts/wow.min.js"></script>
    <script>
      new WOW().init();
    </script>
</head>
<body>
<header>
  <div class="container">
    <nav class="navbar navbar-expand-lg b">
      <div class="container-fluid">
        <a class="navbar-brand" href="../index.php"><img src="../img/BMW_logo_(gray).svg.png" alt="logo" class="logo"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Переключатель навигации">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Автосалон
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item nav-link" href="../models.php">Модельный ряд</a></li>
                <li><a class="dropdown-item nav-link" href="../available_cars.php">Автомобили с пробегом</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Сервис
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item nav-link" href="../index_service.php">Информация о сервисе</a></li>
                <li><a class="dropdown-item nav-link" href="../special_offer.php">Специальные предложения</a></li>
                <li><a class="dropdown-item nav-link" href="../services.php">Услуги</a></li>
              </ul>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../about.php">О нас</a>
            </li>
          </ul>
          
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
    <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Заявки
              </a>
              <ul class="dropdown-menu">
                <li>
             
                  <a class="dropdown-item nav-link" href="../test_drive.php">Тест-драйв</a></li>
                <li><a class="dropdown-item nav-link" href="../maintenance.php">Запись на ТО</a></li>
              </ul>
            </li>
            <li class="nav-item headlogo">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-map" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15.817.113A.5.5 0 0 1 16 .5v14a.5.5 0 0 1-.402.49l-5 1a.502.502 0 0 1-.196 0L5.5 15.01l-4.902.98A.5.5 0 0 1 0 15.5v-14a.5.5 0 0 1 .402-.49l5-1a.5.5 0 0 1 .196 0L10.5.99l4.902-.98a.5.5 0 0 1 .415.103zM10 1.91l-4-.8v12.98l4 .8V1.91zm1 12.98 4-.8V1.11l-4 .8v12.98zm-6-.8V1.11l-4 .8v12.98l4-.8z"/>
              </svg>
              <a class="nav-link" href="../dealers.php">Найти дилера</a>
            </li>
          </ul>
          <ul class="navbar-nav mb-2 mb-lg-0">
            <li class="nav-item headlogo display">
              <img src="../img/tel.png" alt="test-drive" class="logo-in-header">
              <a class="nav-link" href="tel:+79998887766">+7 (999) 888-77-66</a>
            </li>
            <?php
            if (isset($_SESSION['user_name'])) {
                // Если пользователь авторизован, показываем его имя и выпадающий список
                echo '<li class="nav-item dropdown" >
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . htmlspecialchars($_SESSION['user_name']) . '</a>
                        <ul class="dropdown-menu">';
                
                // Проверяем, является ли пользователь администратором
                if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                    echo '<li><a class="dropdown-item" href="../admin/admin_panel.php">Админ панель</a></li>';
                }
                
                echo '<li><a class="dropdown-item" href="../profile.php">Профиль</a></li>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item" href="../logout.php">Выход</a></li>
                      </ul>
                      </li>';
            } else {
                // Если не авторизован, показываем кнопку входа
                echo '<li class="nav-item headlogo" style="margin-left: 20px;">
                        <a class="nav-link" href="../loginform.php">Войти</a>
                      </li>';
            }
            ?>
          </ul>
        </div>
        <li class="nav-item none headlogo ">
            <img src="../img/tel.png" alt="test-drive" class="logo-in-header">
            <a class="nav-link" href="tel:+79998887766">+7 (999) 888-77-66</a>
        </li>
      </div>
    </nav>
  </div>
</header>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
