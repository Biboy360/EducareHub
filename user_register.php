<!DOCTYPE html>
<html>
<head>
    <title>EducareHub</title>

    <style>
        /* Full-screen centered container with gradient */
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

        /* Card-style container */
        .container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
            min-width: 340px;
        }

        /* Header */
        .Header h1 {
            margin-bottom: 25px;
            font-size: 28px;
            color: #007c84;
        }

        /* Left-aligned labels */
        .Login form div label {
            display: block;
            text-align: left;
            font-weight: 500;
            margin-bottom: 5px;
            color: #333;
        }

        /* Input fields */
        .Login form div input[type="text"],
        .Login form div input[type="password"],
        .Login form div input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        /* sign up design */
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

        /* Message box styles */
        .message-box {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            min-width: 300px;
            max-width: 90%;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            z-index: 999;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            font-family: 'Poppins', sans-serif;
        }

        .message-box.success {
            background-color: #d4edda;
            color: #155724;
            border-left: 5px solid #28a745;
        }

        .message-box.error {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 5px solid #dc3545;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 18px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 15px;
            cursor: pointer;
            color: #333;
        }

        .instruction {
        margin-top: 15px;
        padding: 15px 15px;
        background-color: #e0f7fa; 
        border: 1px solid #00A8B5; 
        border-radius: 10px;
        text-align: center;
        font-size: 14px;
        color: #004d4d;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .instruction h3 {
        margin: 0;
        font-weight: 400;
        }

        .instruction a {
        color: purple;
        text-decoration: underline;
        font-weight: bold;
        }

    </style>

</head>
<body>
    <div class="container">
        <div class = "Header">
            <h1>EducareHub</h1>
        </div>

            <div class="Login">
                <form action="user_register_db.php" method = "POST">

                    <div>
                        <label for="">Firstname</label>
                        <input type="text" name = "fname" placeholder = "firstname" required/>
                    </div>

                    <div>
                        <label for="">Lastname</label>
                        <input type="text" name = "lname" placeholder = "lastname" required/>
                    </div>

                    <div>
                        <label for="">Birthdate</label>
                        <input type="date" name = "bdate" placeholder = "birthdate" required/>
                    </div>

                    <div>
                        <label for="">Username</label>
                        <input type="text" name = "uname" placeholder = "username" required/>
                    </div>

                    <div>
                        <label for="">Password</label>
                        <input type="password" name = "pword" placeholder = "password" required/>
                    </div>

                    <div>
                        <label for="">Email</label>
                        <input type="email" name = "em" placeholder = "email" required/>
                    </div>

                    <input type="submit" class = "btn" value = "Sign Up" name = "signup">

                    <div class = "instruction">
                        <h3>Dear user, after successfully creating an account, please proceed to the <a href="user_login.php"> Login Form</a></h3>
                    </div>

                </form>
            </div>  
    </div>

    <!-- Pop-up message container -->

    <div id="messageBox" class="message-box" style="display:none;">

        <button class="close-btn" onclick="closeMessage()">×</button>
        <p id="messageText"></p>

    </div>

    <script> 

    //setting up variables --- connecting to containers
    function showMessage(text, type) {
        const box = document.getElementById('messageBox');
        const msg = document.getElementById('messageText');
        msg.innerHTML = text;
        box.className = `message-box ${type}`;
        box.style.display = 'block';
    }

    //response when the user closes the pop-up message
    function closeMessage() {
        document.getElementById('messageBox').style.display = 'none';
    }

    // Get URL parameters from user_register_db.php 

    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const msg = urlParams.get('msg');

    if (status && msg) {
        showMessage(decodeURIComponent(msg), status === 'success' ? 'success' : 'error');

        // Clear URL parameters without reloading
        window.history.replaceState({}, document.title, window.location.pathname);
    }   

    const form = document.querySelector('form[action="user_register_db.php"]');
    const emailInput = document.querySelector('input[name="em"]');

    const validateEmail = (email) => {
        return String(email)
            .toLowerCase()
            .match(
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            );
    };

    form.addEventListener('submit', function(e) {
        if (!validateEmail(emailInput.value)) {
            showMessage('Please enter a valid email address.', 'error');
            emailInput.focus();
            e.preventDefault();
        }
    });

    </script>

</body>
</html>