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
 * Fetch data from server API and overwrite local tables
 */
function fetch_and_sync_from_server() {
    $url = API_DASHBOARD_URL;
    if (empty($url)) {
        return ['success' => false, 'message' => 'API Dashboard URL is not defined in config.php.'];
    }
    
    // Configure API stream context with timeout
    $options = [
        "http" => [
            "method" => "GET",
            "header" => "Accept: application/json\r\nUser-Agent: PHP-API-Sync-Agent\r\n",
            "timeout" => 15
        ]
    ];
    $context = stream_context_create($options);
    
    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        $error = error_get_last();
        return [
            'success' => false,
            'message' => 'Could not fetch data from ' . $url . '. Error: ' . ($error['message'] ?? 'unknown error')
        ];
    }
    
    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'message' => 'Invalid JSON received from server API: ' . json_last_error_msg()
        ];
    }
    
    if (!isset($data['categories']) || !is_array($data['categories'])) {
        return ['success' => false, 'message' => 'Malformed API response: "categories" array missing.'];
    }
    if (!isset($data['items']) || !is_array($data['items'])) {
        return ['success' => false, 'message' => 'Malformed API response: "items" array missing.'];
    }
    
    $pdo = get_db_connection();
    try {
        $pdo->beginTransaction();
        
        // Truncate existing items and categories
        $pdo->exec("DELETE FROM items");
        $pdo->exec("DELETE FROM categories");
        
        // Insert Categories
        $stmt_cat = $pdo->prepare("INSERT INTO categories (id, name, image, item_class) VALUES (:id, :name, :image, :item_class)");
        foreach ($data['categories'] as $cat) {
            if (!isset($cat['id']) || !isset($cat['name'])) {
                continue;
            }
            $stmt_cat->execute([
                ':id' => $cat['id'],
                ':name' => $cat['name'],
                ':image' => $cat['image'] ?? $cat['image_url'] ?? '',
                ':item_class' => $cat['item_class'] ?? ($cat['id'] . '-item')
            ]);
        }
        
        // Insert Items
        $stmt_item = $pdo->prepare("INSERT INTO items (id, category_id, name, price, discount, image_url, description, delay) VALUES (:id, :category_id, :name, :price, :discount, :image_url, :description, :delay)");
        $delay_count = [];
        
        foreach ($data['items'] as $item) {
            if (!isset($item['id']) || !isset($item['category_id']) || !isset($item['name']) || !isset($item['price'])) {
                continue;
            }
            
            $cat_id = $item['category_id'];
            if (!isset($delay_count[$cat_id])) {
                $delay_count[$cat_id] = 0;
            }
            $delay = isset($item['delay']) ? (int)$item['delay'] : ($delay_count[$cat_id] * 100);
            $delay_count[$cat_id]++;
            
            $stmt_item->execute([
                ':id' => $item['id'],
                ':category_id' => $cat_id,
                ':name' => $item['name'],
                ':price' => floatval($item['price']),
                ':discount' => floatval($item['discount'] ?? 0),
                ':image_url' => $item['image_url'] ?? $item['image'] ?? '',
                ':description' => $item['description'] ?? '',
                ':delay' => $delay
            ]);
        }
        
        $pdo->commit();
        
        // Update sync timestamp
        set_setting('last_sync_time', time());
        
        return [
            'success' => true,
            'message' => 'Successfully synchronized ' . count($data['categories']) . ' categories and ' . count($data['items']) . ' items with server API.'
        ];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
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
