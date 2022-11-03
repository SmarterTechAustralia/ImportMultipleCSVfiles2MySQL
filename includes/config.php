<?php

// Database configuration
$servername = "localhost"; // or any other path to your MySQL server
$username = "username"; // your MySQL username
$password = "password"; //your  MySQL password
$dbname = "yourdatabsename"; //create  a database on MySQL and put its name here
$dir = "yourCSVfolder"; //modify it before

// Create connection compatile with MySQL 5.X and 8.X
$conn = new mysqli($servername, $username, $password, $dbname);
mysqli_set_charset($conn, "utf8mb4");
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    exit;
}
