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

<head>
    <title>ประวัติการสั่งซื้อ</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;700&family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<style>
    body {
        font-family: 'Sarabun', sans-serif;
        background-color: #f8f9fa;
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

    .table {
        margin-top: 2rem;
    }

    .btn-primary {
        background-color: #28a745;
        border-color: #28a745;
    }

    .modal-content {
        text-align: center;
    }

    @media (max-width: 767px) {
        header h1 {
            font-size: 2rem;
        }
    }
</style>

<body>
    <header>
        <h1>ประวัติการสั่งซื้อ</h1>
    </header>

<main>
    <section>
        <div class="container mt-4">
            <!-- ทำให้ตาราง responsive -->
            <div class="table-responsive">
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
                <div class="modal-body">
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
