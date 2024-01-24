<?php
session_start();

include('db_connection.php');
include('common.php');

// Check if the "logout" parameter is present
if (isset($_GET['logout'])) {
    // Destroy the session
    session_destroy();
    // Redirect to the login page
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['uname'])) {
    header("Location: index.php");
    exit();
}

$accountType = isset($_SESSION['account_type']) ? $_SESSION['account_type'] : 0;
$currentBalance = getUserBalance($_SESSION['uname'], $conn);

$_SESSION['balance'] = isset($_SESSION['balance']) ? $_SESSION['balance'] : 1000;
$_SESSION['receiver_balance'] = isset($_SESSION['receiver_balance']) ? $_SESSION['receiver_balance'] : 500;
$_SESSION['phone'] = isset($userPhone) ? $userPhone : '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
    $receiverPhone = isset($_POST['receiver_phone']) ? $_POST['receiver_phone'] : '';

    if ($amount <= 0 || empty($receiverPhone)) {
        $message = "Invalid amount or receiver's phone number.";
    } elseif ($_SESSION['balance'] < $amount) {
        $message = "Insufficient funds. You do not have enough balance for this transaction.";
    } else {
        $senderName = $_SESSION['uname'];
        $receiverName = "Receiver";

        $_SESSION['balance'] -= $amount;
        $_SESSION['receiver_balance'] += $amount;

        recordTransaction($senderName, $receiverName, $amount);

        $message = "Money transferred successfully from $senderName to $receiverName!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hedvig+Letters+Serif:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
        <style>
body {
    margin: 0;
    padding: 0;
    background-image: url(./img/Group7.png);
    background-repeat: no-repeat;
    background-size: cover;
    background-attachment: fixed;
}

nav {
    background-color: #343a40;
}

nav a {
    color: #fff;
}

.navbar-brand,
.navbar-nav .nav-link {
    padding: 15px;
}

section.text-center {
    border-radius: 20px;
    position: relative;
    top: 50px;
}

h1 {
    margin: 0;
    padding-bottom: 10px;
    font-weight: bold;
    font-family: 'Hedvig Letters Serif', serif;
}

#form {
    padding-top: 10px;
    margin-top: 20px;
    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
    margin: 0 auto;
    padding: 20px;
    border-radius: 10px;
    position: relative;
    top: 10px;
    width: 70%;
    max-width: 500px;
    background-image: url(./img/peso.jpg);
}

