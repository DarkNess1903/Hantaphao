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
</head>
<style>
/* General Styling */
body {
    font-family: 'IBM Plex Sans Thai', sans-serif;
    background-color: #f8f9fa;
    color: #333;
    line-height: 1.6;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

/* Header and Banner */
header .banner {
    width: 100%;
    overflow: hidden;
    margin: 0;
    padding: 0;
}

header .banner img {
    width: 100%;
    height: auto;
    object-fit: cover;
    max-height: 300px;
    display: block;
    margin: 0;
}

/* Container Styling */
.container {
    padding: 0;
    max-width: 100%;
    margin: 0;
    box-sizing: border-box;
    width: 100%;
}

/* Two-Column Section */
.row {
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    margin-bottom: 40px;
    margin-top: 0;
    width: 100%;
    box-sizing: border-box;
    margin-left: 0;
    margin-right: 0;
}

.col-md-3 {
    flex: 0 0 40%;
    max-width: 40%;
    padding: 0;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    margin: 0;
    min-height: 425px;
}

.col-md-9 {
    flex: 0 0 60%;
    max-width: 60%;
    padding: 0;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    margin: 0;
    min-height: 425px;
}

.PR_pic, .right {
    flex: 1;
    display: flex;
    flex-direction: column;
    border-radius: 0;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    box-sizing: border-box;
    margin: 0;
}

.PR_pic {
    background-color: #e0e0e0;
    text-align: center;
    padding: 0;
    justify-content: center;
}

.PR_pic img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 0;
}

.right {
    padding: 15px;
    background-color: #3b5929;
    color: white;
    justify-content: flex-start;
    padding-top: 20px;
    box-sizing: border-box;
}

.right h1 {
    font-family: 'Kanit', sans-serif;
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    color: #3abe1f;
    flex-wrap: wrap;
    line-height: 1;
    letter-spacing: 0;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.right h1 img {
    margin-left: 8px;
    width: 50px;
    height: 50px;
}

.right h2 {
    font-family: 'Kanit', sans-serif;
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 20px;
    margin-top: -15px;
    line-height: 1.1;
    letter-spacing: 0;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

/* Styling for Paragraphs */
.right .history-section {
    font-size: 1.1rem;
    color: #fff;
    font-weight: 500;
    line-height: 1.5;
    text-align: justify;
    margin-bottom: 15px;
    letter-spacing: 0;
    word-break: break-word;
}

/* Indent only the first paragraph */
.right .history-section:first-of-type {
    text-indent: 1.5em;
}

.right .highlight {
    font-style: italic;
    color: #f0f0f0;
}

.right .motto {
    display: block;
    font-style: italic;
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.1);
    padding: 6px 10px;
    margin-top: 10px;
    border-left: 4px solid #ffffff;
    letter-spacing: 0;
    text-indent: 0;
    line-height: 1.4;
    word-break: break-word;
}

/* Objective Section */
.objective {
    background-color: #fff;
    padding: 20px 15px;
    border-radius: 0;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    margin: 40px 0;
    width: 100%;
}

/* Product Section */
.product {
    text-align: left;
    width: 100%;
    margin-bottom: 40px;
    padding: 0 15px;
}

.product h1 {
    font-size: 3rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    display: flex;
    justify-content: flex-start;
    align-items: center;
}

.product h1 a {
    margin-left: 10px;
    text-decoration: none;
    transition: transform 0.3s ease;
}

.product h1 a:hover {
    transform: scale(1.1);
}

/* Slideshow Styling */
.slideshow-container {
    width: 100%;
    position: relative;
    margin: 0;
    overflow: hidden;
    height: 400px; /* กำหนดความสูงให้ container */
}

.mySlides {
    width: 100%;
    height: 400px;
    opacity: 0; /* เริ่มต้นโปร่งใส */
    position: absolute; /* วางสไลด์ทับกัน */
    top: 0;
    left: 0;
    transition: opacity 2s ease-in-out; /* ใช้ transition แทน animation */
}

.mySlides img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 0;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    display: block;
}

.prev, .next {
    cursor: pointer;
    position: absolute;
    top: 50%;
    width: auto;
    padding: 16px;
    margin-top: -22px;
    color: white;
    font-weight: bold;
    font-size: 18px;
    transition: 0.6s ease;
    border-radius: 0 3px 3px 0;
    user-select: none;
    background-color: rgba(0, 0, 0, 0.5);
}

