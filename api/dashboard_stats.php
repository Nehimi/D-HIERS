<?php
header('Content-Type: application/json');
include "../dataBaseConnection.php";

// Get total users count
$totalUsersQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM users");
$totalUsers = mysqli_fetch_assoc($totalUsersQuery)['total'];

// Get active HEWs count
$activeHEWsQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM users WHERE role='HEW' AND status='active'");
$activeHEWs = mysqli_fetch_assoc($activeHEWsQuery)['total'];

// Get reports today (you can extend this based on your reports table)
$reportsToday = 89; // Placeholder - replace with actual query when reports table exists

// System status
$systemStatus = "99.9%";

// Get recent activity - last 5 users
$recentUsersQuery = mysqli_query($dataBaseConnection, "SELECT * FROM users ORDER BY id DESC LIMIT 5");
$recentUsers = [];
while ($row = mysqli_fetch_assoc($recentUsersQuery)) {
    $recentUsers[] = [
        'id' => $row['id'],
        'firstName' => $row['first_name'],
        'lastName' => $row['last_name'],
        'role' => $row['role'],
        'kebele' => $row['kebele'],
        'status' => $row['status'],
        'userId' => $row['userId']
    ];
}

// Get user count by status
$activeUsersQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM users WHERE status='active'");
$activeUsers = mysqli_fetch_assoc($activeUsersQuery)['total'];

$pendingUsersQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM users WHERE status='pending'");
$pendingUsers = mysqli_fetch_assoc($pendingUsersQuery)['total'];

$inactiveUsersQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM users WHERE status='inactive'");
$inactiveUsers = mysqli_fetch_assoc($inactiveUsersQuery)['total'];

// Get user count by role
$roleCountsQuery = mysqli_query($dataBaseConnection, "SELECT role, COUNT(*) as count FROM users GROUP BY role");
$roleCounts = [];
while ($row = mysqli_fetch_assoc($roleCountsQuery)) {
    $roleCounts[$row['role']] = $row['count'];
}

// Response
$response = [
    'success' => true,
    'stats' => [
        'totalUsers' => (int)$totalUsers,
        'activeHEWs' => (int)$activeHEWs,
        'reportsToday' => $reportsToday,
        'systemStatus' => $systemStatus,
        'activeUsers' => (int)$activeUsers,
        'pendingUsers' => (int)$pendingUsers,
        'inactiveUsers' => (int)$inactiveUsers
    ],
    'recentUsers' => $recentUsers,
    'roleCounts' => $roleCounts,
    'timestamp' => date('Y-m-d H:i:s')
];

echo json_encode($response);
?>
