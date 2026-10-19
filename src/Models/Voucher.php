<?php

namespace CardVault\Models;

class Voucher
{
    private int $id;
    private int $denominationId;
    private string $code;
    private ?string $serial;
    private string $status;
    private ?int $orderId;
    private ?string $reservedUntil;

    public function __construct(
        int $id,
        int $denominationId,
        string $code,
        ?string $serial = null,
        string $status = 'available',
        ?int $orderId = null,
        ?string $reservedUntil = null
    ) {
        $this->id = $id;
        $this->denominationId = $denominationId;
        $this->code = $code;
        $this->serial = $serial;
        $this->status = $status;
        $this->orderId = $orderId;
        $this->reservedUntil = $reservedUntil;
    }

    public function getId(): int { return $this->id; }
    public function getDenominationId(): int { return $this->denominationId; }
    public function getCode(): string { return $this->code; }
    public function getSerial(): ?string { return $this->serial; }
    public function getStatus(): string { return $this->status; }
    public function getOrderId(): ?int { return $this->orderId; }
    public function getReservedUntil(): ?string { return $this->reservedUntil; }

    public function isSold(): bool { return $this->status === 'sold'; }

    /**
     * Reveal PIN code only if voucher is paid/sold; otherwise mask it.
     */
    public function getMaskedCode(): string
    {
        if ($this->isSold()) {
            return $this->code;
        }
        return '••••-••••-••••-' . substr($this->code, -4);
    }
}
