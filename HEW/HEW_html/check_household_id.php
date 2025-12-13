<?php
include "../../dataBaseConnection.php";

$response = ["exists" => false, "message" => ""];

if (isset($_POST['householdId'])) {
    $id = $_POST['householdId'];
    
    // Check if ID exists in household table
    $checkQuery = "SELECT * FROM household WHERE householdId = ?";
    $stmt = $dataBaseConnection->prepare($checkQuery);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $response["exists"] = true;
        // Optionally return data if needed
    } else {
        $response["exists"] = false;
        $response["message"] = "Household ID not found in database.";
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
