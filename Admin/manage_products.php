<?php 
include 'topnavbar.php';
include 'connectDB.php';

// ฟังก์ชันคำนวณกำไร
function calculateProfit($price, $cost) {
    return $price - $cost; // กำไรต่อชิ้น = ราคาขาย - ต้นทุน
}
if (isset($_POST['add_product'])) {
    $name = $_POST['product_name'] ?? '';
    $price = $_POST['price'] ?? 0.0;
    $cost = $_POST['cost'] ?? 0.0;
    $stock = $_POST['stock'] ?? 0;
    $product_description = $_POST['product_description'] ?? '';
    $category_id = $_POST['category_id'] ?? 0;  // รับค่าหมวดหมู่จากฟอร์ม
    $weight = $_POST['weight'] ?? 0.0;  // รับค่าน้ำหนัก
    $shipping_cost = $_POST['shipping_cost'] ?? 0.0;  // รับค่าส่ง

    if (empty($name)) {
        echo "ชื่อสินค้าต้องไม่ว่าง";
        exit;
    }

    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "product/" . $image);
    }

    // เพิ่มข้อมูลสินค้าในฐานข้อมูล
    $stmt = $conn->prepare("INSERT INTO product (name, price, cost, stock_quantity, product_description, image, category_id, weight, shipping_cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssdissdss', $name, $price, $cost, $stock, $product_description, $image, $category_id, $weight, $shipping_cost);
    
    if ($stmt->execute()) {
        echo "<script>alert('เพิ่มสินค้าสำเร็จ');</script>";
        echo "<script>window.location.href='manage_products.php';</script>";
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }
}

// เมื่อคลิกที่ปุ่มแก้ไข, รับ product_id
if (isset($_GET['edit_product_id'])) {
    $product_id = $_GET['edit_product_id'];

    // ดึงข้อมูลสินค้าจากฐานข้อมูล
    $sql = "SELECT * FROM product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // เก็บข้อมูลในตัวแปร
        $name = $row['name'];
        $price = $row['price'];
        $cost = $row['cost'];
        $stock = $row['stock_quantity'];
        $product_description = $row['product_description'];
        $image = $row['image'];  // ใช้ค่าภาพเดิมจากฐานข้อมูล
    }
}

if (isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'] ?? '';
    $name = $_POST['product_name'] ?? '';
    $price = $_POST['price'] ?? 0.0;
    $cost = $_POST['cost'] ?? 0.0;
    $stock = $_POST['stock'] ?? 0;
    $product_description = $_POST['product_description'] ?? '';
    $category_id = $_POST['category_id'] ?? 0;
    $weight = $_POST['weight'] ?? 0.0;
    $shipping_cost = $_POST['shipping_cost'] ?? 0.0;

    if (empty($name)) {
        echo "ชื่อสินค้าต้องไม่ว่าง";
        exit;
    }

    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "product/" . $image);
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    $stmt = $conn->prepare("UPDATE product SET name=?, price=?, cost=?, stock_quantity=?, product_description=?, image=?, category_id=?, weight=?, shipping_cost=? WHERE product_id=?");
    $stmt->bind_param('ssdissdssi', $name, $price, $cost, $stock, $product_description, $image, $category_id, $weight, $shipping_cost, $product_id);

    if ($stmt->execute()) {
        echo "<script>alert('อัพเดทสินค้าสำเร็จ');</script>";
        echo "<script>window.location.href='manage_products.php';</script>";
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }
}

// การเติมสต็อกสินค้า
if (isset($_POST['restock_product'])) {
    $product_id = $_POST['product_id'];
    $additional_stock = $_POST['quantity'];

    if (is_numeric($additional_stock) && $additional_stock > 0) {
        $sql = "UPDATE product SET stock_quantity = stock_quantity + ? WHERE product_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $additional_stock, $product_id);

        if ($stmt->execute()) {
            echo "<script>alert('เติมสต็อกสำเร็จ');</script>";
            echo "<script>window.location.href='manage_products.php';</script>";
        } else {
            echo "เกิดข้อผิดพลาด: " . $stmt->error;
        }
    } else {
        echo "จำนวนที่เติมต้องเป็นตัวเลขที่มากกว่าศูนย์";
    }
}
// รัน SQL Query
$sql = "SELECT p.product_id, p.name, p.price, p.cost, p.stock_quantity, p.image, p.product_description, p.weight, p.shipping_cost, c.category_id, c.category_name
        FROM product p
        LEFT JOIN category c ON p.category_id = c.category_id";
