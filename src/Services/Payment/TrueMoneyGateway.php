<?php

namespace CardVault\Services\Payment;

use CardVault\Contracts\PaymentGatewayInterface;

class TrueMoneyGateway implements PaymentGatewayInterface
{
    private string $walletNumber;

    public function __construct(string $walletNumber = '0987654321')
    {
        $this->walletNumber = $walletNumber;
    }

    public function getMethodCode(): string
    {
        return 'truemoney';
    }

    public function getMethodName(): string
    {
        return 'ทรูมันนี่ วอลเล็ท (TrueMoney)';
    }

    public function generatePaymentPayload(string $orderRef, float $amount): array
    {
        $barcodePayload = 'TMN' . date('Ymd') . strtoupper(substr(md5($orderRef), 0, 10));

        return [
            'method' => $this->getMethodCode(),
            'name' => $this->getMethodName(),
            'order_ref' => $orderRef,
            'amount' => $amount,
            'barcode' => $barcodePayload,
            'wallet_account' => $this->walletNumber,
            'fee' => 0.00,
            'instructions' => 'โอนเงินผ่าน TrueMoney Wallet หรือสแกนบาร์โค้ดชำระเงิน'
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
            'transaction_id' => 'TMN-' . strtoupper(bin2hex(random_bytes(6)))
        ];
    }
}
