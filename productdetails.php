<?php
session_start();
include 'connectDB.php';
include 'topnavbar.php';

    // รับ product_id จาก URL
    if (isset($_GET['product_id'])) {
        $product_id = $_GET['product_id'];

        // Query เพื่อดึงข้อมูลสินค้าจากฐานข้อมูล
        $query = "SELECT * FROM product WHERE product_id = $product_id";
        $result = mysqli_query($conn, $query);

        // ตรวจสอบว่าเจอข้อมูลสินค้าหรือไม่
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            $product_name = $row['name'];
            $product_price = $row['price'];
            $product_stock = $row['stock_quantity'];
            $product_image = $row['image'];
            $product_description = $row['product_description']; // ดึงรายละเอียดสินค้า
        } else {
            echo '<p class="text-center">ไม่พบสินค้า</p>';
            exit;
        }
    } else {
        echo '<p class="text-center">ไม่มีข้อมูลสินค้า</p>';
        exit;
    }
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5">
        <div class="row">
            <!-- แสดงรูปรายละเอียดสินค้า -->
            <div class="col-md-6">
                <img src="./Admin/product/<?php echo htmlspecialchars($product_image); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product_name); ?>">
            </div>

            <!-- แสดงข้อมูลสินค้า -->
            <div class="col-md-6">
                <h2><?php echo htmlspecialchars($product_name); ?></h2>
                <p class="h4">ราคา: ฿<?php echo number_format($product_price, 2); ?></p>
                <p>สต็อก: <?php echo number_format($product_stock, 2); ?> ชิ้น</p>

                <!-- แสดงรายละเอียดสินค้า -->
                <div class="mb-3">
                    <h5>รายละเอียดสินค้า</h5>
                    <p><?php echo nl2br(htmlspecialchars($product_description)); ?></p> <!-- แสดงรายละเอียดสินค้า -->
                </div>

                <form action="add_to_cart.php" method="GET">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <input type="hidden" name="price" value="<?php echo $product_price; ?>"> <!-- ส่งราคาสินค้า -->
                    <input type="hidden" name="unit" value="1piece"> <!-- ส่งหน่วยเป็นชิ้น -->

                    <div class="mb-3">
                        <label for="quantity" class="form-label">จำนวน:</label>
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product_stock; ?>" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">เพิ่มในตะกร้า</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
mysqli_close($conn);
include 'footer.php';
?>
