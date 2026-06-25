<?php
// Configuration settings for Royal Taste Restaurant

define('APP_NAME', 'Montex Soft QR Menu');
define('APP_TITLE', 'Montex Soft QR Menu - مطعم الذوق الملكي');
define('APP_DESCRIPTION', 'تصفح قائمة طعام مطعم الذوق الملكي مباشرة عبر رمز الاستجابة السريع QR واطلب وجبتك المفضلة عبر الواتساب. منيو رقمي تفاعلي سريع وسهل الاستخدام من تطوير Montex Soft.');

// Contact Info
define('RESTAURANT_PHONE', '201024649844');
define('RESTAURANT_PHONE_DISPLAY', '201024649844');
define('RESTAURANT_EMAIL', 'Montexsoft@gmail.com');
define('RESTAURANT_ADDRESS', 'شارع الملك، وسط المدينة، القاهرة');
define('RESTAURANT_HOURS', 'يومياً من 10 صباحاً حتى 2 بعد منتصف الليل');

// Social Media Links
define('SOCIAL_FACEBOOK', 'https://www.facebook.com/Montexsoft/?locale=ar_AR');
define('SOCIAL_INSTAGRAM', '#');
define('SOCIAL_TWITTER', '#');
define('SOCIAL_TIKTOK', '#');

// Google Maps Embed URL
define('MAPS_EMBED_URL', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13814.282054790387!2d31.2357116!3d30.0444196!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x145840c6a2640247%3A0x3802512f558bc0ad!2sCairo%2C%20Egypt!5e0!3m2!1sen!2sus!4v1634567890123');

// Database & API Sync Settings
define('DB_PATH', __DIR__ . '/db/menu.sqlite');
define('API_DASHBOARD_URL', 'file:///' . __DIR__ . '/api_sample.json'); // Point to local sample file for offline testing and setup
define('API_SYNC_INTERVAL', 3600); // Auto-sync cache interval in seconds (1 hour)
?>