$result = mysqli_query($conn, $sql);
$conn->close();

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>จัดการสินค้า</title>
    <style>
        .modal-body img {
            max-width: 100%;
            height: auto;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body>
<!-- โมดัลฟอร์มเพิ่มสินค้า -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">เพิ่มสินค้า</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="manage_products.php" method="post" enctype="multipart/form-data">
                    <!-- ชื่อสินค้า -->
                    <div class="form-group">
                        <label for="product_name">ชื่อสินค้า:</label>
                        <input type="text" id="product_name" name="product_name" class="form-control" required>
                    </div>
                    
                    <!-- หมวดหมู่สินค้า -->
                    <div class="form-group">
                        <label for="category_id">หมวดหมู่สินค้า:</label>
                        <select id="category_id" name="category_id" class="form-control" required>
                            <option value="">เลือกหมวดหมู่</option>
                            <?php
                            include 'connectDB.php';
                            $categoryQuery = "SELECT * FROM category";
                            $categoryResult = mysqli_query($conn, $categoryQuery);

                            while ($category = mysqli_fetch_assoc($categoryResult)) {
                                echo "<option value='{$category['category_id']}'>" . htmlspecialchars($category['category_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- ราคา -->
                    <div class="form-group">
                        <label for="price">ราคาขาย:</label>
                        <input type="number" id="price" name="price" class="form-control" step="0.01" required>
                    </div>
                    
                    <!-- ต้นทุน -->
                    <div class="form-group">
                        <label for="cost">ต้นทุน:</label>
                        <input type="number" id="cost" name="cost" class="form-control" step="0.01" required>
                    </div>
                    
                    <!-- สต็อก -->
                    <div class="form-group">
                        <label for="stock">สต็อก:</label>
                        <input type="number" id="stock" name="stock" class="form-control" required>
                    </div>

                    <!-- น้ำหนักสินค้า -->
                    <div class="form-group">
                        <label for="weight">น้ำหนักสินค้า (กิโลกรัม):</label>
                        <input type="number" id="weight" name="weight" class="form-control" step="0.01" required>
                    </div>

                    <!-- ค่าส่งสินค้า -->
                    <div class="form-group">
                        <label for="shipping_cost">ค่าส่งสินค้า (บาท):</label>
                        <input type="number" id="shipping_cost" name="shipping_cost" class="form-control" step="0.01" required>
                    </div>

                    <!-- รายละเอียดสินค้า -->
                    <div class="form-group">
                        <label for="product_description">รายละเอียดสินค้า:</label>
                        <textarea id="product_description" name="product_description" class="form-control"></textarea>
                    </div>

                    <!-- รูปภาพสินค้า -->
                    <div class="form-group">
                        <label for="image">รูปภาพสินค้า:</label>
                        <input type="file" id="image" name="image" class="form-control">
                    </div>

                    <button type="submit" name="add_product" class="btn btn-primary">เพิ่มสินค้า</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- โมดัลฟอร์มแก้ไขสินค้า -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">แก้ไขสินค้า</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editProductForm" action="manage_products.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    
                    <!-- ชื่อสินค้า -->
                    <div class="form-group">
                        <label for="edit_product_name">ชื่อสินค้า:</label>
                        <input type="text" id="edit_product_name" name="product_name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>

                    <!-- หมวดหมู่สินค้า -->
                    <div class="form-group">
                        <label for="edit_category">หมวดหมู่สินค้า:</label>
                        <select id="edit_category" name="category_id" class="form-control">
                            <?php
                            // ดึงรายการหมวดหมู่ทั้งหมดจากฐานข้อมูล
                            $categorySql = "SELECT * FROM category";
                            $categoryResult = mysqli_query($conn, $categorySql);
                            
                            while ($category = mysqli_fetch_assoc($categoryResult)) {
                                // กำหนดให้หมวดหมู่ที่ตรงกับสินค้าที่กำลังแก้ไขถูกเลือก
                                $selected = $category['category_id'] == $category_id ? 'selected' : '';
                                echo "<option value=\"{$category['category_id']}\" {$selected}>{$category['category_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- ราคา -->
                    <div class="form-group">
                        <label for="edit_price">ราคา:</label>
                        <input type="number" id="edit_price" name="price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($price); ?>" required>
                    </div>
                    
                    <!-- ต้นทุน -->
                    <div class="form-group">
                        <label for="edit_cost">ต้นทุน:</label>
                        <input type="number" id="edit_cost" name="cost" class="form-control" step="0.01" value="<?php echo htmlspecialchars($cost); ?>" required>
                    </div>
                    
                    <!-- สต็อก -->
                    <div class="form-group">
                        <label for="edit_stock">สต็อก:</label>
                        <input type="number" id="edit_stock" name="stock" class="form-control" value="<?php echo htmlspecialchars($stock); ?>" required>
                    </div>

                    <!-- น้ำหนักสินค้า -->
                    <div class="form-group">
                        <label for="edit_weight">น้ำหนักสินค้า (กิโลกรัม):</label>
                        <input type="number" id="edit_weight" name="weight" class="form-control" step="0.01" value="<?php echo htmlspecialchars($weight); ?>" required>
                    </div>

                    <!-- ค่าส่งสินค้า -->
                    <div class="form-group">
                        <label for="edit_shipping_cost">ค่าส่งสินค้า (บาท):</label>
                        <input type="number" id="edit_shipping_cost" name="shipping_cost" class="form-control" step="0.01" value="<?php echo htmlspecialchars($shipping_cost); ?>" required>
                    </div>

                    <!-- รายละเอียดสินค้า -->
                    <div class="form-group">
                        <label for="edit_product_description">รายละเอียดสินค้า:</label>
                        <textarea id="edit_product_description" name="product_description" class="form-control"><?php echo htmlspecialchars($product_description); ?></textarea>
                    </div>

                    <!-- รูปภาพสินค้า -->
                    <div class="form-group">
                        <label for="edit_image">รูปภาพสินค้า:</label>
                        <input type="file" id="edit_image" name="image" class="form-control">
                    </div>
                    
                    <button type="submit" name="edit_product" class="btn btn-primary">อัพเดทสินค้า</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับเติมสต็อก -->
<div class="modal fade" id="restockModal" tabindex="-1" role="dialog" aria-labelledby="restockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="restockModalLabel">เติมสต็อกสินค้า</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="restockForm">
                    <input type="hidden" id="restock_product_id" name="product_id">
                    <div class="form-group">
                        <label for="restock_quantity">จำนวนที่เติม:</label>
                        <input type="number" class="form-control" id="restock_quantity" name="quantity" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-primary">เติมสต็อก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- ตารางสินค้า -->
<div class="container mt-4">
    <h1 class="text-center mb-4">จัดการสินค้า</h1>

    <!-- ปุ่มเพิ่มสินค้า -->
    <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#addProductModal">
        เพิ่มสินค้า
    </button>

    <!-- ตารางสินค้า -->
    <div class="table-responsive mt-4">
        <table class="table table-striped table-bordered text-center">
            <thead>
                <tr>
                    <th>สินค้า</th>
                    <th>ราคา</th>
                    <th>ต้นทุน</th>
                    <th>สต็อก</th>
                    <th>กำไร</th>
                    <th>หมวดหมู่</th>
                    <th>น้ำหนักสินค้า</th>
                    <th>ค่าส่งสินค้า</th>
                    <th>รูปภาพสินค้า</th>
                    <th>รายละเอียดสินค้า</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { 
                // คำนวณกำไรจากราคาขายและต้นทุน
                $profit_per_piece = $row['price'] - $row['cost'];
                $total_profit = $profit_per_piece * $row['stock_quantity']; // คำนวณจากจำนวนสินค้าตามน้ำหนัก
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo number_format($row['price'], 2); ?> ฿</td>
                    <td><?php echo number_format($row['cost'], 2); ?> ฿</td>
                    <td><?php echo $row['stock_quantity']; ?> </td>
                    <td><?php echo number_format($profit_per_piece, 2); ?> ฿</td>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td> <!-- แสดงหมวดหมู่ -->
                    <td><?php echo number_format($row['weight'], 2); ?> กิโลกรัม</td> <!-- แสดงน้ำหนักสินค้า -->
                    <td><?php echo number_format($row['shipping_cost'], 2); ?> ฿</td> <!-- แสดงค่าส่งสินค้า -->
                    <td>
                        <img src="product/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" style="width: 100px; height: auto;">
                    </td>
                    <td><?php echo htmlspecialchars($row['product_description']); ?></td>
                    <td>
    <div class="btn-group-vertical" role="group">
        <!-- ปุ่มแก้ไข -->
        <button class="btn btn-warning btn-sm editBtn" data-toggle="modal" data-target="#editProductModal"
            data-id="<?php echo $row['product_id']; ?>"
            data-name="<?php echo htmlspecialchars($row['name']); ?>"
            data-price="<?php echo htmlspecialchars($row['price']); ?>"
            data-cost="<?php echo htmlspecialchars($row['cost']); ?>"
            data-stock="<?php echo htmlspecialchars($row['stock_quantity']); ?>"
            data-description="<?php echo htmlspecialchars($row['product_description']); ?>"
            data-category="<?php echo htmlspecialchars($row['category_name']); ?>"
            data-weight="<?php echo htmlspecialchars($row['weight']); ?>"
            data-shipping-cost="<?php echo htmlspecialchars($row['shipping_cost']); ?>">
            <i class="fas fa-edit"></i>
        </button>

        <!-- ปุ่มเติมสต็อก -->
        <button class="btn btn-info btn-sm restockBtn" data-toggle="modal" data-target="#restockModal" data-id="<?php echo $row['product_id']; ?>">
            <i class="fas fa-box-open"></i>
        </button>

        <!-- ปุ่มลบ -->
        <a href="manage_products.php?delete=<?php echo $row['product_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้า?');">
            <i class="fas fa-trash-alt"></i>
        </a>
    </div>
</td>

                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // เมื่อคลิกปุ่มแก้ไขสินค้า
    $(document).on('click', '.editBtn', function() {
        const productId = $(this).data('id');
        const productName = $(this).data('name');
        const price = $(this).data('price');
        const cost = $(this).data('cost');
        const stock = $(this).data('stock');
        const description = $(this).data('description');
        const categoryId = $(this).data('category-id'); // เปลี่ยนชื่อเป็น category-id เพื่อให้ตรงกับข้อมูลที่ใช้
        const weight = $(this).data('weight'); // กำหนดน้ำหนักสินค้าหากมีข้อมูลนี้
        const shippingCost = $(this).data('shipping-cost'); // กำหนดค่าส่งสินค้า

        // กำหนดค่าให้กับฟิลด์ในฟอร์มแก้ไข
        $('#edit_product_id').val(productId); // hidden field สำหรับ product_id
        $('#edit_product_name').val(productName);
        $('#edit_price').val(price);
        $('#edit_cost').val(cost);
        $('#edit_stock').val(stock);
        $('#edit_product_description').val(description);
        $('#edit_category').val(categoryId); // กำหนดค่าหมวดหมู่ในฟอร์ม
        $('#edit_weight').val(weight); // กำหนดค่าน้ำหนัก
        $('#edit_shipping_cost').val(shippingCost); // กำหนดค่าส่ง

        // แสดง Modal แก้ไขสินค้า
        $('#editProductModal').modal('show');
    });

    // เมื่อคลิกปุ่มเติมสต็อก
    $(document).on('click', '.restockBtn', function() {
        const productId = $(this).data('id');
        $('#restock_product_id').val(productId); // กำหนดค่า product_id ในฟอร์มเติมสต็อก
        $('#restockModal').modal('show'); // แสดง Modal
    });

    // เมื่อส่งฟอร์มเติมสต็อก
    $('#restockForm').on('submit', function(e) {
        e.preventDefault(); // ป้องกันการส่งฟอร์มแบบปกติ
        const productId = $('#restock_product_id').val();
        const quantity = $('#restock_quantity').val();

        // ส่งข้อมูลไปยังเซิร์ฟเวอร์
        $.ajax({
            url: 'manage_products.php', // เปลี่ยนให้ตรงกับ URL ที่ต้องการส่งข้อมูล
            method: 'POST',
            data: { restock_product: true, product_id: productId, quantity: quantity },
            success: function(response) {
                alert('เติมสต็อกสำเร็จ'); // แจ้งเตือนเมื่อเติมสต็อกสำเร็จ
                $('#restockModal').modal('hide'); // ปิด Modal หลังเติมสต็อกเสร็จ
                location.reload(); // โหลดหน้าใหม่เพื่อดูข้อมูลที่อัปเดต
            },
            error: function(xhr, status, error) {
                alert('เกิดข้อผิดพลาด: ' + error); // แจ้งเตือนเมื่อเกิดข้อผิดพลาด
            }
        });
    });

    // เมื่อคลิกปุ่มลบสินค้า
    $(document).on('click', '.deleteBtn', function(e) {
        e.preventDefault(); // ป้องกันการเปลี่ยนหน้า
        const productId = $(this).data('id');
        if (confirm('คุณแน่ใจว่าจะลบสินค้านี้?')) {
            $.ajax({
                url: 'manage_products.php',
                method: 'POST',
                data: { delete_product: true, product_id: productId },
                success: function(response) {
                    location.reload(); // โหลดหน้าใหม่เพื่อตรวจสอบการลบ
                },
                error: function(xhr, status, error) {
                    alert('เกิดข้อผิดพลาด: ' + error);
                }
            });
        }
    });
</script>
</body>
</html>