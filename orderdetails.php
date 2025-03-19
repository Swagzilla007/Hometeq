<?php
session_start();
include("db.php");

// Check if user is logged in and is an administrator
if (!isset($_SESSION['userid']) || $_SESSION['usertype'] != 'A') {
    header('Location: stafflogin.php');
    exit();
}

$pagename="Order Details";
echo "<link rel=stylesheet type=text/css href=mystylesheet.css>";
echo "<title>".$pagename."</title>";
echo "<body>";
include ("headfile.html");
echo "<h4>".$pagename."</h4>";

if (isset($_GET['orderno'])) {
    $orderno = $_GET['orderno'];
    
    // Get order details with product information
    $SQL = "SELECT Order_Line.*, Products.prodName, Products.prodPrice 
            FROM Order_Line 
            JOIN Products ON Order_Line.prodId = Products.prodId 
            WHERE Order_Line.orderNo = ?";
            
    if ($stmt = mysqli_prepare($conn, $SQL)) {
        mysqli_stmt_bind_param($stmt, "i", $orderno);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            echo "<h5>Order #".$orderno."</h5>";
            echo "<table id='checkouttable'>";
            echo "<tr>
                    <th>Product Name</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                  </tr>";
            
            $total = 0;
            while ($item = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>".$item['prodName']."</td>";
                echo "<td>&pound;".number_format($item['prodPrice'], 2)."</td>";
                echo "<td>".$item['quantityOrdered']."</td>";
                echo "<td>&pound;".number_format($item['subTotal'], 2)."</td>";
                echo "</tr>";
                $total += $item['subTotal'];
            }
            
            echo "<tr>";
            echo "<td colspan='3'><strong>TOTAL</strong></td>";
            echo "<td>&pound;".number_format($total, 2)."</td>";
            echo "</tr>";
            echo "</table>";
        } else {
            echo "<p>No items found for this order.</p>";
        }
        mysqli_stmt_close($stmt);
    }
    
    echo "<p><a href='processorders.php'>Back to Orders List</a></p>";
} else {
    echo "<p>No order number specified.</p>";
    echo "<p><a href='processorders.php'>Back to Orders List</a></p>";
}

include("footfile.html");
echo "</body>";
?>
