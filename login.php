<?php
session_start();

include 'db_connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = validate($_POST['uname']);
    $pass = validate($_POST['password']);

    if (empty($uname) || empty($pass)) {
        header("Location: index.php?error=User Name, and Password are required");
        exit();
    } else {
        $sql = "SELECT * FROM users WHERE user_name=?  AND password=?";
        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "ss", $uname, $pass);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        // Check if user exists in the database
        if (mysqli_num_rows($result) === 1) {
            $_SESSION['uname'] = $uname;
            header("Location: home.php");
            exit();
        } else {
            header("Location: index.php?error=Incorrect User name, or Password");
            exit();
        }
    }
} else {
    header("Location: index.php");
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
