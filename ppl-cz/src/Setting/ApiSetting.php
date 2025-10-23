<?php
namespace PPLCZ\Setting;

use PPLCZ\Model\Model\MyApi2;

class ApiSetting
{
    public static function getApi()
    {
        $apiKey = pplcz_create_name("client_id");
        $apiSecret = pplcz_create_name("secret");

        $myapi2 = new MyApi2();
        $myapi2->setClientId(get_option($apiKey) ?: "");
        $myapi2->setClientSecret(get_option($apiSecret) ?: "");

        return $myapi2;
    }

    public static function setApi(MyApi2  $myapi2)
    {
        $apiKey = pplcz_create_name("client_id");
        $apiSecret = pplcz_create_name("secret");

        add_option($apiKey, $myapi2->getClientId()) || update_option($apiKey, $myapi2->getClientId());
        add_option($apiSecret, $myapi2->getClientSecret()) || update_option($apiSecret, $myapi2->getClientSecret());

    }
}