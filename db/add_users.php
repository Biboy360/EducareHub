<?php
    session_start();

    $table_name = $_SESSION['table'];
    $_SESSION['table'] = '';

    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $birthdate = $_POST['username'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $tier = $_POST['tier'];
    $points = $_POST['points'];
    $encrypted = password_hash($password, PASSWORD_DEFAULT);

    var_dump($encrypted);

    
?>