<?php
session_start();

if (!isset($_SESSION['uname'])) {
    header("Location: index.php");
    exit();
}

include ('db_connection.php');

$uname = $_SESSION['uname'];
$sql = "SELECT * FROM users WHERE user_name='$uname'";
$result = mysqli_query($conn, $sql);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR CODE</title>
    <style>
        body {
            margin: 10px;
            padding: 0; 
            justify-content: center;
            align-items: center;
            background-image: url(./img/Group7.png);
        }

        h1, p {
            text-align: center;
            color: black;
        }

        img {
            display: block;
            margin: 0 auto;
            width: 25%; 
            box-shadow: 15px 15px 10px rgba(0, 0, 0, 0.3);
        }

        form {
            display: block;
            text-align: center;
            color: black;
            text-decoration: none;
            padding-top: 100px; 
        }
    form.mt-3 {
        text-align: center;
        margin-top: 20px;
    }

    button.btn-success {
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    button.btn-success:hover {
        background-color: #218838;
    }
    h2{
        text-align: center;
        
    }
    </style>
</head>

<body>
    <h1>My Personal Qr Code</h1>
    <p><strong>Username:</strong> <?php echo $uname; ?></p>
    <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo urlencode($uname); ?>&size=100x100" alt="QR Code">
    <h2>SCAN ME!</h2>
    <form action="home.php" method="get" class="mt-3">
        <button type="submit" class="btn btn-success">Home</button></button>
    </form>
</body>

</html>
