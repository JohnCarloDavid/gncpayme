<?php
include 'db_connection.php'; 

function recordTransactionToFile($senderName, $receiverName, $amount) {
    $transactionDetails = "{$senderName} sent ₱{$amount} to {$receiverName} on " . getCurrentDate();
    file_put_contents('payment_history.txt', $transactionDetails . PHP_EOL, FILE_APPEND);
}

function getPaymentHistoryFromFile() {
    $filename = 'payment_history.txt';

    if (!file_exists($filename)) {
        return array();
    }

    $paymentHistory = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $formattedHistory = array();
    foreach ($paymentHistory as $entry) {
        $details = explode(' ', $entry);
        $formattedHistory[] = array(
            'sender_name' => $details[0],
            'receiver_name' => $details[4],
            'amount' => $details[2],
            'transaction_date' => $details[6],
            'transaction_time' => $details[7]
        );
    }
    return $formattedHistory;
}

function getUserIdByUserName($userName) {
    global $conn;
    $query = "SELECT id FROM users WHERE user_name = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $userName);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $userId);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $userId;
}

function getUserIdByPhoneNumber($phoneNumber) {
    global $conn;
    $query = "SELECT id FROM users WHERE phone_number = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $phoneNumber);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $userId);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $userId;
}

function savePaymentHistoryToFile($paymentHistory) {
    $paymentHistoryString = '';

    foreach ($paymentHistory as $entry) {
        $paymentHistoryString .= implode(' ', $entry) . PHP_EOL;
    }

    $result = file_put_contents('payment_history.txt', $paymentHistoryString);

    if ($result === false) {
        die("Failed to save payment history to file.");
    }
}

function getUserNameById($userId) {
    global $conn;
    $sanitizedUserId = mysqli_real_escape_string($conn, $userId);
    $query = "SELECT user_name FROM users WHERE id = '$sanitizedUserId'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['user_name'];
    } else {
        return null;
    }
}

function getCurrentDate() {
    return date("Y-m-d h:i:s");
}

function getUserBalance($userName, $conn) {
    $userId = getUserIdByUserName($userName);
    $query = "SELECT balance FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $userId);

    if (!mysqli_stmt_execute($stmt)) {
        die("Error executing statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_result($stmt, $balance);

    if (!mysqli_stmt_fetch($stmt)) {
        die("Error fetching result: " . mysqli_error($conn));
    }

    mysqli_stmt_close($stmt);
    return $balance;
}

// Modify the addBalance function to accept either username or phone number
function addBalance($userIdentifier, $amount, $conn) {
    // Check phone number
    if (is_numeric($userIdentifier)) {
        $userId = getUserIdByPhoneNumber($userIdentifier);
    } else {
        $userId = getUserIdByUserName($userIdentifier);
    }

    if (!$userId) {
        die("User not found");
    }

    // Deduct balance for the user
    $queryDeduct = "UPDATE users SET balance = balance - ? WHERE id = ?";
    $stmtDeduct = mysqli_prepare($conn, $queryDeduct);
    if (!$stmtDeduct) {
        die("Error preparing deduction statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmtDeduct, "di", $amount, $userId);

    if (!mysqli_stmt_execute($stmtDeduct)) {
        die("Error executing deduction statement: " . mysqli_error($conn));
    }

    mysqli_stmt_close($stmtDeduct);
    $currentUserId = getUserIdByUserName($_SESSION['uname']);
    // Add balance for the current user
    $queryAdd = "UPDATE users SET balance = balance + ? WHERE id = ?";
    $stmtAdd = mysqli_prepare($conn, $queryAdd);
    if (!$stmtAdd) {
        die("Error preparing addition statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmtAdd, "di", $amount, $currentUserId);

    if (!mysqli_stmt_execute($stmtAdd)) {
        die("Error executing addition statement: " . mysqli_error($conn));
    }
    mysqli_stmt_close($stmtAdd);
}

function processPayment($senderName, $amount, $conn) {
    // Deduct the amount from the sender's balance
    $userId = getUserIdByUserName($senderName);
    $query = "UPDATE users SET balance = balance - ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "di", $amount, $userId);

    if (!mysqli_stmt_execute($stmt)) {
        die("Error executing statement: " . mysqli_error($conn));
    }
    mysqli_stmt_close($stmt);
    // Placeholder: Record the transaction 
    recordTransactionToFile($senderName, 'bills', $amount);
    return "Payment processed successfully.";
}


?>