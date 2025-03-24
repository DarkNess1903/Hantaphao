<?php
session_start();
include 'connectDB.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cart_item_id = $_POST['cart_item_id'];
    $action = $_POST['action'];

    // ดึงข้อมูลจากตะกร้าเพื่ออัพเดต
    $query = "SELECT ci.quantity
              FROM cart_items ci
              WHERE ci.cart_item_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $cart_item_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $item = mysqli_fetch_assoc($result);
        $quantity = $item['quantity'];

        if ($action === 'increase') {
            $quantity += 1; // เพิ่ม 1 ชิ้น
        } elseif ($action === 'decrease') {
            $quantity -= 1; // ลด 1 ชิ้น
        }

        if ($quantity <= 0) {
            // แสดงการยืนยันการลบโดยใช้ JavaScript modal
            echo "<script>
                if (confirm('คุณต้องการลบสินค้านี้ออกจากตะกร้าใช่หรือไม่?')) {
                    window.location.href = 'remove_from_cart.php?cart_item_id=' + $cart_item_id;
                } else {
                    window.location.href = 'cart.php';
                }
            </script>";
        } else {
            // อัพเดตจำนวนในฐานข้อมูล
            $update_query = "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, 'di', $quantity, $cart_item_id);
            mysqli_stmt_execute($update_stmt);

            if (mysqli_stmt_affected_rows($update_stmt) > 0) {
                header("Location: cart.php");
                exit();
            } else {
                echo "Error updating quantity: " . mysqli_error($conn);
            }
        }
    } else {
        echo "Error fetching item: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
