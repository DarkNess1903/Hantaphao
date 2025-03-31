<?php
session_start();
include 'connectDB.php'
?>
<head>
    <title>โครงการเทคโนโลยีเพื่อชุมชน</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;700&family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <?php
include 'topnavbar.php'
?>
<html lang="th">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f1f8e9; /* Light green background */
            margin: 0;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Prompt', sans-serif;
            font-weight: 700;
            color: #2e7d32; /* Dark green for headings */
        }

        /* Banner */
        .banner {
            background: linear-gradient(135deg, #4caf50 0%, #81c784 100%); /* Green gradient */
            color: white;
            padding: 4rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .banner h1 {
            font-size: 3rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .banner p {
            font-size: 1.2rem;
            margin-bottom: 0;
        }

        .banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://via.placeholder.com/1920x400?text=Banner+Background') no-repeat center center/cover;
            opacity: 0.2;
        }

        /* Section Styling */
        .section-page {
            background-color: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .section-title {
            background-color: #81c784; /* Medium green */
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .section-title i {
            margin-right: 0.5rem;
        }

        /* Card Styling for Before/After */
        .info-card {
            background-color: #e8f5e9; /* Very light green */
            border: none;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: transform 0.3s;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .info-card h4 {
            color: #388e3c; /* Slightly darker green */
            margin-bottom: 1rem;
        }

        .info-card i {
            font-size: 1.5rem;
            color: #4caf50;
            margin-right: 0.5rem;
        }

        /* Image Styling */
        .img-container {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .img-container img {
            border-radius: 0.75rem;
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .img-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            border-radius: 0.75rem;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .img-container:hover .img-overlay {
            opacity: 1;
        }

        /* Highlight Box */
        .highlight-box {
            background-color: #c8e6c9; /* Light green */
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
            text-align: center;
            font-weight: 500;
            color: #2e7d32;
        }

        /* Responsive Adjustments */
        @media (max-width: 767px) {
            .banner h1 {
                font-size: 2rem;
            }

            .banner p {
                font-size: 1rem;
            }

            .section-page {
                padding: 1rem;
            }

            .img-container img {
                height: 200px;
            }

            .section-title {
                font-size: 1.5rem;
            }
        }
    </style>

</head>

<body>

<!DOCTYPE html>
    <!-- Banner -->
    <section class="banner">
        <div class="container">
            <h1>โครงการเทคโนโลยีเพื่อชุมชน</h1>
            <p>นวัตกรรมเพื่อยกระดับคุณภาพชีวิตและเพิ่มรายได้ให้ชุมชนอย่างยั่งยืน</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container my-5">
        <!-- Smart Mushroom Farm Section -->
        <section id="mushroom" class="section-page">
            <h2 class="section-title"><i class="fas fa-seedling"></i> โรงเห็ดอัจฉริยะ</h2>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="info-card">
                        <h4><i class="fas fa-exclamation-circle"></i> ก่อนปรับปรุง</h4>
                        <p>โรงเรือนเพาะเลี้ยงเห็ดเป็นหลังคามุงจากที่ใช้งานมานานจนเสื่อมคุณภาพ จากนั้นเปลี่ยนเป็นโรงเรือนแบบพื้นปูน เวลารดน้ำหลังการเก็บเกี่ยวดอกเห็ดต้องใช้น้ำฉีด บางครั้งน้ำไปโดนดอกเห็ด ทำให้เกิดความเสียหายได้ และอาจมีน้ำขังในก้อนเชื้อเห็ด ส่งผลให้ก้อนเชื้อเห็ดมีปัญหาการไม่ออกดอก ตลอดจนในฤดูร้อนต้องรดน้ำในโรงเรือนเพื่อให้เกิดความชุ่มชื้นตลอดเวลา ทำให้ต้องมีผู้ดูแลโรงเรือนตลอดเวลา</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="img-container">
                        <img src="https://via.placeholder.com/600x400?text=โรงเห็ดก่อนปรับปรุง" alt="โรงเห็ดก่อนปรับปรุง">
                        <div class="img-overlay">โรงเห็ดก่อนปรับปรุง</div>
                    </div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-6 order-md-2">
                    <div class="info-card">
                        <h4><i class="fas fa-check-circle"></i> หลังปรับปรุง</h4>
                        <p>ปรับปรุงโรงเรือนเพาะเห็ดโดยใช้ผ้าใบสีขาวคลุมตลอดโรงเรือน เพื่อกันแมลง และควบคุมอุณหภูมิและความชื้นในโรงเรือนให้คงที่ ใช้ระบบ IoT สั่งการรดน้ำและระบายความร้อนด้วยพัดลมแบบอัตโนมัติ เพื่อให้อุณหภูมิภายในโรงเรือนคงที่ในช่วงอุณหภูมิและความชื้นที่ตั้งไว้ และเกิดการถ่ายเทอากาศภายในโรงเรือน</p>
                        <div class="highlight-box">
                            <i class="fas fa-arrow-up"></i> ผลผลิตเห็ดเพิ่มขึ้น รายได้ในครัวเรือนของชุมชนเพิ่มมากขึ้น
                        </div>
                    </div>
                </div>
                <div class="col-md-6 order-md-1">
                    <div class="img-container">
                        <img src="https://via.placeholder.com/600x400?text=โรงเห็ดหลังปรับปรุง" alt="โรงเห็ดหลังปรับปรุง">
                        <div class="img-overlay">โรงเห็ดหลังปรับปรุง</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Smokeless Charcoal Kiln Section -->
        <section id="charcoal" class="section-page">
            <h2 class="section-title"><i class="fas fa-fire"></i> เตาเผาไร้ควัน</h2>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="info-card">
                        <h4><i class="fas fa-exclamation-circle"></i> ก่อนปรับปรุง</h4>
                        <p>การผลิตถ่านใช้ผาแบบดินกลบ และต่อปล่องควันออกมาเข้ากับถัง 200 ลิตร กลั่นน้ำให้เป็นน้ำส้มควันไม้ ซึ่งมีต้นทุนการผลิตที่ต่ำ ผลผลิตที่ได้ถ่านจะมีขนาดเล็กเป็นจำนวนมาก การเก็บเกี่ยวใส่ถุงเป็นไปได้ยาก ฝุ่นที่เกิดขณะเก็บเกี่ยวเป็นอันตรายต่อผู้เก็บ และก่อเกิดปัญหาการร้องเรียนเรื่องควันไฟและกลิ่นในบริเวณชุมชน</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="img-container">
                        <img src="https://via.placeholder.com/600x400?text=เตาเผาก่อนปรับปรุง" alt="เตาเผาก่อนปรับปรุง">
                        <div class="img-overlay">เตาเผาก่อนปรับปรุง</div>
                    </div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-6 order-md-2">
                    <div class="info-card">
                        <h4><i class="fas fa-check-circle"></i> หลังปรับปรุง</h4>
                        <p>ออกแบบและสร้างเตาระบบปิดที่สามารถผลิตถ่านที่มีคุณภาพและขนาดเป็นไปตามความต้องการของท้องตลาด</p>
                        <div class="highlight-box">
                            <i class="fas fa-dollar-sign"></i> จำหน่ายได้ในราคาที่สูงกว่าถ่านที่ได้จากวิธีการเดิม
                        </div>
                    </div>
                </div>
                <div class="col-md-6 order-md-1">
                    <div class="img-container">
                        <img src="https://via.placeholder.com/600x400?text=เตาเผาหลังปรับปรุง" alt="เตาเผาหลังปรับปรุง">
                        <div class="img-overlay">เตาเผาหลังปรับปรุง</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Rice Mill Section -->
        <section id="rice" class="section-page">
            <h2 class="section-title"><i class="fas fa-tractor"></i> โรงสีข้าว</h2>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="info-card">
                        <h4><i class="fas fa-exclamation-circle"></i> ก่อนปรับปรุง</h4>
                        <p>โรงสีข้าวมีสภาพแวดล้อมโดยรอบที่ไม่เหมาะสม ผลผลิตข้าวที่ได้มีสิ่งเจือปน และมีลักษณะเมล็ดข้าวที่แตกหักเป็นส่วนใหญ่ มีรำข้าวเกิดขึ้นปริมาณมาก มีฝุ่นละอองเกิดขึ้นขณะกระบวนการสีข้าวปริมาณมาก และเครื่องสีข้าวเคยเกิดน้ำท่วมขังมาก่อน ทำให้ชิ้นส่วนบางชิ้นมีการชำรุด</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="img-container">
                        <img src="https://via.placeholder.com/600x400?text=โรงสีข้าวก่อนปรับปรุง" alt="โรงสีข้าวก่อนปรับปรุง">
                        <div class="img-overlay">โรงสีข้าวก่อนปรับปรุง</div>
                    </div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-6 order-md-2">
                    <div class="info-card">
                        <h4><i class="fas fa-check-circle"></i> หลังปรับปรุง</h4>
                        <p>ปรับปรุงประสิทธิภาพเครื่องสีข้าว และจัดระเบียบบริเวณรอบเครื่องสีข้าวให้สามารถเปิดบริการสีข้าวให้กับเกษตรกรในชุมชนได้อย่างปกติ</p>
                        <div class="highlight-box">
                            <i class="fas fa-star"></i> ข้าวที่สีออกมามีคุณภาพดีขึ้น สามารถส่งจำหน่ายในท้องตลาดได้
                        </div>
                    </div>
                </div>
                <div class="col-md-6 order-md-1">
                    <div class="img-container">
                        <img src="https://via.placeholder.com/600x400?text=โรงสีข้าวหลังปรับปรุง" alt="โรงสีข้าวหลังปรับปรุง">
                        <div class="img-overlay">โรงสีข้าวหลังปรับปรุง</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Bootstrap JS and Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>