<?php
  session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <title>EducareHub_login_IMS</title>

  <style type="text/css">
    body {
      font-family: "Poppins", sans-serif;
      margin: 0;
    }
    .image-bg {
      position: absolute;
      top: 0;
      left: 0;
      z-index: -1;
      object-fit: cover;
      height: 100vh;
      width: 100%;
      overflow: hidden;
    }
    .container {
      display: flex;
      justify-content: center;
      flex-direction: column;
      align-items: center;
      margin-top: 5%;
    }
    .login_header{
      line-height: 0;
      text-align: center;
    }
    .login_header h1 {
      font-size: 100px;
      font-weight: 800;
      color:rgb(72, 154, 167);
    }
    .login_header h3 {
      font-size: 20px;
      font-weight: 300;
      letter-spacing: 11px;
      color: gray;
    }
    h3 span {
      color: black;
      font-weight: 800;
      font-size: 24px;
    }
    .login_body {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      padding: 25px;
      min-width: 300px;
      margin-top: 30px;
      background: rgba( 255, 255, 255, 0.15 );
      box-shadow: 0 8px 32px 0 rgba( 31, 38, 135, 0.37 );
      backdrop-filter: blur( 4.5px );
      -webkit-backdrop-filter: blur( 4.5px );
      border-radius: 20px;
    }
    form {
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .inputContainer {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 20px;
      text-transform: uppercase;
    }
    .inputContainer input {
      font-family: "Poppins", sans-serif;
      background-color:rgba(100, 202, 218, 0.54);
      padding: 10px 15px 10px 15px;
      border: none;
      margin-top: 10px;
      width: 250px;
      border-radius: 10px;
      box-shadow: 3px 3px 5px rgba(132, 132, 132, 0.44) inset;
    }
    input:focus {
      outline: none;
      box-shadow: 0px 0px 20px rgba(132, 132, 132, 0.44);
    }
    button {
      width: 80px;
      height: 40px;
      margin-top: 15px;
      font-family: "Poppins", sans-serif;
      background-color:rgb(100, 203, 218);
      border: none;
      border-radius: 5px;
      color: white;
    }
    button:hover {
      box-shadow: 3px 3px 5px rgba(132, 132, 132, 0.44) inset;
    }
    .alerts {
      color: brown;
      font-style: italic;
    }
  </style>
</head>
<body>
  <img class="image-bg" src="displayComponents\bg.jpg" alt="">
  <div class="container">
    <div class="login_header">
      <h1>EducareHub</h1>
      <h3><span>I</span>nventory <span>M</span>anagement <span>S</span>ystem</h3>
    </div>
    <div class="login_body">
      <form action = "db/login.php" method="POST">
        <div class="inputContainer">
          <label for="">Username</label>
          <input type="text" id="username" placeholder="Enter your username" name="username">
        </div>
        <div class="inputContainer">
          <label for="">Password</label>
          <input type="password" id="password" placeholder="Enter your password" name="password">
        </div>
        <div class="login_button">
          <button>
            Login
          </button>
        </div>
      </form>


      <?php
        if(isset($_SESSION['login_message'])) {
      ?>


        <div class="alert-box">
          <p class="alerts"><?= $_SESSION['login_message']?></p>
          <img src="displayComponents\Question 1.svg" alt="error" width="60px" height="60px">
        </div>
      <?php unset($_SESSION['login_message']);} ?>
    </div>
  </div>
</body>
</html>