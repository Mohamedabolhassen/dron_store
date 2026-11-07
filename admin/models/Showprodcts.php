<?php
$host = 'localhost';
$name = 'root';
$password = '';
$db = 'dropuhut_ecommerce';

// Establish database connection
$con = mysqli_connect($host, $name, $password, $db);

if (!$con) {
    die("Not Connected: " . mysqli_connect_error());
}


$query = "SELECT  id, productName ,sku,product_category,price,stock FROM  products;" ;

$result = mysqli_query($con , $query) ;


$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

// foreach ($products as $row) {
//     echo "Product: " . htmlspecialchars($row['productName']) . "<br>";
//     echo "SKU: " . htmlspecialchars($row['sku']) . "<br>";
//     echo "Category: " . htmlspecialchars($row['product_category']) . "<br>";
//     echo "Price: " . ($row['price']) . "<br>";
//     echo "Stock: " .($row['stock']) . "<br>";
//     echo "<hr>";
// }




?>