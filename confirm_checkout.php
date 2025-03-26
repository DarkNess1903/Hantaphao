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

// ดึงข้อมูลสินค้าในตะกร้า
$items_query = "SELECT p.product_id, p.name, p.weight, p.shipping_type, ci.quantity, ci.price, p.image
                FROM cart_items ci
                JOIN product p ON ci.product_id = p.product_id
                WHERE ci.cart_id = ?";

$stmt = mysqli_prepare($conn, $items_query);
mysqli_stmt_bind_param($stmt, 'i', $cart_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);

$total_weight = 0;  
$grand_total = 0;
$shipping_cost_normal = 0;
$shipping_cost_chilled = 0;
$shipping_cost_frozen = 0;

while ($item = mysqli_fetch_assoc($items_result)) {
    $grand_total += $item['price'] * $item['quantity'];
    $total_weight += $item['weight'] * $item['quantity'];
    
    if ($item['shipping_type'] == 'normal') {
        $shipping_cost_normal += $item['weight'] * $item['quantity'] <= 1 ? 35 : ($item['weight'] * $item['quantity'] <= 5 ? 50 : 100);
    } elseif ($item['shipping_type'] == 'chilled') {
        $shipping_cost_chilled += $item['weight'] * $item['quantity'] <= 5 ? 140 : ($item['weight'] * $item['quantity'] <= 10 ? 180 : ($item['weight'] * $item['quantity'] <= 15 ? 220 : 260));
    } elseif ($item['shipping_type'] == 'frozen') {
        $shipping_cost_frozen += $item['weight'] * $item['quantity'] <= 5 ? 160 : ($item['weight'] * $item['quantity'] <= 10 ? 200 : ($item['weight'] * $item['quantity'] <= 15 ? 240 : 290));
    }
}

