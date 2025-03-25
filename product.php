<?php
session_start();
include 'connectDB.php';
include 'topnavbar.php';

// รับ category จาก URL หรือ default เป็น 0 (ทั้งหมด)
$selected_category = isset($_GET['category']) ? intval($_GET['category']) : 0;
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>สินค้า</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</head>
<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>สินค้า</h1>
    </header>
    
    <main class="container mt-4">
        <?php
        if (isset($_GET['message'])) {
            $message = htmlspecialchars($_GET['message']);
            echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
            echo $message;
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }
        ?>
        <div class="row">
            <aside class="col-lg-3 col-md-4">
                <h5 class="mb-3">หมวดหมู่สินค้า</h5>
                <select id="category-select" class="form-control">
                    <option value="0" <?php echo $selected_category == 0 ? 'selected' : ''; ?>>ทั้งหมด</option>
                    <?php
                    $categoryQuery = "SELECT * FROM category";
                    $categoryResult = mysqli_query($conn, $categoryQuery);
                    while ($category = mysqli_fetch_assoc($categoryResult)) {
                        $selected = $selected_category == $category['category_id'] ? 'selected' : '';
                        echo '<option value="' . $category['category_id'] . '" ' . $selected . '>' . htmlspecialchars($category['category_name']) . '</option>';
                    }
                    ?>
                </select>
            </aside>
            <section class="col-lg-9 col-md-8">
                <div class="row" id="product-list">
                    <?php
                    if ($selected_category == 0) {
                        $query = "SELECT * FROM product";
                    } else {
                        $query = "SELECT * FROM product WHERE category_id = ?";
                    }
                    $stmt = mysqli_prepare($conn, $query);
                    if ($selected_category != 0) {
                        mysqli_stmt_bind_param($stmt, 'i', $selected_category);
                    }
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<div class="col-lg-4 col-md-6 col-sm-6 mb-4">';
                            echo '<div class="card h-100 text-center">';
                            echo '<a href="productdetails.php?product_id=' . $row['product_id'] . '">
                                    <img src="./Admin/product/' . htmlspecialchars($row['image']) . '" class="card-img-top" style="height: 200px; object-fit: contain;">
                                  </a>';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>';
                            echo '<p class="card-text">ราคา: ' . number_format($row['price'], 2) . '฿</p>';
                            echo '<form action="add_to_cart.php" method="POST">';
                            echo '<input type="hidden" name="product_id" value="' . $row['product_id'] . '">';
                            echo '<input type="hidden" name="price" value="' . $row['price'] . '">';
                            echo '<input type="hidden" name="quantity" value="1">';
                            echo '<input type="hidden" name="category_id" value="' . $selected_category . '">';
                            echo '<button type="submit" class="btn btn-primary" onclick="this.disabled=true; this.form.submit();">เพิ่มในตะกร้า</button>';
                            echo '</form>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p class="text-center">ไม่พบสินค้า</p>';
                    }
                    ?>
                </div>
            </section>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#category-select').change(function () {
                var selectedCategory = $(this).val();
                $.ajax({
                    url: 'fetch_products.php',
                    method: 'POST',
                    data: { category: selectedCategory },
                    success: function (response) {
                        $('#product-list').html(response);
                    }
                });
            });
        });
    </script>

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