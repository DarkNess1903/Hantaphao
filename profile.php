<?php
session_start();
include 'connectDB.php';
include 'topnavbar.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// ดึงข้อมูลลูกค้าพร้อมกับชื่อจังหวัดและชื่ออำเภอจากฐานข้อมูล
$profile_query = "
    SELECT c.customer_id, c.name, c.phone, c.address, c.province_id, c.amphur_id, 
           p.PROVINCE_NAME AS province_name, a.AMPHUR_NAME AS amphur_name, 
           d.DISTRICT_NAME AS district_name, a.POSTCODE AS postcode
    FROM customer c 
    JOIN province p ON c.province_id = p.PROVINCE_ID 
    JOIN amphur a ON c.amphur_id = a.AMPHUR_ID 
    LEFT JOIN district d ON c.district_id = d.DISTRICT_ID  -- ใช้ LEFT JOIN แทน
    WHERE c.customer_id = ?
";

$stmt = mysqli_prepare($conn, $profile_query);
mysqli_stmt_bind_param($stmt, 'i', $customer_id);
mysqli_stmt_execute($stmt);
$profile_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($profile_result) === 0) {
    die("Profile not found.");
}

$profile = mysqli_fetch_assoc($profile_result);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>โปรไฟล์</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;700&family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
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
<body>
    <header>
        <h1>โปรไฟล์</h1>
    </header>

    <main class="container mt-4">
        <?php
        // แสดงผลการอัปเดตโปรไฟล์
        if (isset($_GET['update']) && $_GET['update'] == 'success') {
            echo '<div class="alert alert-success">อัปเดตโปรไฟล์สำเร็จ!</div>';
        }

        // แสดงข้อผิดพลาดในการอัปเดตโปรไฟล์
        if (isset($_GET['update_error'])) {
            $error_messages = [
                1 => 'กรุณากรอกข้อมูลให้ครบทุกช่อง.',
                2 => 'รูปแบบอีเมลไม่ถูกต้อง.',
                3 => 'ไม่สามารถเตรียมคำสั่ง SQL ได้.',
                4 => 'เกิดข้อผิดพลาดในการอัปเดตโปรไฟล์.'
            ];
            $error_code = intval($_GET['update_error']);
            echo '<div class="alert alert-danger">' . ($error_messages[$error_code] ?? 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ.') . '</div>';
        }
        ?>

        <section class="profile-info text-center">
            <div class="profile-icon mb-3">
                <i class="fas fa-user fa-3x"></i>
            </div>
            <h2>ข้อมูลส่วนตัว</h2>
            <p><strong>ชื่อ:</strong> <?php echo htmlspecialchars($profile['name'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>เบอร์โทรศัพท์:</strong> <?php echo htmlspecialchars($profile['phone'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>ที่อยู่:</strong> <?php echo nl2br(htmlspecialchars($profile['address'], ENT_QUOTES, 'UTF-8')); ?></p>
            <p><strong>จังหวัด:</strong> <?php echo htmlspecialchars($profile['province_name'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>อำเภอ:</strong> <?php echo htmlspecialchars($profile['amphur_name'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>ตำบล/เขต:</strong> <?php echo htmlspecialchars($profile['district_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>รหัสไปรษณีย์:</strong> <?php echo htmlspecialchars($profile['postcode'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></p>
            <button id="editBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">แก้ไขโปรไฟล์</button>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <!-- Modal สำหรับฟอร์มแก้ไขข้อมูล -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">แก้ไขโปรไฟล์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm" action="update_profile.php" method="POST">
                        <!-- ส่ง customer_id เพื่อใช้ในการอัปเดต -->
                        <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($customer_id, ENT_QUOTES, 'UTF-8'); ?>">
                        
                        <!-- ชื่อ -->
                        <div class="mb-3">
                            <label for="name" class="form-label">ชื่อ:</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($profile['name'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" required>
                        </div>

                        <!-- เบอร์โทรศัพท์ -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">เบอร์โทรศัพท์:</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($profile['phone'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" required>
                        </div>

                        <!-- ที่อยู่ -->
                        <div class="mb-3">
                            <label for="address" class="form-label">ที่อยู่:</label>
                            <textarea id="address" name="address" rows="4" class="form-control" required><?php echo htmlspecialchars($profile['address'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <!-- จังหวัด -->
                        <div class="mb-3">
                            <label for="province_id" class="form-label">จังหวัด:</label>
                            <select id="province_id" name="province_id" class="form-control" required>
                                <option value="">เลือกจังหวัด</option>
                                <?php
                                $province_query = "SELECT PROVINCE_ID, PROVINCE_NAME FROM province";
                                $province_result = mysqli_query($conn, $province_query);
                                while ($province = mysqli_fetch_assoc($province_result)) {
                                    $selected = ($province['PROVINCE_ID'] == $profile['province_id']) ? 'selected' : '';
                                    echo '<option value="' . $province['PROVINCE_ID'] . '" ' . $selected . '>' . $province['PROVINCE_NAME'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <!-- อำเภอ -->
                        <div class="mb-3">
                            <label for="amphur_id" class="form-label">อำเภอ:</label>
                            <select id="amphur_id" name="amphur_id" class="form-control" required>
                                <option value="">เลือกอำเภอ</option>
                                <?php
                                // ตรวจสอบว่าอำเภอมีค่าหรือไม่
                                if (isset($profile['amphur_id'])) {
                                    $amphur_query = "SELECT AMPHUR_ID, AMPHUR_NAME FROM amphur WHERE PROVINCE_ID = {$profile['province_id']}";
                                    $amphur_result = mysqli_query($conn, $amphur_query);
                                    while ($amphur = mysqli_fetch_assoc($amphur_result)) {
                                        $selected = ($amphur['AMPHUR_ID'] == $profile['amphur_id']) ? 'selected' : '';
                                        echo '<option value="' . $amphur['AMPHUR_ID'] . '" ' . $selected . '>' . $amphur['AMPHUR_NAME'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <!-- ตำบล/เขต -->
                        <div class="mb-3">
                            <label for="district_id" class="form-label">ตำบล/เขต:</label>
                            <select id="district_id" name="district_id" class="form-control" required>
                                <option value="">เลือกตำบล/เขต</option>
                                <?php
                                // ตรวจสอบว่าเขตมีค่าหรือไม่
                                if (isset($profile['district_id'])) {
                                    $district_query = "SELECT DISTRICT_ID, DISTRICT_NAME FROM district WHERE AMPHUR_ID = {$profile['amphur_id']}";
                                    $district_result = mysqli_query($conn, $district_query);
                                    while ($district = mysqli_fetch_assoc($district_result)) {
                                        $selected = ($district['DISTRICT_ID'] == $profile['district_id']) ? 'selected' : '';
                                        echo '<option value="' . $district['DISTRICT_ID'] . '" ' . $selected . '>' . $district['DISTRICT_NAME'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <!-- รหัสไปรษณีย์ -->
                        <div class="mb-3">
                            <label for="postcode" class="form-label">รหัสไปรษณีย์:</label>
                            <input type="text" id="postcode" name="postcode" value="<?php echo htmlspecialchars($profile['postcode'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" readonly>
                        </div>

                        <button type="submit" class="btn btn-success">บันทึกการเปลี่ยนแปลง</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#province_id').change(function() {
                var id_province = $(this).val(); // รับค่าจังหวัดที่เลือก
                
                // เช็คว่ามีการเลือกจังหวัดไหม
                if(id_province) {
                    $.ajax({
                        type: "POST",
                        url: "select_Amphur.php", // ไฟล์ PHP สำหรับดึงข้อมูลอำเภอ
                        data: { id: id_province },
                        success: function(data) {
                            $('#amphur_id').html(data); // ใส่ข้อมูลอำเภอที่ดึงมาจาก PHP
                            $('#district_id').html('<option value="">เลือกตำบล</option>'); // ล้างตำบลก่อน
                            $('#postcode').val(''); // ล้างรหัสไปรษณีย์
                        },
                        error: function() {
                            $('#amphur_id').html('<option value="">ไม่สามารถดึงข้อมูลอำเภอได้</option>');
                        }
                    });
                } else {
                    $('#amphur_id').html('<option value="">เลือกอำเภอ/เขต</option>');
                }
            });

            // เมื่อเลือกอำเภอ
            $('#amphur_id').change(function() {
                var id_amphur = $(this).val();
                $.ajax({
                    type: "POST",
                    url: "select_Tambol.php",
                    data: {id: id_amphur, district_id: '<?php echo htmlspecialchars($profile['district_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>'}, // ส่ง district_id ที่เลือก
                    success: function(data) {
                        $('#district_id').html(data);
                        $('#postcode').val(''); // Reset postcode
                    },
                    error: function() {
                        $('#district_id').html('<option value="">ไม่สามารถดึงข้อมูลตำบลได้</option>');
                    }
                });
            });

            // เมื่อเลือกตำบล
            $('#district_id').change(function() {
                var id_district = $(this).val();
                $.ajax({
                    type: "POST",
                    url: "get_zip_code.php",
                    data: { id: id_district },
                    success: function(data) {
                        $('#postcode').val(data); // Update to correct postcode field ID
                    },
                    error: function() {
                        $('#postcode').val(''); // Reset postcode on error
                    }
                });
            });
        });
    </script>

    <?php mysqli_close($conn); ?>
</body>
</html>
