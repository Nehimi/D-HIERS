<?php
header('Content-Type: application/json');
include "../dataBaseConnection.php";

// Get search parameters
$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($dataBaseConnection, $_GET['search']) : '';
$roleFilter = isset($_GET['role']) ? mysqli_real_escape_string($dataBaseConnection, $_GET['role']) : 'all';
$statusFilter = isset($_GET['status']) ? mysqli_real_escape_string($dataBaseConnection, $_GET['status']) : 'all';
$kebeleFilter = isset($_GET['kebele']) ? mysqli_real_escape_string($dataBaseConnection, $_GET['kebele']) : 'all';

// Build query
$query = "SELECT * FROM users WHERE 1=1";

// Search filter
if (!empty($searchTerm)) {
    $query .= " AND (first_name LIKE '%$searchTerm%' 
                 OR last_name LIKE '%$searchTerm%' 
                 OR emali LIKE '%$searchTerm%' 
                 OR userId LIKE '%$searchTerm%' 
                 OR phone_no LIKE '%$searchTerm%')";
}

// Role filter
if ($roleFilter !== 'all') {
    $query .= " AND role = '$roleFilter'";
}

// Status filter
if ($statusFilter !== 'all') {
    $query .= " AND status = '$statusFilter'";
}

// Kebele filter
if ($kebeleFilter !== 'all') {
    $query .= " AND kebele = '$kebeleFilter'";
}

$query .= " ORDER BY id DESC";

// Execute query
$result = mysqli_query($dataBaseConnection, $query);

$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = [
        'id' => $row['id'],
        'userId' => $row['userId'],
        'firstName' => $row['first_name'],
        'lastName' => $row['last_name'],
        'email' => $row['emali'],
        'phone' => $row['phone_no'],
        'role' => $row['role'],
        'kebele' => $row['kebele'],
        'status' => $row['status']
    ];
}

// Response
$response = [
    'success' => true,
    'count' => count($users),
    'users' => $users,
    'filters' => [
        'search' => $searchTerm,
        'role' => $roleFilter,
        'status' => $statusFilter,
        'kebele' => $kebeleFilter
    ]
];

echo json_encode($response);
?>
