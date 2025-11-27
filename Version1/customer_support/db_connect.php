<?php  

$conn = new mysqli('localhost', 'root', 'root', 'css_db', 3306);

if ($conn->connect_error) {
    die("Could not connect to MySQL: " . $conn->connect_error);
}
?>
