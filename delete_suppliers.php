<?php
$db = mysqli_connect('localhost', 'root', '', 'educarehub');
if(!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "DELETE FROM supplier WHERE supplier_id = $id";
    mysqli_query($db, $query);
}

header("location: view_suppliers.php");
exit();
?>