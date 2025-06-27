<?php
    session_start();

    //Destroy / unset session
    unset($_SESSION['admins']);

    $_SESSION['login_message'] = 'Logout Successfully!';
    header('location: ../admin_login.php');