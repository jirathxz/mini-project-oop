<?php

namespace CardVault\Services;

use CardVault\Config\Database;
use CardVault\Repositories\OrderRepository;
use CardVault\Repositories\VoucherRepository;
use InvalidArgumentException;
use RuntimeException;
use PDO;

class CheckoutService
{
    private PDO $db;
    private OrderRepository $orderRepo;
    private VoucherRepository $voucherRepo;

    public function __construct(
        ?OrderRepository $orderRepo = null,
        ?VoucherRepository $voucherRepo = null,
        ?PDO $db = null
    ) {
        $this->db = $db ?? Database::getConnection();
        $this->orderRepo = $orderRepo ?? new OrderRepository($this->db);
        $this->voucherRepo = $voucherRepo ?? new VoucherRepository($this->db);
    }

    /**
     * Create order and reserve vouchers for 15 minutes
     * @param array $items Array of ['denomination_id' => int, 'quantity' => int]
     * @param string $guestEmail Buyer email
     * @param string $paymentMethod 'promptpay' or 'card'
     * @return array Order data
     */
    public function createOrder(array $items, string $guestEmail, string $paymentMethod = 'promptpay'): array
    {
        // 1. Email validation
        $email = trim($guestEmail);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("รูปแบบอีเมลไม่ถูกต้อง กรุณากรอกอีเมลที่ใช้งานได้จริง");
        }

        // 2. Validate items array
        if (empty($items)) {
            throw new InvalidArgumentException("ไม่มีรายการสินค้าในคำสั่งซื้อ");
        }

        // 3. Validate quantity per line item (1 to 10)
        foreach ($items as $item) {
            $qty = isset($item['quantity']) ? (int)$item['quantity'] : 0;
            if ($qty < 1 || $qty > 10) {
                throw new InvalidArgumentException("จำนวนสินค้าต้องอยู่ระหว่าง 1 ถึง 10 ชิ้นต่อรายการ");
            }
        }

        // 4. Calculate total & prepare item details
        $preparedItems = [];
        $totalAmount = 0.0;

        foreach ($items as $item) {
            $denomId = (int)$item['denomination_id'];
            $qty = (int)$item['quantity'];

            $stmt = $this->db->prepare("SELECT id, name, price FROM denominations WHERE id = ? LIMIT 1");
            $stmt->execute([$denomId]);
            $denom = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$denom) {
                throw new InvalidArgumentException("ไม่พบข้อมูลแพ็กเกจสินค้า ID: {$denomId}");
            }

            $price = (float)$denom['price'];
            $subtotal = $price * $qty;
            $totalAmount += $subtotal;

            $preparedItems[] = [
                'denomination_id' => $denomId,
                'name' => $denom['name'],
                'quantity' => $qty,
                'unit_price' => $price,
                'subtotal' => $subtotal
            ];
        }

        // 5. Transaction: create order + reserve vouchers
        $this->db->beginTransaction();

        try {
            $orderRef = 'CV-' . strtoupper(bin2hex(random_bytes(4)));
            $expiresAt = date('Y-m-d H:i:s', time() + 15 * 60); // 15-minute window

            $orderId = $this->orderRepo->createOrder($orderRef, $email, $totalAmount, $expiresAt, $paymentMethod);

            foreach ($preparedItems as $pItem) {
                $this->orderRepo->addOrderItem(
                    $orderId,
                    $pItem['denomination_id'],
                    $pItem['quantity'],
                    $pItem['unit_price'],
                    $pItem['subtotal']
                );

                // Reserve vouchers
                $this->voucherRepo->reserveVouchers(
                    $pItem['denomination_id'],
                    $pItem['quantity'],
                    $orderId,
                    $expiresAt
                );
            }

            $this->db->commit();

            return [
                'order_id' => $orderId,
                'order_ref' => $orderRef,
                'guest_email' => $email,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'expires_at' => $expiresAt,
                'expires_in_seconds' => 15 * 60,
                'items' => $preparedItems
            ];
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
