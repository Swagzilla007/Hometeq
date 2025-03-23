<?php
session_start();
include("db.php");

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
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
    
    // Check if user has access to this order
    $accessSQL = "SELECT userId FROM Orders WHERE orderNo = ?";
    $accessStmt = mysqli_prepare($conn, $accessSQL);
    mysqli_stmt_bind_param($accessStmt, "i", $orderno);
    mysqli_stmt_execute($accessStmt);
    $accessResult = mysqli_stmt_get_result($accessStmt);
    $orderData = mysqli_fetch_array($accessResult);
    mysqli_stmt_close($accessStmt);

    // Only allow access if user is admin or order belongs to user
    if ($_SESSION['usertype'] != 'A' && $orderData['userId'] != $_SESSION['userid']) {
        header('Location: myorders.php');
        exit();
    }
    
    // Get order status first
    $statusSQL = "SELECT orderStatus FROM Orders WHERE orderNo = ?";
    $statusStmt = mysqli_prepare($conn, $statusSQL);
    mysqli_stmt_bind_param($statusStmt, "i", $orderno);
    mysqli_stmt_execute($statusStmt);
    $statusResult = mysqli_stmt_get_result($statusStmt);
    $orderStatus = mysqli_fetch_array($statusResult)['orderStatus'];
    mysqli_stmt_close($statusStmt);
    
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
            
            echo "<div style='margin: 20px 0;'>";
            echo "<p><strong>Order Status:</strong> ";
            if ($_SESSION['usertype'] == 'A') {
                // Admin view
                if($orderStatus != 'Completed' && $orderStatus != 'Received') {
                    echo $orderStatus;
                    echo "<form action='complete_order.php' method='post' style='margin-top: 10px;'>";
                    echo "<input type='hidden' name='order_id' value='".$orderno."'>";
                    echo "<input type='submit' value='Mark Order as Completed' id='submitbtn'>";
                    echo "</form>";
                } else {
                    echo $orderStatus == 'Received' ? "Order completed successfully" : $orderStatus;
                }
            } else {
                // Customer view
                if($orderStatus == 'Completed') {
                    echo "Arriving to your address";
                    echo "<form action='receive_order.php' method='post' style='margin-top: 10px;'>";
                    echo "<input type='hidden' name='order_id' value='".$orderno."'>";
                    echo "<input type='submit' value='Order Received' id='submitbtn'>";
                    echo "</form>";
                } else if($orderStatus == 'Received') {
                    echo "Order received successfully";
                } else {
                    echo $orderStatus;
                }
            }
            echo "</p>";
            echo "</div>";
        } else {
            echo "<p>No items found for this order.</p>";
        }
        mysqli_stmt_close($stmt);
    }
    
    echo "<p><a href='".($_SESSION['usertype'] == 'A' ? 'processorders.php' : 'myorders.php')."'>Back to Orders List</a></p>";
} else {
    echo "<p>No order number specified.</p>";
    echo "<p><a href='".($_SESSION['usertype'] == 'A' ? 'processorders.php' : 'myorders.php')."'>Back to Orders List</a></p>";
}

include("footfile.html");
echo "</body>";
?>
