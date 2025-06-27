<?php
    $servername = 'localhost';
    $username = 'root';
    $password = '';

    //connect database
    $conn = new PDO("mysql:host=$servername;dbname=educarehub", $username, $password);
    //Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
