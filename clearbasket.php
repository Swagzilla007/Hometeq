<?php
session_start();
include ("db.php"); //include db.php file to connect to DB
$pagename="clear smart basket"; //create and populate variable called $pagename
echo "<link rel=stylesheet type=text/css href=mystylesheet.css>";
echo "<title>".$pagename."</title>";
echo "<body>";
include ("headfile.html");
echo "<h4>".$pagename."</h4>";
unset($_SESSION['basket']);
echo "<P>Your basket has been cleared!";
echo "<br><p><a href='clearbasket.php'>CLEAR BASKET</a></p>";
include ("footfile.html");
echo "</body>";
?>