<?php
session_start();
include 'connectDB.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone_or_username = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // ตรวจสอบในตาราง customer ก่อน
    $query_customer = "SELECT * FROM customer WHERE phone = '$phone_or_username'";
    $result_customer = mysqli_query($conn, $query_customer);
    $user_customer = mysqli_fetch_assoc($result_customer);

    // ตรวจสอบในตาราง admin
    $query_admin = "SELECT * FROM admin WHERE username = '$phone_or_username'";
    $result_admin = mysqli_query($conn, $query_admin);
    $user_admin = mysqli_fetch_assoc($result_admin);

    if ($user_customer && password_verify($password, $user_customer['password'])) {
        // ล็อกอินสำเร็จสำหรับลูกค้า
        $_SESSION['customer_id'] = $user_customer['customer_id'];
        echo "<script>window.location.href='index.php';</script>";
        exit();
    } elseif ($user_admin && $password === $user_admin['password']) {
        // ล็อกอินสำเร็จสำหรับแอดมิน
        $_SESSION['admin_id'] = $user_admin['admin_id'];
        echo "<script>window.location.href='Admin/index.php';</script>";
        exit();
    } else {
        // กรณีที่ข้อมูลไม่ถูกต้อง
        $error = "เบอร์มือถือหรือรหัสผ่านไม่ถูกต้อง กรุณาลองอีกครั้ง";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>เข้าสู่ระบบ - Meat Store</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <style>
        body {
            background-image: url('images/login-bg.jpg'); /* Replace with your image URL */
            background-size: cover; /* ปรับภาพให้ครอบคลุมโดยไม่แตก */
            background-position: center; /* จัดภาพให้อยู่กึ่งกลาง */
            background-repeat: no-repeat; /* ไม่ให้ภาพซ้ำ */
            min-height: 100vh; /* ความสูงขั้นต่ำครอบคลุมหน้าจอ */
            margin: 0; /* ลบระยะขอบเริ่มต้น */
            display: flex; /* ใช้ flexbox เพื่อจัดตำแหน่ง */
            flex-direction: column; /* จัดเรียงในแนวตั้ง */
        }
        main {
            flex: 1; /* ขยายเต็มที่ที่เหลือ */
            display: flex; /* ใช้ flexbox เพื่อจัดกึ่งกลาง */
            justify-content: center; /* จัดกึ่งกลางแนวนอน */
            align-items: center; /* จัดกึ่งกลางแนวตั้ง */
            margin-bottom: 30px; /* เพิ่มระยะห่างด้านล่างเพื่อขยับ footer ลง */
        }
        .login-box {
            background-color: rgba(255, 255, 255, 0.9); /* สีพื้นหลังขาวใสเล็กน้อย */
            border: 2px solid #ccc; /* กรอบสีเทา */
            border-radius: 10px; /* มุมโค้ง */
            padding: 20px; /* ระยะห่างภายใน */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* เงา */
            width: 100%; /* ความกว้างเต็มที่ในคอนเทนเนอร์ */
            max-width: 400px; /* จำกัดความกว้างสูงสุด */
            box-sizing: border-box; /* รวม padding และ border ในความกว้าง */
        }
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .navbar {
            background-color: #ffffff; /* สีขาว */
            padding: 10px 20px; /* ระยะห่างภายใน */
        }
        .navbar img {
            height: 60px; /* ขยายขนาดโลโก้ */
        }
        /* ป้องกัน navbar ทับฟอร์ม */
        body {
            padding-top: 80px; /* ปรับตามความสูงของ navbar */
        }
        .footer {
            background-color: rgba(255, 255, 255, 0.9); /* สีขาวใสเล็กน้อย */
            padding: 10px 0; /* ระยะห่างภายใน */
            text-align: center; /* จัดข้อความกึ่งกลาง */
            font-size: 0.9rem; /* ขนาดตัวอักษร */
            color: #333; /* สีตัวอักษร */
        }
        /* ปรับให้เหมาะกับหน้าจอเล็ก */
        @media (max-width: 576px) {
            .login-boxTopology {
                padding: 15px; /* ลด padding ในหน้าจอเล็ก */
                max-width: 90%; /* ลดขนาดกล่องให้แคบลงในหน้าจอเล็ก */
            }
            h2 {
                font-size: 1.5rem; /* ลดขนาดตัวอักษรในหน้าจอเล็ก */
            }
            .navbar img {
                height: 40px; /* ลดขนาดโลโก้ในหน้าจอเล็ก */
            }
            body {
                padding-top: 60px; /* ลด padding-top ในหน้าจอเล็ก */
            }
            main {
                margin-bottom: 20px; /* ลดระยะห่างในหน้าจอเล็ก */
            }
        }
    </style>
</head>
<body>
    <!-- Navbar ใหม่ โลโก้อยู่ซ้าย -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="d-flex w-100 justify-content-start">
                <a href="index.php">
                    <img src="images/logo.jpg" alt="Logo" class="img-fluid">
                </a>
            </div>
        </div>
    </nav>

    <main>
        <div class="login-box">
            <h2 class="text-center mb-4">เข้าสู่ระบบ</h2>
            <form action="login.php" method="post" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="phone" class="form-label">เบอร์มือถือ:</label>
                    <input type="text" id="phone" name="phone" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">รหัสผ่าน:</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" class="form-control" required>
                        <i class="fas fa-eye toggle-password" id="toggle-password" onclick="togglePasswordVisibility('password')"></i>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <p class="text-danger text-center"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>

                <div class="d-grid gap-2">
                    <input type="submit" class="btn btn-primary" value="เข้าสู่ระบบ">
                </div>
            </form>
            <p class="text-center mt-3">ยังไม่มีสมาชิก? <a href="register.php">สมัครสมาชิก</a></p>
        </div>
    </main>


    <script>
        function togglePasswordVisibility(passwordId) {
            const passwordField = document.getElementById(passwordId);
            const toggleIcon = document.getElementById('toggle-password');
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>