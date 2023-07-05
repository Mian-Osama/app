<?php
$servername = "sql301.infinityfree.com";
$username = "epiz_34005637";
$password = "fSlBZS1NdG2LawU";
$dbname = "test";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>