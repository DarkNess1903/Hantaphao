<?php
session_start();
include 'connectDB.php';
include 'topnavbar.php';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>หน้าสินค้า - เว็บไซต์ขายเนื้อ</title>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS Links -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

    <!-- JavaScript Links -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</head>
<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>สินค้า</h1>
    </header>
    
    <main class="container mt-4">
    <section class="row">
    <?php
    // Query to retrieve product data without weight_per_item and price_per_piece
    $query = "SELECT * FROM product";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $product_id = $row['product_id'];
            $stock_kg = $row['stock_quantity']; // Stock in kilograms

            echo '<div class="col-lg-3 col-md-4 col-sm-6 mb-4">';
            echo '<div class="card h-100 text-center">';
            
            // คลิกที่รูปจะไปที่หน้ารายละเอียดสินค้า
            echo '<a href="productdetails.php?product_id=' . $product_id . '">
                    <img src="./Admin/product/' . htmlspecialchars($row['image']) . '" class="card-img-top" alt="' . htmlspecialchars($row['name']) . '" style="height: 200px; width: 100%; object-fit: cover;">
                  </a>';
            
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>';
            echo '<p class="card-text">ราคา: ' . number_format($row['price'], 2) . '฿</p>';
            echo '<p class="card-text">สต็อก: ' . number_format($stock_kg, 2) . ' </p>';  // Show stock in kilograms

            $product_id = $row['product_id'];
            // ฟอร์มสำหรับการเพิ่มสินค้าไปตะกร้า (ไม่ต้องกรอกจำนวน)
            echo '<form action="add_to_cart.php" method="POST" id="addToCartForm' . $product_id . '">';
            echo '<input type="hidden" name="product_id" value="' . $product_id . '">';
            echo '<input type="hidden" name="price" value="' . $row['price'] . '">'; // ส่งราคาสินค้า
            echo '<input type="hidden" name="unit" value="1piece">'; // ส่งหน่วยเป็นชิ้น
            echo '<input type="hidden" name="quantity" value="1">'; // จำนวนเป็น 1 ชิ้นตลอด
            echo '<button type="submit" class="btn btn-primary">เพิ่มในตะกร้า</button>';
            echo '</form>';

            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p class="text-center">ไม่พบสินค้า</p>';
    }
?>
</section>
</main>
       
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
<?php
mysqli_close($conn);
include 'footer.php';
?>
