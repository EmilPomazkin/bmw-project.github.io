<?php
include("db.php");

// Получение ID услуги из URL
$service_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Запрос данных для конкретной услуги
$sql = "SELECT * FROM car_services WHERE id = $service_id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($row["service_name"]); ?></title>
    <link rel="icon" href="img/BMW_logo_(gray).svg.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>
<body>
<div class="wow fadeInDown">
    <?php require_once("header.php") ?>
</div>

<main class="container">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="javascript:history.back();" class="blue">Вернуться</a>
    </li>
    
    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($row['service_name']); ?></li>
  </ol>
</nav>
    <div class="content_h wow fadeIn">
    <div class="h_block ">
    <h1><?php echo htmlspecialchars($row["service_name"]); ?></h1>
    <?php if ($row): ?>   
        <h4> <?php echo htmlspecialchars($row["description"]); ?></h4>
        <div class="price_serv">
        <p > <?php echo htmlspecialchars($row["price"]); ?> руб.</p></div>

        </div>
        <img src="img/service/<?php echo htmlspecialchars($row['image_serv']);?>"  alt=""></div>

        <?php
$icons = [
    'bi-wrench',
    'bi-exclamation-triangle',
    'bi-tools',
    'bi-gear-wide-connected',
    'bi-check-circle'
];
?>
   <h1 class="mt-3">Этапы ремонта</h1>
<div class="timeline" >
 
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <?php $step = "process_step" . $i; ?>
        <?php if (!empty($row[$step])): ?>
            <div class="timeline-step wow fadeInLeft">
                <div class="timeline-icon">
                    <i class="bi <?php echo $icons[$i - 1]; ?>"></i>
                </div>
                <div class="timeline-content wow fadeInRight">
                    <span class="step-number">Шаг <?php echo $i; ?></span>
                    <h3><?php echo htmlspecialchars($row[$step]); ?></h3>
                </div>
            </div>
        <?php endif; ?>
    <?php endfor; ?>
</div>

<div class="dialog_block ">
    <div class="content_with_dialog wow fadeInLeft">
        <h3>Мы всегда готовы помочь вам!</h3>
        <p>Как связаться с нами:</p>
     <ul><li><a href="../dealers.php">Найти диллера</a></li>
     <li><a href="tel:+79998887766" >+7 (999) 888-77-66</a></li></ul>

    </div>
    <div class="dialog_contnent wow fadeInRight">
        
<div class="message-container">
<div class="message-receiver">
        <div class="message-box receiver">
            <div style="font-weight: bold;">Консультант</div>
            <div>Какая у вас проблема?</div>
        </div>
    </div>
    <div class="message-sender">
        <div class="message-box sender">
            <div style="font-weight: bold;">Вы</div>
            <div ><?php echo htmlspecialchars($row["fault_detection"]); ?></div>
        </div>
    </div>

    <div class="message-receiver">
        <div class="message-box receiver">
            <div style="font-weight: bold;">Консультант</div>
            <div>Запиштесь на услугу <a href="maintenance.php" class="blue"><?php echo htmlspecialchars($row["service_name"]); ?></a></div>
        </div>
    </div>


    

</div>
    </div>
</div>

<h1 class="mt-3">Как выявить проблему самому?</h1>
<div class="accordion accordion-flush mt-3" id="accordionFlushExample">

    <?php for ($i = 1; $i <= 5; $i++): ?>
        <?php $diagnostic_step = "diagnostic_step" . $i; ?>
        <?php $recommendation = "diagnostic_step" . $i . "_info"; ?>
        
        <?php if (!empty($row[$diagnostic_step]) || !empty($row[$recommendation])): ?>
            <div class="accordion-item  wow fadeInDown">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#step<?php echo $i; ?>" aria-expanded="false" aria-controls="step<?php echo $i; ?>">
                        <?php echo htmlspecialchars($row[$diagnostic_step]); ?>
                    </button>
                </h2>
                <div id="step<?php echo $i; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                        <?php if (!empty($row[$recommendation])): ?>
                            <p><?php echo nl2br(htmlspecialchars($row[$recommendation])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endfor; ?>

</div>


        
    <?php else: ?>
        <p>Услуга не найдена.</p>
    <?php endif; ?>
</main>

<div class="wow fadeInUp">
    <?php require_once("footer.php"); ?>
</div>
</body>
</html>

<?php
$conn->close();
?>
