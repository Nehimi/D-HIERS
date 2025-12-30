<?php
include("dataBaseConnection.php");

echo "Distinct service_type:\n";
$result = $dataBaseConnection->query("SELECT DISTINCT service_type FROM health_data");
while ($row = $result->fetch_assoc()) {
    echo "- " . $row['service_type'] . "\n";
}

echo "\nDistinct status:\n";
$result = $dataBaseConnection->query("SELECT DISTINCT status FROM health_data");
while ($row = $result->fetch_assoc()) {
    echo "- " . $row['status'] . "\n";
}

echo "\nSample rows with status and created_at:\n";
$result = $dataBaseConnection->query("SELECT status, created_at, service_type FROM health_data LIMIT 10");
while ($row = $result->fetch_assoc()) {
    echo "- Status: " . $row['status'] . " | Date: " . $row['created_at'] . " | Service: " . $row['service_type'] . "\n";
}
?>
