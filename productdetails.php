<?php
session_start();
include 'connectDB.php';

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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    

</head>
<body>
    <?php
    include 'topnavbar.php';
    ?>
        <header class="bg-dark text-white text-center py-3">    
        <h1>รายละเอียดสินค้า</h1>
    </header>

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
                <p>สต็อก: <?php echo number_format($product_stock, 2); ?>  ชิ้น</p>

                <!-- แสดงรายละเอียดสินค้า -->
                <div class="mb-3">
                    <h5>รายละเอียดสินค้า</h5>
                    <p><?php echo nl2br(htmlspecialchars($product_description)); ?></p> <!-- แสดงรายละเอียดสินค้า -->
                </div>

                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">  <!-- ส่ง product_id -->
                    <input type="hidden" name="price" value="<?php echo $product_price; ?>"> <!-- ส่งราคาสินค้า -->
                    <input type="hidden" name="unit" value="1piece"> <!-- ส่งหน่วยเป็นชิ้น -->

                    <div class="mb-3">
                        <label for="quantity" class="form-label">จำนวน:</label>
                        <div class="input-group">
                            <!-- ปุ่มลดจำนวน -->
                            <button class="btn btn-outline-secondary" type="button" id="decreaseBtn" onclick="updateQuantity(-1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            
                            <!-- ช่องแสดงจำนวน -->
                            <span id="quantityDisplay" class="form-control" style="text-align: center; width: 50px; padding: 0.375rem; font-size: 1rem; display: inline-block;">
                                1
                            </span>
                            
                            <!-- ปุ่มเพิ่มจำนวน -->
                            <button class="btn btn-outline-secondary" type="button" id="increaseBtn" onclick="updateQuantity(1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ส่งค่าจำนวนไปยัง add_to_cart.php -->
                    <input type="hidden" name="quantity" id="quantity" value="1">

                    <script>
                        var quantity = 1;  // จำนวนเริ่มต้น

                        // ฟังก์ชันเพื่ออัปเดตจำนวน
                        function updateQuantity(change) {
                            var newQuantity = quantity + change;

                            // ตรวจสอบไม่ให้เกินค่าสูงสุด (max) หรือค่าน้อยสุด (min)
                            if (newQuantity >= 1 && newQuantity <= <?php echo $product_stock; ?>) {
                                quantity = newQuantity;
                                document.getElementById('quantityDisplay').innerText = quantity;
                                document.getElementById('quantity').value = quantity; // ส่งค่าไปใน hidden field
                            }
                        }
                    </script>

                    <button type="submit" class="btn btn-primary">เพิ่มในตะกร้า</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
    <div class="cart-icon fixed-bottom mb-4 ms-4">
        <a href="cart.php" class="btn">
            <i class="fas fa-shopping-cart"></i>
            <?php
            if (isset($_SESSION['cart_id'])) {
                $cart_id = $_SESSION['cart_id'];
                $query = "SELECT COUNT(*) AS item_count FROM cart_items WHERE cart_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'i', $cart_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if ($row = mysqli_fetch_assoc($result)) {
                    echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">' . $row['item_count'] . '</span>';
                }
            }
            ?>
        </a>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
include 'footer.php';
?>
