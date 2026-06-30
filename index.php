<?php
require_once 'config.php';
require_once 'includes/db_helper.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/cart.php';

// Initialize and ensure database has data, auto-sync if cache expired
initialize_database();
$last_sync = (int)get_setting('last_sync_time', 0);
if (time() - $last_sync > API_SYNC_INTERVAL) {
    // Attempt auto-sync (if it fails, page still renders with cache)
    @fetch_and_sync_from_server();
}

$menu_categories = get_categories();
$menu_items = get_all_items();

// Helper function to render a menu item
function render_menu_item($item, $category_class = '') {
    $has_discount = isset($item['discount']) && $item['discount'] > 0;
    $final_price = $has_discount ? ($item['price'] - $item['discount']) : $item['price'];
    ?>
    <div class="col-md-6 col-lg-4 <?php echo $category_class; ?>" data-aos="zoom-in" data-aos-delay="<?php echo $item['delay']; ?>">
        <div class="menu-item-card position-relative">
            <?php if ($has_discount): ?>
                <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-danger text-white fw-bold rounded-pill shadow-sm" style="z-index: 10; font-size: 0.85rem;">
                    خصم <?php echo $item['discount']; ?> جنيه
                </div>
            <?php endif; ?>
            <img src="<?php echo $item['image']; ?>" class="menu-item-img" alt="<?php echo htmlspecialchars($item['name']); ?>">
            <div class="menu-item-body">
                <div>
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                </div>
                <div>
                    <div class="menu-item-price">
                        <?php if ($has_discount): ?>
                            <span class="text-decoration-line-through text-muted fs-6 me-2"><?php echo $item['price']; ?></span>
                            <span><?php echo $final_price; ?> جنيه</span>
                        <?php else: ?>
                            <span><?php echo $item['price']; ?> جنيه</span>
                        <?php endif; ?>
                    </div>
                    <button class="btn btn-primary-custom w-100 mt-2 add-to-cart" 
                            data-id="<?php echo $item['id']; ?>" 
                            data-name="<?php echo htmlspecialchars($item['name']); ?>" 
                            data-price="<?php echo $final_price; ?>" 
                            data-img="<?php echo $item['image']; ?>">إضافة للسلة</button>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>


    <!-- Hero Section -->
    <header id="hero" class="hero">
        <div class="container hero-content" data-aos="fade-up">
            <h1><?php echo APP_NAME; ?></h1>
            <p><?php echo APP_DESCRIPTION; ?></p>
            <div class="d-flex justify-content-center gap-3">
                <a href="#menu" class="btn btn-custom btn-primary-custom btn-lg">اطلب الآن</a>
                <a href="#reservation" class="btn btn-custom btn-outline-light btn-lg">حجز طاولة</a>
            </div>
        </div>
    </header>

    <main>
        <!-- Features Section -->
        <section id="features" class="py-5">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>لماذا نحن؟</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="feature-card">
                            <span class="feature-icon">🔥</span>
                            <h3>طعام طازج</h3>
                            <p>جميع الوجبات تُحضّر يومياً بمكونات عالية الجودة وطازجة تماماً.</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-card">
                            <span class="feature-icon">🚀</span>
                            <h3>توصيل سريع</h3>
                            <p>أسرع خدمة توصيل في المدينة، نصلك أينما كنت وفي وقت قياسي.</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-card">
                            <span class="feature-icon">💯</span>
                            <h3>جودة ممتازة</h3>
                            <p>نلتزم بأعلى معايير النظافة والجودة لضمان تجربة طعام لا تُنسى.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Menu Section -->
        <section id="menu" class="py-5 bg-light">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>أشهر الوجبات</h2>
                </div>
                
                <ul class="nav nav-pills menu-tabs justify-content-center mb-5" data-aos="fade-up">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#all">الكل</button>
                    </li>
                    <?php foreach ($menu_categories as $key => $cat): ?>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#item_<?php echo $key; ?>"><?php echo $cat['name']; ?></button>
                        </li>   
                    <?php endforeach; ?>
                </ul>

                <div class="tab-content">
                    <!-- All Items -->
                    <div class="tab-pane fade show active" id="all">
                        <div class="row g-4" id="menu-container">
                            <?php 
                            foreach ($menu_items as $item) {
                                $category_class = $menu_categories[$item['category']]['item_class'] ?? '';
                                render_menu_item($item, $category_class);
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Filtered Panes -->
                    <?php foreach ($menu_categories as $key => $cat): ?>
                        <div class="tab-pane fade" id="item_<?php echo $key; ?>">
                            <div class="row g-4" id="<?php echo $key; ?>-container">
                                <?php 
                                foreach ($menu_items as $item) {
                                  if ($item['category'] == $key) {
                     render_menu_item($item, $cat['item_class']);
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Reservation Section -->
        <section id="reservation" class="reservation-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-5 mb-lg-0" data-aos="fade-left">
                        <h2 class="display-5 fw-bold mb-4">احجز طاولتك الآن</h2>
                        <p class="lead mb-4">استمتع بتجربة طعام فريدة مع عائلتك وأصدقائك. احجز مكانك مسبقاً لضمان أفضل خدمة.</p>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-telephone-fill fs-3 text-primary me-3"></i>
                            <div>
                                    <h5 class="mb-0">اتصل بنا</h5>
                                <p class="mb-0"><?php echo RESTAURANT_PHONE_DISPLAY; ?></p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock-fill fs-3 text-primary me-3"></i>
                            <div>
                                <h5 class="mb-0">ساعات العمل</h5>
                                <p class="mb-0"><?php echo RESTAURANT_HOURS; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-right">
                        <form id="resForm" class="reservation-form">
                            <div class="mb-3">
                                <label class="form-label">الاسم الكامل</label>
                                <input type="text" class="form-control" required placeholder="أدخل اسمك">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">رقم الهاتف</label>
                                <input type="tel" class="form-control" required placeholder="01xxxxxxxxx">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">التاريخ</label>
                                    <input type="date" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">عدد الأفراد</label>
                                    <input type="number" class="form-control" min="1" max="20" value="2" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-custom btn-primary-custom w-100 mt-3">تأكيد الحجز</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-5">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>اتصل بنا</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-6" data-aos="fade-left">
                        <div class="card border-0 shadow-sm p-4 h-100">
                            <h3>أين تجدنا؟</h3>
                            <p class="text-muted">نحن في قلب المدينة، ننتظر زيارتكم.</p>
                            <p><i class="bi bi-geo-alt-fill text-primary me-2"></i> <?php echo RESTAURANT_ADDRESS; ?></p>
                            <div class="ratio ratio-16x9 rounded overflow-hidden mt-3">
                                <iframe src="<?php echo MAPS_EMBED_URL; ?>" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" data-aos="fade-right">
                        <div class="card border-0 shadow-sm p-4 h-100 text-center d-flex flex-column justify-content-center">
                            <h3>تابعنا على التواصل الاجتماعي</h3>
                            <p class="text-muted mb-4">كن أول من يعرف عن عروضنا الجديدة</p>
                            <div class="social-links">
                                <a href="<?php echo SOCIAL_FACEBOOK; ?>" target="_blank" class="facebook"><i class="bi bi-facebook"></i></a>
                                <a href="<?php echo SOCIAL_INSTAGRAM; ?>" class="instagram"><i class="bi bi-instagram"></i></a>
                                <a href="<?php echo SOCIAL_TWITTER; ?>" class="twitter"><i class="bi bi-twitter-x"></i></a>
                                <a href="<?php echo SOCIAL_TIKTOK; ?>" class="tiktok"><i class="bi bi-tiktok"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php
require_once 'includes/footer.php';
?>
