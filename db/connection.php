<?php

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'educarehub';

// Create database connection
$db = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$db) {
    // If connection fails, stop script execution and display error
    die("Connection failed: " . mysqli_connect_error());
}


?>