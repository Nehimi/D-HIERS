<?php
include 'dataBaseConnection.php';
$res = $dataBaseConnection->query('SELECT userId, role FROM users');
while($row = $res->fetch_assoc()) {
    echo $row['userId'] . " (" . $row['role'] . ")<br>";
}
?>
