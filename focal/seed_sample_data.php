<?php
include("../dataBaseConnection.php");

echo "<h1>D-HEIRS Sample Data Seeder</h1>";

// 1. Check for basic requirements (Users & Kebeles)
$userRes = $dataBaseConnection->query("SELECT id FROM users LIMIT 1");
if (!$userRes || $userRes->num_rows == 0) {
    echo "Creating demo users...<br>";
    $pass = password_hash("password123", PASSWORD_DEFAULT);
    $dataBaseConnection->query("INSERT INTO users (first_name, last_name, email, role, kebele, status, password, userId) 
                                VALUES ('Demo', 'HEW', 'hew@demo.com', 'hew', 'Lich-Amba', 'active', '$pass', 'HEW-DEMO')");
}

$kebeleRes = $dataBaseConnection->query("SELECT kebeleName FROM kebele LIMIT 1");
if (!$kebeleRes || $kebeleRes->num_rows == 0) {
    echo "Creating demo kebeles...<br>";
    $dataBaseConnection->query("INSERT INTO kebele (kebeleName, kebeleCode, status) VALUES ('Lich-Amba', 'KB-01', 'active'), ('Arada', 'KB-02', 'active')");
}

// 2. Insert Sample Health Data (Focal-Validated to show in reports)
$countRes = $dataBaseConnection->query("SELECT COUNT(*) as c FROM health_data");
$count = $countRes->fetch_assoc()['c'];

if ($count < 5) {
    echo "Inserting sample health data...<br>";
    $months = [date('Y-m-d'), date('Y-m-d', strtotime('-1 month'))];
    $services = ['ANC Visit', 'Delivery', 'Immunization', 'Sanitation'];
    $kebeles = ['Lich-Amba', 'Arada'];

    for ($i = 0; $i < 10; $i++) {
        $svc = $services[array_rand($services)];
        $kb = $kebeles[array_rand($kebeles)];
        $date = $months[array_rand($months)];
        $dataBaseConnection->query("INSERT INTO health_data (kebele, service_type, count, status, patient_name, created_at, submitted_by_id) 
                                    VALUES ('$kb', '$svc', " . rand(1, 5) . ", 'Focal-Validated', 'Test Patient $i', '$date', 1)");
    }
} else {
    echo "Health data already exists ($count records).<br>";
}

// 3. Insert Sample Statistical Packages (Validated to show in reports)
$pkgCountRes = $dataBaseConnection->query("SELECT COUNT(*) as c FROM statistical_packages");
$pkgCount = $pkgCountRes->fetch_assoc()['c'];

if ($pkgCount < 2) {
    echo "Inserting sample statistical packages...<br>";
    $p1 = date('Y-m');
    $p2 = date('Y-m', strtotime('-1 month'));
    $dataBaseConnection->query("INSERT INTO statistical_packages (package_id, period, status, received_at) 
                                VALUES ('PKG-DEMO-01', '$p1', 'Validated', NOW()), ('PKG-DEMO-02', '$p2', 'Validated', NOW())");
} else {
    echo "Statistical packages already exist ($pkgCount records).<br>";
}

echo "<br><strong>Seeding complete!</strong> You can now test the reporting pages.";
?>
