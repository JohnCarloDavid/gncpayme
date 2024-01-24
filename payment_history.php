<?php
session_start();
include 'db_connection.php';
include 'common.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$paymentHistory = getPaymentHistoryFromFile();

// Check if the delete all button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_all'])) {
    // Clear the payment history
    $paymentHistory = array();
    // Save the empty history to the file
    savePaymentHistoryToFile($paymentHistory);
    // Redirect to the same page to update the display
    header("Location: payment_history.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-image: url(./img/Group7.png);
        }

        .container {
            margin-top: 50px;
        }

        h2 {
            color: #333;
            text-align: center;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #4caf50;
            color: white;
        }

        .btn-back {
            margin-top: 20px;
        }

        .btn-delete-all {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-delete-all:hover {
            background-color: #c82333;
        }
    </style>
    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete all payment history?');
        }
    </script>
</head>

<body>
    <div class="container">
        <h2><b>Payment History</b></h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Sender</th>
                    <th>Receiver Number</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($paymentHistory as $entry) : ?>
            <tr>
                <td><?php echo $entry['sender_name']; ?></td>
                <td><?php echo $entry['receiver_name']; ?></td>
                <td><?php echo $entry['amount']; ?></td>
                <td><?php echo $entry['transaction_date']; ?></td>
                <td><?php echo $entry['transaction_time']; ?></td>
            </tr>
        <?php endforeach; ?>
            </tbody>
        </table>
        <form method="post">
            <button type="submit" name="delete_all" class="btn btn-delete-all" onclick="return confirm('Are you sure you want to delete all payment history?')">Delete History</button>
        </form>
        <a href="home.php" class="btn btn-primary btn-back">Back Home</a>
    </div>
    <!-- Add Bootstrap JS and jQuery links here -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>
