<?php

namespace CardVault\Services\Payment;

use CardVault\Contracts\PaymentGatewayInterface;

class PromptPayGateway implements PaymentGatewayInterface
{
    private string $merchantId;

    public function __construct(string $merchantId = '0812345678')
    {
        $this->merchantId = $merchantId;
    }

    public function getMethodCode(): string
    {
        return 'promptpay';
    }

    public function getMethodName(): string
    {
        return 'พร้อมเพย์ QR Code';
    }

    public function generatePaymentPayload(string $orderRef, float $amount): array
    {
        // Standard Thai PromptPay simulated QR payload
        $formattedAmount = number_format($amount, 2, '.', '');
        $qrPayload = sprintf("00020101021129370016A0000006770101110113%s5802TH530376454%02d%s5802TH62%02d07%02d%s6304ABCD",
            $this->merchantId,
            strlen($formattedAmount),
            $formattedAmount,
            strlen($orderRef) + 4,
            strlen($orderRef),
            $orderRef
        );

        return [
            'method' => $this->getMethodCode(),
            'name' => $this->getMethodName(),
            'order_ref' => $orderRef,
            'amount' => $amount,
            'qr_payload' => $qrPayload,
            'fee' => 0.00,
            'merchant_account' => $this->merchantId,
            'instructions' => 'เปิดแอปธนาคาร สแกน QR Code เพื่อชำระเงินตามยอดที่ระบุ'
        ];
    }

    public function verifyPayment(string $orderRef, float $amount): array
    {
        return [
            'success' => true,
            'method' => $this->getMethodCode(),
            'order_ref' => $orderRef,
            'amount' => $amount,
            'settled_at' => date('Y-m-d H:i:s'),
            'transaction_id' => 'TXN-' . strtoupper(bin2hex(random_bytes(6)))
        ];
    }
}
