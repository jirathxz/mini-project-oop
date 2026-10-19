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

use CardVault\Services\OrderTrackingService;

$trackingService = new OrderTrackingService();

try {
    $orderRef = $_GET['order_ref'] ?? '';
    $email = $_GET['email'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?? [];
        $orderRef = $data['order_ref'] ?? $orderRef;
        $email = $data['email'] ?? $email;
    }

    $fulfillment = $trackingService->trackOrder($orderRef, $email);

    echo json_encode([
        'status' => 'success',
        'data' => $fulfillment
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (\InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการตรวจสอบ: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
