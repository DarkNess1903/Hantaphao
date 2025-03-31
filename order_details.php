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
$order_id = intval($_GET['order_id'] ?? 0);

if ($order_id <= 0) {
    die("รหัสคำสั่งซื้อไม่ถูกต้อง.");
}

// ดึงข้อมูลคำสั่งซื้อ (รวม address และ shipping_fee)
$order_query = "
    SELECT orders.order_id, orders.customer_id, orders.order_date, orders.total_amount, orders.payment_slip, 
           orders.status, orders.tracking_number, orders.address, orders.shipping_fee,
           COALESCE(amphur.AMPHUR_NAME, 'ไม่ระบุ') AS amphurName, 
           COALESCE(province.PROVINCE_NAME, 'ไม่ระบุ') AS provinceName
    FROM orders
    LEFT JOIN customer ON orders.customer_id = customer.customer_id
    LEFT JOIN amphur ON customer.amphur_id = amphur.AMPHUR_ID
    LEFT JOIN province ON customer.province_id = province.PROVINCE_ID
    WHERE orders.order_id = ? AND orders.customer_id = ?";
$stmt = mysqli_prepare($conn, $order_query);
mysqli_stmt_bind_param($stmt, 'ii', $order_id, $customer_id);
mysqli_stmt_execute($stmt);
$order_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($order_result) === 0) {
    die("ไม่พบคำสั่งซื้อ.");
}

$order = mysqli_fetch_assoc($order_result);

// ดึงรายละเอียดสินค้า
$details_query = "
    SELECT p.name, p.image, od.quantity, od.price
    FROM orderdetails od
    JOIN product p ON od.product_id = p.product_id
    WHERE od.order_id = ?";
$stmt = mysqli_prepare($conn, $details_query);
mysqli_stmt_bind_param($stmt, 'i', $order_id);
mysqli_stmt_execute($stmt);
$details_result = mysqli_stmt_get_result($stmt);
?>

<head>
    <title>รายละเอียดคำสั่งซื้อ</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;700&family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<style>
        :root {
            /* ลบตัวแปรสีที่ไม่ใช้ */
            --green-dark: #2a6041;
            --green-medium: #5da271;
            --green-light: #eef7f1;
            --green-accent: #b8e0c3;
            --black: #1c2526;
        }

        body {
            background-color: #f7f9f8;
            font-family: 'Poppins', sans-serif;
            margin: 0;
        }

        header {
        color: white;
        text-align: center;
        padding: 2rem 0;
        }

        header h1 {
            font-family: 'Prompt', sans-serif;
            font-size: 2.5rem;
        }

        .order-details {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin: 2rem auto;
            max-width: 850px;
        }

        h2, h3 {
            color: #2a6041;  /* ใช้สีแบบตรงๆ แทนการใช้ตัวแปร */
            font-weight: 600;
        }

        .order-info {
            background: #eef7f1; /* ใช้สีตรงๆ */
            border: 1px solid #b8e0c3;
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .order-info p {
            margin: 0.5rem 0;
            color: #444;
        }

        .order-info a {
            color: #5da271; /* ใช้สีตรงๆ */
            text-decoration: none;
        }

        .order-info a:hover {
            text-decoration: underline;
        }

        .list-group-item {
            background: #eef7f1; /* ใช้สีตรงๆ */
            border: 1px solid #b8e0c3;
            margin-bottom: 0.5rem;
            border-radius: 8px;
        }

        .list-group-item img {
            border-radius: 5px;
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .order-details {
                padding: 1rem;
            }
            header h1 {
                font-size: 1.25rem;
            }
            .list-group-item {
                flex-direction: column;
                text-align: center;
            }
            .list-group-item img {
                margin-bottom: 0.5rem;
            }
        }
    </style>
<body>

    <header class="text-center">
        <h1>รายละเอียดคำสั่งซื้อ</h1>
    </header>

    <main class="container">
        <section class="order-details">
            <h2>รหัสคำสั่งซื้อ: <?php echo htmlspecialchars($order['order_id']); ?></h2>
            <div class="order-info">
                <p><strong>วันที่สั่งซื้อ:</strong> <?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($order['order_date']))); ?></p>
                <p><strong>ยอดรวมสินค้า:</strong> ฿<?php echo number_format($order['total_amount'] - $order['shipping_fee'], 2); ?></p>
                <p><strong>ค่าจัดส่ง:</strong> ฿<?php echo number_format($order['shipping_fee'], 2); ?></p>
                <p><strong>ยอดรวมทั้งหมด:</strong> ฿<?php echo number_format($order['total_amount'], 2); ?></p>
                <p><strong>ที่อยู่จัดส่ง:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                <p><strong>อำเภอ: </strong><?php echo htmlspecialchars($order['amphurName']); ?></p>
                <p><strong>จังหวัด: </strong><?php echo htmlspecialchars($order['provinceName']); ?></p>
                <?php
                $payment_slip = $order['payment_slip'] ?? '';
                $image_path = "./Admin/uploads/" . htmlspecialchars(basename($payment_slip));
                $image_url = file_exists($image_path) && is_readable($image_path) ? $image_path : "./Admin/uploads/default-slip.jpg"; 
                ?>
                <p><strong>สลิปการชำระเงิน:</strong>
                    <a href="#" class="view-payment-slip" data-image="<?php echo htmlspecialchars($image_url, ENT_QUOTES, 'UTF-8'); ?>">
                        <i class="fas fa-file-invoice-dollar"></i> ดูสลิป
                    </a>
                </p>
                <p><strong>สถานะ:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                <?php if ($order['tracking_number'] && $order['status'] === 'กำลังจัดส่ง'): ?>
                    <p><strong>เลขพัสดุ:</strong> <?php echo htmlspecialchars($order['tracking_number']); ?></p>
                <?php endif; ?>
            </div>

            <h3>รายการสินค้า</h3>
            <ul class="list-group">
                <?php while ($detail = mysqli_fetch_assoc($details_result)): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="./Admin/product/<?php echo htmlspecialchars($detail['image']); ?>" alt="<?php echo htmlspecialchars($detail['name']); ?>" width="80" class="me-3">
                            <div>
                                <p class="mb-0"><?php echo htmlspecialchars($detail['name']); ?></p>
                                <small>จำนวน: <?php echo $detail['quantity']; ?> ชิ้น</small>
                            </div>
                        </div>
                        <p class="mb-0">ราคา: ฿<?php echo number_format($detail['price'], 2); ?> - ยอดรวม: ฿<?php echo number_format($detail['quantity'] * $detail['price'], 2); ?></p>
                    </li>
                <?php endwhile; ?>
            </ul>
        </section>
    </main>

    <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">สลิปการชำระเงิน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img class="modal-img" id="img01" src="" alt="Payment Slip" style="width: 100%;">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        var modal = document.getElementById("myModal");
        var links = document.querySelectorAll('.view-payment-slip');
        links.forEach(function(link) {
            link.onclick = function(event) {
                event.preventDefault();
                var imageUrl = this.getAttribute('data-image');
                var modalImg = document.getElementById("img01");
                modalImg.src = imageUrl;
                var modalInstance = new bootstrap.Modal(modal);
                modalInstance.show();
            }
        });
    </script>
</body>
</html>

<?php
mysqli_close($conn);
include 'footer.php';
?>