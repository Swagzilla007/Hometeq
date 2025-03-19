<?php
session_start();
include("db.php");

// Check if user is logged in and is an administrator
if (!isset($_SESSION['userid']) || $_SESSION['usertype'] != 'A') {
    header('Location: stafflogin.php');
    exit();
}

if (isset($_POST['order_id'])) {
    $orderno = $_POST['order_id'];
    
    // Update order status to Completed
    $SQL = "UPDATE Orders SET orderStatus = 'Completed' WHERE orderNo = ?";
    
    if ($stmt = mysqli_prepare($conn, $SQL)) {
        mysqli_stmt_bind_param($stmt, "i", $orderno);
        
        if (mysqli_stmt_execute($stmt)) {
            // Redirect back to order details with success message
            header("Location: orderdetails.php?orderno=".$orderno."&status=completed");
        } else {
            // Redirect back with error
            header("Location: orderdetails.php?orderno=".$orderno."&status=error");
        }
        mysqli_stmt_close($stmt);
    }
} else {
    // Redirect to process orders if no order ID provided
    header("Location: processorders.php");
}
exit();
?>
