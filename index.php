<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url(./img/peso.jpg);
        }

        section {
            background-image: url(background.jpg);
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 400px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.3);
            background-image: url(./img/Group7.png);
            background-repeat: no-repeat;
            background-size: cover;
            position: relative;
            top: 10px;
            background-color: rgba(255, 255, 255, 0.8);
        }

        form h2 {
            margin-bottom: 20px;
        }

        form label {
            display: block;
            margin-bottom: 8px;
        }

        form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form button {
            width: 100%;
            padding: 15px;
            margin-top: 10px; 
            margin-bottom: 10px; 
            background-color: #232424;
            color: white;
            border: none;
            border-radius: 5px;
        }

        form button a {
            color: antiquewhite;
            text-decoration: none;
        }

        form button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        @media (max-width: 600px) {
            form {
                width: 90%;
            }
        }
    </style>
</head>

</head>

<body>
    <section>
        <form action="login.php" method="post">
            <h2><strong>Login</strong></h2>
            <?php if (isset($_GET['error'])) : ?>
                <p class="error"><?php echo $_GET['error']; ?></p>
            <?php endif; ?>
            <label for="uname">User Name</label>
            <input type="text" id="uname" name="uname" placeholder="User Name" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Password" required>

            <div>
                <button type="submit">Login</button>
                <button><a href="signup.php">Sign Up</a></button>
            </div>
        </form>
    </section>
</body>

</html>
