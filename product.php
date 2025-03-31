<?php
session_start();
include 'connectDB.php';
include 'topnavbar.php';

// รับ category จาก URL หรือ default เป็น 0 (ทั้งหมด)
$selected_category = isset($_GET['category']) ? intval($_GET['category']) : 0;
?>


<head>
    <title>สินค้า</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;700&family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
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
</head>
<body>
    <header class="bg-dark text-white text-center py-3">
        <h1>สินค้า</h1>
    </header>

    <main class="container">
        <div class="row">
            <!-- Sidebar -->
            <aside class="col-md-3 sidebar">
                <h5>หมวดหมู่สินค้า</h5>
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

            <!-- Product List -->
            <section class="col-md-9 product-list">
                <?php
                if (isset($_GET['message'])) {
                    $message = htmlspecialchars($_GET['message']);
                    echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                    echo $message;
                    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                    echo '</div>';
                }
                ?>
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
                            echo '<div class="card h-100">';
                            echo '<a href="productdetails.php?product_id=' . $row['product_id'] . '">
                                    <img src="./Admin/product/' . htmlspecialchars($row['image']) . '" class="card-img-top" alt="' . htmlspecialchars($row['name']) . '">
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
                        echo '<div class="col-12"><p class="text-center text-muted">ไม่พบสินค้า</p></div>';
                    }
                    ?>
                </div>
            </section>
        </div>
    </main>

    <!-- Cart Icon -->
    <div class="cart-icon">
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
                    echo '<span class="badge">' . $row['item_count'] . '</span>';
                }
            }
            ?>
        </a>
    </div>

    <!-- Scripts -->
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
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>

<?php
mysqli_close($conn);
include 'footer.php';
?>