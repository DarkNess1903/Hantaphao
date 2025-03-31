<?php
include 'connectDB.php'; // เชื่อมต่อฐานข้อมูล
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;700&family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- CSS Links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css"> <!-- ใช้ไฟล์ CSS หลักเสมอ -->

    <!-- JavaScript Links -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</head>
<style>
    body, .navbar-nav .nav-link, .btn {
        font-family: 'Sarabun', sans-serif !important;
        font-size: 1.2rem !important;
        font-weight: 500 !important;
        transition: none !important;
    }

    .navbar {
        position: sticky; /* ใช้ sticky เพื่อให้ Bootstrap จัดการระยะห่าง */
        top: 0;
        width: 100%;
        z-index: 1050; /* ทำให้ Navbar อยู่เหนือทุกๆ อัน */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* เพิ่มเงาให้ Navbar */
        background-color: #fff; /* ตรวจสอบว่า Navbar มีสีพื้นหลัง */
    }

    .navbar-nav {
        display: flex;
        justify-content: center;
        width: 100%;
        gap: 3%;
    }

    .nav-item {
        text-align: center;
    }

    .nav-item .nav-link {
        color: #000 !important;
    }

    .nav-item .nav-link:hover {
        color: green !important;
    }

    .btn {
        font-size: 1rem !important;
        padding: 8px 16px !important;
        transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out !important;
    }

    @media (max-width: 768px) {
        .navbar-nav .dropdown .dropdown-menu {
            position: static;
            float: none;
            display: block;
            opacity: 1;
            visibility: visible;
            background-color: transparent;
            border: none;
            box-shadow: none;
            padding: 0;
        }
    }
</style>
<body>
    <nav class="navbar navbar-expand-md bg-white sticky-top">
        <div class="container-fluid">
            <a href="index.php" class="navbar-brand d-flex align-items-center">
                <img src="images/logo.jpg" alt="Logo" width="100" height="100" class="me-2">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#customNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="customNavbar">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="contact_us.php">ข้อมูลชุมชน</a></li>
                    <li class="nav-item"><a class="nav-link" href="rice_mill.php">เกี่ยวกับโครงการ</a></li>
                    <li class="nav-item"><a class="nav-link" href="product.php">สินค้า</a></li>
                    <?php if (isset($_SESSION['customer_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="order_history.php">ประวัติการสั่งซื้อ</a></li>
                        <li class="nav-item"><a class="nav-link" href="profile.php">ตั้งค่าบัญชี</a></li>
                    <?php endif; ?>
                </ul>
                <?php
                if (isset($_SESSION['customer_id'])) {
                    $customer_id = $_SESSION['customer_id'];
                    $query = "SELECT name FROM customer WHERE customer_id = ?";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, 'i', $customer_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    if ($row = mysqli_fetch_assoc($result)) {
                        echo "<span class='navbar-text me-3 user-name'>" . htmlspecialchars($row['name']) . "</span>";
                    }
                    echo "<button class='btn btn-outline-danger' data-bs-toggle='modal' data-bs-target='#logoutModal'>ออกจากระบบ</button>";
                } else {
                    echo "<a class='btn btn-outline-primary' href='login.php'>เข้าสู่ระบบ</a>";
                    echo "<a class='btn btn-outline-success' href='register.php'>สมัครสมาชิก</a>";
                }
                ?>
            </div>
        </div>
    </nav>

    <!-- Modal สำหรับยืนยันการออกจากระบบ -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">ยืนยันการออกจากระบบ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    คุณแน่ใจหรือไม่ว่าต้องการออกจากระบบ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <a href="logout.php" class="btn btn-danger">ออกจากระบบ</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ปิดเมนูเมื่อคลิกที่ลิงก์
        document.querySelectorAll('.navbar-nav .nav-link').forEach(item => {
            item.addEventListener('click', () => {
                var navbarCollapse = document.getElementById('customNavbar');
                if (navbarCollapse.classList.contains('show')) {
                    navbarCollapse.classList.remove('show'); // ปิดเมนู
                }
            });
        });

        // ปรับ padding-top ของ body ตามความสูงของ navbar เมื่อหน้าโหลด
        window.addEventListener('load', function() {
            var navbarHeight = document.querySelector('.navbar').offsetHeight;
            document.body.style.paddingTop = navbarHeight + 'px';
        });
    </script>
</body>
</html>