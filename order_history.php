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

// ดึงประวัติการสั่งซื้อของลูกค้าจากฐานข้อมูล
$order_query =
    "SELECT order_id, order_date, total_amount, orders.status
    FROM orders
    WHERE customer_id = ?
    ORDER BY order_date DESC
";
$stmt = mysqli_prepare($conn, $order_query);
mysqli_stmt_bind_param($stmt, 'i', $customer_id);
mysqli_stmt_execute($stmt);
$order_result = mysqli_stmt_get_result($stmt);

// ตรวจสอบว่าไม่พบข้อมูลการสั่งซื้อ
$no_order = mysqli_num_rows($order_result) === 0;
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>ประวัติการสั่งซื้อ</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS Links -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;700&family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- JavaScript Links -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</head>
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

        /* Main Container */
        .container {
            margin-top: 2rem;
        }

        /* Sidebar */
        .sidebar {
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-left: -70px; /* ชิดซ้ายสุด */
            margin-bottom: 24px;
            padding-left: 0;
            max-width: 250px; /* ขนาด sidebar ไม่เล็กหรือใหญ่เกินไป */
        }

        .sidebar h5 {
            color: #28a745;
            margin-bottom: 15px;
            margin-left: 15px;
        }

        #category-select {
            width: 90%;
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #28a745;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s, box-shadow 0.3s;
            margin-left: 15px;
        }

        #category-select:hover, #category-select:focus {
            border-color: #218838;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            outline: none;
        }

        /* Product List */
        .product-list .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .product-list .card:hover {
            transform: translateY(-5px);
        }

        .card-img-top {
            height: 200px;
            object-fit: contain;
            padding: 15px;
            background-color: #fff;
        }

        .card-body {
            padding: 15px;
            text-align: center;
        }

        .card-title {
            font-family: 'Prompt', sans-serif;
            font-size: 1.1rem;
            color: #343a40;
            margin-bottom: 10px;
        }

        .card-text {
            color: #28a745;
            margin-bottom: 15px;
        }

        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            border-radius: 8px;
            padding: 8px 20px;
            transition: transform 0.3s, background-color 0.3s;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            background-color: #218838;
            border-color: #218838;
        }

        /* Cart Icon */
        .cart-icon {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
        }

        .cart-icon .btn {
            background-color: #28a745;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
            position: relative;
        }

        .cart-icon .btn:hover {
            transform: scale(1.1);
        }

        .cart-icon .fa-shopping-cart {
            font-size: 1.5rem;
            color: white;
        }

        .cart-icon .badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: #dc3545;
            border-radius: 50%;
            padding: 5px 8px;
            font-size: 0.8rem;
            color: white;
        }

        /* Responsive */
        @media (max-width: 767px) {
            .sidebar {
                margin-bottom: 20px;
                padding-left: 15px;
                max-width: 100%; /* ปรับให้เต็มความกว้างบนมือถือ */
                margin-left:0;
            }

            #category-select {
                margin-left: 0;
            }

            header h1 {
                font-size: 2rem;
            }

            .card-img-top {
                height: 150px;
            }
        }
    </style>
<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>ประวัติการสั่งซื้อ</h1>
    </header>
    <main>
        <section>
            <div class="container mt-4">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>รหัสคำสั่งซื้อ</th>
                            <th>วันที่สั่งซื้อ</th>
                            <th>สถานะ</th>
                            <th>ยอดรวมทั้งหมด</th>
                            <th>รายละเอียด</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$no_order): ?>
                            <?php while ($order = mysqli_fetch_assoc($order_result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                    <td><?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($order['order_date']))); ?></td>
                                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                                    <td>฿<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <a href="order_details.php?order_id=<?php echo htmlspecialchars($order['order_id']); ?>" class="btn btn-primary">
                                            ดูรายละเอียด
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">ไม่มีประวัติการสั่งซื้อ</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Modal สำหรับแจ้งเตือน -->
    <div class="modal fade" id="noOrderModal" tabindex="-1" aria-labelledby="noOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="noOrderModalLabel">แจ้งเตือน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>ไม่มีคำสั่งซื้อในระบบ</p>
                    <a href="product.php" class="btn btn-primary">ไปที่หน้าสินค้า</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    <?php if ($no_order): ?>
        // แสดง Modal เมื่อไม่มีคำสั่งซื้อ
        var noOrderModal = new bootstrap.Modal(document.getElementById('noOrderModal'));
        noOrderModal.show();
    <?php endif; ?>
    </script>
</body>
</html>

<?php
mysqli_close($conn);
include 'footer.php';
?>
