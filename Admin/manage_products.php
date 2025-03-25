<?php 
include 'topnavbar.php';
include 'connectDB.php';

// ฟังก์ชันคำนวณกำไร
function calculateProfit($price, $cost) {
    return $price - $cost; // กำไรต่อชิ้น = ราคาขาย - ต้นทุน
}

// การเพิ่มสินค้าใหม่
if (isset($_POST['add_product'])) {
    $name = $_POST['product_name'] ?? '';
    $price = $_POST['price'] ?? 0.0;
    $cost = $_POST['cost'] ?? 0.0;
    $stock = $_POST['stock'] ?? 0;
    $product_description = $_POST['product_description'] ?? ''; // รายละเอียดสินค้า

    if (empty($name)) {
        echo "ชื่อสินค้าต้องไม่ว่าง";
        exit; // หยุดการทำงานของสคริปต์
    }

    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "product/" . $image);
    }

    // ใช้ Prepared Statements
    $stmt = $conn->prepare("INSERT INTO product (name, price, cost, stock_quantity, product_description, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssdiss', $name, $price, $cost, $stock, $product_description, $image);
    
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

// เมื่อส่งฟอร์มมา, ทำการอัพเดตข้อมูล
if (isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['product_name'];
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;
    $cost = isset($_POST['cost']) ? floatval($_POST['cost']) : 0.0;
    $stock = $_POST['stock'];
    $product_description = $_POST['product_description'];

    // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
    if (!empty($_FILES['image']['name'])) {
        // ถ้ามีการอัพโหลดรูปภาพใหม่
        $image = $_FILES['image']['name'];
        $target_path = "product/" . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_path);
    } else {
        // ใช้รูปภาพเดิมจากฐานข้อมูล
        // ตรวจสอบก่อนว่า $image ถูกกำหนดค่าแล้วหรือยัง
        if (isset($image)) {
            $image = $row['image'];
        }
    }

    // SQL สำหรับอัพเดตข้อมูล
    if (!empty($image)) {
        $sql = "UPDATE product SET name=?, price=?, cost=?, stock_quantity=?, product_description=?, image=? WHERE product_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssdisss', $name, $price, $cost, $stock, $product_description, $image, $product_id);
    } else {
        $sql = "UPDATE product SET name=?, price=?, cost=?, stock_quantity=?, product_description=? WHERE product_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssdiss', $name, $price, $cost, $stock, $product_description, $product_id);
    }

    // Execute the update
    if ($stmt->execute()) {
        echo "<script>alert('แก้ไขสินค้าสำเร็จ');</script>";
        echo "<script>window.location.href='manage_products.php';</script>";
        exit();  // หยุดการทำงานของโปรแกรมหลังจาก redirect
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
$sql = "SELECT product_id, name, price, cost, stock_quantity, image, product_description FROM product";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

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
                    <div class="form-group">
                        <label for="product_name">ชื่อสินค้า:</label>
                        <input type="text" id="product_name" name="product_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="price">ราคาขาย ():</label>
                        <input type="number" id="price" name="price" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="cost">ต้นทุน:</label>
                        <input type="number" id="cost" name="cost" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">สต็อก:</label>
                        <input type="number" id="stock" name="stock" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="product_description">รายละเอียดสินค้า:</label>
                        <textarea id="product_description" name="product_description" class="form-control"></textarea>
                    </div>
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
                    
                    <div class="form-group">
                        <label for="edit_product_name">ชื่อสินค้า:</label>
                        <input type="text" id="edit_product_name" name="product_name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_price">ราคา:</label>
                        <input type="number" id="edit_price" name="price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($price); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_cost">ต้นทุน:</label>
                        <input type="number" id="edit_cost" name="cost" class="form-control" step="0.01" value="<?php echo htmlspecialchars($cost); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_stock">สต็อก:</label>
                        <input type="number" id="edit_stock" name="stock" class="form-control" value="<?php echo htmlspecialchars($stock); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_product_description">รายละเอียดสินค้า:</label>
                        <textarea id="edit_product_description" name="product_description" class="form-control"><?php echo htmlspecialchars($product_description); ?></textarea>
                    </div>
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
                    <th>ชื่อสินค้า</th>
                    <th>ราคา</th>
                    <th>ต้นทุน</th>
                    <th>สต็อก</th>
                    <th>กำไร</th>
                    <th>รูปภาพสินค้า</th>
                    <th>รายละเอียดสินค้า</th>
                    <th>การกระทำ</th>
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
                    <td>
                        <img src="product/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" style="width: 100px; height: auto;">
                    </td>
                    <td><?php echo htmlspecialchars($row['product_description']); ?></td>
                    <td>
                        <div class="btn-group" role="group">
                            <!-- ปุ่มแก้ไข -->
                            <button class="btn btn-warning btn-sm editBtn" data-toggle="modal" data-target="#editProductModal"
                                data-id="<?php echo $row['product_id']; ?>"
                                data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                data-price="<?php echo htmlspecialchars($row['price']); ?>"
                                data-cost="<?php echo htmlspecialchars($row['cost']); ?>"
                                data-stock="<?php echo htmlspecialchars($row['stock_quantity']); ?>"
                                data-description="<?php echo htmlspecialchars($row['product_description']); ?>">
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

        // กำหนดค่าให้กับฟิลด์ในฟอร์มแก้ไข
        $('#edit_product_id').val(productId);
        $('#edit_product_name').val(productName);
        $('#edit_price').val(price);
        $('#edit_cost').val(cost);
        $('#edit_stock').val(stock);
        $('#edit_product_description').val(description);

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