<?php
session_start();
include("db.php");


// Получение идентификатора автомобиля из URL
$car_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Запрос к базе данных для получения информации о конкретном автомобиле
$sql = "SELECT ac.id, c.model, ac.owners_count, ac.year, ac.mileage, ac.transmission, ac.price, 
               d.dealership_name, t.name AS trim_name, 
               ac.img_slide_1, ac.img_slide_2, ac.img_slide_3, ac.img_slide_4
        FROM available_cars ac
        JOIN cars c ON ac.car_id = c.id
        JOIN dealers d ON ac.dealer_id = d.id
        JOIN trims t ON ac.trim_id = t.id
        WHERE ac.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($car['model']); ?> <?php echo htmlspecialchars($car['year']); ?></title>
    <link rel="icon" href="img/BMW_logo_(gray).svg.png" type="image/png">
</head>
<body>
<div class="wow fadeInDown">
<?php require_once("header.php")?></div>
    <main class="container">
    <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="javascript:history.back();" class="blue">Вернуться</a>
    </li>
    
    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($car['model']); ?> <?php echo htmlspecialchars($car['year']); ?>г.</li>
  </ol>
</nav>

    <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success" role="alert">
       <a href="profile.php"><?php echo $_SESSION['message']; ?></a> 
        <?php unset($_SESSION['message']); ?>
                </div>
<?php endif; ?>
<div class="av_content">

        <?php if ($car): ?>
            <div id="carouselExample" class="carousel slide wow fadeInLeft" data-ride="carousel">
  <div class="carousel-inner">
    <?php if (!empty($car['img_slide_1'])): ?>
      <div class="carousel-item active">
        <img src="img/av_cars/<?php echo htmlspecialchars($car['img_slide_1']); ?>" class="d-block w-100 img_av_lp" alt="Изображение автомобиля 1">
      </div>
    <?php endif; ?>
    
    <?php if (!empty($car['img_slide_2'])): ?>
      <div class="carousel-item">
        <img src="img/av_cars/<?php echo htmlspecialchars($car['img_slide_2']); ?>" class="d-block w-100 img_av_lp" alt="Изображение автомобиля 2">
      </div>
    <?php endif; ?>
    
    <?php if (!empty($car['img_slide_3'])): ?>
      <div class="carousel-item">
        <img src="img/av_cars/<?php echo htmlspecialchars($car['img_slide_3']); ?>" class="d-block w-100 img_av_lp" alt="Изображение автомобиля 3">
      </div>
    <?php endif; ?>
    
    <?php if (!empty($car['img_slide_4'])): ?>
      <div class="carousel-item">
        <img src="img/av_cars/<?php echo htmlspecialchars($car['img_slide_4']); ?>" class="d-block w-100 img_av_lp" alt="Изображение автомобиля 4">
      </div>
    <?php endif; ?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Предыдущий</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Следующий</span>
  </button>
</div>

            <div class="car-details wow fadeInRight">
                <h1><?php echo htmlspecialchars($car['model']); ?></h1>
                
                <p>Комплектация: <?php echo htmlspecialchars($car['trim_name']); ?></p>
                <p>Количество владельцев: <?php echo htmlspecialchars($car['owners_count']); ?></p>
                <p>Год выпуска: <?php echo htmlspecialchars($car['year']); ?></p>
                <p>Пробег: <?php echo htmlspecialchars($car['mileage']); ?> км</p>
                <p>Трансмиссия: <?php echo htmlspecialchars($car['transmission']); ?></p>
                <p>Дилер: <?php echo htmlspecialchars($car['dealership_name']); ?></p>
                <h4 class="blue "><?php echo number_format($car['price'], 0, ' ', ' '); ?> руб.</h4>

                

<?php
session_start(); // Начинаем сессию

if (isset($_SESSION['user_id'])) {
    $user_name = htmlspecialchars($_SESSION['user_name']);
   
} else {
    $user_name = '';

}
?>

<!-- Кнопка для открытия модального окна -->
<button type="button" class="btnauth" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
    Оставить заявку
</button>
<div class="model-link">


</div>
</div>
</div>
<!-- Модальное окно -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Оставить заявку</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
    <?php if (!empty($user_name)): ?>
        <form action="submit_application.php" method="POST">
            <input type="hidden" name="car_id" value="<?php echo $car_id; ?>">
            <p>Уважаемый(ая), <?php echo $user_name ?>!</p>
            <p>Отследить свою заявку сможете <a href="profile.php" class="blue">в личном кабинете</a></p>
            <div class="modal-footer">
            <button type="submit" class="btnauth">Отправить заявку</button>
            </div>
        </form>
    <?php else: ?>
        <p>Для отправки заявки вам необходимо <a href="loginform.php" class="blue">войти в систему</a>.</p>
    <?php endif; ?>
</div>

           
        </div>
    </div>
</div>

        <?php else: ?>
            <p>Автомобиль не найден.</p>
        <?php endif; ?>
        <div aria-live="polite" aria-atomic="true" class="d-flex c align-items-center w-100">

<div class="toast  wow fadeInRight w-100 show mt-2  pt-2 pb-2" role="alert" aria-live="assertive" aria-atomic="true">
  <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Закрыть"></button>
  <div class="toast-body d-flex align-items-center ">
    <h5>Желайте узнать о моделе <span class="blue"><?php echo htmlspecialchars($car['model']); ?></span>  больше?</h5> 
    <div class="d-flex gap-2 align-items-center ml-2">
    <a href="car.php?id=<?php echo htmlspecialchars($car_id); ?>" class="btnauth">Узнать</a>
    </div> 
  </div>  
</div>
    </main>
    <div class="wow fadeInUp">
    <?php require_once("footer.php");?></div>
</body>
</html>

<?php
$conn->close();
?>
