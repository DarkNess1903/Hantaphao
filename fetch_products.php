<?php
session_start();
include 'connectDB.php';

$category_id = isset($_POST['category']) ? intval($_POST['category']) : 0;

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($category_id == 0) {
    $query = "SELECT * FROM product";
} else {
    $query = "SELECT * FROM product WHERE category_id = ?";
}

$stmt = mysqli_prepare($conn, $query);
if ($category_id != 0) {
    mysqli_stmt_bind_param($stmt, 'i', $category_id);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$output = '';

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $output .= '<div class="col-lg-4 col-md-6 col-sm-6 mb-4">';
        $output .= '<div class="card h-100 text-center">';
        $output .= '<a href="productdetails.php?product_id=' . $row['product_id'] . '">
                        <img src="./Admin/product/' . htmlspecialchars($row['image']) . '" class="card-img-top" style="height: 200px; object-fit: contain;">
                    </a>';
        $output .= '<div class="card-body">';
        $output .= '<h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>';
        $output .= '<p class="card-text">ราคา: ' . number_format($row['price'], 2) . '฿</p>';
        $output .= '<form action="add_to_cart.php" method="POST">';
        $output .= '<input type="hidden" name="product_id" value="' . $row['product_id'] . '">';
        $output .= '<input type="hidden" name="price" value="' . $row['price'] . '">';
        $output .= '<input type="hidden" name="quantity" value="1">';
        $output .= '<input type="hidden" name="category_id" value="' . $category_id . '">';
        $output .= '<button type="submit" class="btn btn-primary" onclick="this.disabled=true; this.form.submit();">เพิ่มในตะกร้า</button>';
        $output .= '</form>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
    }
} else {
    $output .= '<p class="text-center">ไม่พบสินค้าในหมวดหมู่นี้</p>';
}

echo $output;

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>