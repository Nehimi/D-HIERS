<?php
/**
 * Database Test & Verification Script
 * Run this to check if the database and table exist
 */

include 'dataBaseConnection.php';

echo "<h2>Database Connection Test</h2>";

if ($dataBaseConnection) {
    echo "✅ <strong>Database Connected Successfully!</strong><br><br>";
    
    // Check if users table exists
    $tableCheck = mysqli_query($dataBaseConnection, "SHOW TABLES LIKE 'users'");
    
    if (mysqli_num_rows($tableCheck) > 0) {
        echo "✅ <strong>Users table exists</strong><br><br>";
        
        // Show table structure
        echo "<h3>Users Table Structure:</h3>";
        $structure = mysqli_query($dataBaseConnection, "DESCRIBE users");
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = mysqli_fetch_assoc($structure)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        
        // Count users
        $countQuery = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as total FROM users");
        $count = mysqli_fetch_assoc($countQuery)['total'];
        
        echo "<h3>Total Users in Database: <strong>$count</strong></h3>";
        
        // Show last 5 users
        if ($count > 0) {
            echo "<h3>Recent Users:</h3>";
            $recentUsers = mysqli_query($dataBaseConnection, "SELECT * FROM users ORDER BY id DESC LIMIT 5");
            
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>User ID</th><th>Name</th><th>Phone</th><th>Role</th><th>Status</th></tr>";
            
            while ($user = mysqli_fetch_assoc($recentUsers)) {
                echo "<tr>";
                echo "<td>" . $user['id'] . "</td>";
                echo "<td>" . $user['userId'] . "</td>";
                echo "<td>" . $user['first_name'] . " " . $user['last_name'] . "</td>";
                echo "<td>" . $user['phone_no'] . "</td>";
                echo "<td>" . $user['role'] . "</td>";
                echo "<td>" . $user['status'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "❌ <strong>Users table does NOT exist!</strong><br>";
        echo "Please run the SQL file to create the table.<br>";
    }
    
} else {
    echo "❌ <strong>Database Connection Failed!</strong><br>";
    echo "Error: " . mysqli_connect_error();
}
?>
