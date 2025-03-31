<?php 
include 'topnavbar.php';
include 'connectDB.php';

// ฟังก์ชันคำนวณกำไร
function calculateProfit($price, $cost) {
    return floatval($price) - floatval($cost);
}

// เพิ่มสินค้า
if (isset($_POST['add_product'])) {
    $name = $_POST['product_name'] ?? '';
    $price = $_POST['price'] ?? 0.0;
    $cost = $_POST['cost'] ?? 0.0;
    $stock = $_POST['stock'] ?? 0;
    $weight = $_POST['weight'] ?? 0.0;
    $shipping_type = $_POST['shipping_type'] ?? '';
    $category_id = $_POST['category_id'] ?? 0;
    $product_description = $_POST['product_description'] ?? '';

    if (empty($name)) {
        echo "ชื่อสินค้าต้องไม่ว่าง";
        exit; // หยุดการทำงานของสคริปต์
    }

    if (empty($shipping_type) || !in_array($shipping_type, ['normal', 'chilled', 'frozen'])) {
        echo "ประเภทการส่งต้องไม่ว่างและต้องเลือกประเภทที่ถูกต้อง";
        exit;
    }    

    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "product/" . $image);
    }

    // ใช้ Prepared Statements
    $stmt = $conn->prepare("INSERT INTO product (name, price, cost, stock_quantity, product_description, image, category_id, weight, shipping_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssdissdis', $name, $price, $cost, $stock, $product_description, $image, $category_id, $weight, $shipping_type);
    
    if ($stmt->execute()) {
        // แจ้งเตือนเมื่อเพิ่มสินค้าสำเร็จ
        echo "<script>alert('เพิ่มสินค้าสำเร็จ');</script>";
        // รีเฟรชหน้า
        echo "<script>window.location.href='manage_products.php';</script>";
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }    
}

// แก้ไขสินค้า
if (isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['product_name'];
    $price = $_POST['price'];
    $cost = $_POST['cost'];
    $stock = $_POST['stock'];
    $weight = $_POST['weight'];
    $shipping_type = $_POST['shipping_type'] ?? 'normal';
    $category_id = $_POST['category_id'];
    $product_description = $_POST['product_description'];

    // ตรวจสอบว่าค่า shipping_type อยู่ใน ENUM หรือไม่
    $valid_shipping_types = ['normal', 'chilled', 'frozen'];

    if (!in_array($shipping_type, $valid_shipping_types)) {
        echo "<script>alert('ค่าการจัดส่งไม่ถูกต้อง'); window.history.back();</script>";
        exit;
    }

    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "product/" . $image);

        $sql = "UPDATE product SET name=?, price=?, cost=?, stock_quantity=?, product_description=?, image=?, category_id=?, weight=?, shipping_type=? WHERE product_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssdisssdsi', $name, $price, $cost, $stock, $product_description, $image, $category_id, $weight, $shipping_type, $product_id);
    } else {
        $sql = "UPDATE product SET name=?, price=?, cost=?, stock_quantity=?, product_description=?, category_id=?, weight=?, shipping_type=? WHERE product_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssdissdsi', $name, $price, $cost, $stock, $product_description, $category_id, $weight, $shipping_type, $product_id);
    }

    // Execute SQL
    if ($stmt->execute()) {
        echo "<script>alert('อัพเดทสินค้าสำเร็จ'); window.location.href='manage_products.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด: " . addslashes($stmt->error) . "'); window.history.back();</script>";
    }

}

