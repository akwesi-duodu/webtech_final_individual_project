<?php
$servername = "localhost"; 
$username = "akwesi.duodu";       
$password = "";            
$dbname = "webtech_fall2024_akwesi_duodu"; 

// Create MySQLi connection
//initalise connection with servername,username,password and dbname credentials.
$conn = new mysqli($servername, $username, $password, $dbname);

// Check MySQLi connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create a new PDO instance for database connection
try {
    // Use the $servername variable for the PDO connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle PDO connection errors
    die("Database connection failed: " . $e->getMessage());
}
?>
