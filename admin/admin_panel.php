<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель</title>
    <link rel="icon" href="../img/BMW_logo_(gray).svg.png" type="image/png">

</head>
<body>
<?php
session_start();
include("../db.php"); 

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../loginform.php'); // Перенаправляем на страницу входа
    exit(); // Завершаем выполнение скрипта
}

require_once('../header.php');
?>
<div class="container">
    <div class="admin-content">
        <div class="admin_block">    
            <h2>Заявки по автомобилям</h2>
 <a href="admin_application.php" class="btnauth">Перейти</a></div>
 <div class="admin_block">    
            <h2>Заявки по спец.предложениям</h2>
 <a href="admin_request_for_special.php" class="btnauth">Перейти</a></div>
 <div class="admin_block">    
            <h2>Заявки на тест-драйв</h2>
 <a href="admin_test_drive.php" class="btnauth">Перейти</a></div>
 <div class="admin_block">    
            <h2>Пользователи</h2>
 <a href="admin_user_list.php" class="btnauth">Перейти</a></div>
 <div class="admin_block">   
            <h2>Модельный ряд автомобилей</h2>
 <a href="admin_car.php" class="btnauth">Перейти</a></div>
 <div class="admin_block">    
            <h2>Комплектации</h2>
 <a href="admin_trims.php" class="btnauth">Перейти</a></div>
 <div class="admin_block">    
            <h2>Автомобили с пробегом</h2>
 <a href="admin_available_cars.php" class="btnauth">Перейти</a></div>
 <div class="admin_block">    
            <h2>Дилеры</h2>
 <a href="admin_dealers.php" class="btnauth">Перейти</a></div>
 <div class="admin_block">    
            <h2>Города</h2>
 <a href="admin_cities.php" class="btnauth">Перейти</a></div>
 <div class="admin_block">    
            <h2>Услуги</h2>
 <a href="admin_services.php" class="btnauth">Перейти</a></div>
 <div class="admin_block">    
            <h2>Специальные предложения</h2>
 <a href="admin_special_offer.php" class="btnauth">Перейти</a></div>
 <div class="admin_block">    
            <h2>Заявки на ТО</h2>
 <a href="admin_service_appointments.php" class="btnauth">Перейти</a></div>
    </div>

</div>
<?php require_once("../footer.php"); ?> </body>
</html>