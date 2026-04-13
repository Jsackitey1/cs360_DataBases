<?php
$db_hostname = "cray";
$db_username = "sackjo02_web";
$db_password = "";
$db_database = "s26_sackjo02";

$conn = new mysqli($db_hostname, $db_username, $db_password);

$conn->select_db($db_database) or
    die("Unable to select database: " . $conn->error);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>