// การเติมสต็อก
if (isset($_POST['restock_product'])) {
    $product_id = intval($_POST['product_id']);
    $additional_stock = intval($_POST['quantity']);

    if ($additional_stock <= 0) {
        echo "<script>alert('จำนวนที่เติมต้องมากกว่าศูนย์'); window.history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("UPDATE product SET stock_quantity = stock_quantity + ? WHERE product_id=?");
    $stmt->bind_param('ii', $additional_stock, $product_id);

    if ($stmt->execute()) {
        echo "<script>alert('เติมสต็อกสำเร็จ'); window.location.href='manage_products.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด: " . addslashes($stmt->error) . "'); window.history.back();</script>";
    }
    $stmt->close();
}

// การลบสินค้า
if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    $stmt = $conn->prepare("SELECT image FROM product WHERE product_id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $image = $result->fetch_assoc()['image'];

    $stmt = $conn->prepare("DELETE FROM product WHERE product_id = ?");
    $stmt->bind_param('i', $product_id);
    
    if ($image && file_exists("product/" . $image)) {
        unlink("product/" . $image);
    }
    
    if ($stmt->execute()) {
        if ($image && file_exists("product/" . $image)) {
            unlink("product/" . $image);
        }
        echo "<script>alert('ลบสินค้าสำเร็จ'); window.location.href='manage_products.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด: " . addslashes($stmt->error) . "'); window.history.back();</script>";
    }
    $stmt->close();
}

// ดึงข้อมูลสินค้า
$sql = "SELECT p.*, c.category_name 
        FROM product p 
        LEFT JOIN category c ON p.category_id = c.category_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสินค้า</title>
    <style>
        .product-image {
            max-width: 100px;
            height: auto;
            cursor: pointer;
        }
        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center mb-4">จัดการสินค้า</h1>
    
    <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addProductModal">
        เพิ่มสินค้า
    </button>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">เพิ่มสินค้า</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="product_name" class="form-label">ชื่อสินค้า:</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">หมวดหมู่:</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">เลือกหมวดหมู่</option>
                                <?php
                                $cat_query = $conn->query("SELECT * FROM category");
                                while ($cat = $cat_query->fetch_assoc()) {
                                    echo "<option value='{$cat['category_id']}'>" . htmlspecialchars($cat['category_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">ราคาขาย:</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="cost" class="form-label">ต้นทุน:</label>
                            <input type="number" step="0.01" class="form-control" id="cost" name="cost" required>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">สต็อก:</label>
                            <input type="number" class="form-control" id="stock" name="stock" required>
                        </div>
                        <div class="mb-3">
                            <label for="weight" class="form-label">น้ำหนัก (กก.):</label>
                            <input type="number" step="0.01" class="form-control" id="weight" name="weight" required>
                        </div>
                        <div class="mb-3">
                            <label for="shipping_type" class="form-label">ประเภทการส่ง:</label>
                            <select class="form-select" id="shipping_type" name="shipping_type" required>
                                <option value="normal">ปกติ</option>
                                <option value="chilled">แช่เย็น</option>
                                <option value="frozen">แช่แข็ง</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="product_description" class="form-label">รายละเอียด:</label>
                            <textarea class="form-control" id="product_description" name="product_description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">รูปภาพ:</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        </div>
                        <button type="submit" name="add_product" class="btn btn-primary">เพิ่มสินค้า</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">แก้ไขสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" id="edit_product_id">
                    <div class="mb-3">
                        <label for="edit_product_name" class="form-label">ชื่อสินค้า:</label>
                        <input type="text" class="form-control" id="edit_product_name" name="product_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_category_id" class="form-label">หมวดหมู่:</label>
                        <select class="form-select" id="edit_category_id" name="category_id" required>
                            <?php
                            // ดึงข้อมูลหมวดหมู่สินค้า
                            $cat_query = $conn->query("SELECT * FROM category");
                            while ($cat = $cat_query->fetch_assoc()) {
                                echo "<option value='{$cat['category_id']}'>" . htmlspecialchars($cat['category_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price" class="form-label">ราคาขาย:</label>
                        <input type="number" step="0.01" class="form-control" id="edit_price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_cost" class="form-label">ต้นทุน:</label>
                        <input type="number" step="0.01" class="form-control" id="edit_cost" name="cost" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_stock" class="form-label">สต็อก:</label>
                        <input type="number" class="form-control" id="edit_stock" name="stock" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_weight" class="form-label">น้ำหนัก (กก.):</label>
                        <input type="number" step="0.01" class="form-control" id="edit_weight" name="weight" required>
                    </div>
                    <div class="mb-3">
                        <label for="shipping_type" class="form-label">ประเภทการส่ง:</label>
                        <select class="form-select" id="shipping_type" name="shipping_type" required>
                            <option value="normal">ปกติ</option>
                            <option value="chilled">แช่เย็น</option>
                            <option value="frozen">แช่แข็ง</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_product_description" class="form-label">รายละเอียด:</label>
                        <textarea class="form-control" id="edit_product_description" name="product_description"></textarea>
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

    <!-- Restock Modal -->
    <div class="modal fade" id="restockModal" tabindex="-1" aria-labelledby="restockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="restockModalLabel">เติมสต็อกสินค้า</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <input type="hidden" id="restock_product_id" name="product_id">
                        <div class="mb-3">
                            <label for="restock_quantity" class="form-label">จำนวนที่เติม:</label>
                            <input type="number" class="form-control" id="restock_quantity" name="quantity" min="1" required>
                        </div>
                        <button type="submit" name="restock_product" class="btn btn-primary">เติมสต็อก</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Table -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>สินค้า</th>
                    <th>ราคา</th>
                    <th>ต้นทุน</th>
                    <th>สต็อก</th>
                    <th>กำไร</th>
                    <th>หมวดหมู่</th>
                    <th>น้ำหนัก</th>
                    <th>ประการจัดส่ง</th>
                    <th>รูปภาพ</th>
                    <th>รายละเอียด</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td class="text-center align-middle"><?= htmlspecialchars($row['name']) ?></td>
            <td class="text-center align-middle"><?= number_format($row['price'], 2) ?> ฿</td>
            <td class="text-center align-middle"><?= number_format($row['cost'], 2) ?> ฿</td>
            <td class="text-center align-middle"><?= $row['stock_quantity'] ?></td>
            <td class="text-center align-middle"><?= number_format(calculateProfit($row['price'], $row['cost']), 2) ?> ฿</td>
            <td class="text-center align-middle"><?= htmlspecialchars($row['category_name'] ?? 'ไม่มีหมวดหมู่') ?></td>
            <td class="text-center align-middle"><?= number_format($row['weight'], 2) ?> กก.</td>
            <td class="text-center align-middle">
            <?php 
                $shipping_types = [
                    'normal' => 'ปกติ',
                    'chilled' => 'แช่เย็น',
                    'frozen' => 'แช่แข็ง'
                ];

                // ตรวจสอบว่าค่าที่ได้มาตรงกับตัวเลือกที่กำหนดไว้หรือไม่
                if (array_key_exists($row['shipping_type'], $shipping_types)) {
                    echo htmlspecialchars($shipping_types[$row['shipping_type']]);
                } else {
                    echo 'ไม่ระบุ';
                }
                ?>

            </td>
            <td class="text-center align-middle">
                <?php if (!empty($row['image'])): ?>
                    <button class="btn btn-primary btn-sm view-image-btn" 
                        data-bs-toggle="modal" 
                        data-bs-target="#imageModal" 
                        data-image="<?= 'product/' . htmlspecialchars($row['image']) ?>">
                        ดูรูปภาพ
                    </button>
                <?php else: ?>
                    ไม่มีรูปภาพ
                <?php endif; ?>
            </td>
            <td class="text-center align-middle">
                <button class="btn btn-info btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#productDetailsModal-<?= $row['product_id'] ?>">
                    ดูรายละเอียด
                </button>
            </td>
            <td class="text-center align-middle">
                <div class="btn-group" role="group">
                    <button class="btn btn-warning btn-sm edit-btn" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editProductModal"
                        data-id="<?= $row['product_id'] ?>"
                        data-name="<?= htmlspecialchars($row['name']) ?>"
                        data-price="<?= $row['price'] ?>"
                        data-cost="<?= $row['cost'] ?>"
                        data-stock="<?= $row['stock_quantity'] ?>"
                        data-description="<?= htmlspecialchars($row['product_description']) ?>"
                        data-category="<?= $row['category_id'] ?>"
                        data-weight="<?= $row['weight'] ?>"
                        data-shipping="<?= $row['shipping_type'] ?>">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-info btn-sm restock-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#restockModal"
                        data-id="<?= $row['product_id'] ?>">
                        <i class="fas fa-box-open"></i>
                    </button>
                    <a href="?delete=<?= $row['product_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('แน่ใจหรือไม่ว่าต้องการลบ?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </td>
        </tr>

        <!-- Image Modal -->
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">ดูรูปภาพสินค้า</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="modalImage" src="" class="img-fluid" alt="รูปสินค้า" style="max-width: 100%; height: auto;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal สำหรับแสดงรายละเอียดสินค้า -->
        <div class="modal fade" id="productDetailsModal-<?= $row['product_id'] ?>" tabindex="-1" aria-labelledby="productDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productDetailsModalLabel"><?= htmlspecialchars($row['name']) ?> - รายละเอียด</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto; word-wrap: break-word; white-space: normal;">
                        <h5>รายละเอียดสินค้า</h5>
                        <p><strong>ราคา:</strong> <?= number_format($row['price'], 2) ?> ฿</p>
                        <p><strong>ต้นทุน:</strong> <?= number_format($row['cost'], 2) ?> ฿</p>
                        <p><strong>จำนวนในสต็อก:</strong> <?= $row['stock_quantity'] ?></p>
                        <p><strong>หมวดหมู่:</strong> <?= htmlspecialchars($row['category_name'] ?? 'ไม่มีหมวดหมู่') ?></p>
                        <p><strong>น้ำหนัก:</strong> <?= number_format($row['weight'], 2) ?> กก.</p>
                        <p><strong>รายละเอียด:</strong> <?= nl2br(htmlspecialchars($row['product_description'])) ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</tbody>

        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.edit-btn').click(function() {
    const data = $(this).data();
    console.log("ข้อมูลที่ดึงได้:", data); // ตรวจสอบค่าทั้งหมดที่ส่งมา

    $('#edit_product_id').val(data.id);
    $('#edit_product_name').val(data.name);
    $('#edit_price').val(data.price);
    $('#edit_cost').val(data.cost);
    $('#edit_stock').val(data.stock);
    $('#edit_product_description').val(data.description);
    $('#edit_category_id').val(data.category);
    $('#edit_weight').val(data.weight);
    
    // เช็คค่า shipping_type ก่อนกำหนดค่า
    if (data.shipping) {
        $('#shipping_type').val(data.shipping);
    } else {
        console.warn("shipping_type ไม่มีค่า");
    }
});

    // Restock button handler
    $('.restock-btn').click(function() {
        $('#restock_product_id').val($(this).data('id'));
    });

    // Image preview on click
    $('.product-image').click(function() {
        window.open($(this).attr('src'), '_blank');
    });
});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".view-image-btn").forEach(button => {
            button.addEventListener("click", function () {
                const imageUrl = this.getAttribute("data-image");
                document.getElementById("modalImage").src = imageUrl;
            });
        });
    });
</script>

</body>
</html>
<?php $conn->close(); ?>