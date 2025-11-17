<?php
$servername = "DESKTOP-8J8PHKR";
$username = "";
$password = "";
$dbname = "Charity_Foundation_System";

try {
    $conn = new PDO("sqlsrv:Server=$servername;Database=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
echo "Connected successfully";
?>