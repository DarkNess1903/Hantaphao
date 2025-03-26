<?php
session_start();
include 'connectDB.php';
include 'topnavbar.php';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>สารสนเทศตำบลหันตะเภา</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- เพิ่ม Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;700&family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<style>
    body {
    font-family: 'Sarabun', sans-serif;
    font-weight: 400;
    line-height: 1.6;
}

h1, h2, h3, h4, h5, h6 {
    font-family: 'Prompt', sans-serif;
    font-weight: 700;
}

.btn {
    font-family: 'Prompt', sans-serif;
    font-weight: 500;
    
}

.carousel-caption h5, .carousel-caption p {
    font-family: 'Prompt', sans-serif;
}

/* CSS เดิม */
header {
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-success {
    transition: transform 0.3s, background-color 0.3s;
}

.btn-success:hover {
    transform: scale(1.05);
    background-color: #218838;
}

.card {
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

.card-img-top {
    height: 200px;
    object-fit: cover;
}

.carousel-item img {
    height: 400px;
    object-fit: cover;
}

.carousel-caption {
    padding: 10px;
}

section {
    line-height: 1.6;
}

.text-success {
    color: #28a745 !important;
}
</style>
<body>
    <!-- Modern Header with Green Theme -->
    <header class="position-relative text-white text-center py-5" style="background: url('https://images.unsplash.com/photo-1506748686214-e9df14d2d9d0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1500&h=500&q=80') no-repeat center center; background-size: cover; min-height: 500px;">
        <div class="overlay position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0, 128, 0, 0.7), rgba(34, 139, 34, 0.3));"></div>
        <div class="container position-relative d-flex flex-column justify-content-center h-100">
            <h1 class="display-3 fw-bold animate__animated animate__zoomIn" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);">สารสนเทศตำบลหันตะเภา</h1>
            <p class="lead animate__animated animate__fadeInUp animate__delay-1s" style="text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);">ตำบลต้นแบบเพื่อการพัฒนาคุณภาพชีวิต อำเภอวังน้อย จังหวัดพระนครศรีอยุธยา</p>
            <a href="#main-content" class="btn btn-success btn-lg mt-3 animate__animated animate__bounceIn animate__delay-2s">สำรวจตำบล</a>
        </div>
    </header>

    <!-- Main Content -->
    <main id="main-content" class="container my-5">
        <div class="row g-4">
            <!-- General Information -->
            <div class="col-12">
                <section class="card shadow-sm border-0 animate__animated animate__fadeInUp">
                    <div class="card-body p-4">
                        <h3 class="card-title text-success mb-3">ข้อมูลทั่วไปของตำบล</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>ประวัติความเป็นมา</h5>
                                <p>ตำบลหันตะเภาเป็นชุมชนเกษตรกรรม อุดมสมบูรณ์ไปด้วยป่าไม้ มีลำคลองไหลผ่าน ในอดีตชาวบ้านใช้คลองในการสัญจรไปมาโดยใช้เรือเป็นพาหนะ เรียกคลองนี้ว่าคลองลำแดง ต่อมามีคนจีนอพยพมาค้าขายและเป็นจุดกลับลำเรือสำเภา จึงเรียกว่า “หันสำเภา” และเพี้ยนมาเป็น “หันตะเภา”</p>
                            </div>
                            <div class="col-md-6">
                                <h5>สภาพทั่วไป</h5>
                                <p>ตำบลหันตะเภา อำเภอวังน้อย จังหวัดพระนครศรีอยุธยา ห่างจากอำเภอวังน้อย 10 กม. พื้นที่ 24.66 ตร.กม. (15,413 ไร่) มี 5 หมู่บ้าน เป็นพื้นที่ราบลุ่มเกษตรกรรม</p>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-users me-2 text-success"></i><strong>ประชากร:</strong> 3,261 คน</li>
                                    <li><i class="fas fa-home me-2 text-success"></i><strong>หลังคาเรือน:</strong> 890 หลัง</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Geography and Infrastructure -->
            <div class="col-lg-6 col-md-12">
                <section class="card shadow-sm border-0 h-100 animate__animated animate__fadeInUp animate__delay-1s">
                    <img src="https://images.unsplash.com/photo-1506748686214-e9df14d2d9d0?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=300&q=80" class="card-img-top" alt="คลองชลประทาน">
                    <div class="card-body p-4">
                        <h3 class="card-title text-success mb-3">ลักษณะภูมิประเทศ</h3>
                        <p>พื้นที่ราบลุ่มเกษตรกรรม ทำนาและสวนผลไม้ มีคลองชลประทาน 4 สาย และถนนลาดยาง</p>
                        <h5>แหล่งน้ำผิวดิน</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">คลอง 28</li>
                            <li class="list-group-item">คลอง 6</li>
                            <li class="list-group-item">คลอง 27</li>
                            <li class="list-group-item">คลอง 8 ขวา</li>
                        </ul>
                    </div>
                </section>
            </div>

            <!-- Economy and Culture -->
            <div class="col-lg-6 col-md-12">
                <section class="card shadow-sm border-0 h-100 animate__animated animate__fadeInUp animate__delay-1s">
                    <img src="https://images.unsplash.com/photo-1513415756790-2ac1db1297d0?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=300&q=80" class="card-img-top" alt="ประเพณีท้องถิ่น">
                    <div class="card-body p-4">
                        <h3 class="card-title text-success mb-3">สภาพทางเศรษฐกิจและสังคม</h3>
                        <p><strong>อาชีพ:</strong> ทำนา, รับจ้างโรงงาน, ค้าขาย รายได้เฉลี่ย 82,722.72 บาท/คน/ปี</p>
                        <h5>ประเพณีประจำปี</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">กวนข้าวทิพย์</li>
                            <li class="list-group-item">สงกรานต์</li>
                            <li class="list-group-item">แข่งเรือยาว</li>
                            <li class="list-group-item">ลอยกระทง</li>
                        </ul>
                    </div>
                </section>
            </div>

            <!-- Tourism -->
            <div class="col-12">
                <section class="card shadow-sm border-0 animate__animated animate__fadeInUp">
                    <div class="card-body p-4">
                        <h3 class="card-title text-success mb-3">แหล่งท่องเที่ยว</h3>
                        <div class="row">
                        <div class="col-md-6">
                        <p><strong>หมู่บ้าน OTOP นวัตวิถี บ้านลำแดง:</strong> วิถีชาวนา เรียนรู้ชีวิตชุมชน</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-pray me-2 text-success"></i>ไหว้พระรับบุญ</li>
                            <li><i class="fas fa-landmark me-2 text-success"></i>พิพิธภัณฑ์วังชาวนา</li>
                            <li><i class="fas fa-ship me-2 text-success"></i>ล่องเรือคลอง 28</li>
                            <li><i class="fas fa-leaf me-2 text-success"></i>ล่องเรือชมบัว (ก.ย.-พ.ย.)</li>
                        </ul>
                    </div>
                            <div class="col-md-6">
                                <img src="https://images.unsplash.com/photo-1506748686214-e9df14d2d9d0?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&h=300&q=80" class="img-fluid rounded" alt="ล่องเรือชมบัว">
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- OTOP Products Carousel -->
            <div class="col-12">
                <section class="card shadow-sm border-0 animate__animated animate__fadeInUp">
                    <div class="card-body p-4">
                        <h3 class="card-title text-success mb-3">สินค้า OTOP</h3>
                        <div id="otopCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="https://images.unsplash.com/photo-1572964701945-7f2f8f245d65?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=400&q=80" class="d-block w-100" alt="ข้าว กข 43">
                                    <div class="carousel-caption d-none d-md-block" style="background: rgba(0, 128, 0, 0.7); border-radius: 5px;">
                                        <h5>ข้าวพันธุ์ กข 43</h5>
                                        <p>ข้าวน้ำตาลน้อย เหมาะสำหรับผู้ป่วยเบาหวาน</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=400&q=80" class="d-block w-100" alt="สมุนไพรดาวอินคา">
                                    <div class="carousel-caption d-none d-md-block" style="background: rgba(0, 128, 0, 0.7); border-radius: 5px;">
                                        <h5>สมุนไพรดาวอินคา</h5>
                                        <p>อาหารสุขภาพสำหรับคนรักสุขภาพ</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=400&q=80" class="d-block w-100" alt="กล้วยฉาบ/กล้วยตาก">
                                    <div class="carousel-caption d-none d-md-block" style="background: rgba(0, 128, 0, 0.7); border-radius: 5px;">
                                        <h5>กล้วยฉาบ/กล้วยตาก</h5>
                                        <p>ของว่างอร่อย หยุดไม่ได้</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=400&q=80" class="d-block w-100" alt="พวงกุญแจมะพร้าว">
                                    <div class="carousel-caption d-none d-md-block" style="background: rgba(0, 128, 0, 0.7); border-radius: 5px;">
                                        <h5>พวงกุญแจมะพร้าว</h5>
                                        <p>งานฝีมือสร้างสรรค์จากธรรมชาติ</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=400&q=80" class="d-block w-100" alt="วุ้นมะพร้าวในน้ำเชื่อม">
                                    <div class="carousel-caption d-none d-md-block" style="background: rgba(0, 128, 0, 0.7); border-radius: 5px;">
                                        <h5>วุ้นมะพร้าวในน้ำเชื่อม</h5>
                                        <p>ของหวานสดชื่นจากมะพร้าว</p>
                                    </div>
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#otopCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#otopCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Map -->
            <div class="col-12">
                <section class="card shadow-sm border-0 animate__animated animate__fadeInUp">
                    <div class="card-body p-4">
                        <h3 class="card-title text-success mb-3">แผนที่ตำบลหันตะเภา</h3>
                        <div class="ratio ratio-16x9">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d123720.81345479304!2d100.68755501887905!3d14.2954235218288!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x311d8e426972cd4b%3A0x4019237450ca530!2sHan%20Taphao%2C%20Wang%20Noi%20District%2C%20Phra%20Nakhon%20Si%20Ayutthaya%2013170!5e0!3m2!1sen!2sth!4v1742894713917!5m2!1sen!2sth"
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy">
                            </iframe>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="js/script.js"></script>
</body>
</html>

<?php
mysqli_close($conn);
include 'footer.php';
?>