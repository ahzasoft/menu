<?php
// SQLite Database helper functions for Royal Taste Restaurant

require_once __DIR__ . '/../config.php';

/**
 * Get PDO Database Connection for SQLite
 */
function get_db_connection() {
    $db_dir = dirname(DB_PATH);
    if (!is_dir($db_dir)) {
        mkdir($db_dir, 0755, true);
    }
    
    try {
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection to SQLite failed: " . $e->getMessage());
    }
}

/**
 * Initialize SQLite tables and seed default data if empty
 */
function initialize_database() {
    $pdo = get_db_connection();
    
    // Create categories table
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id TEXT PRIMARY KEY,
        name TEXT NOT NULL,
        image TEXT,
        item_class TEXT
    )");
    
    // Create items table
    $pdo->exec("CREATE TABLE IF NOT EXISTS items (
        id TEXT PRIMARY KEY,
        category_id TEXT NOT NULL,
        name TEXT NOT NULL,
        price REAL NOT NULL,
        discount REAL DEFAULT 0,
        image_url TEXT,
        description TEXT,
        delay INTEGER DEFAULT 0,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )");
    
    // Create settings table
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        key TEXT PRIMARY KEY,
        value TEXT
    )");
    
    // Check if categories are empty to seed default data
    $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
    $count = $stmt->fetchColumn();
    if ($count == 0) {
        $menu_data_file = dirname(__DIR__) . '/menu_data.php';
        if (file_exists($menu_data_file)) {
            include $menu_data_file;
            
            if (isset($menu_categories)) {
                $stmt_cat = $pdo->prepare("INSERT OR IGNORE INTO categories (id, name, image, item_class) VALUES (:id, :name, :image, :item_class)");
                foreach ($menu_categories as $key => $cat) {
                    $stmt_cat->execute([
                        ':id' => $key,
                        ':name' => $cat['name'],
                        ':image' => $cat['image'] ?? 'imgs/default-category.jpg',
                        ':item_class' => $cat['item_class'] ?? ($key . '-item')
                    ]);
                }
            }
            
            if (isset($menu_items)) {
                $stmt_item = $pdo->prepare("INSERT OR IGNORE INTO items (id, category_id, name, price, discount, image_url, description, delay) VALUES (:id, :category_id, :name, :price, :discount, :image_url, :description, :delay)");
                foreach ($menu_items as $item) {
                    $stmt_item->execute([
                        ':id' => $item['id'],
                        ':category_id' => $item['category'],
                        ':name' => $item['name'],
                        ':price' => $item['price'],
                        ':discount' => $item['discount'] ?? 0,
                        ':image_url' => $item['image'],
                        ':description' => $item['description'] ?? '',
                        ':delay' => (int)($item['delay'] ?? 0)
                    ]);
                }
            }
            
            // Set initial sync time
            set_setting('last_sync_time', time());
        }
    }
}

/**
 * Retrieve database setting value
 */
function get_setting($key, $default = null) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE key = :key");
    $stmt->execute([':key' => $key]);
    $val = $stmt->fetchColumn();
    return $val !== false ? $val : $default;
}

/**
 * Set database setting value
 */
function set_setting($key, $value) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (:key, :value)");
    $stmt->execute([':key' => $key, ':value' => $value]);
}

/**
 * Download a remote image into the local imgs/ directory.
 * Returns the local relative path (e.g. "imgs/filename.jpg") or empty string on failure.
 */
function download_image($url) {
    if (empty($url)) return '';

    // Ensure imgs directory exists
    $imgs_dir = defined('IMGS_DIR') ? IMGS_DIR : dirname(__DIR__) . '/imgs';
    if (!is_dir($imgs_dir)) {
        mkdir($imgs_dir, 0755, true);
    }

    // Build a safe local filename from the URL
    $parsed   = parse_url($url);
    $basename = basename($parsed['path'] ?? 'image.jpg');
    // Sanitize: keep only safe characters
    $basename = preg_replace('/[^A-Za-z0-9._-]/', '_', $basename);
    if (empty($basename) || $basename === '_') {
        $basename = md5($url) . '.jpg';
    }

    $local_path = $imgs_dir . '/' . $basename;
    $relative   = 'imgs/' . $basename;

    // Skip download if already cached locally
    if (file_exists($local_path)) {
        return $relative;
    }

    $options = [
        "http" => [
            "method"  => "GET",
            "header"  => "User-Agent: PHP-Image-Downloader\r\n",
            "timeout" => 20
        ]
    ];
    $ctx     = stream_context_create($options);
    $content = @file_get_contents($url, false, $ctx);
    if ($content !== false) {
        file_put_contents($local_path, $content);
        return $relative;
    }
    return ''; // failed – return empty so the app can use a placeholder
}

/**
 * Helper: fetch JSON from a URL. Returns decoded array or throws RuntimeException.
 */
