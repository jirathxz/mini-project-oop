<?php

namespace CardVault\Services\Payment;

use CardVault\Contracts\PaymentGatewayInterface;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    /**
     * @var array<string, class-string<PaymentGatewayInterface>>
     */
    private static array $registry = [
        'promptpay' => PromptPayGateway::class,
        'truemoney' => TrueMoneyGateway::class,
    ];

    /**
     * Factory method: Instantiate a payment gateway by method code
     */
    public static function create(string $method = 'promptpay'): PaymentGatewayInterface
    {
        $normalized = strtolower(trim($method));

        if (!isset(self::$registry[$normalized])) {
            throw new InvalidArgumentException("ไม่พบช่องทางการชำระเงินประเภท: {$method}");
        }

        $className = self::$registry[$normalized];
        return new $className();
    }

    /**
     * Register a new gateway at runtime (Open-Closed Principle)
     */
    public static function registerGateway(string $code, string $className): void
    {
        if (!is_subclass_of($className, PaymentGatewayInterface::class)) {
            throw new InvalidArgumentException("Gateway class ต้อง implement PaymentGatewayInterface");
        }
        self::$registry[strtolower(trim($code))] = $className;
    }

    /**
     * Get list of all available payment methods
     */
    public static function getAvailableGateways(): array
    {
        $list = [];
        foreach (self::$registry as $code => $class) {
            /** @var PaymentGatewayInterface $instance */
            $instance = new $class();
            $list[] = [
                'code' => $instance->getMethodCode(),
                'name' => $instance->getMethodName(),
            ];
        }
        return $list;
    }
}
