<?php

require_once __DIR__ . '/../../src/autoload.php';

use CardVault\Services\AdminService;

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$adminService = new AdminService();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Public action: login
if ($action === 'login' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    $admin = $adminService->authenticate($username, $password);
    if ($admin) {
        $_SESSION['admin_user'] = $admin;
        echo json_encode([
            'success' => true,
            'message' => 'เข้าสู่ระบบสำเร็จ',
            'admin' => $admin
        ], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// Public action: check current session auth status
if ($action === 'check_auth') {
    if (isset($_SESSION['admin_user'])) {
        echo json_encode([
            'authenticated' => true,
            'admin' => $_SESSION['admin_user']
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'authenticated' => false
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// Public action: logout
if ($action === 'logout') {
    unset($_SESSION['admin_user']);
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'ออกจากระบบเรียบร้อย'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Guard all subsequent actions with authentication check
if (!isset($_SESSION['admin_user'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'กรุณาเข้าสู่ระบบก่อนดำเนินการ'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    switch ($action) {
        case 'dashboard':
            $metrics = $adminService->getDashboardMetrics();
            echo json_encode(['success' => true, 'data' => $metrics], JSON_UNESCAPED_UNICODE);
            break;

        case 'stock_overview':
            $stock = $adminService->getStockOverview();
            echo json_encode(['success' => true, 'data' => $stock], JSON_UNESCAPED_UNICODE);
            break;

        case 'import_vouchers':
            if ($method !== 'POST') {
                throw new \BadMethodCallException("Method must be POST");
            }
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $denomId = (int)($input['denomination_id'] ?? 0);
            $codes = $input['codes'] ?? [];

            if (is_string($codes)) {
                // Split by newline if submitted as string from textarea
                $codes = explode("\n", $codes);
            }

            $imported = $adminService->bulkImportVouchers($denomId, (array)$codes);
            echo json_encode([
                'success' => true,
                'message' => "นำเข้ารหัสบัตรสำเร็จ {$imported} รายการ",
                'imported_count' => $imported
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'orders':
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
            $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
            $orders = $adminService->getOrders($limit, $offset);
            echo json_encode(['success' => true, 'data' => $orders], JSON_UNESCAPED_UNICODE);
            break;

        case 'order_details':
            $orderRef = $_GET['order_ref'] ?? '';
            $details = $adminService->getOrderDetails($orderRef);
            if (!$details) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'ไม่พบคำสั่งซื้อ'], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['success' => true, 'data' => $details], JSON_UNESCAPED_UNICODE);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action'], JSON_UNESCAPED_UNICODE);
            break;
    }
} catch (\Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