.next {
    right: 0;
    border-radius: 3px 0 0 3px;
}

.prev:hover, .next:hover {
    background-color: rgba(0, 0, 0, 0.8);
}

.dot {
    cursor: pointer;
    height: 15px;
    width: 15px;
    margin: 0 2px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
    transition: background-color 0.6s ease;
}

.active, .dot:hover {
    background-color: #27ae60;
}

/* ลบ .fade ออก เพราะใช้ transition แทน */

/* Responsive Design */
@media (max-width: 768px) {
    .col-md-3, .col-md-9 {
        flex: 0 0 100%;
        max-width: 100%;
    }

    .right {
        padding: 12px;
        padding-top: 15px;
    }

    .right h1 {
        font-size: 2.2rem;
        letter-spacing: 0;
        margin-bottom: 12px;
        line-height: 1.1;
    }

    .right h1 img {
        width: 40px;
        height: 40px;
        margin-left: 6px;
    }

    .right h2 {
        font-size: 1.6rem;
        letter-spacing: 0;
        margin-bottom: 15px;
        margin-top: -12px;
        line-height: 1.2;
    }

    .right .history-section {
        font-size: 1rem;
        font-weight: 500;
        line-height: 1.4;
        margin-bottom: 12px;
        letter-spacing: 0;
        text-align: justify;
        word-break: break-word;
    }

    .right .history-section:first-of-type {
        text-indent: 1em;
    }

    .right .motto {
        padding: 5px 8px;
        margin-top: 8px;
        border-left: 3px solid #ffffff;
        letter-spacing: 0;
        line-height: 1.3;
        word-break: break-word;
    }

    .prev, .next {
        font-size: 14px;
        padding: 10px;
    }

    .objective {
        padding: 15px 12px;
    }

    .product {
        padding: 0 12px;
    }

    .product h1 {
        font-size: 1.8rem;
    }

    .slideshow-container {
        width: 100%;
        margin: 0;
        height: 300px; /* ปรับความสูงให้สอดคล้อง */
    }

    .mySlides {
        height: 300px;
    }
}

