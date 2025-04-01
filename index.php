<?php
session_start();
include 'connectDB.php';
include 'topnavbar.php';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hantaphao Project</title>

    <link rel="icon" type="image/png" sizes="32x32" href="/images/favi/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favi/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/favi/apple-touch-icon.png">
    <link rel="shortcut icon" href="/images/favi/favicon.ico">
    <link rel="stylesheet" href="css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
        
    <style>
        :root {
            --primary-color: #2e7d32;
            --primary-light: #60ad5e;
            --primary-dark: #005005;
            --secondary-color: #f5f5f5;
            --accent-color: #ffab00;
            --text-dark: #333333;
            --text-light: #f5f5f5;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1), 0 1px 3px rgba(0,0,0,0.08);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.1), 0 5px 10px rgba(0,0,0,0.05);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            height: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'IBM Plex Sans Thai', sans-serif;
            background-color: #f9f9f9;
            color: var(--text-dark);
            line-height: 1.7;
            display: flex;
            flex-direction: column;
        }

        .full-width-section {
            width: 100%;
            padding: 0;
            margin: 0;
        }

        .container-fluid {
            width: 100%;
            padding: 0;
            margin: 0;
        }

        h1, h2, h3 {
            font-family: 'Kanit', sans-serif;
            font-weight: 600;
            margin-top: 0;
        }

        /* Modern Hero Banner - Full Screen */
        .hero-banner {
            width: 100%;
            height: 100vh;
            min-height: 600px;
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), 
                        url('images/Banner_home.jpg') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .hero-content {
            text-align: center;
            color: white;
            z-index: 2;
            padding: 2rem;
            max-width: 800px;
            animation: fadeInUp 1s ease-out;
        }

        .hero-content h1 {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
            line-height: 1.2;
        }

        .hero-content p {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }

        .scroll-down {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            font-size: 2rem;
            animation: bounce 2s infinite;
            cursor: pointer;
        }

        /* Full Width Content Sections */
        .content-section {
            width: 100%;
            padding: 5rem 0;
            position: relative;
        }

        .content-section:nth-child(even) {
            background-color: white;
        }

        .content-section:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .section-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Modern Card Layout - Full Width */
        .history-section {
            display: flex;
            width: 100%;
            min-height: 500px;
            border-radius: 0;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .history-image {
            flex: 1;
            min-height: 500px;
            background: url('images/example.jpg') no-repeat center center;
            background-size: cover;
            position: relative;
            transition: all 0.5s ease;
        }

        .history-image:hover {
            flex: 1.2;
        }

        .history-content {
            flex: 1;
            padding: 4rem;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .history-content h1 {
            color: var(--primary-color);
            font-size: 3rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .history-content h1 img {
            margin-left: 1rem;
            width: 60px;
            height: 60px;
            transition: transform 0.5s ease;
        }

        .history-content h1:hover img {
            transform: rotate(15deg) scale(1.1);
        }

        .history-content h2 {
            color: var(--primary-dark);
            font-size: 2rem;
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
        }

        .history-content h2::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--primary-light);
        }

        .highlight {
            color: var(--primary-light);
            font-weight: 500;
            position: relative;
        }

        .highlight::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: currentColor;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .highlight:hover::after {
            transform: scaleX(1);
        }

        /* Objectives Section - Full Width */
        .objectives-section {
            text-align: center;
            padding: 6rem 2rem;
        }

        .section-title {
            margin-bottom: 3rem;
            position: relative;
        }

        .section-title h1 {
            font-size: 3rem;
            color: var(--primary-color);
            display: inline-block;
        }

        .section-title h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--accent-color);
            border-radius: 2px;
        }

        .objectives-content {
            max-width: 900px;
            margin: 0 auto;
            font-size: 1.2rem;
            line-height: 1.8;
        }

        /* Full Width Product Slideshow */
        .product-section {
            width: 100%;
            padding: 6rem 0;
            background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
        }

        .product-header {
            display: flex; /* ใช้ flex เพื่อจัดเรียง element ด้านใน */
            align-items: center; /* จัดแนวตั้งให้อยู่ตรงกลาง */
            justify-content: flex-start; /* ชิดซ้ายเสมอ */
            margin-bottom: 3rem;
            padding-left: 2rem; /* ชิดขอบซ้าย */
            gap: 1rem; /* เพิ่มช่องว่างระหว่างข้อความและไอคอน */
        }

        .product-header h1 {
            font-size: 3rem;
            color: var(--primary-color);
            margin: 0; /* ลบ margin เดิมเพื่อป้องกันการย้ายตำแหน่ง */
        }

        .product-link {
            display: flex; /* ใช้ flex เพื่อจัดไอคอนให้อยู่ในตำแหน่งที่เหมาะสม */
            align-items: center; /* จัดแนวตั้งให้อยู่ตรงกลาง */
            transition: var(--transition);
            text-decoration: none; /* ลบเส้นใต้ของลิงก์ */
        }

        .product-link img {
            width: 70px; /* ขนาดไอคอนปกติ */
            height: 70px; /* ขนาดไอคอนปกติ */
            margin-left: 0; /* ลบ margin เดิม */
            transition: var(--transition);
        }

        .product-link:hover {
            transform: translateY(-3px);
        }

        .product-link:hover img {
            transform: rotate(-15deg) scale(1.1);
        }

        .slideshow-container {
            width: 100%;
            height: 70vh;
            min-height: 500px;
            position: relative;
            overflow: hidden;
        }

        .mySlides {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .mySlides img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .slide-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            transition: var(--transition);
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .slide-nav:hover {
            background: white;
            transform: translateY(-50%) scale(1.1);
        }

        .prev {
            left: 2rem;
        }

        .next {
            right: 2rem;
        }

        .slide-dots {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 1rem;
            z-index: 10;
        }

        .dot {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: var(--transition);
        }

        .dot.active {
            background: white;
            transform: scale(1.2);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0) translateX(-50%);
            }
            40% {
                transform: translateY(-20px) translateX(-50%);
            }
            60% {
                transform: translateY(-10px) translateX(-50%);
            }
        }

        /* Responsive Adjustments */
        @media (max-width: 1200px) {
            .hero-content h1 {
                font-size: 3.5rem;
            }
            
            .history-content {
                padding: 3rem;
            }
        }

        @media (max-width: 992px) {
            .history-section {
                flex-direction: column;
            }
            
            .history-image {
                min-height: 300px;
                flex: unset;
            }
            
            .hero-content h1 {
                font-size: 3rem;
            }
            
            .section-title h1 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 768px) {
            .hero-banner {
                height: 80vh;
                min-height: 500px;
            }
            
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .hero-content p {
                font-size: 1.2rem;
            }
            
            .history-content h1 {
                font-size: 2.2rem;
            }
            
            .history-content {
                padding: 2rem;
            }
            
            .slideshow-container {
                height: 50vh;
            }

            .product-header {
                padding-left: 1rem; /* ลด padding ใน mobile */
                gap: 0.5rem; /* ลดช่องว่างใน mobile */
            }

            .product-header h1 {
                font-size: 3rem; /* เพิ่มขนาดข้อความใน mobile ให้ใหญ่ขึ้นมาก */
            }

            .product-link img {
                width: 80px; /* เพิ่มขนาดไอคอนใน mobile ให้ใหญ่ขึ้นมาก */
                height: 80px;
            }
        }

        @media (max-width: 576px) {
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .section-title h1 {
                font-size: 2rem;
            }
            
            .product-header h1 {
                font-size: 2.5rem; /* เพิ่มขนาดข้อความใน mobile เล็กให้ใหญ่ขึ้น */
            }
            
            .product-link img {
                width: 70px; /* เพิ่มขนาดไอคอนใน mobile เล็กให้ใหญ่ขึ้น */
                height: 70px;
            }
            
            .slide-nav {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Full Screen Hero Banner -->
    <section class="full-width-section hero-banner">
        <div class="hero-content">
            <h1>ชุมชนหันตะเภา</h1>
            <p>แหล่งเกษตรกรรมอุดมสมบูรณ์ และวัฒนธรรมท้องถิ่นอันงดงาม</p>
        </div>
        <div class="scroll-down" onclick="document.querySelector('.history-section').scrollIntoView({ behavior: 'smooth' })">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Full Width History Section -->
    <section class="full-width-section content-section">
        <div class="history-section">
            <div class="history-image"></div>
            <div class="history-content">
                <h1>Hantaphao <img src="images/leaf.png" alt="Leaf Icon"></h1>
                <h2>History</h2>
                <p>
                    ตำบลหันตะเภาเป็นชุมชนเกษตรกรรมอุดมสมบูรณ์ไปด้วยป่าไม้ มีลำคลองไหลผ่านในอดีตชาวบ้านใช้คลองในการสัญจรไปมาโดยใช้เรือเป็นพาหนะ ต่อมามีคนจีนอพยพมาอยู่ ประกอบอาชีพค้าขาย เป็นทั้งแหล่งรวบรวมสินค้านานาภัณฑ์ และเป็นจุดกลับลำเรือสำเภา จึงเรียกขานที่นี่ว่า <span class="highlight">"หันสำเภา"</span> และเรียกเพี้ยนมาเป็น <span class="highlight">"หันตะเภา"</span> ต่อมาคนจีนได้อพยพออกจากพื้นที่ไปอยู่ที่อื่นๆ พื้นที่บ้านลำแดงมีป่าลดน้อยลง ทำให้ชาวบ้านยึดอาชีพทำการเกษตรจนถึงปัจจุบัน ตำบลหันตะเภา อำเภอวังน้อย จังหวัดพระนครศรีอยุธยา มีคำขวัญประจำตำบลว่า <span class="highlight">"ตำบลน่าอยู่ชุมชนเข้มแข็ง พัฒนาคุณภาพชีวิตส่งเสริมเศรษฐกิจ มุ่งสู่ชีวิตพอเพียง และมีการบริหารจัดการที่ดีระดับแนวหน้า"</span>
                </p>
            </div>
        </div>
    </section>

    <!-- Full Width Objectives Section -->
    <section class="full-width-section content-section">
        <div class="section-container objectives-section">
            <div class="section-title">
                <h1>วัตถุประสงค์</h1>
            </div>
            <div class="objectives-content">
                <p>
                    โครงการนี้มุ่งเน้นการพัฒนาชุมชนหันตะเภาในอำเภอวังน้อย จังหวัดพระนครศรีอยุธยา โดยมีวัตถุประสงค์หลักในการศึกษาพัฒนาระบบควบคุมอัตโนมัติในโรงเรือนเห็ดและสร้างโรงเรือนเห็ดอัจฉริยะเพื่อเพิ่มผลผลิตอย่างมีประสิทธิภาพ, ออกแบบและสร้างเตาเผาชีวมวลไร้ควันเพื่อการผลิตถ่านและน้ำส้มควันไม้เชิงพาณิชย์ที่เป็นมิตรกับสิ่งแวดล้อม, พัฒนาเครื่องสีข้าวอินทรีย์ดัชนีน้ำตาลต่ำเพื่อเพิ่มประสิทธิภาพการผลิตและยกระดับคุณภาพผลิตภัณฑ์ของชุมชน, รวมทั้งศึกษาการนำเทคโนโลยีแพลตฟอร์มดิจิตอลมาใช้เพื่อเพิ่มมูลค่าผลิตภัณฑ์ของภูมิปัญญาท้องถิ่นและพัฒนาแพลตฟอร์มดิจิตอลสำหรับธุรกิจผลิตภัณฑ์ชุมชนอย่างยั่งยืน
                </p>
            </div>
        </div>
    </section>

    <!-- Full Width Product Section -->
    <section class="full-width-section product-section">
        <div class="section-container">
            <div class="product-header">
                <h1>ผลิตภัณฑ์</h1>
                <a href="product.php" class="product-link">
                    <img src="images/cart2.png" alt="Cart Icon">
                </a>
            </div>
            
            <div class="slideshow-container">
                <div class="mySlides">
                    <img src="images/Element/Product_1.jpg" alt="Product 1">
                </div>
                <div class="mySlides">
                    <img src="images/Element/Product_2.jpg" alt="Product 2">
                </div>
                <div class="mySlides">
                    <img src="images/Element/product_3.jpg" alt="Product 3">
                </div>
                
                <div class="slide-nav prev" onclick="plusSlides(-1)">❮</div>
                <div class="slide-nav next" onclick="plusSlides(1)">❯</div>
                
                <div class="slide-dots">
                    <span class="dot" onclick="currentSlide(1)"></span>
                    <span class="dot" onclick="currentSlide(2)"></span>
                    <span class="dot" onclick="currentSlide(3)"></span>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script>
        let slideIndex = 1;
        showSlides(slideIndex);

        // Auto slide change every 8 seconds
        setInterval(() => {
            plusSlides(1);
        }, 8000);

        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            let i;
            let slides = document.getElementsByClassName("mySlides");
            let dots = document.getElementsByClassName("dot");

            if (n > slides.length) { slideIndex = 1; }
            if (n < 1) { slideIndex = slides.length; }

            // Hide all slides
            for (i = 0; i < slides.length; i++) {
                slides[i].style.opacity = "0";
            }

            // Update dots
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }

            // Show current slide
            slides[slideIndex - 1].style.opacity = "1";
            dots[slideIndex - 1].className += " active";
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>