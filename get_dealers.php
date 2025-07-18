<?php

include("db.php");


$result = $conn->query("SELECT dealership_name, latitude, longitude, phone, address FROM dealers");
$dealers = [];

while ($row = $result->fetch_assoc()) {
    $dealers[] = $row;
}
// Отладочный вывод
header('Content-Type: application/json');
echo json_encode($dealers);
$conn->close();
?>