function fetch_json_api($url) {
    $options = [
        "http" => [
            "method"  => "GET",
            "header"  => "Accept: application/json\r\nUser-Agent: PHP-API-Sync-Agent\r\n",
            "timeout" => 20
        ]
    ];
    $context  = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        $error = error_get_last();
        throw new RuntimeException('Could not fetch ' . $url . ': ' . ($error['message'] ?? 'unknown error'));
    }
    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new RuntimeException('Invalid JSON from ' . $url . ': ' . json_last_error_msg());
    }
    if (empty($data['success']) || !isset($data['data']) || !is_array($data['data'])) {
        throw new RuntimeException('API returned failure or unexpected structure from ' . $url);
    }
    return $data['data'];
}

/**
 * Fetch data from both API endpoints (categories + products),
 * download product images, and overwrite local SQLite tables.
 */
function fetch_and_sync_from_server() {
    if (!defined('API_CATEGORIES_URL') || !defined('API_PRODUCTS_URL')) {
        return ['success' => false, 'message' => 'API endpoint constants are not defined in config.php.'];
    }

    // ── 1. Fetch categories ──────────────────────────────────────────
    try {
        $categories = fetch_json_api(API_CATEGORIES_URL);
    } catch (RuntimeException $e) {
        return ['success' => false, 'message' => 'Categories API error: ' . $e->getMessage()];
    }

    // ── 2. Fetch products ────────────────────────────────────────────
    try {
        $products = fetch_json_api(API_PRODUCTS_URL);
    } catch (RuntimeException $e) {
        return ['success' => false, 'message' => 'Products API error: ' . $e->getMessage()];
    }

    $pdo = get_db_connection();
    try {
        $pdo->beginTransaction();

        // Clear existing data
        $pdo->exec("DELETE FROM items");
        $pdo->exec("DELETE FROM categories");

        // ── 3. Insert categories ─────────────────────────────────────
        $stmt_cat = $pdo->prepare(
            "INSERT INTO categories (id, name, image, item_class) VALUES (:id, :name, :image, :item_class)"
        );
        $cats_inserted = 0;
        foreach ($categories as $cat) {
            if (!isset($cat['id'], $cat['name'])) continue;

            // Download category image if present
            $img_remote = $cat['image_url'] ?? $cat['image'] ?? '';
            $img_local  = !empty($img_remote) ? download_image($img_remote) : '';

            // Build a CSS-safe item_class from the numeric id
            $item_class = $cat['item_class'] ?? ('cat-' . $cat['id'] . '-item');

            $stmt_cat->execute([
                ':id'         => (string)$cat['id'],
                ':name'       => $cat['name'],
                ':image'      => $img_local,
                ':item_class' => $item_class,
            ]);
            $cats_inserted++;
        }

        // ── 4. Insert products + download images ─────────────────────
        $stmt_item = $pdo->prepare(
            "INSERT INTO items (id, category_id, name, price, discount, image_url, description, delay)
             VALUES (:id, :category_id, :name, :price, :discount, :image_url, :description, :delay)"
        );
        $delay_count   = [];
        $items_inserted = 0;
        foreach ($products as $item) {
            if (!isset($item['id'], $item['category_id'], $item['name'], $item['price'])) continue;
            if (($item['status'] ?? 1) == 0) continue; // skip inactive items

            $cat_id = (string)$item['category_id'];
            if (!isset($delay_count[$cat_id])) $delay_count[$cat_id] = 0;
            $delay = (int)($item['item_order'] ?? $delay_count[$cat_id]) * 100;
            $delay_count[$cat_id]++;

            // Download product image
            $img_remote = $item['image_url'] ?? '';
            $img_local  = !empty($img_remote) ? download_image($img_remote) : '';

            $stmt_item->execute([
                ':id'          => (string)$item['id'],
                ':category_id' => $cat_id,
                ':name'        => $item['name'],
                ':price'       => floatval($item['price']),
                ':discount'    => floatval($item['discount'] ?? 0),
                ':image_url'   => $img_local,
                ':description' => $item['description'] ?? '',
                ':delay'       => $delay,
            ]);
            $items_inserted++;
        }

        $pdo->commit();
        set_setting('last_sync_time', time());

        return [
            'success' => true,
            'message' => "تمت المزامنة بنجاح: {$cats_inserted} فئة و {$items_inserted} صنف. تم تحميل الصور إلى مجلد imgs/."
        ];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        return [
            'success' => false,
            'message' => 'Transaction failed during sync: ' . $e->getMessage()
        ];
    }
}

/**
 * Retrieve categories from SQLite database
 */
function get_categories() {
    initialize_database();
    $pdo = get_db_connection();
    $stmt = $pdo->query("SELECT * FROM categories");
    $categories = [];
    while ($row = $stmt->fetch()) {
        $categories[$row['id']] = [
            'name' => $row['name'],
            'image' => $row['image'],
            'item_class' => $row['item_class']
        ];
    }
    return $categories;
}

/**
 * Retrieve items from SQLite database
 */
function get_all_items() {
    initialize_database();
    $pdo = get_db_connection();
    $stmt = $pdo->query("SELECT * FROM items");
    $items = [];
    while ($row = $stmt->fetch()) {
        $items[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => floatval($row['price']),
            'discount' => floatval($row['discount']),
            'image' => $row['image_url'],
            'description' => $row['description'],
            'category' => $row['category_id'],
            'delay' => (int)$row['delay']
        ];
    }
    return $items;
}
