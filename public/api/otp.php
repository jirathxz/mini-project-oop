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

use CardVault\Services\OtpService;

$otpService = new OtpService();

try {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true) ?? [];
    $action = $data['action'] ?? '';
    $email = $data['email'] ?? '';

    if ($action === 'request') {
        $result = $otpService->requestOtp($email);
        echo json_encode([
            'status' => 'success',
            'message' => "ส่งรหัส OTP 6 หลักไปยังอีเมล {$result['email']} เรียบร้อยแล้ว (จำลองรหัส: {$result['otp_code']})",
            'data' => [
                'email' => $result['email'],
                'expires_at' => $result['expires_at'],
                'demo_otp' => $result['otp_code'] // For instant testing without external mail server
            ]
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    if ($action === 'verify') {
        $otpCode = $data['otp_code'] ?? '';
        $isVerified = $otpService->verifyOtp($email, $otpCode);

        if (!$isVerified) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'รหัส OTP ไม่ถูกต้องหรือหมดอายุแล้ว'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Return order history upon successful verification
        $history = $otpService->getBuyerOrderHistory($email);

        echo json_encode([
            'status' => 'success',
            'message' => 'ยืนยันรหัส OTP สำเร็จ',
            'data' => [
                'email' => $email,
                'orders' => $history
            ]
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    throw new \InvalidArgumentException("ไม่ระบุ Action (รองรับ: request, verify)");
} catch (\InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
