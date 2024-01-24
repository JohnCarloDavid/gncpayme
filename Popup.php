<?php
session_start();
include 'db_connection.php';
include 'common.php';

if (!isset($_SESSION['uname'])) {
    header("Location: index.php");
    exit();
}

$currentUserBalance = getUserBalance($_SESSION['uname'], $conn);
$message = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transactionType = isset($_POST['transaction_type']) ? $_POST['transaction_type'] : '';

    if ($transactionType === 'add_balance') {
        $addedAmount = isset($_POST['added_amount']) ? (float)$_POST['added_amount'] : 0;
        $phoneNumber = isset($_POST['phone_number']) ? $_POST['phone_number'] : '';

        if ($addedAmount <= 0 || empty($phoneNumber)) {
            $message = "Invalid amount or phone number.";
        } else {
            // add balance
            addBalance($phoneNumber, $addedAmount, $conn);
            $currentUserBalance += $addedAmount; // Update the user's balance
            $message = "Pop Up successfully! ₱$addedAmount added to your balance.";
        }
    }
}

$selectedLoadAmount = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pop Up</title>
    <style>
    body {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        background-image: url(./img/Group7.png);
    }

    .container {
        width: 50%;
        margin: 0 auto;
        background-color: #fff;
        padding: 20px;
        margin-top: 50px;
        border-radius: 8px;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        background-image: url(./img/peso.jpg);
    }

    h2 {
        color: #333;
        text-align: center;
    }

    p {
        text-align: center;
        margin-top: 15px;
        color: #333;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    label {
        margin-top: 15px;
        color: black;
    }

    input,
    select {
        width: 100%;
        padding: 10px;
        margin-top: 8px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    button {
        background-color: #4caf50;
        color: white;
        padding: 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background-color: #45a049;
    }

    .mt-3 {
        margin-top: 20px;
    }

    select {
        appearance: none;
        background-color: #f9f9f9;
        border: 1px solid #ccc;
        padding: 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    select:focus {
        outline: none;
        border-color: #4caf50;
    }
</style>

    <script>
        function confirmAddBalance() {
            var addedAmount = document.getElementById("added_amount").value;
            return confirm("Are you sure you want to pop ₱" + addedAmount + " in your balance?");
        }
    </script>
</head>

<body>
    <div class="container">
        <h2>Pop Up</h2>
        <?php if (isset($message)) : ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
        <h2>Your Balance: ₱<?php echo $currentUserBalance; ?></h2>
        <form action="popup.php" method="post" onsubmit="return confirmAddBalance()" class="d-flex flex-column align-items-center">
            <input type="hidden" name="transaction_type" value="add_balance">
            <label for="added_amount" class="mb-4"><strong>Select Amount:</strong></label>
            <select name="added_amount" id="added_amount" required>
                <option value=""></option>
                <option value="50">₱50</option>
                <option value="100">₱100</option>
                <option value="200">₱300</option>
                <option value="500">₱500</option>
                <option value="1000">₱1000</option>
                <option value="3000">₱3000</option>
            </select>

            <label for="phone_number" class="mb-4"><strong>Reference Number:</strong></label>
            <input type="tel" name="phone_number" id="phone_number" required class="mb-4">

            <button type="submit" class="btn btn-primary">Pop Up</button>
        </form>

        <form action="home.php" method="get" class="mt-3">
            <button type="submit" class="btn btn-primary">Back Home</button>
        </form>
    </div>
</body>

</html>