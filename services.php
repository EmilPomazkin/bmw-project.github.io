<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Услуги</title>
    <link rel="icon" href="img/BMW_logo_(gray).svg.png" type="image/png">

</head>
<body>
<div class="wow fadeInDown">
<?php require_once("header.php")?></div>
<main class="container">
<section>
<div aria-live="polite" aria-atomic="true" class="d-flex c align-items-center w-100">

<div class="toast  wow fadeInRight w-100 show mt-2  pt-2 pb-2" role="alert" aria-live="assertive" aria-atomic="true">
  <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Закрыть"></button>
  <div class="toast-body d-flex align-items-center ">

    <h5>Если гарантия на ваш автомобиль <span class="blue">BMW</span>  ещё действует, то многие услуги могут предоставляться бесплатно. </h5> 
    <div class="d-flex gap-2 align-items-center ml-2">
      <buttontype="button" class="btnauth" data-bs-toggle="modal" data-bs-target="#exampleModal">Узнать</button>
    </div> 
  </div>  
</div>
</div></div>


<!-- Модальное окно -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Гарантия BMW</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body">
      <h3 class="blue">1. Гарантия на новый автомобиль</h3>
    <ul>
        <li><strong>Срок действия:</strong> Обычно <span class="blue">2</span>-<span class="blue">3</span> года с момента покупки.</li>
        <li><strong>Пробег:</strong> Безлимитный пробег в течение первого года (в зависимости от модели и страны).</li>
        <li><strong>Покрытие:</strong> Защита от дефектов материалов и изготовления.</li>
    </ul>

    <h3 class="blue">2. Гарантия на двигатель и трансмиссию</h3>
    <ul>
        <li><strong>Срок действия:</strong> Обычно до <span class="blue">100,000</span> км или <span class="blue">5</span> лет (в зависимости от модели).</li>
        <li><strong>Покрытие:</strong> Все основные компоненты, включая блок цилиндров, головку блока и системы смазки.</li>
    </ul>

    <h3 class="blue">3. Гарантия на кузов</h3>
    <ul>
        <li><strong>Срок действия:</strong> Обычно <span class="blue">12</span> лет на коррозию.</li>
        <li><strong>Покрытие:</strong> Защита от ржавчины и коррозии на кузове автомобиля.</li>
    </ul>

    <h3 class="blue">4. Гарантия на оригинальные запчасти</h3>
    <ul>
        <li><strong>Срок действия:</strong> Обычно <span class="blue">2</span> года с момента установки.</li>
        <li><strong>Покрытие:</strong> Все оригинальные запчасти, установленные в авторизованных сервисных центрах BMW.</li>
    </ul>

    <h3 class="blue">5. Дополнительные программы гарантии</h3>
    <ul>
        <li><strong>Расширенная гарантия:</strong> Возможность продления гарантии на определённый срок или пробег.</li>
        <li><strong>Гарантия на электромобили:</strong> Специальные условия гарантии для электрических и гибридных моделей.</li>
    </ul>

    <h3 class="blue">Условия гарантии</h3>
    <p>Регулярное техническое обслуживание является обязательным для сохранения гарантии.</p>
    
    <h3 class="blue">Как воспользоваться гарантией</h3>
    <ul>
        <li>Обратитесь в авторизованный сервисный центр.</li>
        <li>Предоставьте документы, подтверждающие покупку и предыдущие обслуживания.</li>
        <li>Ожидайте диагностику от специалистов.</li>
    </ul>
      </div>
    </div>
  </div>
</div>

</section>
<section>
    <div class="services_blocks mt-3">
        <?php
        include("db.php");
        // Запрос данных из таблицы car_services
        $sql = "SELECT * FROM car_services WHERE status = 'Показать'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Вывод данных каждой услуги
            while($row = $result->fetch_assoc()) {
                echo "<a class='service' href='service_page.php?id=" . $row["id"] . "'>";
                echo "<img src='img/service/" . htmlspecialchars($row['image_serv']) . "' alt=''>";
                echo "<div class='serv_cart_text'>";
                echo "<h4>" . htmlspecialchars($row["service_name"]) . "</h4>";
                echo "<p class='blue'> " . htmlspecialchars($row["price"]) . " руб.</p>";
                echo "</div>";
                echo "</a>";
            }
        } else {
            echo "<p>Услуги не найдены.</p>";
        }
        $conn->close();
        ?>
    </div>
</section>
</main>
<div class="wow fadeInUp">
<?php require_once("footer.php");?></div>
</body>
</html>