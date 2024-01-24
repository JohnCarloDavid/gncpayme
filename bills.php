<?php
session_start();

include 'db_connection.php';
include 'common.php';

if (!isset($_SESSION['uname'])) {
    header("Location: index.php");
    exit();
}

$currentBalance = getUserBalance($_SESSION['uname'], $conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['amount'])) {
        $amount = (float)$_POST['amount'];
        if ($amount <= 0) {
            $message = "Invalid amount.";
        } elseif ($currentBalance < $amount) {
            $message = "Insufficient funds.";
        } else {
            $senderName = $_SESSION['uname'];
            $paymentStatus = processPayment($senderName, $amount, $conn);
            $message = "Successfully paid from $senderName to your bills!";
            $currentBalance = getUserBalance($senderName, $conn);
        }
    } elseif (isset($_POST['bill_type'])) {
        $billType = isset($_POST['bill_type']) ? $_POST['bill_type'] : '';
        $billAmount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
        $accountNumber = isset($_POST['account_number']) ? $_POST['account_number'] : '';

        if ($billAmount <= 0) {
            $message = "Invalid bill amount.";
        } else {
            $paymentStatus = processBillsPayment($billType, $billAmount, $accountNumber, $conn, $_SESSION['uname']);
            $currentBalance = getUserBalance($_SESSION['uname'], $conn);
        }
    } else {
        $message = "Invalid request.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bills Payment</title>
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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-image: url(./img/peso.jpg);

        }

        h2 {
            color: #333;
            text-align: center;
        }
        

        .form1 {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form2 {
            padding-top: 10px;
            display: flex;
            align-items: center;
            flex-direction: column;
        }

        label {
            margin-top: 15px;
            color: black;
        }

        select,
        input {
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

        p {
            text-align: center;
            margin-top: 15px;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Bills Payment</h2>
        <?php if (isset($paymentStatus)) : ?>
            <p><?php echo $paymentStatus; ?></p>
        <?php endif; ?>
        <?php if (isset($message)) : ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="bills.php" method="post" onsubmit="return confirmPayment()" class="form1 d-flex flex-column align-items-center">
            <h2>Balance: ₱<?php echo $currentBalance; ?></h2>
            <label for="bill_type"><strong>Select Bill Type:</strong></label>
            <select name="bill_type" id="bill_type" required>
                <option value=""></option>
                <option value="electricity">Electricity</option>
                <option value="water">Water</option>
                <option value="internet">Internet</option>
            </select>
            <label for="account_number"><strong>Account Number:</strong></label>
            <input type="text" name="account_number" id="account_number" required>
            <label for="amount" class="mb-4"><strong>Enter Amount to Transfer:</strong></label>
            <input type="number" name="amount" id="amount" min="1" required class="mb-4">
            <button type="submit" class="btn btn-primary">Pay Now</button>
        </form>
        <form action="home.php" method="get" class="form2 mt-3">
            <button type="submit" class="btn btn-success">Home</button>
        </form>
    </div>
    <script>
        function confirmPayment() {
            var billType = document.getElementById("bill_type").value;
            var accountNumber = document.getElementById("account_number").value;
            var amount = document.getElementById("amount").value;

            if (billType === "" || accountNumber === "" || amount === "") {
                alert("Please fill in all fields.");
                return false;
            }

            return confirm("Confirm payment of ₱" + amount + " for " + billType + " with account number " + accountNumber + "?");
        }
    </script>
</body>
</html>
