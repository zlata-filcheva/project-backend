<?php
$servername = "mariadb";  // Use the service name defined in docker-compose.yml
$username = "root";
$password = "";
$dbname = "newReddit";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

$conn->close();