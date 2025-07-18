<?php
session_start();
include("db.php");

// Получение ID автомобиля из URL
$car_id = intval($_GET['id']);

// Запрос к базе данных
$sql = "SELECT * FROM cars WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Ошибка подготовки запроса: " . $conn->error);
}

$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();

// Проверка на наличие автомобиля
if (!$car) {
    die("Автомобиль не найден.");
}

// Запрос для получения всех комплектаций
$sql_trims = "SELECT * FROM trims";
$stmt_trims = $conn->prepare($sql_trims);

if ($stmt_trims === false) {
    die("Ошибка подготовки запроса для комплектаций: " . $conn->error);
}

$stmt_trims->execute();
$result_trims = $stmt_trims->get_result();

// Закрытие соединения
$stmt->close();
$stmt_trims->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($car['model']); ?></title>
    <link rel="icon" href="img/BMW_logo_(gray).svg.png" type="image/png">
   
</head>
<body>

  <div class="wow fadeInDown">
<?php
  require_once("header.php")?> </div>
  <div class="container">

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="javascript:history.back();" class="blue">Вернуться</a>
    </li>
    
    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($car['model']); ?></li>
  </ol>
</nav>

    <div class="hero_car">
      <div class="hero__car-txt">
    <h1 class="h_hero_car"><?php echo htmlspecialchars($car['model']); ?></h1>
    <p> <?php echo nl2br(htmlspecialchars($car['description'])); ?></p></div>
 <img src="img/cars/<?php echo htmlspecialchars($car['img_slide1']); ?>" class="img-fluid" alt="..."></div>

 <div class="infogroupcar">
  <h2>Особенности автомобиля </h2>
  
  <div class="row">
    <div class="col-6 col-md-4 infoflex4 text-center wow fadeInLeft"><h2><span class="blue"></strong> <?php echo htmlspecialchars($car['body_type']); ?></span></h2>
    <p>Тип кузова</p></div>
    <div class="col-6 col-md-4 infoflex4 text-center wow zoomIn"><h2><span class="blue"><?php echo htmlspecialchars($car['seats']); ?></span></h2>
    <p>Количество мест</p></div>
    <div class="col-6 col-md-4 infoflex4 text-center wow fadeInRight"><h2><span class="blue"><?php echo htmlspecialchars($car['doors']); ?></span></h2>
    <p>Количество дверей</p></div>
  </div>

 </div>
 <div class="infoexs">
  <div class="infoblockexs wow fadeInLeft">
    <img src="img/cars/<?php echo htmlspecialchars($car['img_slide2']); ?>" alt="">
    <h3>Вид спереди</h3>
    <p>Автомобиль <?php echo htmlspecialchars($car['model']); ?> выделяется агрессивным дизайном с характерными двойными решетками радиатора и элегантными светодиодными фарами. Низкий капот и динамичные линии кузова создают ощущение движения и уверенности. </p>
  </div>
  <div class="infoblockexs wow zoomIn">
    <img src="img/cars/<?php echo htmlspecialchars($car['img_slide3']); ?>" alt="">
    <h3>Вид сбоку</h3>
    <p> <?php echo htmlspecialchars($car['model']); ?> демонстрирует идеальный баланс элегантности и спорта. Плавные линии и высокая линия окон создают аэродинамичность, а широкие колесные арки подчеркивают мощь, делая автомобиль легко узнаваемым.</p>
  </div>
  <div class="infoblockexs wow fadeInRight">
    <img src="img/cars/<?php echo htmlspecialchars($car['img_slide4']); ?>" alt="">
    <h3>Вид сзади</h3>
    <p>Автомобиль <?php echo htmlspecialchars($car['model']); ?> выглядит мощно и стильно. Широкие задние крылья и выразительные фонари с трехмерным эффектом подчеркивают стабильность. Спортивный диффузор и выхлопные трубы добавляют динамики и запоминаемости.</p>
  </div>
 </div>
 <h2>Характеристики автомобиля</h2>
   <div class="character ">
   <img src="img/cars/<?php echo htmlspecialchars($car['img_drawing']); ?>" class="wow fadeInRight" alt="">

    <div class="characterinfo wow fadeInLeft">
    <p ><strong>Типы двигателей:</strong> <?php echo nl2br(htmlspecialchars($car['engine_types'])); ?></p>
    <p><strong>Трансмиссия:</strong> <?php echo htmlspecialchars($car['transmission']); ?></p>
    <p ><strong>Габариты:</strong> <?php echo nl2br(htmlspecialchars($car['dimensions'])); ?></p>
    <p><strong>Подвеска:</strong> <?php echo nl2br(htmlspecialchars($car['suspension'])); ?></p>
    <p ><strong>Производительность:</strong> <?php echo nl2br(htmlspecialchars($car['performance'])); ?></p>
    <p><strong>Экономия топлива:</strong> <?php echo nl2br(htmlspecialchars($car['fuel_efficiency'])); ?></p>
  </div>
   </div>
    
    <h2>Интерьер автомобиля</h2>
<div class="infointer ">
<div class="card wow fadeInLeft" style="width: 18rem;">
  <img src="img/cars/<?php echo htmlspecialchars($car['img_inter1']); ?>" class="card-img-top" alt="...">
  <div class="card-body">
    <h5 class="card-title">Безопасность</h5>
    <p class="card-text"><?php echo nl2br(htmlspecialchars($car['safety'])); ?></p>
   
  </div>
</div>
<div class="card wow zoomIn" style="width: 18rem;">
  <img src="img/cars/<?php echo htmlspecialchars($car['img_inter2']); ?>" class="card-img-top" alt="...">
  <div class="card-body">
    <h5 class="card-title">Комфорт и технологии</h5>
    <p class="card-text">Комфорт и технологии:</strong> <?php echo nl2br(htmlspecialchars($car['comfort_technology']));?></p>
  
  </div>
</div>
<div class="card wow fadeInRight" style="width: 18rem;">
  <img src="img/cars/<?php echo htmlspecialchars($car['img_inter3']); ?>" class="card-img-top" alt="...">
  <div class="card-body">
    <h5 class="card-title">Дополнительные опции</h5>
    <p class="card-text"><?php echo nl2br(htmlspecialchars($car['additional_options'])); ?></p>
   
  </div>
</div>
</div>

    <h2>Комплектации</h2>

<div class="accordion accordion-flush" id="accordionFlushExample">

<?php while ($trim = $result_trims->fetch_assoc()): ?>
  <div class="accordion-item wow fadeInDown">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo htmlspecialchars($trim['name']);?>" aria-expanded="false" aria-controls="<?php echo htmlspecialchars($trim['name']);?>">
      <?php echo htmlspecialchars($trim['name']);?>
      </button>
    </h2>
    <div id="<?php echo htmlspecialchars($trim['name']);?>" class="accordion-collapse collapse" data-bs-parent="#<?php echo htmlspecialchars($trim['name']);?>">
      <div class="accordion-body"><?php echo nl2br(htmlspecialchars($trim['features'])); ?></div>
    </div>
  </div><?php endwhile; ?>
  
  <div class="wow fadeInUp">
  <?php require_once("footer.php");?></div>
</body>
</html>
