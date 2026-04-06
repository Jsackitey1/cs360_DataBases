<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
</head>

<body>
    <h1>Welcome to the App!</h1>

    <p>Hello, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>
    <img src="cat.gif" alt="Happy Cat" width="200" style="margin-bottom: 20px;">


    <p>You have successfully authenticated.</p>

    <br>
    <a href="logout.php">Click here to Log Out</a>
</body>

</html>