<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../../src/autoload.php';

use CardVault\Services\PaymentService;

$service = new PaymentService();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?? [];
        $orderRef = $data['order_ref'] ?? '';

        if (empty($orderRef)) {
            throw new \InvalidArgumentException("ไม่ระบุหมายเลขคำสั่งซื้อ (order_ref)");
        }

        $res = $service->confirmPayment($orderRef);
        $fulfillment = $service->getOrderFulfillment($orderRef);

        echo json_encode([
            'status' => 'success',
            'message' => 'ยืนยันการชำระเงินเรียบร้อยแล้ว ได้รับรหัส PIN ทันที',
            'data' => $fulfillment
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $orderRef = $_GET['order_ref'] ?? '';
        if (empty($orderRef)) {
            throw new \InvalidArgumentException("ไม่ระบุหมายเลขคำสั่งซื้อ (order_ref)");
        }

        $fulfillment = $service->getOrderFulfillment($orderRef);

        echo json_encode([
            'status' => 'success',
            'data' => $fulfillment
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
} catch (\InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (\RuntimeException $e) {
    http_response_code(410); // Gone / Expired
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
