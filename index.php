<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style/custom.css">
    <link rel="stylesheet" href="style/animate.css">
    <link rel="icon" href="img/BMW_logo_(gray).svg.png" type="image/png">

    
    <script>
      new WOW().init();
      </script>
</head>
<body>
<div class="wow fadeInDown">
<?php require_once("header.php")?></div>
      <main>
        <section><div class="container wow fadeIn"><div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <div class="textslide">
                <h3 >Узнайте об автомобилях BMW</h3>
                <p>Информация о модельном ряде</p>
                <a href="models.php" class="btninfo">Подробнее</a>
              </div>
              <img src="img/ban1.jpg" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
              <div class="textslide">
              <h3 >Добиваемся новых высот вместе</h3>
              <p>Выбор BMW - выбор мечты</p>
       
            </div>
              <img src="img/ban2.jpg" class="d-block w-100 " alt="...">
            </div>
            <div class="carousel-item">
              <div class="textslide">
                <h3 >Мы заботимся о вас</h3>
                <p>Официальный автосервис BMW</p>
                <a href="index_service.php" class="btninfo">Подробнее</a>
              </div>
              <img src="img/ban3.jpg" class="d-block w-100" alt="...">
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Предыдущий</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Следующий</span>
          </button>
        </div></div></section>

        <section class="container">
          <div class="infogroup wow fadeInUp">
          <div class="blinfo">
          <!-- <img src="img/icon-car.png" alt="" class="iconinfo"> -->
     
          <h3 class="hinfo">Авто с пробегом</h3>
          <a href="available_cars.php" class="btninfo">Подробнее</a>
        
        </div>
        <div class="blinfo">
          <!-- <img src="img/service.png" alt="" class="iconinfo"> -->
        
          <h3 class="hinfo">Сервис</h3>
          <a href="index_service.php" class="btninfo">Подробнее</a>
       
        </div>
        <div class="blinfo">
          <!-- <img src="img/testdrive.png" alt="" class="iconinfo"> -->
         
          <h3 class="hinfo">Тест-драйв</h3>
          <a href="test_drive.php" class="btninfo">Подробнее</a>
     
        </div>
          </div>
       </section>

       <section>
        
        <div class="container">
          <h1 class="h1" >Преимущества BMW</h1>
          <div class="advflex">
            <div class="advblock">
            
              <img src="img/bmw.jpg" alt="" class="advimg wow fadeInLeft">
              
              <div class="adv-text wow fadeInRight">
                <h2><span class="blue">Динамика и  производительность</span></h2>
                <p><span class="blue"> BMW</span> предлагает широкий ассортимент мощных и эффективных двигателей, которые обеспечивают отличную динамику разгона. Системы управления, такие как xDrive (полный привод), обеспечивают отличную управляемость и стабильность на дороге.</p>
              </div>
            </div>
            <div class="advblock"> <img src="img/advjpg.jpg" alt="" class="advimg wow fadeInRight">
              <div class="adv-text wow fadeInLeft">
                <h2><span class="blue"> Комфорт  и качество интерьера</span></h2>
                <p>Использование высококачественных материалов в отделке интерьера создает роскошную атмосферу. Интуитивно понятные мультимедийные системы и комфортные сиденья делают поездки приятными и удобными.</p>
              </div>
              
             
            </div>
            <div class="advblock">
             
              <img src="img/bmw_large.jpeg" alt="" class="advimg wow fadeInLeft">
              <div class="adv-text wow fadeInRight">
                <h2><span class="blue">Безопасность и инновации </span></h2>
                <p><span class="blue">BMW</span> интегрирует передовые технологии безопасности, включая системы помощи водителю и активные системы предотвращения аварий. Постоянные инвестиции в исследования и разработки позволяют <span class="blue">BMW</span> оставаться на переднем крае автомобильных технологий.</p>
              </div>
            </div>
          </div>
        </div>
       </section>
       <section><div class="container ">
        
        <div class="video-background">
          <video autoplay loop muted>
              <source src="img/bmwwid.mp4" type="video/mp4">
              Ваш браузер не поддерживает видео.
          </video>
          <div class="overlay"></div>
          <div class="content wow fadeInRight">
              <h1>Время покупки автомобиля мечты</h1>
              <p>Получите персональные скидки от компании BMW</p>
                  <!-- Вывод сообщений об успехе -->
                  <?php if (isset($_SESSION['registration_success'])): ?>
        <div class="alert alert-success mt-3">
            <?php
            echo $_SESSION['registration_success'];
            unset($_SESSION['registration_success']); // Удаляем сообщение после отображения
            ?>
        </div>
    <?php endif; ?>

    <!-- Вывод ошибок, если есть -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mt-3">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

              <form action="add_request.php" method="POST" class="formspec">
                <label for="name">Имя</label>
                <input type="text" id="name" name="name" required>
                <label for="phone">Телефон</label>
                <input type="text" id="phone" name="phone" placeholder="+7 (___) ___-__-__" required>
    
                <button type="submit" class="btninfo">Отправить</button>
            </form>
          </div>
      </div>
 
 </div> </section>
 <div class="wow fadeInUp">
 <?php require_once("footer.php");?></div>

      </main>

 <script>
  $(document).ready(function() {
      $('#phone').mask('+7 (999) 999-99-99');
  });
</script>
 
</body>
</html>