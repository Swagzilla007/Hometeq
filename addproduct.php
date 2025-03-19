<?php
session_start();
include("db.php");

// Check if user is logged in and is an administrator
if (!isset($_SESSION['userid']) || $_SESSION['usertype'] != 'A') {
    header('Location: login.php');
    exit();
}

$pagename="Add New Product"; 
echo "<link rel=stylesheet type=text/css href=mystylesheet.css>";
echo "<title>".$pagename."</title>";
echo "<body>";
include ("headfile.html");
echo "<h4>".$pagename."</h4>";

echo "<div class='formStyle'>";
echo "<form method=post action=addproduct_process.php enctype='multipart/form-data'>";
echo "<table style='border: 0px'>";

echo "<tr><td style='border: 0px'>*Product Name:</td>";
echo "<td style='border: 0px'><input type=text name=p_name size=40></td></tr>";

echo "<tr><td style='border: 0px'>*Small Description:</td>";
echo "<td style='border: 0px'><textarea name=p_desc_short rows=3 cols=40></textarea></td></tr>";

echo "<tr><td style='border: 0px'>*Long Description:</td>";
echo "<td style='border: 0px'><textarea name=p_desc_long rows=5 cols=40></textarea></td></tr>";

echo "<tr><td style='border: 0px'>*Price:</td>";
echo "<td style='border: 0px'><input type=text name=p_price size=40></td></tr>";

echo "<tr><td style='border: 0px'>*Quantity:</td>";
echo "<td style='border: 0px'><input type=number name=p_quantity min=0></td></tr>";

echo "<tr><td style='border: 0px'>*Small Image:</td>";
echo "<td style='border: 0px'><input type=file name=p_image_small accept='image/*'></td></tr>";

echo "<tr><td style='border: 0px'>*Large Image:</td>";
echo "<td style='border: 0px'><input type=file name=p_image_large accept='image/*'></td></tr>";

echo "<tr>";
echo "<td style='border: 0px'><input type=submit value='Add Product' name='submitbtn' id='submitbtn'></td>";
echo "<td style='border: 0px'><input type=reset value='Clear Form' name='submitbtn' id='submitbtn'></td>";
echo "</tr>";

echo "</table>";
echo "</form>";
echo "</div>";

include("footfile.html");
echo "</body>";
?>
