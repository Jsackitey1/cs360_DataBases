<?php
session_start();
include('db_connect.php');

// Security Check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}


if (isset($_GET['id'])) {
    $challenge_id = $_GET['id'];
    $my_id = $_SESSION['user_id'];

    // Update the challenge status to 'declined' 
    $sql = "UPDATE challenges SET status = 'declined' WHERE id = ? AND challenged_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $challenge_id, $my_id);

    if ($stmt->execute()) {
        header("Location: lobby.php?msg=Challenge Declined");
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    header("Location: lobby.php");
}
exit;
?>