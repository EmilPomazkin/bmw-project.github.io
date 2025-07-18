<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Модельный ряд</title>
    <link rel="icon" href="img/BMW_logo_(gray).svg.png" type="image/png">
</head>
<body>
<div class="wow fadeInDown">
<?php require_once("header.php")?></div>
  <div class="container">

    <?php
  session_start();
  include("db.php");  // Пример запроса для получения всех автомобилей
$sql = "SELECT * FROM cars";
$result = $conn->query($sql);?>

<div class="modelblocks">
<?while ($car = $result->fetch_assoc()) {?>

  <a href="car.php?id=<?php echo htmlspecialchars($car['id']); ?>" class="modelbutton wow fadeInLeft">
     <img src="img/cars/<?php echo htmlspecialchars($car['img_logo']); ?>" alt="">
     <p><?php echo htmlspecialchars($car['model']); ?></p>
    </a>

<?}?></div>
<div aria-live="polite" aria-atomic="true" class="d-flex c align-items-center w-100">

<div class="toast  wow fadeInRight w-100 show mt-2  pt-2 pb-2" role="alert" aria-live="assertive" aria-atomic="true">
  <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Закрыть"></button>
  <div class="toast-body d-flex align-items-center ">
    <h5>Желайте посмотреть автомобили с пробегом уже сейчас?</h5> 
    <div class="d-flex gap-2 align-items-center ml-2">
      <button type="button" class="btnauth" onclick="window.location.href = 'available_cars.php';">Посмотреть</button>
    </div> 
  </div>  
</div>
</div></div>
<div class="wow fadeInUp">
<?php require_once("footer.php");?></div>
</body>
</html>