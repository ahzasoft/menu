    <!-- WhatsApp Button -->
    <a href="https://wa.me/<?php echo RESTAURANT_PHONE; ?>" class="whatsapp-float" target="_blank" id="wa-float">
        <i class="bi bi-whatsapp"></i>
    </a>

    <!-- Navbar -->
    <nav id="navbar-main" class="navbar navbar-expand-lg sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <span class="fs-4">🍔 <?php echo APP_NAME; ?></span>
            </a>
            
            <div class="d-flex align-items-center order-lg-last">
                <button class="btn btn-outline-primary position-relative me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                    <i class="bi bi-cart3 fs-5"></i>
                    <span class="cart-badge" id="cart-count">0</span>
                </button>
                <button id="themeToggle" class="btn btn-outline-dark rounded-circle p-2 me-2">
                    <i class="bi bi-moon-fill"></i>
                </button>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="#hero">الرئيسية</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">لماذا نحن؟</a></li>
                    <li class="nav-item"><a class="nav-link" href="#menu">قائمة الطعام</a></li>
                    <li class="nav-item"><a class="nav-link" href="#reservation">حجز طاولة</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">اتصل بنا</a></li>
                </ul>
            </div>
        </div>
    </nav>
