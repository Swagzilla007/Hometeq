<?php
session_start();
include("db.php");

// Check if user is logged in and is an administrator
if (!isset($_SESSION['userid']) || $_SESSION['usertype'] != 'A') {
    header('Location: stafflogin.php');
    exit();
}

$pagename="Process Orders";
echo "<link rel=stylesheet type=text/css href=mystylesheet.css>";
echo "<title>".$pagename."</title>";
echo "<body>";
include ("headfile.html");
echo "<h4>".$pagename."</h4>";

// Get all orders with user details
$SQL = "SELECT Orders.*, Users.userFName, Users.userSName, Users.userAddress, Users.userPostCode
        FROM Orders 
        JOIN Users ON Orders.userId = Users.userId 
        ORDER BY orderDateTime DESC";

$exeSQL = mysqli_query($conn, $SQL) or die(mysqli_error($conn));

if (mysqli_num_rows($exeSQL) == 0) {
    echo "<p>No orders found.</p>";
} else {
    echo "<table id='processOrdersTable'>";
    echo "<tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Address</th>
            <th>Order Date/Time</th>
            <th>Order Status</th>
            <th>Order Total</th>
            <th>Details</th>
          </tr>";

    while ($order = mysqli_fetch_array($exeSQL)) {
        echo "<tr>";
        echo "<td>".$order['orderNo']."</td>";
        echo "<td>".$order['userFName']." ".$order['userSName']."</td>";
        echo "<td>".$order['userAddress']."<br>".$order['userPostCode']."</td>";
        echo "<td>".$order['orderDateTime']."</td>";
        echo "<td>";
        echo $order['orderStatus'];
        if($order['orderStatus'] != 'Completed') {
            echo "<form action='complete_order.php' method='post' style='margin-top: 5px;'>";
            echo "<input type='hidden' name='order_id' value='".$order['orderNo']."'>";
            echo "<input type='submit' value='Complete' id='submitbtn'>";
            echo "</form>";
        }
        echo "</td>";
        echo "<td>&pound;".number_format($order['orderTotal'], 2)."</td>";
        echo "<td><a href='orderdetails.php?orderno=".$order['orderNo']."'>View Details</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}

include("footfile.html");
echo "</body>";
?>
