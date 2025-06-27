<!DOCTYPE html>
<html>
<head>
    <title>EducareHub</title>
    
    <style>
            /* General body and background */
        body {
            margin: 0;
            padding: 0;
            font-family: 'poppins', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to bottom right, #c2f0f7, #aee1f9);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container for the login card */
        .container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
            min-width: 320px;
        }

        /* Header styling */
        .Header h1 {
            margin-bottom: 25px;
            font-size: 28px;
            color: #007c84;
        }

        /* Input field styles */
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #00a1a7;
        }

        /* Button styles */
        .btn {
            background-color: #00A8B5; 
            color: white;              
            border: none;             
            padding: 10px 20px;       
            text-align: center;
            font-size: 16px;           
            border-radius: 10px;     
            width: 100%;              
            cursor: pointer;          
        }

        .btn:hover {
            background-color: #0096a3;
        }
        
        .register {
            margin-top: 15px;
            font-size: 14px;
        }

        .register a {
            color: #5a00f0;
            text-decoration: none;
        }

        .register a:hover {
            text-decoration: underline;
        }

        .Login form div label {
            display: block;
            text-align: left;
            font-weight: 500;
            margin-bottom: 5px;
            color: #333;
        }

        /* Popup Message Box */
        .popup {
            position: fixed;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #ffeded;
            color: #b30000;
            padding: 15px 25px;
            border: 1px solid #ffb3b3;
            border-radius: 10px;
            font-size: 14px;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .popup.success {
            background-color: #e0ffe0;
            color: #007a00;
            border: 1px solid #7dd87d;
        }

        .popup button {
            background: none;
            border: none;
            font-size: 16px;
            font-weight: bold;
            color: inherit;
            cursor: pointer;
        }

    </style>
    
</head>

<body>
    <div class="container">
        <div class = "Header">
            <h1>EducareHub</h1>
        </div>
        <div class="Login">
            <form action="user_login_db.php" method="POST">
                <div>
                    <label for="">Username</label>
                    <input placeholder = "username" name = "username" type="text" required/>
                </div>
                <div>
                    <label for="">Password</label>
                    <input placeholder = "password" name = "password" type="password" required/>
                </div>

                <input type="submit" class = "btn" value = "Login" name = "login">

                <div class="register">
                    </h3>Don't have an account?</h3> <a href="user_register.php">Click to Register</a>
                </div>
            </form>

        </div>

        <!-- Message Box -->
        <?php if (isset($_GET['status']) && isset($_GET['msg'])): ?>
            <div class="popup <?= $_GET['status'] === 'success' ? 'success' : '' ?>">
                <?= htmlspecialchars($_GET['msg']) ?>
                <button onclick="this.parentElement.style.display='none'">×</button>
            </div>
        <?php endif; ?>

    </div>

     <script>
        // Auto-close message after 5 seconds
        setTimeout(() => {
            const popup = document.querySelector('.popup');
            if (popup) popup.style.display = 'none';
        }, 5000);
    </script>

</body>
</html>