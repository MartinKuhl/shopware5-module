<?php

namespace RpayRatePay\Enum;

use RuntimeException;
use Shopware\Models\Payment\Payment;

final class PaymentMethods extends Enum
{

    const PAYMENT_INVOICE = 'rpayratepayinvoice';
    const PAYMENT_RATE = 'rpayratepayrate';
    const PAYMENT_DEBIT = 'rpayratepaydebit';
    const PAYMENT_INSTALLMENT0 = 'rpayratepayrate0';
    const PAYMENT_PREPAYMENT = 'rpayratepayprepayment';

    const PAYMENTS = [
        self::PAYMENT_INVOICE => [
            'name' => self::PAYMENT_INVOICE,
            'description' => 'Rechnung',
            'action' => 'rpay_ratepay',
            'active' => 0,
            'position' => 1,
            'additionalDescription' => 'Kauf auf Rechnung',
            'template' => 'RatePAYInvoice.tpl',
            'ratepay' => [
                'methodName' => 'INVOICE'
            ]
        ],
        self::PAYMENT_RATE => [
            'name' => self::PAYMENT_RATE,
            'description' => 'Ratenzahlung',
            'action' => 'rpay_ratepay',
            'active' => 0,
            'position' => 2,
            'additionalDescription' => 'Kauf auf Ratenzahlung',
            'template' => 'RatePAYRate.tpl',
            'ratepay' => [
                'methodName' => 'INSTALLMENT'
            ]
        ],
        self::PAYMENT_DEBIT => [
            'name' => self::PAYMENT_DEBIT,
            'description' => 'Lastschrift',
            'action' => 'rpay_ratepay',
            'active' => 0,
            'position' => 3,
            'additionalDescription' => 'Kauf auf SEPA Lastschrift',
            'template' => 'RatePAYDebit.tpl',
            'ratepay' => [
                'methodName' => 'ELV'
            ]
        ],
        self::PAYMENT_INSTALLMENT0 => [
            'name' => self::PAYMENT_INSTALLMENT0,
            'description' => '0% Finanzierung',
            'action' => 'rpay_ratepay',
            'active' => 0,
            'position' => 4,
            'additionalDescription' => 'Kauf per 0% Finanzierung',
            'template' => 'RatePAYRate.tpl',
            'ratepay' => [
                'methodName' => 'INSTALLMENT0'
            ]
        ],
        self::PAYMENT_PREPAYMENT => [
            'name' => self::PAYMENT_PREPAYMENT,
            'description' => 'Vorkasse',
            'action' => 'rpay_ratepay',
            'active' => 0,
            'position' => 5,
            'additionalDescription' => 'Kauf per Vorkasse',
            'template' => 'RatePAYPrepayment.tpl',
            'ratepay' => [
                'methodName' => 'PREPAYMENT'
            ]
        ],
    ];

    public static function getNames()
    {
        return array_keys(self::PAYMENTS);
    }

    /**
     * @param string|Payment $paymentMethod
     * @return boolean
     */
    public static function exists($paymentMethod)
    {
        $paymentMethod = $paymentMethod instanceof Payment ? $paymentMethod->getName() : $paymentMethod;
        return array_key_exists($paymentMethod, self::PAYMENTS);
    }

    /**
     * @param string|Payment $paymentMethod
     * @return string
     */
    public static function getRatepayPaymentMethod($paymentMethod)
    {
        $paymentMethod = $paymentMethod instanceof Payment ? $paymentMethod->getName() : $paymentMethod;
        if (!self::exists($paymentMethod)) {
            throw new RuntimeException('the method ' . $paymentMethod . ' is not a ratepay payment method');
        }
        return self::PAYMENTS[$paymentMethod]['ratepay']['methodName'];
    }

    public static function isInstallment($paymentMethod)
    {
        $paymentMethod = $paymentMethod instanceof Payment ? $paymentMethod->getName() : $paymentMethod;
        return in_array($paymentMethod, [self::PAYMENT_INSTALLMENT0, self::PAYMENT_RATE]);
    }

}
