<?php
include 'connectDB.php'; // เชื่อมต่อฐานข้อมูล
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS Links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

    <!-- JavaScript Links -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</head>
<style>

    .navbar-nav {
        display: flex;
        justify-content: center;
        width: 100%;
        gap: 3%;
    }
    .nav-item {
        text-align: center;
    }
    .nav-item.dropdown .dropdown-menu {
        position: absolute; /* ให้ตำแหน่งของ dropdown ถูกต้อง */
        display: none;
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    .nav-item.dropdown:hover .dropdown-menu {
        display: block;
        visibility: visible;
        opacity: 1;
    }

    .navbar-nav .nav-item .nav-link {
        cursor: pointer;
        transition: color 0.1s ease-in-out;
        color: #000 !important; /* ตั้งค่าสีเริ่มต้นให้เป็นสีดำ */
        font-size: 22px;
    }

    .navbar-nav .nav-item .nav-link:hover {
        color: green !important; /* เปลี่ยนสีเป็นเขียวเมื่อ hover */
        font-size: 22px;
    }

    @media (max-width: 768px) {
    /* ทำให้ dropdown menu แสดงเป็นรายการปกติ */
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
    /* ซ่อนลูกศร dropdown */
    .navbar-nav .dropdown .dropdown-toggle::after {
        display: none;
    }
    /* ปรับสไตล์ของ dropdown items ให้เหมาะกับ nav */
    .navbar-nav .dropdown .dropdown-item {
        padding-left: 2rem; /* ย้ายเข้าเล็กน้อย */
        font-size: 20px;
        color: #000;
        text-align: center; /* จัดกลางตัวอักษร */
    }
    .navbar-nav .dropdown .dropdown-item:hover {
        color: green;
    }
    }

</style>
<body>
    <nav class="navbar navbar-expand-md bg-white">
        <div class="container-fluid">
            <a href="index.php" class="navbar-brand d-flex align-items-center">
                <img src="images/logo.jpg" alt="Logo" width="100" height="100" class="me-2">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#customNavbar" aria-controls="customNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="customNavbar">
                <ul class="navbar-nav me-auto">

                    <li class="nav-item">
                        <a class="nav-link text-dark" href="contact_us.php">ข้อมูลชุมชน</a>
                    </li>

                    <!-- เมนูเกี่ยวกับโครงการ (ทำให้แสดงเมื่อ cursor hover) -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-dark" href="#" id="projectDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            เกี่ยวกับโครงการ
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="projectDropdown">
                            <li><a class="dropdown-item" href="rice_mill.php">โรงสีข้าว</a></li>
                            <li><a class="dropdown-item" href="mushroom_farm.php">โรงเห็ด</a></li>
                            <li><a class="dropdown-item" href="smokeless_kiln.php">เตาเผาไร้ควัน</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="#">ข่าวสารชุมชน</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="product.php">สินค้า</a>
                    </li>
                    <?php if (isset($_SESSION['customer_id'])): ?>
                        <li class="nav-item"><a class="nav-link text-dark" href="order_history.php">ประวัติการสั่งซื้อ</a></li>
                        <li class="nav-item"><a class="nav-link text-dark" href="profile.php">ตั้งค่าบัญชี</a></li>
                    <?php endif; ?>
                    
                    <li class="nav-item"><a class="nav-link text-dark"> </a></li>
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
                        echo "<span class='navbar-text me-3 user-name text-white'>" . htmlspecialchars($row['name']) . "</span>";
                    }
                    echo "<button class='btn btn-outline-danger ms-2'  data-bs-toggle='modal' data-bs-target='#logoutModal'>ออกจากระบบ</button>";
                } else {
                    echo "<a class='btn btn-outline-primary me-2'  href='login.php'>เข้าสู่ระบบ</a>";
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
document.querySelectorAll('.navbar-nav .nav-link').forEach(item => {
    item.addEventListener('click', () => {
        // ถ้า navbar อยู่ในสถานะที่เปิด (collapsed), ให้ปิด
        var navbarCollapse = document.getElementById('customNavbar');
        if (navbarCollapse.classList.contains('show')) {
            navbarCollapse.classList.remove('show'); // ปิดเมนู
        }
    });
});

// เมื่อหน้าโหลดเสร็จ ให้ตั้งค่าระยะห่างให้ตรงกับความสูงของ navbar
window.addEventListener('load', function() {
    var navbarHeight = document.querySelector('.navbar').offsetHeight;
    document.body.style.paddingTop = navbarHeight + 'px';
});
</script>
</body>
</html>
