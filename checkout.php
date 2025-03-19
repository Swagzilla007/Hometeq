<?php
session_start();
include("db.php");
mysqli_report(MYSQLI_REPORT_OFF); //this turns off error reporting to the user
$pagename="Checkout"; //Create and populate a variable called $pagename
echo "<link rel=stylesheet type=text/css href=mystylesheet.css>"; //Call in stylesheet
echo "<title>".$pagename."</title>"; //display name of the page as window title
echo "<body>";
include ("headfile.html"); //include header layout file
include ("detectlogin.php");//created in login process
echo "<h4>".$pagename."</h4>"; //display name of the page on the web page
//store the current date and time in a local variable $currentdatetime
$currentdatetime = date('Y-m-d H:i:s');
//write a SQL query to insert a new record in the Orders table to generate a new order.
$SQL = "INSERT into Orders (userId, orderDateTime, orderStatus)
VALUES ('".$_SESSION['userid']."','".$currentdatetime."', 'Placed')";
//if execution of the INSERT INTO SQL query to add new order is correct
if (mysqli_query($conn, $SQL) and isset($_SESSION['basket']) and count($_SESSION['basket'])>0) 
{
    $orderno = mysqli_insert_id($conn); // Get the ID of newly inserted order
    echo "<p><b>Order successfully placed!</b></p>";
    echo "<p>Order No: <b>".$orderno."</b></p>";
    
    $total = 0;
    echo "<table id='checkouttable'>";
    echo "<tr>";
    echo "<th>Product name</th>";
    echo "<th>Price</th>";
    echo "<th>Quantity</th>";
    echo "<th>Subtotal</th>";
    echo "</tr>";
    
    foreach($_SESSION['basket'] as $index => $value)
    {
        // Fixed table name from Product to Products
        $stmt = mysqli_prepare($conn, "SELECT prodId, prodName, prodPrice FROM Products WHERE prodId = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $index);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if($arrayb = mysqli_fetch_array($result)) {
                $subtotal = $value * $arrayb['prodPrice'];
                
                $stmtLine = mysqli_prepare($conn, "INSERT INTO Order_Line (orderNo, prodId, quantityOrdered, subTotal) VALUES (?, ?, ?, ?)");
                if ($stmtLine) {
                    mysqli_stmt_bind_param($stmtLine, "iiid", $orderno, $index, $value, $subtotal);
                    mysqli_stmt_execute($stmtLine);
                    mysqli_stmt_close($stmtLine);
                    
                    echo "<tr>";
                    echo "<td>".$arrayb['prodName']."</td>";
                    echo "<td>&pound;".number_format($arrayb['prodPrice'], 2)."</td>";
                    echo "<td>".$value."</td>";
                    echo "<td>&pound;".number_format($subtotal, 2)."</td>";
                    echo "</tr>";
                    
                    $total = $total + $subtotal;
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    echo "<tr>";
    echo "<th colspan='3'>TOTAL</th>";
    echo "<th>&pound;".number_format($total, 2)."</th>";
    echo "</tr>";
    echo "</table>";
    
    $stmtTotal = mysqli_prepare($conn, "UPDATE Orders SET orderTotal = ? WHERE orderNo = ?");
    if ($stmtTotal) {
        mysqli_stmt_bind_param($stmtTotal, "di", $total, $orderno);
        mysqli_stmt_execute($stmtTotal);
        mysqli_stmt_close($stmtTotal);
    }
    
    unset($_SESSION['basket']);
}
else
{
    echo "<p><b>Error with the placing of your order!</b></p>";
}

include("footfile.html"); //include head layout
echo "</body>";
?>