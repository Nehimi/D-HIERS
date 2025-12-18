<?php
/**
 * Delete Password Reset Request API
 * D-HEIRS System
 */

session_start();
header('Content-Type: application/json');
include "../dataBaseConnection.php";

$response = ['success' => false, 'message' => ''];

try {
    if (!$dataBaseConnection) {
        throw new Exception("Database connection failed");
    }
    
    $id = $_POST['id'] ?? null;
    
    if (!$id) {
        throw new Exception("Request ID is required");
    }
    
    $stmt = $dataBaseConnection->prepare("DELETE FROM password_resets WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Request deleted successfully';
    } else {
        throw new Exception("Failed to delete request");
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