$shipping_cost = $shipping_cost_normal + $shipping_cost_chilled + $shipping_cost_frozen;
$total_order_amount = $grand_total + $shipping_cost;

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
        $order_query = "INSERT INTO orders (customer_id, total_amount, payment_slip, order_date, status, address, shipping_fee) VALUES (?, ?, ?, NOW(), ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $order_query);
        $status = 'รอตรวจสอบ';
        $total_order_amount = $grand_total + $shipping_cost;
        mysqli_stmt_bind_param($stmt, 'idssss', $customer_id, $total_order_amount, $file_name, $status, $address, $shipping_cost);
        mysqli_stmt_execute($stmt);
        $order_id = mysqli_insert_id($conn);

        mysqli_data_seek($items_result, 0);

        while ($item = mysqli_fetch_assoc($items_result)) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];

            $orderdetails_query = "INSERT INTO orderdetails (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $orderdetails_query);
            mysqli_stmt_bind_param($stmt, 'iiid', $order_id, $product_id, $quantity, $price);
            mysqli_stmt_execute($stmt);

            $update_stock_query = "UPDATE product SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
            $stmt = mysqli_prepare($conn, $update_stock_query);
            mysqli_stmt_bind_param($stmt, 'ii', $quantity, $product_id);
            mysqli_stmt_execute($stmt);
        }

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันการสั่งซื้อ - Meat Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ธีมสี */
        :root {
            --green-dark: #2a6041;    /* เขียวเข้มทันสมัย */
            --green-medium: #5da271;  /* เขียวกลางสดใส */
            --green-light: #eef7f1;   /* เขียวอ่อนสะอาด */
            --green-accent: #b8e0c3;  /* เขียวเน้นนุ่มนวล */
            --black: #1c2526;         /* ดำเข้มสำหรับ header */
        }

        body {
            background-color: #f7f9f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0; /* ลบ margin default ของ body */
        }

        /* Header */
        header {
            background: #212529!important;
            color: white;
            padding: 0.75rem 0; /* ลดความสูง header อีกจาก 1rem */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            margin-top: 0; /* ลดระยะห่างจากขอบบน */
        }
        header h1 {
            font-size: 2.5rem;
            margin: 0;
            padding-bottom:15px ;
        }

        /* Main Container */
        .confirm-checkout {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin: 2rem auto;
            max-width: 850px;
        }

        /* Section Titles */
        h2, h3, h4 {
            color: var(--green-dark);
            font-weight: 600;
        }

        /* Table */
        .table {
            border-radius: 8px;
            overflow: hidden;
            background: var(--green-light);
        }
        .table thead {
            background: var(--black); /* เปลี่ยนสีกรอบบนเป็นดำ */
            color: white;
            font-family: 'Poppins', sans-serif; /* ฟอนต์ทันสมัย */
            font-weight: 500;
            font-size: 0.9rem; /* ปรับขนาดฟอนต์ */
        }
        .table img {
            border-radius: 5px;
            object-fit: cover;
            width: 70px;
        }

        /* Order Summary */
        .order-summary {
            background: var(--green-light);
            border: 1px solid var(--green-accent);
            border-radius: 10px;
            padding: 1.25rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .order-title {
            color: var(--green-dark);
            font-size: 1.35rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 0.6rem 0;
            border-bottom: 1px solid #e8ecea;
            font-size: 0.9rem;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .order-label {
            color: #555;
            font-weight: 500;
        }
        .order-value {
            color: var(--green-medium);
            font-weight: 600;
        }
        .total-amount {
            background: var(--green-accent);
            padding: 0.6rem;
            border-radius: 8px;
            font-size: 1rem;
            color: var(--green-dark);
            font-weight: 700;
            margin-top: 0.75rem;
        }

        /* Shipping Info */
        .shipping-info {
            background: var(--green-light);
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid var(--green-accent);
        }
        .shipping-info p {
            margin: 0.4rem 0;
            color: #444;
            font-size: 0.95rem;
        }

        /* Payment Info */
        .payment-info {
            text-align: center;
            background: var(--green-light);
            padding: 1.25rem;
            border-radius: 10px;
            border: 1px solid var(--green-accent);
        }
        .payment-info img {
            max-width: 160px;
            border-radius: 5px;
        }

        /* Button */
        .btn-confirm {
            background: var(--green-medium);
            border: none;
            padding: 0.6rem 1.5rem;
            font-size: 0.95rem;
            border-radius: 20px;
            transition: background 0.3s;
        }
        .btn-confirm:hover {
            background: var(--green-dark);
        }
    

        /* Responsive */
        @media (max-width: 768px) {
            .confirm-checkout {
                padding: 1rem;
            }
            .order-item, .total-amount {
                flex-direction: column;
                text-align: center;
            }
            .order-value {
                margin-top: 0.2rem;
            }
            header h1 {
                font-size: 1.25rem;
            }
        }
        /* Button */
        .btn-confirm {
            background: var(--green-medium); /* #5da271 */
            border: none;
            padding: 0.75rem 2rem; /* เพิ่ม padding ให้ปุ่มดูใหญ่ขึ้นเล็กน้อย */
            font-size: 1rem; /* เพิ่มขนาดฟอนต์ */
            font-weight: 600; /* ฟอนต์หนาขึ้น */
            border-radius: 25px; /* วงรีมากขึ้น */
            color: white; /* สีตัวอักษรขาวเพื่อคอนทราสต์ */
            transition: all 0.3s ease; /* เพิ่ม transition ให้ smooth */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15); /* เงาเบาๆ */
        }
        .btn-confirm:hover {
            background: var(--green-dark); /* #2a6041 */
            transform: translateY(-2px); /* ยกขึ้นเมื่อ hover */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); /* เงาเข้มขึ้น */
        }
        .btn-confirm:active {
            transform: translateY(1px); /* กดลงเมื่อคลิก */
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1); /* เงาลดลง */
        }
    </style>
