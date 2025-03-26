<?php 
include 'topnavbar.php';
include 'connectDB.php';

// ฟังก์ชันคำนวณกำไร
function calculateProfit($price, $cost) {
    return floatval($price) - floatval($cost);
}

// การเพิ่มสินค้า
if (isset($_POST['add_product'])) {
    $name = trim($_POST['product_name'] ?? '');
    $price = floatval($_POST['price'] ?? 0.0);
    $cost = floatval($_POST['cost'] ?? 0.0);
    $stock = intval($_POST['stock'] ?? 0);
    $product_description = trim($_POST['product_description'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $weight = floatval($_POST['weight'] ?? 0.0);
    $shipping_cost = floatval($_POST['shipping_cost'] ?? 0.0);

    // ตรวจสอบข้อมูล
    if (empty($name)) {
        echo "<script>alert('ชื่อสินค้าต้องไม่ว่าง'); window.history.back();</script>";
        exit;
    }
    if ($price < 0 || $cost < 0 || $stock < 0 || $weight < 0 || $shipping_cost < 0) {
        echo "<script>alert('ราคา, ต้นทุน, สต็อก, น้ำหนัก และค่าส่งต้องไม่เป็นค่าติดลบ'); window.history.back();</script>";
        exit;
    }

    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        if (in_array($_FILES['image']['type'], $allowed_types) && $_FILES['image']['size'] <= $max_size) {
            $image = time() . '_' . basename($_FILES['image']['name']);
            $target = "product/" . $image;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                echo "<script>alert('ไม่สามารถอัพโหลดรูปภาพได้'); window.history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('รูปภาพต้องเป็น JPG, PNG หรือ GIF และขนาดไม่เกิน 5MB'); window.history.back();</script>";
            exit;
        }
    }

    $stmt = $conn->prepare("INSERT INTO product (name, price, cost, stock_quantity, product_description, image, category_id, weight, shipping_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (is_numeric($shipping_cost)) {
        $stmt->bind_param('sddisiddd', $name, $price, $cost, $stock, $product_description, $image, $category_id, $weight, floatval($shipping_cost));
    } else {
        $stmt->bind_param('sddisidds', $name, $price, $cost, $stock, $product_description, $image, $category_id, $weight, $shipping_cost);
    }
    $stmt->bind_param('sddisidds', $name, $price, $cost, $stock, $product_description, $image, $category_id, $weight, $shipping_cost);
        
    if ($stmt->execute()) {
        echo "<script>alert('เพิ่มสินค้าสำเร็จ'); window.location.href='manage_products.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด: " . addslashes($stmt->error) . "'); window.history.back();</script>";
    }
    $stmt->close();
}

// การแก้ไขสินค้า
if (isset($_GET['edit_product_id'])) {
    $product_id = intval($_GET['edit_product_id']);
    $stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $edit_product = $row;
    }
    $stmt->close();
}

if (isset($_POST['edit_product'])) {
    $product_id = intval($_POST['product_id'] ?? 0);
    $name = trim($_POST['product_name'] ?? '');
    $price = floatval($_POST['price'] ?? 0.0);
    $cost = floatval($_POST['cost'] ?? 0.0);
    $stock = intval($_POST['stock'] ?? 0);
    $product_description = trim($_POST['product_description'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $weight = floatval($_POST['weight'] ?? 0.0);
    $shipping_cost = floatval($_POST['shipping_cost'] ?? 0.0);

    if (empty($name)) {
        echo "<script>alert('ชื่อสินค้าต้องไม่ว่าง'); window.history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("SELECT image FROM product WHERE product_id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_image = $result->fetch_assoc()['image'];

    $image = $current_image;
    if (!empty($_FILES['image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        if (in_array($_FILES['image']['type'], $allowed_types) && $_FILES['image']['size'] <= $max_size) {
            $image = time() . '_' . basename($_FILES['image']['name']);
            $target = "product/" . $image;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target) && $current_image && file_exists("product/" . $current_image)) {
                unlink("product/" . $current_image);
            }
        }
    }

    $stmt = $conn->prepare("UPDATE product SET name=?, price=?, cost=?, stock_quantity=?, product_description=?, image=?, category_id=?, weight=?, shipping_type=? WHERE product_id=?");
    if (is_numeric($shipping_cost)) {
        $stmt->bind_param('sddisidddi', $name, $price, $cost, $stock, $product_description, $image, $category_id, $weight, floatval($shipping_cost), $product_id);
    } else {
        $stmt->bind_param('sddisiddsi', $name, $price, $cost, $stock, $product_description, $image, $category_id, $weight, $shipping_cost, $product_id);
    }
    $stmt->bind_param('sddisiddsi', $name, $price, $cost, $stock, $product_description, $image, $category_id, $weight, $shipping_cost, $product_id);

    if ($stmt->execute()) {
        echo "<script>alert('อัพเดทสินค้าสำเร็จ'); window.location.href='manage_products.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด: " . addslashes($stmt->error) . "'); window.history.back();</script>";
    }
    $stmt->close();
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
                            <label for="shipping_cost" class="form-label">ประเภทการส่ง:</label>
                            <select class="form-select" id="shipping_cost" name="shipping_cost" required>
                                <option value="normal">Normal</option>
                                <option value="express">Express</option>
                                <option value="free">Free Shipping</option>
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
                            <label for="shipping_cost" class="form-label">ประเภทการส่ง:</label>
                            <select class="form-select" id="shipping_cost" name="shipping_cost" required>
                                <option value="normal">Normal</option>
                                <option value="express">Express</option>
                                <option value="free">Free Shipping</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_product_description" class="form-label">รายละเอียด:</label>
                            <textarea class="form-control" id="edit_product_description" name="product_description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_image" class="form-label">รูปภาพ:</label>
                            <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
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
        <table class="table table-striped table-bordered text-center">
            <thead class="table-dark">
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
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= number_format($row['price'], 2) ?> ฿</td>
                    <td><?= number_format($row['cost'], 2) ?> ฿</td>
                    <td><?= $row['stock_quantity'] ?></td>
                    <td><?= number_format(calculateProfit($row['price'], $row['cost']), 2) ?> ฿</td>
                    <td><?= htmlspecialchars($row['category_name'] ?? 'ไม่มีหมวดหมู่') ?></td>
                    <td><?= number_format($row['weight'], 2) ?> กก.</td>
                    <td>
                        <?php 
                        if (is_numeric($row['shipping_type'])) {
                            echo number_format(floatval($row['shipping_type']), 2) . ' ฿';
                        } else {
                            echo htmlspecialchars($row['shipping_type']);
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($row['image']): ?>
                            <img src="product/<?= htmlspecialchars($row['image']) ?>" class="product-image" alt="<?= htmlspecialchars($row['name']) ?>">
                        <?php else: ?>
                            ไม่มีรูปภาพ
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['product_description']) ?></td>
                    <td>
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
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Edit button handler
    $('.edit-btn').click(function() {
        const data = $(this).data();
        $('#edit_product_id').val(data.id);
        $('#edit_product_name').val(data.name);
        $('#edit_price').val(data.price);
        $('#edit_cost').val(data.cost);
        $('#edit_stock').val(data.stock);
        $('#edit_product_description').val(data.description);
        $('#edit_category_id').val(data.category);
        $('#edit_weight').val(data.weight);
        $('#edit_shipping_cost').val(data.shipping);
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
</body>
</html>
<?php $conn->close(); ?>