<?php
session_start();

include ('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = validate($_POST['fname']);
    $lname = validate($_POST['lname']);
    $uname = validate($_POST['uname']);
    $phone = validate($_POST['phone']);
    $pass = validate($_POST['password']);

    if (empty($fname) || empty($lname) || empty($uname) || empty($pass) || empty($phone)) {
        header("Location: signup.php?error=All fields are required");
        exit();
    } else {
        // Check if the username is already taken
        $check_user_sql = "SELECT * FROM users WHERE user_name=?";
        $check_user_stmt = mysqli_prepare($conn, $check_user_sql);
        mysqli_stmt_bind_param($check_user_stmt, "s", $uname);
        mysqli_stmt_execute($check_user_stmt);
        $check_user_result = mysqli_stmt_get_result($check_user_stmt);

        // Check if the phone number is already taken
        $check_phone_sql = "SELECT * FROM users WHERE phone_number=?";
        $check_phone_stmt = mysqli_prepare($conn, $check_phone_sql);
        mysqli_stmt_bind_param($check_phone_stmt, "s", $phone);
        mysqli_stmt_execute($check_phone_stmt);
        $check_phone_result = mysqli_stmt_get_result($check_phone_stmt);

        // Check if both username and phone number are already taken together
        $check_both_sql = "SELECT * FROM users WHERE user_name=? AND phone_number=?";
        $check_both_stmt = mysqli_prepare($conn, $check_both_sql);
        mysqli_stmt_bind_param($check_both_stmt, "ss", $uname, $phone);
        mysqli_stmt_execute($check_both_stmt);
        $check_both_result = mysqli_stmt_get_result($check_both_stmt);

        if (mysqli_num_rows($check_user_result) > 0) {
            header("Location: signup.php?error=User Name is already taken");
            exit();
        } elseif (mysqli_num_rows($check_phone_result) > 0) {
            header("Location: signup.php?error=Phone Number is already taken");
            exit();
        } elseif (mysqli_num_rows($check_both_result) > 0) {
            header("Location: signup.php?error=User Name and Phone Number combination is already taken");
            exit();
        }

        // Get the maximum account type from the database
        $max_account_type_query = "SELECT MAX(account_type) AS max_account_type FROM users";
        $max_account_type_result = mysqli_query($conn, $max_account_type_query);
        $max_account_type_row = mysqli_fetch_assoc($max_account_type_result);
        $max_account_type = $max_account_type_row['max_account_type'];
        $accountType = $max_account_type + 1;

        $insertUserQuery = "INSERT INTO users (user_name, password, phone_number, account_type) VALUES (?, ?, ?, ?)";
        $insertUserStmt = mysqli_prepare($conn, $insertUserQuery);
        mysqli_stmt_bind_param($insertUserStmt, "sssi", $uname, $pass, $phone, $accountType);

        if (mysqli_stmt_execute($insertUserStmt)) {
            $_SESSION['uname'] = $uname;
            $_SESSION['fname'] = $fname;
            $_SESSION['phone'] = $phone;
            $_SESSION['lname'] = $lname;
            $_SESSION['account_type'] = $accountType;
            header("Location: signup.php?success=Registration successful. You can now log in.");
            exit();
        } else {
            header("Location: signup.php?error=Registration failed. Please try again later.");
            exit();
        }
    }
} else {
    header("Location: signup.php");
    exit();
}

function validate($data) {
    global $conn;
    $data = trim($data);
    $data = mysqli_real_escape_string($conn, $data); 
    $data = htmlspecialchars($data);
    return $data;
}

mysqli_close($conn); 
?>