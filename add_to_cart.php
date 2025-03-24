<?php
session_start();
include 'connectDB.php';

// ตรวจสอบว่ามีการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// รับค่าจาก POST
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;

// ตรวจสอบว่ามีการเลือกสินค้าหรือไม่
if ($product_id > 0 && $quantity > 0 && $price > 0) {
    // ตรวจสอบว่ามีการเข้าสู่ระบบหรือยัง
    if (!isset($_SESSION['customer_id'])) {
        header("Location: login.php");
        exit();
    }

    // รับ customer_id จาก session
    $customer_id = $_SESSION['customer_id'];

    // ตรวจสอบว่ามีการสร้างตะกร้าสินค้าใน session หรือไม่
    if (!isset($_SESSION['cart_id'])) {
        // สร้างตะกร้าสินค้าใหม่
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

    // ตรวจสอบว่ามีสินค้าในตะกร้าแล้วหรือไม่
    $query = "SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $cart_id, $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // ถ้ามีสินค้าในตะกร้าแล้ว
        $query = "UPDATE cart_items SET quantity = quantity + ? WHERE cart_id = ? AND product_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'iii', $quantity, $cart_id, $product_id);
    } else {
        // ถ้ายังไม่มีสินค้าในตะกร้า
        $query = "INSERT INTO cart_items (cart_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'iiid', $cart_id, $product_id, $quantity, $price);
    }

    // Execute the statement to add or update the item in the cart
    if (mysqli_stmt_execute($stmt)) {
        header("Location: cart.php"); // ไปที่หน้าตะกร้า
        exit();
    } else {
        die("Error adding item to cart: " . mysqli_error($conn));
    }
} else {
    die("Invalid product ID, quantity, or price.");
}

// ปิดการเชื่อมต่อฐานข้อมูล
mysqli_close($conn);
?>
