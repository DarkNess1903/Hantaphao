<?php
session_start();
include 'connectDB.php';

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$cart_id = $_POST['cart_id'] ?? null;

if (!$cart_id) {
    die("ไม่พบ Cart ID");
}

// ดึงข้อมูลที่อยู่ของลูกค้า
$address_query = "
    SELECT 
        customer.name, 
        customer.phone AS customer_phone, 
        customer.address, 
        amphur.AMPHUR_NAME AS amphurName, 
        province.PROVINCE_NAME AS provinceName,
        CASE 
            WHEN province.PROVINCE_NAME = 'กรุงเทพมหานคร' THEN district.DISTRICT_CODE
            ELSE amphur.POSTCODE 
        END AS postal_code,
        district.DISTRICT_NAME AS districtName
    FROM customer 
    JOIN amphur ON customer.amphur_id = amphur.AMPHUR_ID 
    JOIN province ON amphur.PROVINCE_ID = province.PROVINCE_ID 
    LEFT JOIN district ON customer.district_id = district.DISTRICT_ID
    WHERE customer.customer_id = ?";

$stmt = mysqli_prepare($conn, $address_query);
mysqli_stmt_bind_param($stmt, 'i', $customer_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $customer_name, $customer_phone, $address, $amphurName, $provinceName, $postal_code, $districtName);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// ดึงข้อมูลตะกร้าสินค้า
$cart_query = "SELECT * FROM cart WHERE customer_id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = mysqli_prepare($conn, $cart_query);
mysqli_stmt_bind_param($stmt, 'i', $customer_id);
mysqli_stmt_execute($stmt);
$cart_result = mysqli_stmt_get_result($stmt);

$cart = mysqli_fetch_assoc($cart_result);
if (!$cart) {
    die("ไม่พบข้อมูลตะกร้าสินค้า");
}

$cart_id = $cart['cart_id'];

// ดึงข้อมูลสินค้าในตะกร้า
$items_query = "SELECT ci.cart_item_id, p.product_id, p.name, p.image, ci.quantity, ci.price, p.weight, p.shipping_cost, (ci.quantity * ci.price) AS total, p.stock_quantity
                FROM cart_items ci
                JOIN product p ON ci.product_id = p.product_id
                WHERE ci.cart_id = ?";

$stmt = mysqli_prepare($conn, $items_query);
mysqli_stmt_bind_param($stmt, 'i', $cart_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);

$total_weight = 0;
$grand_total = 0;
$total_shipping_cost = 0; // กำหนดค่าเริ่มต้น
$shipping_cost_by_weight = 0; // กำหนดค่าเริ่มต้น

while ($item = mysqli_fetch_assoc($items_result)) {
    $item_total = $item['price'] * $item['quantity'];
    $grand_total += $item_total;

    // คำนวณน้ำหนักรวม
    $total_weight += $item['weight'] * $item['quantity'];

    // คำนวณค่าบวกเพิ่มสำหรับการจัดส่ง
    $shipping_cost_per_item = $item['shipping_cost'] * $item['quantity'];
    $total_shipping_cost += $shipping_cost_per_item;
}

// คำนวณค่าจัดส่งตามน้ำหนัก
$shipping_cost = 0;
if ($total_weight <= 1) {
    $shipping_cost = 50;  // ค่าจัดส่งสำหรับน้ำหนักไม่เกิน 1 กิโลกรัม
} elseif ($total_weight <= 5) {
    $shipping_cost = 100; // ค่าจัดส่งสำหรับน้ำหนัก 1-5 กิโลกรัม
} else {
    $shipping_cost = 200; // ค่าจัดส่งสำหรับน้ำหนักเกิน 5 กิโลกรัม
}

