<?php
session_start();
include("db.php");

// Check if user is logged in and is an administrator
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit();
} else if ($_SESSION['usertype'] != 'A') {
    header('Location: index.php');
    exit();
}

if (isset($_POST['order_id'])) {
    $orderno = $_POST['order_id'];
    
    // Update order status to Completed
    $SQL = "UPDATE Orders SET orderStatus = 'Completed' WHERE orderNo = ?";
    
    if ($stmt = mysqli_prepare($conn, $SQL)) {
        mysqli_stmt_bind_param($stmt, "i", $orderno);
        
        if (mysqli_stmt_execute($stmt)) {
            // Redirect back to process orders with status update
            header("Location: processorders.php?status=completed");
        } else {
            header("Location: processorders.php?status=error");
        }
        mysqli_stmt_close($stmt);
    }
} else {
    // Redirect to process orders if no order ID provided
    header("Location: processorders.php");
}
exit();
?>
