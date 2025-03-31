<?php
session_start();
include 'connectDB.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = 1; // บังคับให้เพิ่มแค่ 1 ชิ้น
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;
$category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0; // รับ category_id จากฟอร์ม

if ($product_id > 0 && $quantity > 0 && $price > 0) {
    if (!isset($_SESSION['customer_id'])) {
        header("Location: login.php");
        exit();
    }

    $customer_id = $_SESSION['customer_id'];

    if (!isset($_SESSION['cart_id'])) {
        $query = "INSERT INTO cart (customer_id) VALUES (?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $customer_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['cart_id'] = mysqli_insert_id($conn);
        } else {
            die("Error creating cart: " . mysqli_error($conn));
        }
    }

    $cart_id = $_SESSION['cart_id'];
    
    $query = "SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $cart_id, $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $referer = $_SERVER['HTTP_REFERER'] ?? 'product.php'; // หน้าก่อนหน้า
    $redirect_url = "$referer?message=";

    if (mysqli_num_rows($result) > 0) {
        $redirect_url .= urlencode("สินค้านี้อยู่ในตะกร้าแล้ว");
    } else {
        $query = "INSERT INTO cart_items (cart_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'iiid', $cart_id, $product_id, $quantity, $price);
        
        if (mysqli_stmt_execute($stmt)) {
            $redirect_url .= urlencode("เพิ่มสินค้าสำเร็จ");
        } else {
            die("Error adding item to cart: " . mysqli_error($conn));
        }
    }

    // เพิ่ม category_id ใน URL ถ้ามี
    if ($category_id > 0) {
        $redirect_url .= "&category=$category_id";
    }

    header("Location: $redirect_url");
    exit();
} else {
    die("Invalid product ID, quantity, or price.");
}

mysqli_close($conn);
?>