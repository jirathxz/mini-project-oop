<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

require_once __DIR__ . '/../../src/autoload.php';

use CardVault\Services\CheckoutService;

try {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);

    if (!is_array($data)) {
        throw new \InvalidArgumentException("รูปแบบข้อมูล JSON ไม่ถูกต้อง");
    }

    $email = $data['email'] ?? '';
    $items = $data['items'] ?? [];
    $paymentMethod = $data['payment_method'] ?? 'promptpay';

    $checkoutService = new CheckoutService();
    $order = $checkoutService->createOrder($items, $email, $paymentMethod);

    echo json_encode([
        'status' => 'success',
        'message' => 'สร้างคำสั่งซื้อและจองรหัสสินค้าเรียบร้อยแล้ว',
        'data' => $order
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (\InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} catch (\RuntimeException $e) {
    http_response_code(409); // Conflict / Out of stock
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาดในการประมวลผลคำสั่งซื้อ: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
