<?php
include('db_connect.php');
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $sql = "INSERT INTO users (username, password) VALUES (?, SHA2(?, 256))";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $user, $pass);

    try {
        if (mysqli_stmt_execute($stmt)) {
            $message = "Registration successful! <a href='login.php'>Login here</a>";
        }
    } catch (mysqli_sql_exception $e) {

        if ($e->getCode() == 1062) {
            $message = "Error: The username '" . htmlspecialchars($user) . "' is already taken. Please choose another.";
        } else {
            $message = "An unexpected error occurred. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
</head>

<body>
    <h2>Create Account</h2>
    <p style="color: red;"><?php echo $message; ?></p>

    <form method="POST" action="register.php">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <input type="submit" value="Register">
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>

</html>