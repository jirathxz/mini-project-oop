<?php

namespace CardVault\Models;

class Order
{
    private int $id;
    private string $orderRef;
    private string $guestEmail;
    private float $totalAmount;
    private string $paymentMethod;
    private string $status;
    private ?string $paidAt;
    private string $expiresAt;
    private string $createdAt;

    public function __construct(
        int $id,
        string $orderRef,
        string $guestEmail,
        float $totalAmount,
        string $paymentMethod = 'promptpay',
        string $status = 'pending',
        ?string $paidAt = null,
        string $expiresAt = '',
        string $createdAt = ''
    ) {
        $this->id = $id;
        $this->orderRef = $orderRef;
        $this->guestEmail = $guestEmail;
        $this->totalAmount = $totalAmount;
        $this->paymentMethod = $paymentMethod;
        $this->status = $status;
        $this->paidAt = $paidAt;
        $this->expiresAt = $expiresAt;
        $this->createdAt = $createdAt;
    }

    public function getId(): int { return $this->id; }
    public function getOrderRef(): string { return $this->orderRef; }
    public function getGuestEmail(): string { return $this->guestEmail; }
    public function getTotalAmount(): float { return $this->totalAmount; }
    public function getPaymentMethod(): string { return $this->paymentMethod; }
    public function getStatus(): string { return $this->status; }
    public function getPaidAt(): ?string { return $this->paidAt; }
    public function getExpiresAt(): string { return $this->expiresAt; }
    public function getCreatedAt(): string { return $this->createdAt; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'order_ref' => $this->orderRef,
            'guest_email' => $this->guestEmail,
            'total_amount' => $this->totalAmount,
            'payment_method' => $this->paymentMethod,
            'status' => $this->status,
            'paid_at' => $this->paidAt,
            'expires_at' => $this->expiresAt,
            'created_at' => $this->createdAt,
        ];
    }
}
