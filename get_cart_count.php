<?php
session_start();
include 'connectDB.php';

header('Content-Type: application/json'); // บังคับให้ response เป็น JSON

if (isset($_SESSION['cart_id'])) {
    $cart_id = $_SESSION['cart_id'];
    $query = "SELECT SUM(quantity) AS item_count FROM cart_items WHERE cart_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $cart_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $item_count = $row['item_count'] ?? 0;

    echo json_encode(['status' => 'success', 'item_count' => $item_count]);
} else {
    echo json_encode(['status' => 'success', 'item_count' => 0]);
}

mysqli_close($conn);
?>