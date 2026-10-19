<?php

namespace CardVault\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Unique identifier code for the payment gateway (e.g., 'promptpay', 'truemoney')
     */
    public function getMethodCode(): string;

    /**
     * Human-readable display name for the customer
     */
    public function getMethodName(): string;

    /**
     * Generate payment instruction or QR/payload for the order
     */
    public function generatePaymentPayload(string $orderRef, float $amount): array;

    /**
     * Verify payment status or simulate settlement
     */
    public function verifyPayment(string $orderRef, float $amount): array;
}
