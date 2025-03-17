<?php
$dbhost = "localhost";  // Change if needed
$dbuser = "root";       // Default user in XAMPP
$dbpass = "";       // Default is empty
$dbname = "hometeq"; // Replace with your actual DB name

//create a DB connection
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
//if the DB connection fails, display an error message and exit
if (!$conn)
{
die('Could not connect: ' . mysqli_error($conn));
}
//select the database
mysqli_select_db($conn, $dbname);
?>
