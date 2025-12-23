<?php
session_start();
include("../dataBaseConnection.php");

// Simple role check (assuming focal person roles)
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'linkage') {
//     header("Location: ../index.html");
//     exit();
// }

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportingMonth = mysqli_real_escape_string($dataBaseConnection, $_POST['reportingMonth']);
    $preparerName = mysqli_real_escape_string($dataBaseConnection, $_POST['preparerName']);
    
    // Generate a unique package ID
    $packageId = "PK-" . date('Ymd') . "-" . strtoupper(substr(uniqid(), -4));
    
    // Format period from month input (e.g., 2025-01 to January 2025)
    $timestamp = strtotime($reportingMonth . "-01");
    $period = date('F Y', $timestamp);
    
    // Preparer ID (from session if available, otherwise fallback)
    $preparerId = $_SESSION['user_db_id'] ?? 1; 

    // Metadata as JSON
    $dataSummary = json_encode([
        'new_patients' => $_POST['newPatients'],
        'deliveries' => $_POST['deliveries'],
        'facility_id' => $_POST['facilityId']
    ]);

    $query = "INSERT INTO statistical_packages (package_id, period, focal_person_id, focal_person_name, status, data_summary) 
              VALUES ('$packageId', '$period', $preparerId, '$preparerName', 'Pending', '$dataSummary')";

    if ($dataBaseConnection->query($query)) {
        $message = "<div class='success-alert'>Data submitted successfully to the HMIS Officer! Package ID: $packageId</div>";
    } else {
        $message = "<div class='error-alert'>Error: " . $dataBaseConnection->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMIS Data Submission | Focal Person</title>
    <link rel="stylesheet" href="hmis_data_submission.css">
    <style>
        .success-alert { padding: 1rem; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #bbf7d0; text-align: center; }
        .error-alert { padding: 1rem; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #fecaca; text-align: center; }
    </style>
</head>
<body>

    <a href="focal_dashboard.html" class="back-button">
        &larr; Go Back
    </a>

    <header>
        <h1>HMIS Report Submission</h1>
        <p>Please enter all required data fields for the current reporting period.</p>
    </header>

    <main class="form-container">
        <?php echo $message; ?>
        
        <form action="hmis_data_submission.php" method="POST" id="hmis-report-form">

            <fieldset class="report-metadata">
                <legend>Report Details</legend>
                <div class="form-group">
                    <label for="facility-id">Facility ID</label>
                    <input type="text" id="facility-id" name="facilityId" placeholder="e.g. FAC-001" required>
                </div>
                <div class="form-group">
                    <label for="reporting-month">Reporting Month</label>
                    <input type="month" id="reporting-month" name="reportingMonth" required>
                </div>
                <div class="form-group">
                    <label for="preparer-name">Prepared By</label>
                    <input type="text" id="preparer-name" name="preparerName" value="<?php echo $_SESSION['full_name'] ?? ''; ?>" required>
                </div>
            </fieldset>

            <fieldset class="data-section">
                <legend>Key Service Indicators</legend>
                <div class="form-group">
                    <label for="new-patients">New Patients Registered</label>
                    <input type="number" id="new-patients" name="newPatients" min="0" value="0" required>
                </div>
                <div class="form-group">
                    <label for="deliveries">Facility Deliveries</label>
                    <input type="number" id="deliveries" name="deliveries" min="0" value="0" required>
                </div>
            </fieldset>

            <div class="submit-area">
                <button type="submit" class="submit-button">
                    Submit Processed Report to HMIS
                </button>
            </div>
        </form>
    </main>

    <footer>
        <p>&copy; 2025 Health Management Information System | D-HEIRS</p>
    </footer>

</body>
</html>
