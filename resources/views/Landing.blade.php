<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GESA (gerakan Sayang Anak)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<style>
    .hero-section {
        background-color: #FFCB05;
        color: #333;
        border-radius: 0 0 50% 50% / 0 0 100px 100px;
        padding-bottom: 150px;
        margin-bottom: 50px;
        position: relative;
        overflow: hidden;
    }
    
    .hero-title {
        margin-top: 50px;
        margin-bottom: 20px;
    }
    
    .subtitle {
        color: #333;
        font-size: 1.1rem;
    }
    
    .phone-container {
        position: relative;
        margin: 30px auto;
        max-width: 200px; /* Reduced from 300px to 200px */
    }
    
    .phone-image {
        width: 100%;
        max-width: 180px; /* Reduced from 250px to 180px */
        border-radius: 15px; /* Also slightly reduced border radius */
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }
    
    .carousel-indicators {
        bottom: -30px;
    }
    
    .carousel-indicators button {
        width: 8px; /* Reduced from 10px */
        height: 8px; /* Reduced from 10px */
        border-radius: 50%;
        background-color: rgba(51, 51, 51, 0.5);
        margin: 0 5px;
    }
    
    .carousel-indicators button.active {
        background-color: #333;
    }
    
    .app-info {
        font-size: 0.95rem;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }
    .navbar-nav {
            margin-left: auto;
            margin-right: 20px;
        }
    .navbar-nav .nav-item {
            margin-left: 15px;
        }
    .d-flex {
            gap: 10px;
        }
</style>

<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center p-0" href="#">
                <img src="Images/Gesa_Logo.png" alt="Logo GESA" style="height: 80px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pengguna">Pengguna</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
                </ul>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-warning btn-sm rounded-pill px-4 py-2" href="#download">Download App</a>
            </div>
        </div>
    </nav>


    <!-- Hero Section -->
    <section class="hero-section">
    <div class="container text-center py-5">
        <h1 class="hero-title fw-bold">GESA - Gerakan Sayang anak</h1>
        <div class="row justify-content-center sub-title-a">
            <p class="col-lg-5 col-md-5 col-sm-4">Solusi cerdas untuk edukasi, perlindungan, dan pemantauan tumbuh kembang anak.Informasi lengkap, laporan cepat, dan deteksi stunting dalam satu genggaman!</p>
        </div>
        
        
        <div id="carouselExampleIndicators" class="carousel slide mt-5 phone-container" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="images/tampilan1.png" class="d-block mx-auto phone-image shadow rounded" alt="Tampilan Aplikasi 1">
                </div>
                <div class="carousel-item">
                    <img src="images/tampilan2.png" class="d-block mx-auto phone-image shadow rounded" alt="Tampilan Aplikasi 2">
                </div>
                <div class="carousel-item">
                    <img src="images/tampilan3.png" class="d-block mx-auto phone-image shadow rounded" alt="Tampilan Aplikasi 3">
                </div>
            </div>
            
            <div class="carousel-indicators position-relative mt-4">
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
        </div>
        
        <div class="app-info mt-5">
            <p>GESA (Gerakan Sayang Anak) adalah aplikasi inovatif yang dikembangkan untuk mendukung kesejahteraan anak-anak, khususnya di Provinsi Kalimantan Barat. Aplikasi ini dirancang untuk memberikan edukasi, memudahkan pelaporan kasus perlindungan anak, serta mendeteksi risiko stunting guna memastikan tumbuh kembang anak yang optimal.</p>
        </div>
    </div>
</section>

<!-- Tentang Section -->
<section id="tentang" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center">
                    <img src="images/mockup.jpeg" alt="Mockup Aplikasi" class="img-fluid rounded shadow">
                </div>
                <div class="col-md-6">
                    <h3 class="fw-bold">Tentang GESA</h3>
                    <p>GESA adalah aplikasi yang membantu dalam perlindungan hak-hak anak.</p>
                    <div class="row">
                        <div class="col-md-6 d-flex mb-3">
                            <p><strong>Perlindungan:</strong> Menjaga anak dari kekerasan dan eksploitasi.</p>
                        </div>
                        <div class="col-md-6 d-flex mb-3">
                            <p><strong>Monitoring:</strong> Memantau kasus sosial anak.</p>
                        </div>
                        <div class="col-md-6 d-flex mb-3">
                            <p><strong>Edukasi:</strong> Meningkatkan kesadaran masyarakat.</p>
                        </div>
                        <div class="col-md-6 d-flex">
                            <p><strong>Kolaborasi:</strong> Mendorong kerja sama berbagai pihak.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<section id="pengguna" class="py-5 bg-white">
        <div class="container">
            <h3 class="fw-bold text-center">Pengguna Aplikasi</h3>
            <div class="row mt-4">
                <div class="col-md-6 text-center">
                    <h5 class="mt-3">Anak</h5>
                    <p>Aplikasi ini membantu anak melaporkan dan mendapatkan perlindungan.</p>
                </div>
                <div class="col-md-6 text-center">
                    <h5 class="mt-3">Orang Tua</h5>
                    <p>Orang tua dapat memantau keamanan anak mereka dan mendapatkan informasi perlindungan.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="kontak">
    <footer class="text-dark py-4 bg-light border-top">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-4 text-md-start">
                    <h5><i class="bi bi-geo-alt"></i> Alamat Kantor:</h5>
                    <p>Jl. Slt. Abdurrahman No. 101 Kota Pontianak Kalimantan Barat 78116</p>
                </div>
                <div class="col-md-4 text-md-start">
                    <h5><i class="bi bi-telephone"></i> Kontak Kami:</h5>
                    <p>📞 081345581234 | 0561 766375</p>
                    <p>📱 081349950905</p>
                    <p>📧 dpppa@kalbarprov.go.id</p>
                </div>
                <div class="col-md-4 text-md-start">
                    <h5>Didukung oleh:</h5>
                    <img src="images/kalbar.jpg" alt="Logo Kalbar" style="width: 80px;">
                </div>
            </div>
            <hr>
            <p class="mb-0">&copy; 2025 Dinas Pemberdayaan Perempuan dan Perlindungan Anak Provinsi Kalimantan Barat</p>
        </div>
    </footer>

    </section>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>