<?php
include("db.php");
// Получение id из URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Извлечение данных из таблицы special_offer по id
$sql = "SELECT * FROM special_offer WHERE id = $id AND status = 'Показать'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($result && $result->num_rows > 0) ? $result->fetch_assoc()['title'] : 'Предложение не найдено'; ?></title>
    <link rel="icon" href="img/BMW_logo_(gray).svg.png" type="image/png">
</head>
<body>
<div class="wow fadeInDown">
<?php require_once("header.php")?></div>
<main class="container">

<?php
$sql = "SELECT * FROM special_offer WHERE id = $id AND status = 'Показать'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    // Получаем данные предложения
    $row = $result->fetch_assoc();
    
    // Функция для форматирования даты
    function formatDate($date) {
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);
        return $dateTime ? $dateTime->format('d.m.Y') : 'Неизвестная дата';
    }
    function formatDatetimestamp($date) {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        return $dateTime ? $dateTime->format('d.m.Y') : 'Неизвестная дата';
    }

?>
<section>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="javascript:history.back();" class="blue">Вернуться</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($row['title']); ?></li>
  </ol>
</nav>
<div class="h_block_offer">
    <div class="h_offer_content wow fadeInLeft">
        <p class="offer_timestamp">Дата опубликования: <span class="blue"><?php echo formatDatetimestamp($row['timestamp']); ?></span></p>
        <h1><?php echo htmlspecialchars($row['title']); ?></h1>
        <h3><?php echo htmlspecialchars($row['short_description']); ?></h3>
        <a href="maintenance.php" class="btnauth">Записаться на сервис</a>
    </div>
    <img src="img/offer/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?> " class="wow fadeInRight">
</div>
    </section>
<section>  <div class="row mt-3">
    <div class="col-6 col-md-6 infoflex4 text-center wow fadeInLeft"><h2><span class="blue"></strong> <?php echo formatDate($row['start_date']); ?></span></h2>
    <p>Дата начала акции</p></div>
    <div class="col-6 col-md-6 infoflex4 text-center wow fadeInRight"><h2><span class="blue"><?php echo formatDate($row['end_date']); ?></span></h2>
    <p>Дата окончания акции</p></div>
  </div>
</section>       
<section class="mt-3 wow fadeInLeft">
<h1>Описацие специального предложения </h1>  
<p class="offer_desc"><?php echo nl2br(htmlspecialchars($row['full_description'])); ?></p>
</section>
<section>
<div class="conditions_block mt-3">
    <div class="content_with_conditions wow fadeInLeft">
        <h3>Мы всегда готовы помочь вам!</h3>
        <p>Как связаться с нами:</p>
     <ul><li><a href="../dealers.php">Найти диллера</a></li>
     <li><a href="tel:+79998887766" >+7 (999) 888-77-66</a></li></ul>

    </div>
    <div class="conditions wow fadeInRight">
 <h1 class="blue">Условия акции</h1>
 <p><?php echo htmlspecialchars($row['conditions']); ?></p>
    </div>
</div>
</section>
<?php
} else {
?>
    <div>
        <h1>Предложение не найдено</h1>
    </div>
<?php
}

$conn->close();
?>

</main>
</body>
<div class="wow fadeInUp">
<?php require_once("footer.php");?></div>
</html>