</head>
<body>
    <?php include 'topnavbar.php'; ?>
    
    <header class="text-center">
        <h1>ยืนยันคำสั่งซื้อของคุณ</h1>
    </header>

    <main class="container">
        <section class="confirm-checkout">
            <h2 class="mb-4">ยืนยันคำสั่งซื้อ</h2>
            <form action="confirm_checkout.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="cart_id" value="<?php echo htmlspecialchars($cart_id, ENT_QUOTES, 'UTF-8'); ?>">
                
                <h3 class="mb-3">รายการสินค้าในตะกร้า</h3>
                <?php if (mysqli_num_rows($items_result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="color:#f7f9f8">รูปภาพ</th>
                                    <th style="color:#f7f9f8">สินค้า</th>
                                    <th style="color:#f7f9f8">จำนวน</th>
                                    <th style="color:#f7f9f8">ราคา</th>
                                    <th style="color:#f7f9f8">รวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                                    <tr>
                                        <td><img src="./Admin/product/<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>" width="70"></td>
                                        <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="order-summary mt-4">
                        <h3 class="order-title">ยอดคำสั่งซื้อ</h3>
                        <div class="order-item">
                            <span class="order-label">ยอดรวมสินค้า:</span>
                            <span class="order-value"><?php echo number_format($grand_total, 2); ?> บาท</span>
                        </div>
                        <div class="order-item">
                            <span class="order-label">น้ำหนักรวม:</span>
                            <span class="order-value"><?php echo number_format($total_weight, 2); ?> กก.</span>
                        </div>
                        <div class="order-item">
                            <span class="order-label">ค่าจัดส่ง (Normal):</span>
                            <span class="order-value"><?php echo number_format($shipping_cost_normal, 2); ?> บาท</span>
                        </div>
                        <div class="order-item">
                            <span class="order-label">ค่าจัดส่ง (Chilled):</span>
                            <span class="order-value"><?php echo number_format($shipping_cost_chilled, 2); ?> บาท</span>
                        </div>
                        <div class="order-item">
                            <span class="order-label">ค่าจัดส่ง (Frozen):</span>
                            <span class="order-value"><?php echo number_format($shipping_cost_frozen, 2); ?> บาท</span>
                        </div>
                        <div class="order-item">
                            <span class="order-label">ค่าจัดส่งรวม:</span>
                            <span class="order-value"><?php echo number_format($shipping_cost, 2); ?> บาท</span>
                        </div>
                        <div class="order-item total-amount">
                            <span class="order-label">ยอดรวมทั้งหมด:</span>
                            <span class="order-value"><?php echo number_format($total_order_amount, 2); ?> บาท</span>
                        </div>
                    </div>

                    <h4 class="mt-4">ข้อมูลจัดส่ง</h4>
                    <div class="shipping-info mb-4">
                        <p><strong><?php echo htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8'); ?></strong> | <strong><?php echo htmlspecialchars($customer_phone, ENT_QUOTES, 'UTF-8'); ?></strong></p>
                        <p><?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars($districtName, ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars($amphurName, ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars($provinceName, ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars($postal_code, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>

                    <div class="payment-info mb-4">
                        <h3 class="mb-3">ชำระเงิน</h3>
                        <p>กรุณาสแกน QR Code หรือโอนเงินไปยังบัญชี:</p>
                        <img src="./Admin/images/qr_code.png" alt="QR Code" class="img-fluid mb-3">
                        <p><strong>บัญชีธนาคาร:</strong> 407-8689387</p>
                        <p><strong>ชื่อบัญชี:</strong> ประภาภรณ์ จันปุ่ม</p>
                    </div>

                    <div class="mb-4">
                        <label for="payment_slip" class="form-label">อัปโหลดใบเสร็จ:</label>
                        <input type="file" class="form-control" id="payment_slip" name="payment_slip" accept="image/*" required>
                    </div>

                    <div class="button-container">
                        <button type="submit" class="btn btn-confirm">ยืนยันการสั่งซื้อ</button>
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted">ไม่มีรายการสินค้าในตะกร้า</p>
                <?php endif; ?>
            </form>
        </section>
    </main>

    <?php
    mysqli_close($conn);
    include 'footer.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>