<?php
include "../../dataBaseConnection.php";

$response = ["success" => false, "message" => "", "data" => null];

if (isset($_GET['householdId'])) {
    $householdId = $_GET['householdId'];
    
    // Prepare statement to prevent SQL injection
    $stmt = $dataBaseConnection->prepare("SELECT * FROM household WHERE householdId = ?");
    $stmt->bind_param("s", $householdId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $response["success"] = true;
        $response["data"] = $data;
    } else {
        $response["message"] = "Household not found";
    }
    
    $stmt->close();
} else {
    $response["message"] = "No ID provided";
}

header('Content-Type: application/json');
echo json_encode($response);
?>
