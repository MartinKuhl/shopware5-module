<?php


namespace RpayRatePay\Helper;


use RpayRatePay\Component\Mapper\PaymentRequestData;
use RuntimeException;
use Shopware\Models\Order\Detail;
use Shopware\Models\Order\Order;
use SwagBackendOrder\Components\Order\Struct\PositionStruct;

class TaxHelper
{
    /**
     * @param float|int $netPrice
     * @param float|int $grossPrice
     * @return float|int
     */
    public static function taxFromPrices($netPrice, $grossPrice)
    {
        $tax = ($grossPrice - $netPrice) / $netPrice;
        return $tax * 100;
    }

    /**
     * @param Order|PaymentRequestData $order
     * @param Detail|PositionStruct $item
     * @return float
     */
    public static function getItemGrossPrice($order, $item, $price = null)
    {
        return $price ? $price : $item->getPrice();                                           //TODO tax calculation
        /*if($item instanceof Detail) {
        }*/
    }

    /**
     * @param Order|PaymentRequestData $order
     * @param Detail|PositionStruct $item
     * @return float|int
     */
    public static function getItemTaxRate($order, $item, $price = null)
    {
        if ($order instanceof Order) {
            // TODO verify if this is correct
            return $order->getNet() == 1 && $order->getTaxFree() == 1 ? 0 : $item->getTaxRate();
        }
        return $item->getTaxRate();                                           //TODO tax calculation
        /*if($item instanceof Detail) {
        }*/
    }

    /**
     * @param Order|PaymentRequestData $order
     * @return float
     */
    public static function getShippingGrossPrice($order)
    {
        if ($order instanceof Order) {
            return $order->getInvoiceShipping();
        } else if ($order instanceof PaymentRequestData) {                   //TODO tax calculation
            return $order->getShippingCost();
        } else {
            throw new RuntimeException('Invalid argument');
        }
    }

    /**
     * @param Order|PaymentRequestData $order
     * @return float|int
     */
    public static function getShippingTaxRate($order)
    {

        if ($order instanceof Order) {
            // TODO verify if this is correct
            $calculatedShippingTaxRate = self::taxFromPrices($order->getInvoiceShippingNet(), $order->getInvoiceShipping());
            return $calculatedShippingTaxRate > 0 ? $order->getInvoiceShippingTaxRate() : 0;
        } else if ($order instanceof PaymentRequestData) {                   //TODO tax calculation
            return $order->getShippingTax();
        } else {
            throw new RuntimeException('Invalid argument');
        }
    }


}
