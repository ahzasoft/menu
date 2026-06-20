<?php
// Administrative Sync Script for Royal Taste Restaurant Menu
require_once 'config.php';
require_once 'includes/db_helper.php';

// Initialize the database tables if not done
initialize_database();

$sync_result = null;

// Trigger sync if request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'sync') {
    $sync_result = fetch_and_sync_from_server();
}

$last_sync = get_setting('last_sync_time');
$categories_count = 0;
$items_count = 0;

try {
    $pdo = get_db_connection();
    $categories_count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    $items_count = $pdo->query("SELECT COUNT(*) FROM items")->fetchColumn();
} catch (Exception $e) {
    // Silent fail
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تزامن لوحة التحكم | مطعم الذوق الملكي</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #ff5722;
            --secondary-color: #ffb300;
            --dark-color: #1a1a1a;
            --success-color: #2e7d32;
            --card-shadow: 0 10px 30px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }

        .sync-card {
            background: white;
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(0,0,0,0.05);
            max-width: 650px;
            width: 100%;
            overflow: hidden;
        }

        .sync-header {
            background: linear-gradient(135deg, var(--dark-color), #2d2d2d);
            padding: 30px;
            text-align: center;
            color: white;
            position: relative;
        }

        .sync-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--secondary-color);
        }

        .sync-body {
            padding: 40px 30px;
        }

        .stat-box {
            background-color: #fcfcfc;
            border: 1px solid #f1f1f1;
            border-radius: 16px;
            padding: 15px;
            text-align: center;
            transition: var(--transition);
        }

        .stat-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
            border-color: #e0e0e0;
        }

        .stat-icon {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .stat-num {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        .stat-label {
            font-size: 0.85rem;
            color: #666;
        }

        .info-list {
            list-style: none;
            padding: 0;
            margin: 25px 0;
            font-size: 0.9rem;
        }

        .info-list li {
            padding: 12px 15px;
            background: #fdfdfd;
            border: 1px solid #f5f5f5;
            border-radius: 10px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-label {
            font-weight: 600;
            color: #555;
        }

        .info-val {
            color: #111;
            font-family: monospace;
            word-break: break-all;
        }

        .btn-sync {
            background: var(--primary-color);
            border: none;
            color: white;
            font-weight: 700;
            padding: 15px 30px;
            border-radius: 50px;
            width: 100%;
            transition: var(--transition);
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 6px 20px rgba(255, 87, 34, 0.3);
        }

        .btn-sync:hover {
            background: #e64a19;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 87, 34, 0.4);
            color: white;
        }

        .btn-sync:active {
            transform: translateY(0);
        }

        .btn-sync .spinner-border {
            display: none;
        }

        .btn-sync.loading .spinner-border {
            display: inline-block;
        }

        .btn-sync.loading .btn-text {
            display: none;
        }

        .btn-sync.loading .bi-arrow-repeat {
            display: none;
        }

        .alert-custom {
            border-radius: 14px;
            padding: 15px 20px;
            font-size: 0.95rem;
            margin-bottom: 25px;
            border: none;
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: #666;
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .back-link a:hover {
            color: var(--primary-color);
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="sync-card">
        <div class="sync-header">
            <div class="fs-1 mb-2">🔄</div>
            <h1>مزامنة قائمة الطعام</h1>
            <p class="mb-0 text-white-50">تحديث فئات وأصناف الطعام من لوحة التحكم الرئيسية</p>
        </div>
        
        <div class="sync-body">
            <?php if ($sync_result !== null): ?>
                <?php if ($sync_result['success']): ?>
                    <div class="alert alert-success alert-custom d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                        <div>
                            <strong>تم التحديث بنجاح!</strong><br>
                            <?php echo htmlspecialchars($sync_result['message']); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger alert-custom d-flex align-items-center" role="alert">
                        <i class="bi text-danger bi-exclamation-triangle-fill fs-4 me-3"></i>
                        <div>
                            <strong>فشل التحديث!</strong><br>
                            <?php echo htmlspecialchars($sync_result['message']); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-6">
                    <div class="stat-box">
                        <div class="stat-icon"><i class="bi bi-grid-fill"></i></div>
                        <div class="stat-num"><?php echo $categories_count; ?></div>
                        <div class="stat-label">الفئات المتاحة</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box">
                        <div class="stat-icon"><i class="bi bi-egg-fried"></i></div>
                        <div class="stat-num"><?php echo $items_count; ?></div>
                        <div class="stat-label">الوجبات والأصناف</div>
                    </div>
                </div>
            </div>

            <ul class="info-list">
                <li>
                    <span class="info-label">مسار قاعدة البيانات (SQLite):</span>
                    <span class="info-val" title="<?php echo DB_PATH; ?>">
                        db/<?php echo basename(DB_PATH); ?>
                    </span>
                </li>
                <li>
                    <span class="info-label">رابط خادم البيانات (API):</span>
                    <span class="info-val" title="<?php echo API_DASHBOARD_URL; ?>">
                        <?php 
                        $parsed_url = parse_url(API_DASHBOARD_URL);
                        echo ($parsed_url['host'] ?? 'Local') . ($parsed_url['path'] ?? ''); 
                        ?>
                    </span>
                </li>
                <li>
                    <span class="info-label">آخر تحديث ناجح:</span>
                    <span class="info-val">
                        <?php echo $last_sync ? date('Y-m-d H:i:s', $last_sync) : 'لم يتم المزامنة مسبقاً'; ?>
                    </span>
                </li>
            </ul>

            <form method="POST" id="syncForm" onsubmit="startSync()">
                <input type="hidden" name="action" value="sync">
                <button type="submit" class="btn btn-sync" id="syncBtn">
                    <i class="bi bi-arrow-repeat fs-5"></i>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="btn-text">ابدأ المزامنة الآن</span>
                </button>
            </form>
            
            <div class="back-link">
                <a href="index.php"><i class="bi bi-arrow-right me-1"></i> العودة للموقع الرئيسي</a>
            </div>
        </div>
    </div>
</div>

<script>
function startSync() {
    const btn = document.getElementById('syncBtn');
    btn.classList.add('loading');
    btn.disabled = true;
}
</script>
</body>
</html>