.container.d-flex {
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.d-flex.align-items-center {
    width: 100%;
    margin-top: 20px;
    justify-content: space-between;
}

.logo {
    width: 80px;
    max-width: 200px;
}

h3.mb-0 {
    margin-bottom: 0;
    margin-left: 10px;
    font-family: 'Hedvig Letters Serif', serif;
}

.col-md-lg.mb-8.offset {
    margin-bottom: 10px;
    offset: 1;
    padding-top: 150px;
    position: relative;
    bottom: 50px;
}

.feature-box {
    margin-bottom: 20px;
    cursor: pointer;
}

.feature-box i {
    display: block;
    font-size: 3em;
}

.feature-title {
    margin-top: 10px; 
}

@media (max-width: 768px) {
    .logo {
        width: 40%;
        max-width: 100px;
    }

    .container.d-flex,
    .d-flex.align-items-center {
        flex-direction: column;
    }

    .feature-box {
        margin-bottom: 20px;
    }

    .row.text-center.pt-4.mt-4.container .feature-title {
        margin-top: 10px;
    }

    .col-md-lg.mb-8.offset {
        padding-top: 50px;
        bottom: 0;
    }

    .navbar-nav {
        flex-direction: column;
        text-align: center;
    }

    .nav-item {
        margin-bottom: 10px;
    }
}

.delete-notification {
    color: red;
    margin-left: 5px;
    text-decoration: none;
}

.delete-notification:hover {
    text-decoration: underline;
}

    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="home.php">Payme</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="payment_history.php">History</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="login.php?logout">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <section class="text-center container">
    <div class="container d-flex align-items-center flex-column">
            <div class="d-flex align-items-center">
                <img src="img/images-removebg-preview (1).png" class="logo" alt="img1">
                <div>
                    <h3 class="mb-0">Welcome <?php echo $_SESSION['uname']; ?>!</h3>
                    <p><?php echo $_SESSION['phone']; ?></p>
                </div>

            <div class="container container-fluid">
        <div class="row text-center pt-4 mt-4 container">
        <div class="col-4">
            <div id="icon1" class="feature-box"
                onclick="window.location='https://shopee.ph/?gad_source=1&gclid=CjwKCAiAvdCrBhBREiwAX6-6Uk3MJZrzYh9pSRVZ3SMcIN9fbXEsarhp8WOc84oYjZQ9IxyZQX3FCBoCWm0QAvD_BwE'">
                <i class="fa-solid fa-bag-shopping fa-3x"></i>
                <h3 class="feature-title">Shopee</h3>
            </div>
        </div>

        <div class="col-4">
            <div id="icon2" class="feature-box"
                onclick="window.location='https://www.lazada.com.ph/#'">
                <i class="fa-solid fa-heart fa-3x"></i>
                <h3 class="feature-title">Lazada</h3>
            </div>
        </div>

        <div class="col-4">
            <div id="icon3" class="feature-box"
                onclick="window.location='https://www.grab.com/ph/business/food/?utm_source=google&utm_medium=search-paid&utm_term=food%20delivery%20services&utm_campaign=PH_G013_CLUSTERALL-ALL_PAX_GFB_ALL_160721_ACQ-MABA-WEBC_GGR_PH23GFBUSINESS_SEM_GENERIC-EXT&utm_content=PH_G013_CLUSTERALL-ALL_PAX_GFB_ALL_160721_ACQ-MABA-WEBC_GGR_PH23GFBUSINESS_SEM_GENERIC-EXT_FOOD&gad=1&gclid=CjwKCAiAvdCrBhBREiwAX6-6UnnQ_x9cT1kwmvVHDYMda4GSWK_FpBv2BOW4YrfuWNjs3qOT92VUGxoCGCYQAvD_BwE'">
                <i class="fa-solid fa-utensils fa-3x"></i>
                <h3 class="feature-title">Grab Foods</h3>
            </div>
        </div>
    </div>

<div class="row text-center pt-4 mt-4 container">
    <!-- Bills Icon -->
    <div id="icon1" class="feature-box col-3" onclick="window.location='bills.php'">
        <i class="fa-solid fa-money-bill fa-3x"></i>
        <h3 class="feature-title">Bills</h3>
    </div>

    <!-- QR Code Icon -->
    <div id="icon2" class="feature-box col-3" onclick="window.location='qrcode.php'">
        <i class="fa-solid fa-qrcode fa-3x"></i>
        <h3 class="feature-title">QR Code</h3>
    </div>

    <!-- Send Money Icon -->
    <div id="icon3" class="feature-box col-3" onclick="window.location='addbal.php'">
        <i class="fa-solid fa-coins fa-3x"></i>
        <h3 class="feature-title">Send Money</h3>
    </div>

    <!-- Buy Load Icon -->
    <div id="icon4" class="feature-box col-3" onclick="window.location='popup.php'">
        <i class="fa-solid fa-mobile fa-3x"></i>
        <h3 class="feature-title">Pop Up</h3>
    </div>
</div>
    </section>
    <section class="text-center">
    <div class="container">
        <div class="row">
            <div class="col-md-lg mb-8 offset">
                <form id="form" action="home.php" method="post" class="d-flex flex-column align-items-center">
                    <h2>Payme!</h2>
                    <h2>Balance: ₱<?php echo $currentBalance; ?></h2>
                    <?php if (isset($message)) : ?>
                        <p><?php echo $message; ?></p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</section>          
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>