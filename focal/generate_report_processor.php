<?php
session_start();
include "../dataBaseConnection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reportMonth'], $_POST['kebeleFilter'])) {
    $month = mysqli_real_escape_string($dataBaseConnection, $_POST['reportMonth']);
    $kebeleFilter = mysqli_real_escape_string($dataBaseConnection, $_POST['kebeleFilter']);
    
    // UC-15: Lock validated reports and aggregate
    // Note: In a real system, this would create a record in a 'statistical_summaries' table.
    // For this pro version, we will simulate the aggregation of 'Validated' reports.
    
    $whereClause = "WHERE status='Validated'";
    if ($kebeleFilter !== 'All Kebeles') {
        $whereClause .= " AND kebele='$kebeleFilter'";
    }
    // Simple mock aggregation
    $aggregationQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total_indicators, SUM(CASE WHEN count IS NOT NULL THEN count ELSE 0 END) as total_value FROM health_data $whereClause");
    $result = mysqli_fetch_assoc($aggregationQuery);
    
    // Simulate updating status to 'Summarized'
    mysqli_query($dataBaseConnection, "UPDATE health_data SET status='Summarized' $whereClause");
    
    header("Location: statistical_report.php?success=Summarization complete for $month. " . $result['total_indicators'] . " indicators processed.");
    exit();
} else {
    header("Location: statistical_report.php?error=Invalid request");
    exit();
}
?>
