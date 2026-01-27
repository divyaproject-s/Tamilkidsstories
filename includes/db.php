<?php
$conn = new mysqli("localhost", "root", "", "tamilkidsstories");

if ($conn->connect_error) {
    die("Database Connection Failed");
}
?>