@media (max-width: 576px) {
    .container {
        padding: 0;
    }

    .right {
        padding: 10px;
        padding-top: 12px;
    }

    .right h1 {
        font-size: 1.6rem;
        letter-spacing: 0;
        margin-bottom: 10px;
        line-height: 1.1;
    }

    .right h1 img {
        width: 30px;
        height: 30px;
        margin-left: 5px;
    }

    .right h2 {
        font-size: 1.3rem;
        letter-spacing: 0;
        margin-bottom: 12px;
        margin-top: -8px;
        line-height: 1.2;
    }

    .right .history-section {
        font-size: 0.9rem;
        font-weight: 400;
        line-height: 1.3;
        margin-bottom: 10px;
        letter-spacing: 0;
        text-align: left;
        word-break: break-word;
    }

    .right .history-section:first-of-type {
        text-indent: 0;
    }

    .right .motto {
        padding: 4px 6px;
        margin-top: 6px;
        border-left: 2px solid #ffffff;
        letter-spacing: 0;
        line-height: 1.2;
        word-break: break-word;
    }

    .objective {
        padding: 12px 10px;
    }

    .product {
        padding: 0 10px;
    }

    .product h1 {
        font-size: 1.6rem;
    }

    .slideshow-container {
        width: 100%;
        margin: 0;
        height: 200px; /* ปรับความสูงให้สอดคล้อง */
    }

    .mySlides {
        height: 200px;
    }
}
</style>
<body>
    <header>
        <div class="banner">
            <img src="images/Banner_home.jpg" alt="Banner" class="img-fluid">
        </div>
    </header>

    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-12 PR_pic">
                <img src="images/example.jpg" alt="Example Picture" class="img-fluid">
            </div>
            <div class="col-md-9 col-sm-12 right">
                <h1>Hantaphao <img src="images/leaf.png" alt="Leaf Icon" class="img-fluid" width="50px" height="50px"></h1>
                <h2>History</h2>
                <p class="history-section" style="margin-top:15px">
                    ตำบลหันตะเภาเป็นชุมชนเกษตรกรรมอุดมสมบูรณ์ไปด้วยป่าไม้ มีลำคลองไหลผ่าน ในอดีตชาวบ้านใช้คลองในการสัญจรไปมา โดยใช้เรือเป็นพาหนะ ต่อมามีคนจีนอพยพมาอยู่ ประกอบอาชีพค้าขาย เป็นทั้งแหล่งรวบรวมสินค้านานาภัณฑ์ และเป็นจุดกลับลำเรือสำเภา จึงเรียกขานที่นี่ว่า <span class="highlight">“หันสำเภา”</span> และเรียกเพี้ยนมาเป็น <span class="highlight">“หันตะเภา”</span> ต่อมาคนจีนได้อพยพออกจากพื้นที่ไปอยู่ที่อื่นๆ พื้นที่บ้านลำแดงมีป่าลดน้อยลง ทำให้ชาวบ้านยึดอาชีพทำการเกษตรจนถึงปัจจุบัน ตำบลหันตะเภา อำเภอวังน้อย จังหวัดพระนครศรีอยุธยา มีคำขวัญประจำตำบลว่า <span class="motto">“ตำบลน่าอยู่ชุมชนเข้มแข็ง พัฒนาคุณภาพชีวิตส่งเสริมเศรษฐกิจ มุ่งสู่ชีวิตพอเพียง <BR>และมีการบริหารจัดการที่ดีระดับแนวหน้า”</span>
                </p>
            </div>
        </div>
    </div>

    <div class="objective container my-4">
        <h1 style="color:#3abe1f;font-weight: bold;font-size:3rem;">วัตถุประสงค์</h1>
        <p style="font-size:1.2rem;">
            โครงการนี้มุ่งเน้นการพัฒนาชุมชนหันตะเภาในอำเภอวังน้อย จังหวัดพระนครศรีอยุธยา โดยมีวัตถุประสงค์หลักในการศึกษาพัฒนาระบบควบคุมอัตโนมัติในโรงเรือนเห็ดและสร้างโรงเรือนเห็ดอัจฉริยะเพื่อเพิ่มผลผลิตอย่างมีประสิทธิภาพ, ออกแบบและสร้างเตาเผาชีวมวลไร้ควันเพื่อการผลิตถ่านและน้ำส้มควันไม้เชิงพาณิชย์ที่เป็นมิตรกับสิ่งแวดล้อม, พัฒนาเครื่องสีข้าวอินทรีย์ดัชนีน้ำตาลต่ำเพื่อเพิ่มประสิทธิภาพการผลิตและยกระดับคุณภาพผลิตภัณฑ์ของชุมชน, รวมทั้งศึกษาการนำเทคโนโลยีแพลตฟอร์มดิจิตอลมาใช้เพื่อเพิ่มมูลค่าผลิตภัณฑ์ของภูมิปัญญาท้องถิ่นและพัฒนาแพลตฟอร์มดิจิตอลสำหรับธุรกิจผลิตภัณฑ์ชุมชนอย่างยั่งยืน
        </p>
    </div>

    <div class="product container my-4">
        <h1>
            ผลิตภัณฑ์ 
            <a href="product.php">
                <img src="images/cart2.png" alt="Cart Icon" class="img-fluid" width="75px" height="75px">
            </a>
        </h1>
        <div class="slideshow-container">
            <div class="mySlides">
                <img src="images/Element/Product_1.jpg" class="img-fluid" alt="Product 1">
            </div>
            <div class="mySlides">
                <img src="images/Element/Product_2.jpg" class="img-fluid" alt="Product 2">
            </div>
            <div class="mySlides">
                <img src="images/Element/product_3.jpg" class="img-fluid" alt="Product 3">
            </div>
            <a class="prev" onclick="plusSlides(-1)">❮</a>
            <a class="next" onclick="plusSlides(1)">❯</a>
        </div>
        <br>
        <div style="text-align:center">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
        </div>
    </div>
    <?php
    include 'footer.php';
    ?>

    <script>
        let slideIndex = 1;
        showSlides(slideIndex);

        // เปลี่ยนสไลด์อัตโนมัติทุก 10 วินาที
        setInterval(() => {
            plusSlides(1);
        }, 10000);

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

            // รีเซ็ต opacity ของทุกสไลด์
            for (i = 0; i < slides.length; i++) {
                slides[i].style.opacity = "0";
            }

            // อัปเดต dots
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }

            // แสดงสไลด์ปัจจุบัน
            slides[slideIndex - 1].style.opacity = "1";
            dots[slideIndex - 1].className += " active";
        }
    </script>
</body>
</html>