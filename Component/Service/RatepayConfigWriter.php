<?php

namespace RpayRatePay\Component\Service;

class RatepayConfigWriter
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @return bool
     */
    public function truncateConfigTables()
    {
        $configSql = 'TRUNCATE TABLE `rpay_ratepay_config`;';
        $configPaymentSql = 'TRUNCATE TABLE `rpay_ratepay_config_payment`;';
        $configInstallmentSql = 'TRUNCATE TABLE `rpay_ratepay_config_installment`;';
        try {
            $this->db->query($configSql);
            $this->db->query($configPaymentSql);
            $this->db->query($configInstallmentSql);
        } catch (\Exception $exception) {
            Logger::singleton()->info($exception->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Sends a Profile_request and saves the data into the Database
     *
     * @param string $profileId
     * @param string $securityCode
     * @param int $shopId
     * @param string $country
     * @param bool $backend
     *
     * @return bool
     */
    public function writeRatepayConfig($profileId, $securityCode, $shopId, $country, $backend = false)
    {
        $factory = new \Shopware_Plugins_Frontend_RpayRatePay_Component_Mapper_ModelFactory(null, $backend);
        $data = [
            'profileId' => $profileId,
            'securityCode' => $securityCode
        ];

        try {
            $response = $factory->callProfileRequest($data);
        } catch (\Exception $e) {
            Logger::singleton()->error(
                'RatePAY: Profile_Request failed for profileId ' . $profileId
            );
            return false;
        }

        if (!is_array($response) || $response === false) {
            Logger::singleton()
                ->info('RatePAY: Profile_Request for profileId ' . $profileId . ' was empty ');
            return false;
        }

        $payments = ['invoice', 'elv', 'installment', 'prepayment'];

        $type = [];
        //INSERT INTO rpay_ratepay_config_payment AND sets $type[]
        foreach ($payments as $payment) {
            if (strstr($profileId, '_0RT') !== false) {
                if ($payment !== 'installment') {
                    continue;
                }
            }

            $dataPayment = [
                $response['result']['merchantConfig']['activation-status-' . $payment],
                $response['result']['merchantConfig']['b2b-' . $payment] == 'yes' ? 1 : 0,
                $response['result']['merchantConfig']['tx-limit-' . $payment . '-min'],
                $response['result']['merchantConfig']['tx-limit-' . $payment . '-max'],
                $response['result']['merchantConfig']['tx-limit-' . $payment . '-max-b2b'],
                $response['result']['merchantConfig']['delivery-address-' . $payment] == 'yes' ? 1 : 0,
            ];

            $paymentSql = 'INSERT INTO `rpay_ratepay_config_payment`'
                . '(`status`, `b2b`,`limit_min`,`limit_max`,'
                . '`limit_max_b2b`, `address`)'
                . 'VALUES(' . substr(str_repeat('?,', 6), 0, -1) . ');';
            try {
                $this->db->query($paymentSql, $dataPayment);
                $id = $this->db->fetchOne('SELECT `rpay_id` FROM `rpay_ratepay_config_payment` ORDER BY `rpay_id` DESC');
                $type[$payment] = $id;
            } catch (\Exception $exception) {
                Logger::singleton()->error($exception->getMessage());
                return false;
            }
        }

        //performs insert into the 'config installment' table
        if ($response['result']['merchantConfig']['activation-status-installment'] == 2) {
            $installmentConfig = [
                $type['installment'],
                $response['result']['installmentConfig']['month-allowed'],
                $response['result']['installmentConfig']['valid-payment-firstdays'],
                $response['result']['installmentConfig']['rate-min-normal'],
                $response['result']['installmentConfig']['interestrate-default'],
            ];
            $paymentSql = 'INSERT INTO `rpay_ratepay_config_installment`'
                . '(`rpay_id`, `month_allowed`,`payment_firstday`,`interestrate_default`,'
                . '`rate_min_normal`)'
                . 'VALUES(' . substr(str_repeat('?,', 5), 0, -1) . ');';
            try {
                $this->db->query($paymentSql, $installmentConfig);
            } catch (\Exception $exception) {
                Logger::singleton()->error($exception->getMessage());
                return false;
            }
        }

        //updates 0% field in rpay_ratepay_config or inserts into rpay_ratepay_config THIS MEANS WE HAVE TO SEND the 0RT profiles last
        if (strstr($profileId, '_0RT') !== false) {
            $qry = "UPDATE rpay_ratepay_config SET installment0 = '" . $type['installment'] . "' WHERE profileId = '" . substr($profileId, 0, -4) . "'";
            $this->db->query($qry);
        } else {
            $data = [
                $response['result']['merchantConfig']['profile-id'],
                $type['invoice'],
                $type['installment'],
                $type['elv'],
                0,
                0,
                $type['prepayment'],
                $response['result']['merchantConfig']['eligibility-device-fingerprint'] ?: 'no',
                $response['result']['merchantConfig']['device-fingerprint-snippet-id'],
                strtoupper($response['result']['merchantConfig']['country-code-billing']),
                strtoupper($response['result']['merchantConfig']['country-code-delivery']),
                strtoupper($response['result']['merchantConfig']['currency']),
                strtoupper($country),
                $response['sandbox'],
                $backend,
                //shopId always needs be the last line
                $shopId
            ];

            $activePayments[] = '"rpayratepayinvoice"';
            $activePayments[] = '"rpayratepaydebit"';
            $activePayments[] = '"rpayratepayrate"';
            $activePayments[] = '"rpayratepayrate0"';
            $activePayments[] = '"rpayratepayprepayment"';

            $updateSqlActivePaymentMethods = 'UPDATE `s_core_paymentmeans` SET `active` = 1 WHERE `name` in(' . implode(',', $activePayments) . ') AND `active` <> 0';

            $configSql = 'INSERT INTO `rpay_ratepay_config`'
                . '(`profileId`, `invoice`, `installment`, `debit`, `installment0`, `installmentDebit`, `prepayment`,'
                . '`device_fingerprint_status`, `device_fingerprint_snippet_id`,'
                . '`country_code_billing`, `country_code_delivery`,'
                . '`currency`,`country`, `sandbox`,'
                . '`backend`, `shopId`)'
                . 'VALUES(' . substr(str_repeat('?,', 16), 0, -1) . ');'; // In case of altering cols change 14 by amount of affected cols
            try {
                $this->db->query($configSql, $data);
                if (count($activePayments) > 0) {
                    $this->db->query($updateSqlActivePaymentMethods);
                }

                return true;
            } catch (\Exception $exception) {
                Logger::singleton()->error($exception->getMessage());
                return false;
            }
        }
    }
}
