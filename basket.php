<?php
session_start();
include("db.php");

// Redirect admins away from basket
if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'A') {
    header('Location: index.php');
    exit();
}

$pagename="Smart Basket"; //Create and populate a variable called $pagename
echo "<link rel=stylesheet type=text/css href=mystylesheet.css>"; //Call in stylesheet
echo "<title>".$pagename."</title>"; //display name of the page as window title
echo "<body>";
include ("headfile.html"); //include header layout file
echo "<h4>".$pagename."</h4>"; //display name of the page on the web page
//if the value of the product id to be deleted (which was posted through the hidden field) is set
if (isset($_POST['del_prodid']))
{
//capture the posted product id and assign it to a local variable $delprodid
$delprodid=$_POST['del_prodid'];
//unset the cell of the session for this posted product id variable
unset ($_SESSION['basket'][$delprodid]);
//display a "1 item removed from the basket" message
echo "<p>1 item removed";
}
//if the posted ID of the new product is set i.e. if the user is adding a new product into the basket
if (isset($_POST['h_prodid']))
{
//capture the ID of selected product using the POST method and the $_POST superglobal variable
//and store it in a new local variable called $newprodid
$newprodid=$_POST['h_prodid'];
//capture the required quantity of selected product using the POST method and $_POST superglobal variable
//and store it in a new local variable called $reququantity
$reququantity=$_POST['p_quantity'];
//Display id of selected product
//echo "<p>Id of selected product: ".$newprodid;
//Display quantity of selected product
//echo "<br>Quantity of selected product: ".$reququantity;
//create a new cell in the basket session array. Index this cell with the new product id.
//Inside the cell store the required product quantity
$_SESSION['basket'][$newprodid]=$reququantity;
//Display "1 item added to the basket " message
echo "<p>1 item added";
}
//else
//Display "Current basket unchanged " message
else
{
echo "<p>Basket unchanged";
}

$total= 0; //Create a variable $total and initialize it to zero
//Create HTML table with header to display the content of the basket: prod name, price, selected quantity and subtotal
echo "<p><table id='baskettable'>";
echo "<tr>";
echo "<th>Product Name</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th>";
echo "</tr>";
//if the session array $_SESSION['basket'] is set
if (isset($_SESSION['basket']))
{
//loop through the basket session array for each data item inside the session using a foreach loop
//to split the session array between the index and the content of the cell
//for each iteration of the loop
//store the id in a local variable $index & store the required quantity into a local variable $value
foreach($_SESSION['basket'] as $index => $value)
{
// Use prepared statement to prevent SQL injection
$SQL = "SELECT prodId, prodName, prodPrice FROM products WHERE prodId = ?";
$stmt = mysqli_prepare($conn, $SQL);
mysqli_stmt_bind_param($stmt, "i", $index);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($arrayp = mysqli_fetch_array($result)) {
    echo "<tr>";
    echo "<td>".$arrayp['prodName']."</td>";
    echo "<td>&pound".number_format($arrayp['prodPrice'],2)."</td>"; //2 means the number of decimal places
    echo "<td style='text-align:center;'>".$value."</td>";
    $subtotal = $arrayp['prodPrice'] * $value;
    echo "<td>&pound".number_format($subtotal,2)."</td>";
    // Add Remove button form
    echo "<td>";
    echo "<form action=basket.php method=post>";
    echo "<input type=submit value='Remove' id='submitbtn'>";
    echo "<input type=hidden name=del_prodid value='".$arrayp['prodId']."'>";
    echo "</form>";
    echo "</td>";
    echo "</tr>";
    $total = $total + $subtotal;
} else {
    // Handle case where product doesn't exist
    echo "<tr>";
    echo "<td colspan='4'>Product ID ".$index." not found</td>";
    echo "</tr>";
    // Remove invalid product from basket
    unset($_SESSION['basket'][$index]);
}
}
}
//else display empty basket message
else
{
echo "<p>Empty basket";
}

if (isset($_SESSION['userid'])) //check wheather the user logged in 
{
echo "<br><p><a href=checkout.php>CHECKOUT</a></p>";
}
else
{
echo "<br><p>New homteq customers: <a href='signup.php'>Sign up</a></p>";
echo "<p>Returning homteq customers: <a href='login.php'>Login</a></p>";
}
// Display total
echo "<tr>";
echo "<td colspan=3>TOTAL</td>";
echo "<td>&pound".number_format($total,2)."</td>";
echo "</tr>";
echo "</table>";

// Add Clear Basket link if basket is not empty
if (isset($_SESSION['basket']) && count($_SESSION['basket']) > 0) {
    echo "<p><a href='clearbasket.php' class='button'>Clear Basket</a></p>";
}
echo "<br><p>New homteq customers: <a href='signup.php'>Sign up</a></p>";
echo "<p>Returning homteq customers: <a href='login.php'>Login</a></p>";
include("footfile.html"); //include head layout
echo "</body>";
?>