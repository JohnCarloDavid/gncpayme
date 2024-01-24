<?php
session_start();
include 'db_connection.php';
include 'common.php';

if (!isset($_SESSION['uname'])) {
    header("Location: index.php");
    exit();
}
$currentUserBalance = getUserBalance($_SESSION['uname'], $conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transactionType = isset($_POST['transaction_type']) ? $_POST['transaction_type'] : '';

    if ($transactionType === 'send_money') {
        $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
        $receiverPhone = isset($_POST['receiver_phone']) ? $_POST['receiver_phone'] : '';
        $currentUserBalance = getUserBalance($_SESSION['uname'], $conn);
    
        if ($amount <= 0 || empty($receiverPhone)) {
            $message = "Invalid amount or receiver's phone number.";
        } elseif ($amount > 30000) {
            $message = "You can only send up to ₱30,000 at a time.";
        } else {
            $senderUserId = getUserIdByUserName($_SESSION['uname']);
            $receiverUserId = getUserIdByPhoneNumber($receiverPhone);

            if ($receiverUserId !== null && $senderUserId !== $receiverUserId) {
                $balance = $currentUserBalance;

                if ($balance >= $amount) {
                    $currentUserBalance -= $amount;
                    // Record the transaction
                    recordTransaction($_SESSION['uname'], $receiverPhone, $amount, $conn);
                    $message = "Transaction of ₱$amount successful! Sent to $receiverPhone";
                } else {
                    $message = "Insufficient balance.";
                }
            } else {
                $message = "Invalid receiver's phone number.";
            }
        }
    } elseif ($transactionType === 'delete_notification') {
        // Handle the deletion of notifications
        $_SESSION['notifications'] = array();
        $message = "Notifications deleted.";
    }
}

function recordTransaction($senderName, $receiverPhone, $amount, $conn) {
    $senderUserId = getUserIdByUserName($senderName);
    $receiverUserId = getUserIdByPhoneNumber($receiverPhone);

    $updateSenderBalanceSQL = "UPDATE users SET balance = balance - $amount WHERE id = $senderUserId";
    mysqli_query($conn, $updateSenderBalanceSQL);

    $updateReceiverBalanceSQL = "UPDATE users SET balance = balance + $amount WHERE id = $receiverUserId";
    mysqli_query($conn, $updateReceiverBalanceSQL);

    notifySender($senderName, $amount);

    $insertTransactionSQL = "INSERT INTO payment_history (sender_id, receiver_id, amount, transaction_date) 
                             VALUES ($senderUserId, $receiverUserId, $amount, NOW())";
    mysqli_query($conn, $insertTransactionSQL);

    recordTransactionToFile($senderName, $receiverPhone, $amount);
}

function notifySender($senderName, $amount) {
    $notificationText = "You sent ₱$amount to " . $_POST['receiver_phone'] . ".";
    $_SESSION['notifications'][] = $notificationText;
}

$amount = isset($amount) ? $amount : 0;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction</title>
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

        .mt-3 {
            margin-top: 20px;
        }
    </style>
    <script>
        function confirmSendMoney() {
            var amount = document.getElementById("amount").value;
            var receiverPhone = document.getElementById("receiver_phone").value;
            return confirm("Are you sure you want to send ₱" + amount + " to " + receiverPhone + "?");
        }
    </script>
</head>

<body>
    <div class="container">
        <h2>Send Money</h2>
        <?php if (isset($message)) : ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="addbal.php" method="post" onsubmit="return confirmSendMoney()" class="d-flex flex-column align-items-center">
            <h2>Your Current Balance: ₱<?php echo $currentUserBalance; ?></h2>

            <label for="transaction_type" class="mb-4"><strong>Select Transaction Type:</strong></label>
            <select name="transaction_type" id="transaction_type" required>
                <option value=""></option>
                <option value="send_money">Send Money</option>
            </select>
            <label for="receiver_phone" class="mb-4"><strong>Enter Receiver's Phone Number:</strong></label>
            <input type="tel" name="receiver_phone" id="receiver_phone" required class="mb-4">
            <label for="amount" class="mb-4"><strong>Enter Amount:</strong></label>
            <input type="number" name="amount" id="amount" min="1" required class="mb-4">
            <button type="submit" class="btn btn-success">Submit</button>
        </form>
        
        <form action="home.php" method="get" class="mt-3">
            <button type="submit" class="btn btn-primary">Back Home</button>
        </form>
    </div>
</body>


</html>