<?php
$db = mysqli_connect('localhost', 'root', '', 'educarehub');
if(!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "DELETE FROM products WHERE product_id = $id";
    mysqli_query($db, $query);
}

header("location: view_product.php");
exit();
?>