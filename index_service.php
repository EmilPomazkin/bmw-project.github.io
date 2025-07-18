<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сервис BMW</title>
    <link rel="icon" href="img/BMW_logo_(gray).svg.png" type="image/png">
    
</head>
<body>
<div class="wow fadeInDown">
<?php require_once("header.php")?></div>
<div class="container wow fadeIn"><div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <div class="textslide">
                <h3>Официальный автосервис BMW</h3>
                <p>Поддержа автомобилей по немецким стандартам</p>
              </div>
              <img src="img/serv_slide2.jpg" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
              <div class="textslide">
              <h3 >Специальные предложения для владельцев BMW</h3>

              <a href="special_offer.php" class="btninfo">Подробнее</a>
            </div>
              <img src="img/serv_slide1.jpg" class="d-block w-100 " alt="...">
            </div>
            <div class="carousel-item">
              <div class="textslide">
              <h3>Покупка премиального автомобиля BMW</h3>

                <a href="index.php" class="btninfo">Подробнее</a>
              </div>
              <img src="img/serv_slide3.jpg" class="d-block w-100" alt="...">
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
        </div></div>
  
        <section class="container mt-3 ">

    <div id="serviceSlider" class="carousel slide mt-3 wow fadeIn" data-ride="carousel" data-interval="5000">
        <div class="carousel-inner ">
            <div class="carousel-item active">
             
                    <div class="row ">
                        <div class="col-12 col-md-6 service_container_content ">
                            <img src="img/iconserv1.png" alt="">
                            <div>
                                <h2>Качество обслуживания</h2>
                                <p>Оригинальные запчасти: Использование только оригинальных запчастей BMW, что обеспечивает высокое качество и долговечность.</p>
                                <p>Квалифицированный персонал: Все механики проходят обучение и сертификацию в BMW, что гарантирует высокий уровень профессионализма.</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 service_container_content ">
                            <img src="img/iconserv2.png" alt="">
                            <div>
                                <h2>Гарантия на работы и запчасти</h2>
                                <p>Гарантия на запчасти: Обычно предоставляется гарантия на замененные детали, которая может составлять от 1 до 2 лет.</p>
                                <p>Гарантия на услуги: На выполненные работы также может предоставляться гарантия, что подтверждает надежность сервиса.</p>
                            </div>
                        </div>
         
                </div>
            </div>
            <div class="carousel-item">
   
                    <div class="row">
                        <div class="col-12 col-md-6 service_container_content">
                            <img src="img/iconserv3.png" alt="" >
                            <div>
                                <h2>Клиентская поддержка</h2>
                                <p>Консультации и помощь: Оперативная поддержка клиентов по вопросам обслуживания и ремонта автомобилей.</p>
                                <p>Запись на обслуживание: Удобная система записи на техническое обслуживание через веб-сайт или мобильное приложение.</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 service_container_content">
                            <img src="img/iconserv4.png" alt="">
                            <div>
                                <h2>Программы обслуживания</h2>
                                <p>Плановое ТО: Регулярное техническое обслуживание, которое помогает предотвратить серьезные поломки и продлить срок службы автомобиля.</p>
                                <p>Расширенные гарантии: Возможность приобретения дополнительных программ гарантии на определенные компоненты автомобиля.</p>
                            </div>
                        </div>
         
                </div>
            </div>
        </div>

     

        <div class="carousel-indicators">
    <button type="button" data-bs-target="#serviceSlider" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#serviceSlider" data-bs-slide-to="1" aria-label="Slide 2"></button>

  </div>

    </div>
</section>
<section class="container">
<div class="guaranteeblock">
  <img src="img/gar.png" alt="" class="wow fadeInLeft">
  <div class="guarantinfo wow fadeInRight">
    <h1>Официальная гарантия BMW</h1>
    <p>Компания BMW предоставляет официальную гарантию, где представлен ряд преимуществ и услуг, которые обеспечивают владельцам автомобилей уверенность в качестве и надежности их транспортных средств.</p>
  </div>
</div>
</section>
<section class="container mt-3">
  <h1>Специальные предложения</h1>
  
  <div id="offersCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <?php
      include("db.php");
      $sql = "SELECT * FROM special_offer WHERE status = 'Показать' ORDER BY timestamp DESC LIMIT 5";
      $result = $conn->query($sql);
      
      if ($result->num_rows > 0) {
          $first = true;
          while ($row = $result->fetch_assoc()) {
              $active = $first ? 'active' : '';
              $first = false;
      ?>
      <div class="carousel-item <?php echo $active; ?>">
        <div class="offer-slide">
          <img src="img/offer/<?php echo htmlspecialchars($row['image']); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($row['title']); ?>">
          <div class="carousel-caption">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><?php echo htmlspecialchars($row['short_description']); ?></p>
            <a href="special_offer.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Подробнее</a>
          </div>
        </div>
      </div>
      <?php
          }
      } else {
      ?>
      <div class="carousel-item active">
        <div class="offer-slide">
          <div class="carousel-caption">
            <h3>Нет доступных предложений</h3>
            <p>Скоро появятся новые специальные предложения</p>
          </div>
        </div>
      </div>
      <?php
      }
      $conn->close();
      ?>
    </div>
    
    <button class="carousel-control-prev" type="button" data-bs-target="#offersCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Предыдущий</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#offersCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Следующий</span>
    </button>
    
    <div class="carousel-indicators">
      <?php
      if ($result->num_rows > 0) {
          for ($i = 0; $i < $result->num_rows; $i++) {
              $active = $i === 0 ? 'active' : '';
              echo '<button type="button" data-bs-target="#offersCarousel" data-bs-slide-to="'.$i.'" class="'.$active.'" aria-label="Slide '.($i+1).'"></button>';
          }
      }
      ?>
    </div>
  </div>
</section>

<section class="container mt-3 reasonsbg wow fadeIn ">
  <h1 style="padding-top:10px; padding-bottom:10px;">4 причины выбрать официальный сервис</h1>
  <div class="reasons">
    <div class="reasonsblock">
      <div class="reasons_header">
        <h1 class="number">1</h1>
        <h4 class="h_text">Качество и надежность</h>
      </div>
      <p>Высокие стандарты производства и долговечность автомобилей.</p>
    </div>
    <div class="reasonsblock">
      <div class="reasons_header">
        <h1 class="number">2</h1>
        <h4 class="h_text">Инновационные технологии</h>
      </div>
      <p>Современные системы и экономичные электрические модели.</p>
    </div>
    <div class="reasonsblock">
      <div class="reasons_header">
        <h1 class="number">3</h1>
        <h4 class="h_text">Индивидуальный подход</h>
      </div>
      <p>Персонализированные услуги и консультации, учитывающие потребности каждого клиента.</p>
    </div>
    <div class="reasonsblock">
      <div class="reasons_header">
        <h1 class="number">4</h1>
        <h4 class="h_text">Удобство обслуживания</h>
      </div>
      <p>Удобная запись на сервис и возможность предоставления временного автомобиля.</p>
    </div>
  </div>
</section>
<div class="wow fadeInUp">
<?php require_once("footer.php");?></div>
</body>
</html>