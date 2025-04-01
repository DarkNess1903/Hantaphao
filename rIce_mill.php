<?php
session_start();
include 'connectDB.php';
include 'topnavbar.php';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>โครงการเทคโนโลยีเพื่อชุมชน</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;700&family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        :root {
            --primary-color: #2e7d32;
            --primary-dark: #1b5e20;
            --primary-light: #81c784;
            --secondary-color: #ffab00;
            --text-dark: #333333;
            --text-light: #ffffff;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #6c757d;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background-color: var(--light-gray);
            color: var(--text-dark);
            line-height: 1.7;
            padding-top: 56px;
            margin-bottom: 60px;
        }

        /* Hero Banner Styling */
        .hero-banner {
            position: relative;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
            color: var(--text-light);
            padding: 6rem 0; /* เพิ่ม padding เพื่อความโปร่ง */
            text-align: center;
            overflow: hidden;
            margin-bottom: 3rem;
            animation: gradientShift 10s ease infinite; /* Animation พื้นหลัง */
        }

        /* Gradient Animation */
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.2); /* Overlay เพื่อให้ข้อความเด่น */
            z-index: 1;
        }

        .hero-banner .container {
            position: relative;
            z-index: 2; /* ให้ข้อความอยู่เหนือ overlay */
        }

        .hero-title {
            font-family: 'Prompt', sans-serif;
            font-size: 3rem; /* ขยายขนาดตัวอักษร */
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5); /* เงาเข้มขึ้น */
            animation: fadeInDown 1s ease-out; /* Animation ข้อความ */
        }

        .hero-subtitle {
            font-size: 1.5rem; /* ขยายขนาดเล็กน้อย */
            margin-bottom: 2rem;
            opacity: 0.9;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1.2s ease-out; /* Animation ข้อความ */
        }

        /* Fade In Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Icon Decoration */
        .hero-icon {
            font-size: 3rem;
            color: var(--primary-light);
            margin-bottom: 1rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        /* Section Styling */
        .project-section {
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 4rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-light);
        }

        .section-icon {
            background-color: var(--primary-color);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.5rem;
            font-size: 1.5rem;
        }

        .section-title {
            font-family: 'Prompt', sans-serif;
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin: 0;
        }

        /* Swiper Gallery */
        .swiper {
            width: 100%;
            min-height: 250px;
            margin-bottom: 1.5rem;
        }

        .swiper-slide img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.3s;
        }

        .swiper-slide img:hover {
            transform: scale(1.02);
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: var(--primary-color);
        }

        .swiper-pagination-bullet-active {
            background-color: var(--primary-color);
        }

        /* Info Cards */
        .info-card {
            background-color: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--medium-gray);
            height: auto;
        }

        .info-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .info-card-icon {
            color: var(--primary-color);
            font-size: 1.25rem;
            margin-right: 0.75rem;
        }

        .info-card-title {
            font-family: 'Prompt', sans-serif;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin: 0;
        }

        .highlight-box {
            background-color: #e8f5e9;
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            border-radius: 0 8px 8px 0;
            margin-top: 1.5rem;
            font-weight: 500;
            color: var(--primary-dark);
        }

        /* Video Container */
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            margin-bottom: 1.5rem;
            border-radius: 8px;
        }

        .video-container iframe,
        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Footer Spacing */
        main {
            min-height: calc(100vh - 200px);
            padding-bottom: 60px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .hero-banner {
                padding: 3rem 0;
            }
            
            .hero-title {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1.2rem;
            }

            .hero-icon {
                font-size: 2rem;
            }
            
            .section-header {
                flex-direction: column;
                text-align: center;
            }
            
            .section-icon {
                margin-right: 0;
                margin-bottom: 1rem;
            }
            
            .swiper-slide img {
                height: 200px;
            }

            .project-section {
                margin-bottom: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="container">
            <i class="fas fa-leaf hero-icon"></i> <!-- ไอคอนใบไม้เคลื่อนไหว -->
            <h1 class="hero-title">โครงการเทคโนโลยีเพื่อชุมชน</h1>
            <p class="hero-subtitle">นวัตกรรมเพื่อยกระดับคุณภาพชีวิตและเพิ่มรายได้ให้ชุมชนอย่างยั่งยืน</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container my-5">
        <!-- Charcoal Production Section -->
        <section id="charcoal" class="project-section">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-fire"></i>
                </div>
                <h2 class="section-title">การพัฒนากระบวนการผลิตถ่านให้มีประสิทธิภาพสูงขึ้น</h2>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <h4 class="mb-3"><i class="fas fa-exclamation-circle text-danger me-2"></i> ก่อนปรับปรุง</h4>
                    <div class="info-card">
                        <p>การผลิตถ่านในรูปแบบดั้งเดิมมักเผาถ่านโดยใช้วิธีการกลบด้วยดิน และมีการต่อปล่องควันเข้าถังขนาด 200 ลิตรเพื่อกลั่นน้ำส้มควันไม้ ซึ่งเป็นวิธีที่มีต้นทุนต่ำและอาศัยวัสดุจากธรรมชาติ อย่างไรก็ตาม วิธีนี้มีข้อเสียหลายประการ เช่น</p>
                        <ul>
                            <li>ถ่านที่ได้มักมีขนาดเล็กและแตกหักง่าย ทำให้ขายได้ในราคาต่ำ</li>
                            <li>เกิดฝุ่นละอองจำนวนมาก ซึ่งส่งผลกระทบต่อสุขภาพของผู้ปฏิบัติงาน</li>
                            <li>มีปัญหาควันและกลิ่นรบกวนชุมชนโดยรอบ</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <h4 class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> หลังปรับปรุง</h4>
                    <div class="info-card">
                        <p>มีการพัฒนาเตาเผาถ่านแบบใหม่ที่เป็นระบบปิด ซึ่งสามารถควบคุมอุณหภูมิและกระบวนการเผาไหม้ได้ดียิ่งขึ้น ส่งผลให้สามารถผลิตถ่านที่มีขนาดใหญ่ขึ้น คุณภาพดีขึ้น และไม่มีเศษฝุ่นหรือของเสียมากนัก อีกทั้งยังสามารถลดปริมาณควันและกลิ่นที่เกิดขึ้นในระหว่างกระบวนการเผาไหม้ ทำให้เป็นมิตรต่อสิ่งแวดล้อมและสามารถดำเนินกิจการได้อย่างยั่งยืน</p>
                        <div class="highlight-box">
                            <i class="fas fa-dollar-sign text-warning me-2"></i> ถ่านที่ผลิตได้มีคุณภาพดีขึ้น สามารถจำหน่ายในราคาสูงกว่าเดิม
                        </div>
                    </div>
                </div>
            </div>
            <!-- Swiper Slideshow and Video -->
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide"><img src="images/charcoal_before1.jpg" alt="ภาพการผลิตถ่าน 1" class="img-fluid"></div>
                            <div class="swiper-slide"><img src="images/charcoal_after1.jpg" alt="ภาพการผลิตถ่าน 2" class="img-fluid"></div>
                            <div class="swiper-slide"><img src="images/charcoal_after2.jpg" alt="ภาพการผลิตถ่าน 3" class="img-fluid"></div>
                        </div>
                        <!-- Pagination -->
                        <div class="swiper-pagination"></div>
                        <!-- Navigation -->
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="video-container">
                        <video controls>
                            <source src="videos/charcoal_after.mp4" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            </div>
        </section>

        <!-- Rice Milling Section -->
        <section id="rice" class="project-section">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-tractor"></i>
                </div>
                <h2 class="section-title">การปรับปรุงกระบวนการสีข้าวเพื่อเพิ่มคุณภาพของผลผลิต</h2>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <h4 class="mb-3"><i class="fas fa-exclamation-circle text-danger me-2"></i> ก่อนปรับปรุง</h4>
                    <div class="info-card">
                        <p>ข้าวเป็นพืชเศรษฐกิจหลักของประเทศไทย และการสีข้าวเป็นกระบวนการสำคัญที่ช่วยให้เกษตรกรสามารถจำหน่ายผลผลิตในตลาดได้ อย่างไรก็ตาม โรงสีข้าวขนาดเล็กในชุมชนมักเผชิญกับปัญหาหลายประการ เช่น</p>
                        <ul>
                            <li>ข้าวเปลือกที่นำเข้าสีมักมีสิ่งเจือปน เช่น ฝุ่น หิน หรือเมล็ดข้าวที่เสียหาย</li>
                            <li>เครื่องจักรที่ใช้สีข้าวมีประสิทธิภาพต่ำ ทำให้ข้าวที่ได้มีเมล็ดแตกหักจำนวนมาก</li>
                            <li>โรงสีบางแห่งมีปัญหาสิ่งแวดล้อม เช่น ฝุ่นละอองที่เกิดขึ้นระหว่างการสีข้าว</li>
                            <li>ปัญหาน้ำท่วมส่งผลให้เครื่องจักรบางส่วนเสียหาย</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <h4 class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> หลังปรับปรุง</h4>
                    <div class="info-card">
                        <p>มีการนำเทคโนโลยีมาปรับปรุงโรงสีข้าว เช่น การติดตั้งเครื่องจักรที่สามารถคัดแยกสิ่งเจือปนออกจากข้าวเปลือกได้ก่อนเข้าสู่กระบวนการสี และการใช้ระบบควบคุมอุณหภูมิในโรงสีเพื่อลดปัญหาฝุ่นละออง นอกจากนี้ยังมีการพัฒนาเครื่องสีข้าวที่สามารถลดการแตกหักของเมล็ดข้าว ทำให้ได้ผลผลิตที่มีคุณภาพดีขึ้น และสามารถจำหน่ายได้ในราคาสูงขึ้น</p>
                        <div class="highlight-box">
                            <i class="fas fa-star text-warning me-2"></i> ข้าวที่สีออกมามีคุณภาพดีขึ้น สามารถส่งจำหน่ายในท้องตลาดได้
                        </div>
                    </div>
                </div>
            </div>
            <!-- Swiper Slideshow and Video -->
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide"><img src="images/rice_before1.jpg" alt="ภาพการสีข้าว 1" class="img-fluid"></div>
                            <div class="swiper-slide"><img src="images/rice_after1.jpg" alt="ภาพการสีข้าว 2" class="img-fluid"></div>
                            <div class="swiper-slide"><img src="images/rice_after2.jpg" alt="ภาพการสีข้าว 3" class="img-fluid"></div>
                        </div>
                        <!-- Pagination -->
                        <div class="swiper-pagination"></div>
                        <!-- Navigation -->
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="video-container">
                        <video controls>
                            <source src="videos/rice_after.mp4" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mushroom Farm Section -->
        <section id="mushroom" class="project-section">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <h2 class="section-title">การพัฒนาโรงเรือนเพาะเห็ดให้สามารถควบคุมสภาพแวดล้อมได้ดียิ่งขึ้น</h2>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <h4 class="mb-3"><i class="fas fa-exclamation-circle text-danger me-2"></i> ก่อนปรับปรุง</h4>
                    <div class="info-card">
                        <p>การเพาะเห็ดเป็นอีกหนึ่งอุตสาหกรรมในระดับชุมชนที่ได้รับความนิยม เนื่องจากเป็นกระบวนการผลิตที่ใช้ต้นทุนไม่สูง และสามารถทำได้ภายในพื้นที่ขนาดเล็ก อย่างไรก็ตาม โรงเรือนเพาะเห็ดแบบดั้งเดิมมักมีข้อจำกัดหลายประการ เช่น</p>
                        <ul>
                            <li>ใช้วัสดุมุงหลังคาที่เสื่อมสภาพเร็ว เช่น หญ้าคาหรือจาก ซึ่งอาจทำให้โรงเรือนไม่สามารถควบคุมอุณหภูมิและความชื้นได้ดีพอ</li>
                            <li>การรดน้ำหลังเก็บเกี่ยวอาจส่งผลให้ดอกเห็ดเสียหาย</li>
                            <li>ในช่วงฤดูร้อน อุณหภูมิสูงอาจทำให้ผลผลิตลดลง และต้องรดน้ำบ่อยขึ้นเพื่อรักษาสภาพแวดล้อมให้เหมาะสม</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <h4 class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> หลังปรับปรุง</h4>
                    <div class="info-card">
                        <p>มีการพัฒนาโรงเรือนเพาะเห็ดให้มีความทันสมัยมากขึ้น เช่น</p>
                        <ul>
                            <li>การใช้ผ้าใบสีขาวคลุมโรงเรือนเพื่อลดอุณหภูมิและป้องกันแมลง</li>
                            <li>การติดตั้งระบบควบคุมอุณหภูมิและความชื้นแบบอัตโนมัติ</li>
                            <li>การใช้เทคโนโลยี Internet of Things (IoT) เพื่อช่วยในการรดน้ำและระบายอากาศ</li>
                        </ul>
                        <p>เทคโนโลยีเหล่านี้ช่วยให้สามารถเพาะเห็ดได้อย่างมีประสิทธิภาพมากขึ้น ลดการสูญเสียผลผลิต และสามารถเพาะเห็ดได้ตลอดทั้งปี ซึ่งช่วยให้ชุมชนมีรายได้ที่มั่นคงยิ่งขึ้น</p>
                        <div class="highlight-box">
                            <i class="fas fa-arrow-up text-warning me-2"></i> ผลผลิตเห็ดเพิ่มขึ้น รายได้ในครัวเรือนของชุมชนเพิ่มมากขึ้น
                        </div>
                    </div>
                </div>
            </div>
            <!-- Swiper Slideshow and Video -->
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide"><img src="images/mushroom_before1.jpg" alt="ภาพโรงเรือนเพาะเห็ด 1" class="img-fluid"></div>
                            <div class="swiper-slide"><img src="images/mushroom_after1.jpg" alt="ภาพโรงเรือนเพาะเห็ด 2" class="img-fluid"></div>
                            <div class="swiper-slide"><img src="images/mushroom_after2.jpg" alt="ภาพโรงเรือนเพาะเห็ด 3" class="img-fluid"></div>
                        </div>
                        <!-- Pagination -->
                        <div class="swiper-pagination"></div>
                        <!-- Navigation -->
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="video-container">
                        <video controls>
                            <source src="videos/mushroom_after.mp4" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <script>
        $(document).ready(function(){
            // Initialize Swiper
            const swiper = new Swiper('.swiper', {
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                slidesPerView: 1,
                spaceBetween: 10,
            });

            // Smooth scrolling for anchor links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $($(this).attr('href')).offset().top - 70
                }, 500);
            });
        });
    </script>
</body>
</html>