// รีเซ็ต pointer เพื่อนำข้อมูลไปใช้ต่อ
mysqli_data_seek($items_result, 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['payment_slip'])) {
    $payment_slip = $_FILES['payment_slip'];
    $upload_dir = realpath(__DIR__ . '/Admin/uploads/');
    $file_name = basename($payment_slip['name']);
    $upload_file = $upload_dir . '/' . $file_name;

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($payment_slip['type'], $allowed_types)) {
        die("ประเภทไฟล์ไม่ถูกต้อง");
    }
    if ($payment_slip['size'] > 2 * 1024 * 1024) {
        die("ขนาดไฟล์เกินกว่าที่กำหนด");
    }

    if (move_uploaded_file($payment_slip['tmp_name'], $upload_file)) {
        // เพิ่มคำสั่งซื้อ
        $order_query = "INSERT INTO orders (customer_id, total_amount, payment_slip, order_date, status, address, shipping_cost) VALUES (?, ?, ?, NOW(), ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $order_query);
        $status = 'รอตรวจสอบ';
        $total_order_amount = $grand_total + $shipping_cost; // เพิ่มค่าจัดส่ง
        mysqli_stmt_bind_param($stmt, 'idssss', $customer_id, $total_order_amount, $file_name, $status, $address, $shipping_cost);
        mysqli_stmt_execute($stmt);
        $order_id = mysqli_insert_id($conn);

        mysqli_data_seek($items_result, 0);

        // วนลูปสินค้าในตะกร้าเพื่อบันทึก order details
        while ($item = mysqli_fetch_assoc($items_result)) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];

            // เพิ่มรายการใน orderdetails
            $orderdetails_query = "INSERT INTO orderdetails (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $orderdetails_query);
            mysqli_stmt_bind_param($stmt, 'iiid', $order_id, $product_id, $quantity, $price);
            mysqli_stmt_execute($stmt);

            // อัปเดตสต็อกสินค้า
            $update_stock_query = "UPDATE product SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
            $stmt = mysqli_prepare($conn, $update_stock_query);
            mysqli_stmt_bind_param($stmt, 'ii', $quantity, $product_id);
            mysqli_stmt_execute($stmt);
        }

        // ลบข้อมูลจาก cart_items
        $delete_cart_items_query = "DELETE FROM cart_items WHERE cart_id = ?";
        $stmt = mysqli_prepare($conn, $delete_cart_items_query);
        mysqli_stmt_bind_param($stmt, 'i', $cart_id);
        mysqli_stmt_execute($stmt);

        echo "<script>alert('คำสั่งซื้อของคุณถูกยืนยันแล้ว!'); window.location.href = 'order_history.php';</script>";
    } else {
        die("การอัปโหลดไฟล์ล้มเหลว");
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>ยืนยันการสั่งซื้อ - Meat Store</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .confirm-checkout {
            margin-top: 50px;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <?php
    include 'topnavbar.php';
    ?>
    
    <header class="text-white text-center py-3">
        <h1>ยืนยันคำสั่งซื้อของคุณ</h1>
    </header>

    <main class="container">
        <section class="confirm-checkout mx-auto">
            <h2>ยืนยันคำสั่งซื้อ</h2>
            <form action="confirm_checkout.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="cart_id" value="<?php echo htmlspecialchars($cart_id, ENT_QUOTES, 'UTF-8'); ?>">
                <h3>รายการสินค้าในตะกร้าของคุณ:</h3>
                <?php if (mysqli_num_rows($items_result) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>รูปภาพ</th>
                                <th>สินค้า</th>
                                <th>จำนวน</th>
                                <th>ราคา</th>
                                <th>รวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                                <tr>
                                    <td><img src="./Admin/product/<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>" width="100"></td>
                                    <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo number_format($item['total'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                    </table>

                    <div class="order-summary">
                        <h4>ยอดคำสั่งซื้อ: <span class="text-success"><?php echo number_format($grand_total, 2); ?> บาท</span></h4>
                        <h4>น้ำหนักรวม: <span class="text-success"><?php echo number_format($total_weight, 2); ?> กิโลกรัม</span></h4>
                        <h4>ค่าจัดส่งจากน้ำหนัก: <span class="text-success"><?php echo number_format($shipping_cost, 2); ?> บาท</span></h4>
                        <h4>ค่าจัดส่งพิเศษ: <span class="text-success"><?php echo number_format($total_shipping_cost, 2); ?> บาท</span></h4>
                        <h4>ยอดรวมทั้งหมด: <span class="text-danger"><?php echo number_format($grand_total + $shipping_cost + $total_shipping_cost, 2); ?> บาท</span></h4>
                    </div>

                <!-- ข้อมูลสำหรับจัดส่ง -->
                <h4 class="mt-4">ข้อมูลสำหรับจัดส่ง:</h4>
                <div class="shipping-info">
                    <p><strong><?php echo htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8'); ?></strong> | <strong><?php echo htmlspecialchars($customer_phone, ENT_QUOTES, 'UTF-8'); ?></strong></p>
                    <p>ที่อยู่: <?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars($districtName, ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars($amphurName, ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars($provinceName, ENT_QUOTES, 'UTF-8') . ', รหัสไปรษณีย์: ' . htmlspecialchars($postal_code, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>

                <!-- เพิ่ม QR Code และเลขบัญชีธนาคาร -->
                <div class="payment-info mt-4">
                    <h3>ข้อมูลการชำระเงิน</h3>
                    <p>กรุณาสแกน QR Code ด้านล่างเพื่อทำการชำระเงิน:</p>
                    <img src="./Admin/images/qr_code.png" alt="QR Code" width="200" class="img-fluid mb-3">
                    <p><strong>บัญชีธนาคาร:</strong> 407-8689387</p>
                    <p><strong>ชื่อบัญชี:</strong> ประภาภรณ์ จันปุ่ม</p>
                </div>

                <!-- อัปโหลดใบเสร็จ -->
                <div class="mb-3 mt-4">
                    <label for="payment_slip" class="form-label">ใบเสร็จการชำระเงิน:</label>
                    <input type="file" class="form-control" id="payment_slip" name="payment_slip" accept="image/*" required>
                </div>

                <button type="submit" class="btn btn-primary">ยืนยันการสั่งซื้อ</button>

                <?php else: ?>
                    <p>ไม่มีรายการสินค้าในตะกร้า</p>
                <?php endif; ?>
            </form>
        </section>
    </main>

    <?php
    mysqli_close($conn);
    include 'footer.php';
    ?>
</body>
</html>