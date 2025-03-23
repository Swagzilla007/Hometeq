<?php
session_start();
include("db.php");

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['order_id'])) {
    $orderno = $_POST['order_id'];
    
    // Verify order belongs to current user
    $SQL = "UPDATE Orders SET orderStatus = 'Received' 
            WHERE orderNo = ? AND userId = ?";
    
    if ($stmt = mysqli_prepare($conn, $SQL)) {
        mysqli_stmt_bind_param($stmt, "ii", $orderno, $_SESSION['userid']);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: myorders.php?status=received");
        } else {
            header("Location: myorders.php?status=error");
        }
        mysqli_stmt_close($stmt);
    }
}
header("Location: myorders.php");
exit();
?>
