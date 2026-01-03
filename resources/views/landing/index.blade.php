<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <!-- ========== BASIC SEO META TAGS ========== -->
    <title>Challenge Tracker - Platform Kelola Challenge Komunitas & Tracking Progress Harian</title>
    <meta name="title" content="Challenge Tracker - Platform Kelola Challenge Komunitas & Tracking Progress Harian">
    <meta name="description" content="Platform terpusat untuk mengelola daily challenge, memantau progress peserta, dan membangun kebiasaan positif. Cocok untuk fitness, learning, reading, dan habit building challenges.">
    <meta name="keywords" content="challenge tracker, daily challenge, habit tracker, fitness challenge, learning challenge, reading challenge, komunitas, progress tracking, challenge management">
    <meta name="author" content="Challenge Tracker Team">
    <meta name="robots" content="index, follow">
    <meta name="language" content="id">
    <meta name="theme-color" content="#0d6efd">

    <!-- ========== CANONICAL URL ========== -->
    <link rel="canonical" href="{{ url('/') }}">

    <!-- ========== OPEN GRAPH / FACEBOOK ========== -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Challenge Tracker - Platform Kelola Challenge Komunitas">
    <meta property="og:description" content="Platform terpusat untuk mengelola daily challenge, memantau progress peserta, dan membangun kebiasaan positif. Tingkatkan engagement komunitas Anda!">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Challenge Tracker - Platform Kelola Challenge Komunitas">
    <meta property="og:site_name" content="Challenge Tracker">
    <meta property="og:locale" content="id_ID">

    <!-- ========== TWITTER ========== -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="Challenge Tracker - Platform Kelola Challenge Komunitas">
    <meta name="twitter:description" content="Platform terpusat untuk mengelola daily challenge, memantau progress peserta, dan membangun kebiasaan positif.">
    <meta name="twitter:image" content="{{ asset('images/og-image.jpg') }}">
    <meta name="twitter:image:alt" content="Challenge Tracker - Platform Kelola Challenge Komunitas">
    <meta name="twitter:site" content="@challengetracker">
    <meta name="twitter:creator" content="@challengetracker">

    <!-- ========== FAVICON ========== -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- ========== STRUCTURED DATA (JSON-LD) ========== -->
    @php
        $structuredData = [
            [
                "@context" => "https://schema.org",
                "@type" => "WebApplication",
                "name" => "Challenge Tracker",
                "description" => "Platform terpusat untuk mengelola daily challenge, memantau progress peserta, dan membangun kebiasaan positif",
                "url" => url('/'),
                "applicationCategory" => "ProductivityApplication",
                "operatingSystem" => "Web",
                "offers" => [
                    "@type" => "Offer",
                    "price" => "0",
                    "priceCurrency" => "IDR"
                ],
                "aggregateRating" => [
                    "@type" => "AggregateRating",
                    "ratingValue" => "4.8",
                    "ratingCount" => "150",
                    "bestRating" => "5",
                    "worstRating" => "1"
                ],
                "creator" => [
                    "@type" => "Organization",
                    "name" => "Challenge Tracker Team",
                    "url" => url('/')
                ],
                "featureList" => [
                    "Challenge Builder dengan custom rules",
                    "Real-time progress tracking",
                    "Automated submission validation",
                    "Leaderboard & streak counter",
                    "Secure authentication via Google",
                    "Cloud storage untuk file uploads",
                    "Export reports berbagai format",
                    "Responsive mobile-friendly design"
                ]
            ],
            [
                "@context" => "https://schema.org",
                "@type" => "Organization",
                "name" => "Challenge Tracker",
                "url" => url('/'),
                "logo" => asset('images/logo.png'),
                "description" => "Platform challenge tracking untuk komunitas",
                "sameAs" => [
                    "https://www.facebook.com/challengetracker",
                    "https://www.twitter.com/challengetracker",
                    "https://www.instagram.com/challengetracker",
                    "https://www.linkedin.com/company/challengetracker"
                ],
                "contactPoint" => [
                    "@type" => "ContactPoint",
                    "contactType" => "Customer Support",
                    "email" => "support@challengetracker.com",
                    "availableLanguage" => ["Indonesian", "English"]
                ]
            ],
            [
                "@context" => "https://schema.org",
                "@type" => "FAQPage",
                "mainEntity" => [
                    [
                        "@type" => "Question",
                        "name" => "Apakah platform ini gratis?",
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => "Ya, platform ini dapat digunakan secara gratis. Untuk fitur advanced dan enterprise requirements, kami menyediakan opsi upgrade dengan pricing yang kompetitif."
                        ]
                    ],
                    [
                        "@type" => "Question",
                        "name" => "Apa saja jenis challenge yang bisa dibuat?",
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => "Platform kami sangat fleksibel. Anda dapat membuat berbagai jenis challenge seperti fitness workout, learning programs, reading challenges, habit building, creative projects, dan banyak lagi."
                        ]
                    ],
                    [
                        "@type" => "Question",
                        "name" => "Apakah data saya aman?",
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => "Sangat aman. Kami menggunakan enkripsi data standar industri dan authentication via Google OAuth. Semua data participant dan submissions disimpan dengan security protocols yang ketat."
                        ]
                    ]
                ]
            ]
        ];
    @endphp

    @foreach($structuredData as $schema)
    <script type="application/ld+json">
        {{ json_encode($schema) }}
    </script>
    @endforeach

    <!-- ========== PRECONNECT ========== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- ========== FONTS ========== -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- ========== BOOTSTRAP CSS ========== -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ========== FONT AWESOME ========== -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- ========== CUSTOM CSS ========== -->
    <link href="{{ asset('css/landing.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-trophy text-warning me-2"></i>Challenge Tracker
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#benefits">Keunggulan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#solutions">Solusi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
                </ul>
                <div class="d-flex ms-lg-3">
                    <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-warning">Daftar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="display-3 fw-bold mb-4">
                            Terhubung, Bertumbuh, <span class="text-warning">Menangkan Challenge</span>
                        </h1>
                        <p class="lead mb-5">
                            Platform terpusat untuk mengelola daily challenge, memantau progress peserta, dan membangun kebiasaan positif bersama komunitas Anda.
                        </p>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="{{ route('register') }}" class="btn btn-warning btn-lg px-5 fw-bold">
                                Mulai Sekarang <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                            <a href="#features" class="btn btn-outline-light btn-lg px-5">
                                Pelajari Lebih
                            </a>
                        </div>
                        <div class="mt-5 d-flex gap-4">
                            <div class="stat-item">
                                <h3 class="fw-bold mb-0">500+</h3>
                                <small class="text-light-50">Active Participants</small>
                            </div>
                            <div class="stat-item">
                                <h3 class="fw-bold mb-0">50+</h3>
                                <small class="text-light-50">Challenges</small>
                            </div>
                            <div class="stat-item">
                                <h3 class="fw-bold mb-0">99.9%</h3>
                                <small class="text-light-50">Uptime</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <div class="floating-card">
                            <div class="card shadow-lg border-0">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-circle bg-success">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">Daily Submission Complete!</h6>
                                            <small class="text-muted">Just now</small>
                                        </div>
                                    </div>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: 75%"></div>
                                    </div>
                                    <small class="text-muted">Day 15 of 30</small>
                                </div>
                            </div>
                        </div>
                        <div class="floating-card floating-card-2">
                            <div class="card shadow-lg border-0">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-circle bg-warning">
                                                <i class="fas fa-fire"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">10 Day Streak!</h6>
                                            <small class="text-muted">Keep it up!</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-waves">
            <svg viewBox="0 0 1440 320" class="waves">
                <path fill="#ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                <path fill="#ffffff" fill-opacity="0.2" d="M0,64L48,80C96,96,192,128,288,128C384,128,480,96,576,80C672,64,768,64,864,80C960,96,1056,128,1152,128C1248,128,1344,96,1392,80L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                <path fill="#ffffff" d="M0,128L48,144C96,160,192,192,288,192C384,192,480,160,576,144C672,128,768,128,864,144C960,160,1056,192,1152,192C1248,192,1344,160,1392,144L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            </svg>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2">FITUR UNGGULAN</span>
                <h2 class="display-5 fw-bold mb-3">Semua yang Anda Butuhkan</h2>
                <p class="lead text-muted">Platform lengkap untuk mengelola challenge komunitas Anda</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon bg-gradient bg-gradient-1">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Challenge Builder</h4>
                        <p class="text-muted mb-0">
                            Buat challenge dengan custom rules dan form fields yang fleksibel. Sesuaikan dengan kebutuhan komunitas Anda tanpa coding.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon bg-gradient bg-gradient-2">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Real-time Tracking</h4>
                        <p class="text-muted mb-0">
                            Monitor progress participant secara real-time. Lihat leaderboard, streak, dan achievements dengan dashboard yang intuitive.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon bg-gradient bg-gradient-3">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Automated Workflow</h4>
                        <p class="text-muted mb-0">
                            Reduce admin time hingga 80% dengan automasi submission validation, progress tracking, dan report generation.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="py-5 bg-light">
        <div class="container py-5">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6">
                    <span class="badge bg-warning bg-opacity-10 text-warning mb-3 px-3 py-2">MENGAPA KAMI</span>
                    <h2 class="display-5 fw-bold mb-3">Platform yang Komunitas Anda Butuhkan</h2>
                </div>
                <div class="col-lg-6">
                    <p class="lead text-muted">
                        Tingkatkan engagement dan bangun kebiasaan positif dengan sistem yang terbukti efektif.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="benefit-card">
                        <div class="benefit-number">01</div>
                        <div class="benefit-content">
                            <h4 class="fw-bold mb-2">Hemat Waktu Berharga</h4>
                            <p class="text-muted mb-0">
                                Tidak perlu lagi manual recap di WhatsApp atau spreadsheet. Semua data terkumpul otomatis di satu tempat.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="benefit-card">
                        <div class="benefit-number">02</div>
                        <div class="benefit-content">
                            <h4 class="fw-bold mb-2">Tingkatkan Engagement</h4>
                            <p class="text-muted mb-0">
                                Gamification dengan leaderboard, streak counter, dan achievements membuat participant lebih motivated.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="benefit-card">
                        <div class="benefit-number">03</div>
                        <div class="benefit-content">
                            <h4 class="fw-bold mb-2">Data Terstruktur</h4>
                            <p class="text-muted mb-0">
                                Data lengkap untuk analisis dan decision making. Export reports dalam berbagai format sesuai kebutuhan.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="benefit-card">
                        <div class="benefit-number">04</div>
                        <div class="benefit-content">
                            <h4 class="fw-bold mb-2">Fleksibel & Scalable</h4>
                            <p class="text-muted mb-0">
                                Sesuaikan dengan berbagai jenis challenge - fitness, learning, habit building, dan banyak lagi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Solutions Section -->
    <section id="solutions" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="badge bg-success bg-opacity-10 text-success mb-3 px-3 py-2">SOLUSI KAMI</span>
                <h2 class="display-5 fw-bold mb-3">Cocok untuk Berbagai Challenge</h2>
                <p class="lead text-muted">Platform fleksibel yang dapat disesuaikan dengan kebutuhan Anda</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="solution-card">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="fw-bold mb-2">
                                    <i class="fas fa-dumbbell text-primary me-2"></i>
                                    Fitness Challenge
                                </h4>
                                <p class="text-muted mb-3">
                                    Track workout routines, progress photos, dan achievements. Perfect untuk gym challenges, weight loss programs, dan fitness competitions.
                                </p>
                                <a href="{{ route('register') }}" class="btn btn-primary">
                                    Mulai Challenge <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="solution-icon bg-primary">
                                    <i class="fas fa-running"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="solution-card">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="fw-bold mb-2">
                                    <i class="fas fa-book-reader text-success me-2"></i>
                                    Learning Challenge
                                </h4>
                                <p class="text-muted mb-3">
                                    Monitor study hours, course completion, dan skill development. Ideal untuk study groups, online courses, dan learning communities.
                                </p>
                                <a href="{{ route('register') }}" class="btn btn-success">
                                    Mulai Challenge <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="solution-icon bg-success">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="solution-card">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="fw-bold mb-2">
                                    <i class="fas fa-book text-warning me-2"></i>
                                    Reading Challenge
                                </h4>
                                <p class="text-muted mb-3">
                                    Track daily reading, book reviews, dan reading goals. Perfect untuk book clubs, reading marathons, dan literacy programs.
                                </p>
                                <a href="{{ route('register') }}" class="btn btn-warning">
                                    Mulai Challenge <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="solution-icon bg-warning">
                                    <i class="fas fa-glasses"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="solution-card">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="fw-bold mb-2">
                                    <i class="fas fa-heart text-danger me-2"></i>
                                    Habit Building
                                </h4>
                                <p class="text-muted mb-3">
                                    Build positive habits dengan daily tracking - meditation, journaling, gratitude, dan lainnya. Transform your life, one day at a time.
                                </p>
                                <a href="{{ route('register') }}" class="btn btn-danger">
                                    Mulai Challenge <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="solution-icon bg-danger">
                                    <i class="fas fa-seedling"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-5 bg-gradient-primary">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="badge bg-white bg-opacity-25 text-white mb-3 px-3 py-2">CARA KERJA</span>
                <h2 class="display-5 fw-bold text-white mb-3">3 Langkah Mudah</h2>
                <p class="lead text-white-50">Mulai challenge Anda dalam hitungan menit</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h4 class="fw-bold text-white mb-3">Setup Challenge</h4>
                        <p class="text-white-50 mb-0">
                            Admin buat challenge dengan rules custom. Tentukan form fields, duration, dan requirements dalam 2 menit.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h4 class="fw-bold text-white mb-3">Invite Participants</h4>
                        <p class="text-white-50 mb-0">
                            Share link challenge ke komunitas. Participant join dengan satu click dan login via Google account.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h4 class="fw-bold text-white mb-3">Track & Validate</h4>
                        <p class="text-white-50 mb-0">
                            Participant submit daily progress, admin validate, semua dapat notifikasi dan achievements. Done!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2">TESTIMONI</span>
                <h2 class="display-5 fw-bold mb-3">Apa Kata Mereka</h2>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="testimonial-card">
                        <div class="testimonial-quote mb-3">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p class="mb-4">
                            "Platform ini mengubah cara kami mengelola fitness challenge. Participant lebih engaged dan admin time berkurang drastis. Sangat recommended!"
                        </p>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle bg-primary">
                                <span>AR</span>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold">Andi Rahmat</h6>
                                <small class="text-muted">Fitness Coach</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="testimonial-card">
                        <div class="testimonial-quote mb-3">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p class="mb-4">
                            "Saya gunakan untuk reading challenge di book club saya. Member love the leaderboard feature dan jadi lebih semangat untuk baca tiap hari."
                        </p>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle bg-success">
                                <span>SW</span>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold">Siti Wulandari</h6>
                                <small class="text-muted">Book Club Founder</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="testimonial-card">
                        <div class="testimonial-quote mb-3">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p class="mb-4">
                            "Finally ada platform yang bisa handle learning challenges dengan custom forms. Study group kami jadi lebih organized dan productive!"
                        </p>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle bg-warning">
                                <span>BP</span>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-bold">Budi Pratama</h6>
                                <small class="text-muted">Tech Community Lead</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-5 bg-light">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="badge bg-info bg-opacity-10 text-info mb-3 px-3 py-2">FAQ</span>
                <h2 class="display-5 fw-bold mb-3">Pertanyaan Umum</h2>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Apakah platform ini gratis?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Ya, platform ini dapat digunakan secara gratis. Untuk fitur advanced dan enterprise requirements, kami menyediakan opsi upgrade dengan pricing yang kompetitif.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Apa saja jenis challenge yang bisa dibuat?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Platform kami sangat fleksibel. Anda dapat membuat berbagai jenis challenge seperti fitness workout, learning programs, reading challenges, habit building, creative projects, dan banyak lagi. Form builder dinamis memungkinkan Anda customize sesuai kebutuhan.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Apakah data saya aman?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sangat aman. Kami menggunakan enkripsi data standar industri dan authentication via Google OAuth. Semua data participant dan submissions disimpan dengan security protocols yang ketat. Anda juga dapat export data kapan saja.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Dapatkah saya export data?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Tentu saja! Admin dapat export challenge data, submissions, dan participant reports dalam berbagai format seperti CSV dan Excel untuk analisis lebih lanjut atau backup purposes.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    Bagaimana jika challenge sudah selesai?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Setelah challenge selesai, Anda dapat export final reports, view complete participant history, dan buat challenge baru. Data challenge tetap tersimpan untuk reference dan analisis di masa depan.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-gradient-warning">
        <div class="container py-5">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h2 class="display-4 fw-bold mb-4">Siap untuk Meningkatkan Engagement Komunitas Anda?</h2>
                    <p class="lead mb-5">
                        Join ribuan participant yang sudah achieve their goals dengan Challenge Tracker
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ route('register') }}" class="btn btn-dark btn-lg px-5 fw-bold">
                            Daftar Sekarang <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-dark btn-lg px-5">
                            Masuk
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-trophy text-warning me-2"></i>Challenge Tracker
                    </h5>
                    <p class="text-white-50 mb-4">
                        Platform terpusat untuk mengelola daily challenge, memantau progress peserta, dan membangun kebiasaan positif bersama komunitas Anda.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin fa-lg"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <h6 class="fw-bold mb-3">Platform</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#features" class="text-white-50 text-decoration-none">Fitur</a></li>
                        <li class="mb-2"><a href="#benefits" class="text-white-50 text-decoration-none">Keunggulan</a></li>
                        <li class="mb-2"><a href="#solutions" class="text-white-50 text-decoration-none">Solusi</a></li>
                        <li class="mb-2"><a href="#faq" class="text-white-50 text-decoration-none">FAQ</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-4">
                    <h6 class="fw-bold mb-3">Challenge</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#solutions" class="text-white-50 text-decoration-none">Fitness</a></li>
                        <li class="mb-2"><a href="#solutions" class="text-white-50 text-decoration-none">Learning</a></li>
                        <li class="mb-2"><a href="#solutions" class="text-white-50 text-decoration-none">Reading</a></li>
                        <li class="mb-2"><a href="#solutions" class="text-white-50 text-decoration-none">Habits</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-4">
                    <h6 class="fw-bold mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('login') }}" class="text-white-50 text-decoration-none">Login</a></li>
                        <li class="mb-2"><a href="{{ route('register') }}" class="text-white-50 text-decoration-none">Register</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Help Center</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Contact</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-4">
                    <h6 class="fw-bold mb-3">Legal</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Privacy Policy</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Terms of Service</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-white-50">
                        &copy; 2025 Challenge Tracker. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 text-white-50">
                        Made with <i class="fas fa-heart text-danger"></i> by Challenge Tracker Team
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });

        // Animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card, .benefit-card, .solution-card, .testimonial-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    </script>
</body>
</html>
