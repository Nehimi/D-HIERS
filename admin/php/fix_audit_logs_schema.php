<?php
/**
 * Database Fix Script: Update Audit Logs Table
 * This script adds missing columns to the audit_logs table.
 */

include("../../dataBaseConnection.php");

echo "<h2>Audit Logs Schema Fix</h2>";

if (!$dataBaseConnection) {
    die("<p style='color:red;'>❌ Database connection failed: " . mysqli_connect_error() . "</p>");
}

echo "<p style='color:green;'>✅ Database connected successfully.</p>";

// 1. Add user_role column
$checkRole = mysqli_query($dataBaseConnection, "SHOW COLUMNS FROM `audit_logs` LIKE 'user_role'");
if (mysqli_num_rows($checkRole) == 0) {
    $alterRole = "ALTER TABLE `audit_logs` ADD COLUMN `user_role` VARCHAR(50) AFTER `user_name`";
    if (mysqli_query($dataBaseConnection, $alterRole)) {
        echo "<p style='color:green;'>✅ Added 'user_role' column to audit_logs.</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to add 'user_role' column: " . mysqli_error($dataBaseConnection) . "</p>";
    }
} else {
    echo "<p>ℹ️ 'user_role' column already exists.</p>";
}

// 2. Add status column
$checkStatus = mysqli_query($dataBaseConnection, "SHOW COLUMNS FROM `audit_logs` LIKE 'status'");
if (mysqli_num_rows($checkStatus) == 0) {
    $alterStatus = "ALTER TABLE `audit_logs` ADD COLUMN `status` VARCHAR(20) DEFAULT 'success' AFTER `ip_address`";
    if (mysqli_query($dataBaseConnection, $alterStatus)) {
        echo "<p style='color:green;'>✅ Added 'status' column to audit_logs.</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to add 'status' column: " . mysqli_error($dataBaseConnection) . "</p>";
    }
} else {
    echo "<p>ℹ️ 'status' column already exists.</p>";
}

echo "<h3>Check results:</h3>";
$desc = mysqli_query($dataBaseConnection, "DESCRIBE `audit_logs`");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th></tr>";
while ($row = mysqli_fetch_assoc($desc)) {
    echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td></tr>";
}
echo "</table>";

echo "<p><a href='audit_logs.php'>Go to Audit Logs</a></p>";
?>
