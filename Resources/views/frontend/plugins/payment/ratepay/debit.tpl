{extends file='frontend/plugins/payment/ratepay/abstract.tpl'}

{block name="ratepay_payment_method_content"}
    {include file="frontend/plugins/payment/ratepay/common/bank_account.tpl"}
    {include file="frontend/plugins/payment/ratepay/common/terms_and_conditions.tpl"}
{/block}
