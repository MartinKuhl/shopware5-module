<?php

namespace RpayRatePay\Component\Service;

use RpayRatePay\Services\Config\ConfigService;
use Shopware;
use Shopware\Models\Customer\Customer;

/**
 * This program is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, see <http://www.gnu.org/licenses/>.
 */
class ShopwareUtil
{
    /**
     * @deprecated
     */
    protected $debitPayTypes = [
        '2' => 'DIRECT-DEBIT',
        '28' => 'BANK-TRANSFER',
        '2,28' => 'FIRSTDAY-SWITCH'
    ];

    /**
     * @return ConfigService
     */
    protected static function getConfigInstance()
    {
        return Shopware()->Container()->get(ConfigService::class);
    }

    /**
     * @deprecated
     */
    protected static function getLoggerInstance()
    {

    }

    /**
     * Return the methodname for RatePAY
     *
     * @return string
     */
    public static function getPaymentMethod($payment)
    {
        switch ($payment) {
            case 'rpayratepayinvoice':
                return 'INVOICE';
                break;
            case 'rpayratepayrate':
                return 'INSTALLMENT';
                break;
            case 'rpayratepaydebit':
                return 'ELV';
                break;
            case 'rpayratepayrate0':
                return 'INSTALLMENT0';
                break;
            case 'rpayratepayprepayment':
                return 'PREPAYMENT';
                break;
        }
    }

    /**
     * Return the debit pay type depending on payment first day
     *
     * @param $paymentFirstday
     * @return String
     */
    public function getDebitPayType($paymentFirstday)
    {
        return $this->debitPayTypes[$paymentFirstday];
    }

    /**
     * @param $table string
     * @param $column string
     *
     * @return bool
     */
    public static function tableHasColumn($table, $column)
    {
        $sql = "SHOW COLUMNS FROM $table LIKE '$column'";
        $res = Shopware()->Db()->fetchRow($sql);
        if (empty($res)) {
            return false;
        }
        return true;
    }

    /**
     * @param Customer $customer
     * @return bool
     */
    public static function customerCreatesNetOrders(Customer $customer)
    {
        return $customer->getGroup()->getTax() === false;
    }


    /**
     * @param $key
     * @param $array
     * @return bool
     */
    public static function hasValueAndIsNotEmpty($key, $array)
    {
        return key_exists($key, $array) && !empty($array[$key]);
    }

    /**
     * @param $version
     * @return bool
     */
    public static function assertMinimumShopwareVersion($version)
    {
        $sExpected = explode('.', $version);
        $expected = array_map('intval', $sExpected);
        $sConfigured = explode('.', Shopware()->Config()->version);
        $configured = array_map('intval', $sConfigured);

        for ($i = 0; $i < 3; $i++) {
            if ($expected[$i] < $configured[$i]) {
                return true;
            }

            if ($expected[$i] > $configured[$i]) {
                return false;
            }
        }

        return true;
    }
}
