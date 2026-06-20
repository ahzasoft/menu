<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo APP_DESCRIPTION; ?>">
    <title><?php echo APP_TITLE; ?></title>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SOCIAL_FACEBOOK; ?>">
    <meta property="og:title" content="<?php echo APP_TITLE; ?>">
    <meta property="og:description" content="<?php echo APP_DESCRIPTION; ?>">
    <meta property="og:image" content="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&auto=format&fit=crop">
    <meta property="og:image:secure_url" content="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&auto=format&fit=crop">
    <meta property="og:image:width" content="800">
    <meta property="og:image:height" content="600">
    <meta property="og:image:type" content="image/jpeg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo SOCIAL_FACEBOOK; ?>">
    <meta property="twitter:title" content="<?php echo APP_TITLE; ?>">
    <meta property="twitter:description" content="<?php echo APP_DESCRIPTION; ?>">
    <meta property="twitter:image" content="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&auto=format&fit=crop">

    <!-- WhatsApp / Viber specific -->
    <meta itemprop="name" content="<?php echo APP_TITLE; ?>">
    <meta itemprop="description" content="<?php echo APP_DESCRIPTION; ?>">
    <meta itemprop="image" content="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&auto=format&fit=crop">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- AOS Animations -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #ff5722;
            --secondary-color: #ffb300;
            --dark-color: #1a1a1a;
            --light-bg: #f8f9fa;
            --white: #ffffff;
            --transition: all 0.3s ease;
        }

        html, body {
            overflow-x: hidden;
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Cairo', sans-serif;
            color: #333;
            background-color: var(--white);
        }


        h1, h2, h3, .display-font {
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
        }

        /* Navbar */
        .navbar {
            padding: 1rem 0;
            transition: var(--transition);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color) !important;
        }

        .nav-link {
            font-weight: 600;
            color: var(--dark-color) !important;
            margin: 0 5px;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: var(--transition);
            transform: translateX(-50%);
        }

        .nav-link:hover::after, .nav-link.active::after {
            width: 80%;
        }

        /* Hero Section */
        .hero {
            height: 80vh;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=1600&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--white);
        }

        .hero-content h1 {
            font-size: clamp(2.5rem, 8vw, 4rem);
            margin-bottom: 1.5rem;
            color: var(--secondary-color);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .hero-content p {
            font-size: clamp(1rem, 4vw, 1.5rem);
            margin-bottom: 2rem;
        }

        .btn-custom {
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 700;
            transition: var(--transition);
            text-transform: uppercase;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border: none;
            color: white;
        }

        .btn-primary-custom:hover {
            background-color: #e64a19;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 87, 34, 0.4);
        }

        /* Features */
        .feature-card {
            padding: 20px;
            border-radius: 20px;
            background: var(--white);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: var(--transition);
            height: 100%;
            text-align: center;
            border: 1px solid #eee;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            display: inline-block;
        }

        /* Menu */
        .section-title {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            padding-bottom: 15px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .menu-tabs .nav-link {
            border: none;
            color: var(--dark-color) !important;
            padding: 8px 20px;
            border-radius: 50px;
            margin: 5px;
            background: #f1f1f1;
            font-size: 0.9rem;
        }

        .menu-tabs .nav-link.active {
            background: var(--primary-color) !important;
            color: white !important;
        }

        .menu-item-card {
            border-radius: 15px;
            overflow: hidden;
            background: var(--white);
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .menu-item-card:hover {
            transform: translateY(-5px);
        }

        .menu-item-img {
            height: 180px;
            width: 100%;
            object-fit: cover;
        }

        .menu-item-body {
            padding: 15px;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .menu-item-body h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .menu-item-body p {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
        }

        .menu-item-price {
            color: var(--primary-color);
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        /* Cart Styles */
        .offcanvas-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: 2px solid var(--secondary-color);
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--secondary-color);
            color: var(--dark-color);
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            font-weight: 700;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: var(--transition);
        }

        .cart-item-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            margin-left: 12px;
            border: 1px solid #eee;
        }

        .cart-item-info h6 {
            margin-bottom: 4px;
            font-weight: 700;
            color: var(--dark-color);
        }

        .cart-item-info p {
            margin-bottom: 0;
            font-size: 0.85rem;
            color: var(--primary-color);
            font-weight: 700;
        }

        #cart-total {
            color: var(--primary-color);
            font-size: 1.3rem;
        }

        .order-type-btn {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .order-type-btn:hover, .order-type-btn.active {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
        }

        .btn-quantity {
            width: 28px;
            height: 28px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 1px solid #ddd;
            background: white;
            transition: var(--transition);
        }

        .btn-quantity:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Dark Mode Cart Adjustments */
        body.bg-dark .offcanvas {
            background-color: #1a1a1a;
            color: #e0e0e0;
        }

        body.bg-dark .cart-item {
            border-bottom-color: rgba(255,255,255,0.1);
        }

        body.bg-dark .cart-item-info h6 {
            color: #fff;
        }

        body.bg-dark .btn-quantity {
            background: #333;
            border-color: #444;
            color: #fff;
        }

        /* Reservation */
        .reservation-section {
            background: var(--light-bg);
            padding: 60px 0;
        }

        .reservation-form {
            background: var(--white);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        }

        /* Footer */
        footer {
            background: var(--dark-color);
            color: #ccc;
            padding: 40px 0 20px;
        }

        .footer-logo {
            color: var(--secondary-color);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        /* WhatsApp Button */
        .whatsapp-float {
            position: fixed;
            bottom: 30px;
            left: 30px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 25px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: var(--transition);
        }

        .whatsapp-float:hover {
            transform: scale(1.1);
            color: white;
        }

        /* Social Links Styling */
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: rgba(255, 87, 34, 0.1);
            color: var(--primary-color);
            border-radius: 50%;
            margin: 0 8px;
            font-size: 1.3rem;
            transition: var(--transition);
            text-decoration: none;
        }

        .social-links a:hover {
            transform: translateY(-5px);
            color: white !important;
        }

        .social-links a.facebook:hover { background: #1877F2 !important; }
        .social-links a.instagram:hover { background: #E4405F !important; }
        .social-links a.twitter:hover { background: #000000 !important; }
        .social-links a.tiktok:hover { background: #000000 !important; }

        footer .social-links a {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: white;
                padding: 20px;
                border-radius: 10px;
                margin-top: 10px;
                box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            }
            .hero {
                background-attachment: scroll;
            }
        }

        @media (max-width: 576px) {
            .hero { height: 60vh; }
            .section-title h2 { font-size: 1.8rem; }
            .btn-custom { width: 100%; margin-bottom: 10px; }
            .hero-content .d-flex { flex-direction: column; }
        }

        /* RTL Specifics */
        [dir="rtl"] .ms-auto {
            margin-right: auto !important;
            margin-left: 0 !important;
        }
    </style>
</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-main">
