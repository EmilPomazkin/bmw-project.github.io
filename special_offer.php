<?php
        include("db.php");
// Извлечение данных из таблицы special_offer
$sql = "SELECT * FROM special_offer WHERE status = 'Показать'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Специальные предложения</title>
    <link rel="icon" href="img/BMW_logo_(gray).svg.png" type="image/png">
</head>
<body>
<div class="wow fadeInDown">
<?php require_once("header.php")?></div>
<main class="container">
<h1>Специальные предложения</h1>
<div class="offer_blocks">
<?php
    function formatDatetimestamp($date) {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        return $dateTime ? $dateTime->format('d.m.Y') : 'Неизвестная дата';
    }
if ($result->num_rows > 0) {
    // Вывод данных каждой карточки
    while ($row = $result->fetch_assoc()) {
?>
<div class="offer_block">
        <a href="special_offer_page.php?id=<?php echo $row['id']; ?>">   
            <img src="img/offer/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
            <p class="timestamp"><?php echo formatDatetimestamp($row['timestamp']); ?></p>
           <div class="offer_block_cont">
            <h2><?php echo htmlspecialchars($row['title']); ?></h2>
            <p><?php echo htmlspecialchars($row['short_description']); ?></p></div>
        </a></div>
<?php
    }
} else {
?>
    <p>Нет доступных предложений.</p>
<?php
}

$conn->close();
?>
</div>
</main>
<div class="wow fadeInUp">
<?php require_once("footer.php");?></div>
</body>
</html>
