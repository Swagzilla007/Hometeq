<?php
session_start();
include("db.php");

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit();
}

$pagename="My Orders";
echo "<link rel=stylesheet type=text/css href=mystylesheet.css>";
echo "<title>".$pagename."</title>";
echo "<body>";
include ("headfile.html");
echo "<h4>".$pagename."</h4>";

// Get all orders for current user
$SQL = "SELECT Orders.*, Users.userFName, Users.userSName, Users.userAddress, Users.userPostCode
        FROM Orders 
        JOIN Users ON Orders.userId = Users.userId 
        WHERE Orders.userId = ?
        ORDER BY orderDateTime DESC";

if ($stmt = mysqli_prepare($conn, $SQL)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['userid']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 0) {
        echo "<p>No orders found.</p>";
    } else {
        echo "<table id='processOrdersTable'>";
        echo "<tr>
                <th>Order ID</th>
                <th>Order Date/Time</th>
                <th>Status</th>
                <th>Total</th>
                <th>Action</th>
            </tr>";

        while ($order = mysqli_fetch_array($result)) {
            echo "<tr>";
            echo "<td>".$order['orderNo']."</td>";
            echo "<td>".$order['orderDateTime']."</td>";
            echo "<td>";
            if ($order['orderStatus'] == 'Completed') {
                echo "Arriving to your address";
                // Show receive button if order is completed but not received
                echo "<form action='receive_order.php' method='post' style='margin-top: 5px;'>";
                echo "<input type='hidden' name='order_id' value='".$order['orderNo']."'>";
                echo "<input type='submit' value='Order Received' id='submitbtn'>";
                echo "</form>";
            } else if ($order['orderStatus'] == 'Received') {
                echo "Order received successfully";
            } else {
                echo $order['orderStatus'];
            }
            echo "</td>";
            echo "<td>&pound;".number_format($order['orderTotal'], 2)."</td>";
            echo "<td><a href='orderdetails.php?orderno=".$order['orderNo']."'>View Details</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    mysqli_stmt_close($stmt);
}

include("footfile.html");
echo "</body>";
?>
