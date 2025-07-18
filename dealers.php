<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Найти диллера</title>
    <link rel="icon" href="img/BMW_logo_(gray).svg.png" type="image/png">
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=5d2e1362-c0eb-4e5e-a658-5c0471d8f300" type="text/javascript"></script>
    <style>
        #map {
            width: 100%;
            height: 500px;
        }
    </style>
</head>
<body>
    <div class="wow fadeInDown">
        <?php require_once("header.php"); ?>
    </div>
    <div class="container">
        <h1>Выберите город</h1>
        <form class="role-form">
            <select id="citySelect" onchange="updateMap()" style="font-size:20px; margin-bottom:10px">
                <?php
                 session_start();
                 include("db.php");
         

                // Запрос для получения городов
                $sql = "SELECT name, latitude, longitude FROM cities";
                $result = $conn->query($sql);

                // Генерация опций для выпадающего списка
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['name']}' data-lat='{$row['latitude']}' data-lng='{$row['longitude']}'>{$row['name']}</option>";
                    }
                } else {
                    echo "<option value=''>Нет городов</option>";
                }

                $conn->close();
                ?>
            </select>
        </form>
        <div id="map"></div>

        <script>
            let map;

            function initMap() {
                map = new ymaps.Map("map", {
                    center: [55.7558, 37.6173], // Координаты Москвы
                    zoom: 10
                });
                updateMap();
                loadDealers(); 
            }

            function updateMap() {
                const select = document.getElementById('citySelect');
                const selectedOption = select.options[select.selectedIndex];
                const latitude = parseFloat(selectedOption.getAttribute('data-lat'));
                const longitude = parseFloat(selectedOption.getAttribute('data-lng'));

                const coordinates = [latitude, longitude];
                map.setCenter(coordinates, 10);
                loadDealers(); // Загрузка диллеров для выбранного города
            }

            function loadDealers() {
                fetch('get_dealers.php')
                    .then(response => response.json())
                    .then(dealers => {
                        clearMarkers();

                        dealers.forEach(dealer => {
                            const latitude = parseFloat(dealer.latitude);
                            const longitude = parseFloat(dealer.longitude);
                            
                            // Создаем маркер
                            const marker = new ymaps.Placemark([latitude, longitude], {
                                balloonContent: `
                                    <strong>${dealer.dealership_name}</strong><br>
                                    Адрес: ${dealer.address}<br>
                                    Телефон: ${dealer.phone}<br>
                                    График работы: Пн-Пт с 10:00 до 20:00, Сб-Вс выходной
                                `
                            }, {
                                iconLayout: 'default#image',
                                iconImageHref: 'img/BMW_logo_(gray).svg.png',
                                iconImageSize: [30, 30],
                                iconImageOffset: [-15, -15]
                            });

                            // Добавляем маркер на карту
                            map.geoObjects.add(marker);
                        });
                    })
                    .catch(error => console.error('Ошибка загрузки диллеров:', error));
            }

            function clearMarkers() {
                map.geoObjects.removeAll();
            }

            ymaps.ready(initMap);
        </script>
    </div>
    <div class="wow fadeInUp">
        <?php require_once("footer.php"); ?>
    </div>
</body>
</html>
