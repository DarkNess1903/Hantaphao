<?php
session_start();
include 'connectDB.php';
include 'topnavbar.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}
$customer_id = $_SESSION['customer_id'];

// ดึงข้อมูลตะกร้าสินค้า
$cart_query = "SELECT * FROM cart WHERE customer_id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = mysqli_prepare($conn, $cart_query);
mysqli_stmt_bind_param($stmt, 'i', $customer_id);
mysqli_stmt_execute($stmt);
$cart_result = mysqli_stmt_get_result($stmt);

if (!$cart_result) {
    echo "Error fetching cart: " . mysqli_error($conn);
    exit();
}

$cart = mysqli_fetch_assoc($cart_result);

if ($cart) {
    $cart_id = $cart['cart_id'];

    // ดึงข้อมูลสินค้าจากตะกร้า
    $items_query = "SELECT ci.cart_item_id, p.name, p.image, ci.quantity, ci.price, (ci.quantity * ci.price) AS total, p.stock_quantity
    FROM cart_items ci
    JOIN product p ON ci.product_id = p.product_id
    WHERE ci.cart_id = ?";    
    
    $stmt = mysqli_prepare($conn, $items_query);
    mysqli_stmt_bind_param($stmt, 'i', $cart_id);
    mysqli_stmt_execute($stmt);
    $items_result = mysqli_stmt_get_result($stmt);

    if (!$items_result) {
        echo "Error fetching items: " . mysqli_error($conn);
        exit();
    }

    // คำนวณยอดรวมที่ถูกต้อง
    $grand_total = 0; // ตั้งค่าเริ่มต้นยอดรวม
    while ($item = mysqli_fetch_assoc($items_result)) {
        $item_total = $item['price'] * $item['quantity'];  // คำนวณจากจำนวนและราคา
        $grand_total += $item_total;  // เพิ่มยอดรวม
    }

    // Reset the result pointer to fetch items again
    mysqli_data_seek($items_result, 0);

} else {
    $items_result = [];
    $grand_total = 0;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>ตะกร้าสินค้า</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;700&family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            font-weight: 400;
            line-height: 1.6;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Prompt', sans-serif;
            font-weight: 700;
        }

        .btn {
            font-family: 'Prompt', sans-serif;
            font-weight: 500;
        }

        /* Header */
        header {
            
            color: white;
            text-align: center;
            padding: 2rem 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        header h1 {
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            margin: 0;
        }
    </style>
</head>
<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>ตะกร้าสินค้าของคุณ</h1>
    </header>
    <main class="container mt-4">
        <section class="cart">
            <h2>รายการสินค้าในตะกร้า</h2>
            <?php if ($cart): ?>
                <div class="table-responsive"> <!-- ทำให้ตารางเลื่อนข้างได้บนมือถือ -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>รูปภาพ</th>
                                <th>สินค้า</th>
                                <th>จำนวน</th>
                                <th>ราคา</th>
                                <th>รวมทั้งหมด</th>
                                <th>สต็อก</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                        <tr>
                            <td>
                                <img src="./Admin/product/<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" 
                                    alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>" 
                                    width="100" class="img-fluid"> <!-- ใช้ img-fluid เพื่อให้รูปปรับขนาดตามอุปกรณ์ -->
                            </td>
                            <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <form action="update_cart.php" method="post" class="d-flex align-items-center">
                                    <input type="hidden" name="cart_item_id" value="<?php echo htmlspecialchars($item['cart_item_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <button type="submit" name="action" value="decrease" class="btn btn-outline-secondary">-</button>
                                    <input type="text" name="quantity" 
                                        value="<?php echo number_format($item['quantity'], 0); ?>" 
                                        class="form-control mx-2" style="width: 80px; text-align: center;" readonly>
                                    <button type="submit" name="action" value="increase" class="btn btn-outline-secondary">+</button>
                                </form>
                            </td>
                            <td>
                                <?php echo number_format($item['price'], 2); ?>
                            </td>
                            <td>
                                <?php echo number_format(($item['price'] * $item['quantity']), 2); ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['stock_quantity'], ENT_QUOTES, 'UTF-8'); ?></td> 
                            <td>
                                <a href="remove_from_cart.php?cart_item_id=<?php echo htmlspecialchars($item['cart_item_id'], ENT_QUOTES, 'UTF-8'); ?>" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?')">
                                    <i class="fas fa-trash-alt" title="ลบ"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <p><strong>รวมทั้งหมด: <?php echo number_format($grand_total, 2); ?></strong></p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                    ชำระเงิน
                </button>
            <?php else: ?>
                <p>ตะกร้าสินค้าของคุณว่างเปล่า</p>
            <?php endif; ?>
        </section>
    </main>

    <!-- Modal ยืนยันการสั่งซื้อ -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkoutModalLabel">ยืนยันการสั่งซื้อ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    คุณแน่ใจหรือไม่ว่าต้องการสั่งซื้อรายการนี้?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <form action="confirm_checkout.php" method="post">
                        <input type="hidden" name="cart_id" value="<?php echo htmlspecialchars($cart_id, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-primary">ยืนยัน</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
    include 'footer.php';
    mysqli_close($conn);
    ?>
</body>
</html>
