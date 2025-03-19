<?php
session_start();
include("db.php");

// Check if user is logged in and is an administrator
if (!isset($_SESSION['userid']) || $_SESSION['usertype'] != 'A') {
    header('Location: login.php');
    exit();
}

$pagename="Add Product Results";
echo "<link rel=stylesheet type=text/css href=mystylesheet.css>";
echo "<title>".$pagename."</title>";
echo "<body>";
include ("headfile.html");
echo "<h4>".$pagename."</h4>";

// Get form data
$prod_name = trim($_POST['p_name']);
$prod_desc_short = trim($_POST['p_desc_short']);
$prod_desc_long = trim($_POST['p_desc_long']);
$prod_price = trim($_POST['p_price']);
$prod_quantity = trim($_POST['p_quantity']);

// Validate inputs
if (empty($prod_name) || empty($prod_desc_short) || empty($prod_desc_long) || 
    empty($prod_price) || empty($prod_quantity)) {
    echo "<p><b>Add product failed!</b></p>";
    echo "<br><p>All fields are mandatory";
    echo "<br>Make sure you provide all the required details</p>";
} else {
    // Handle file uploads
    $small_image = $_FILES['p_image_small']['name'];
    $large_image = $_FILES['p_image_large']['name'];
    
    // Move uploaded files
    move_uploaded_file($_FILES['p_image_small']['tmp_name'], "images/".$small_image);
    move_uploaded_file($_FILES['p_image_large']['tmp_name'], "images/".$large_image);

    // Use prepared statement
    $SQL = "INSERT INTO Products (prodName, prodPicNameSmall, prodPicNameLarge, prodDescripShort, 
            prodDescripLong, prodPrice, prodQuantity) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
            
    // Prepare statement
    if ($stmt = mysqli_prepare($conn, $SQL)) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "sssssdi", 
            $prod_name,
            $small_image,
            $large_image,
            $prod_desc_short,
            $prod_desc_long,
            $prod_price,
            $prod_quantity
        );
        
        // Execute statement
        if (mysqli_stmt_execute($stmt)) {
            echo "<p><b>Product added successfully!</b></p>";
            echo "<p>New product '".htmlspecialchars($prod_name)."' has been added to the database.</p>";
        } else {
            echo "<p><b>Error adding product!</b></p>";
            echo "<p>Error: " . mysqli_stmt_error($stmt) . "</p>";
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        echo "<p><b>Error preparing statement!</b></p>";
        echo "<p>Error: " . mysqli_error($conn) . "</p>";
    }
}

echo "<br><p><a href='addproduct.php'>Add Another Product</a></p>";
include("footfile.html");
echo "</body>";
?>
