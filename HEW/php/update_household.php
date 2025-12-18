<?php
include "../../dataBaseConnection.php";

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (isset($data['householdId'])) {
        $householdId = $data['householdId'];
        $memberName = $data['memberName'];
        $age = $data['age'];
        $sex = $data['sex'];
        $kebele = $data['kebele']; // Assuming we allow editing Kebele too
        
        // Update query
        $sql = "UPDATE household SET memberName=?, age=?, sex=?, kebele=? WHERE householdId=?";
        $stmt = $dataBaseConnection->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sisss", $memberName, $age, $sex, $kebele, $householdId);
            
            if ($stmt->execute()) {
                $response["success"] = true;
                $response["message"] = "Household updated successfully";
            } else {
                $response["message"] = "Error updating record: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $response["message"] = "Database prepare error: " . $dataBaseConnection->error;
        }
    } else {
        $response["message"] = "Missing household ID";
    }
} else {
    $response["message"] = "Invalid request method";
}

header('Content-Type: application/json');
echo json_encode($response);
?>
