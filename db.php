<?php
// includes/db.php

$host = 'localhost';
$dbname = 'clinic_db';
$username = 'root'; // Adjust this according to your local environment
$password = '';     // Adjust this according to your local environment

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>
