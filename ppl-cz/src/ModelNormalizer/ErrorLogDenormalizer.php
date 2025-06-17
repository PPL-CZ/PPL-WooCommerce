<?php
namespace PPLCZ\ModelNormalizer;

use PPLCZ\Admin\CPLOperation;
use PPLCZ\Data\LogDataStore;
use PPLCZ\Model\Model\ErrorLogItemModel;
use PPLCZ\Model\Model\ErrorLogModel;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ErrorLogDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        /**
         * @var ErrorLogModel $data
         */

        $client_id = null;
        $client_secret = null;
        $accessToken = null;
        try {
            $client_id = get_option(pplcz_create_name("client_id"));
            $client_secret = get_option(pplcz_create_name("client_secret"))  ?: get_option(pplcz_create_name("secret"));
            if ($client_id && $client_secret) {
                $accessToken = (new CPLOperation())->getAccessToken();
            }
        }
        catch (\Error $e) {

        }


        $active_plugins = get_option('active_plugins');

        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        $all_plugins = get_plugins();
        $active_plugins = get_option('active_plugins');

        foreach ($all_plugins as $key => $value) {
            if (!in_array($key, $active_plugins, true))
                unset($all_plugins[$key]);
        }

        $wordpress = get_bloginfo('version');
        $php = phpversion();

        $plugins = array_map(function ($plugin, $path) {
            return $plugin['Name'] . ' - '. $plugin['Version'] . ' (' . explode('/', $path)[0] .')';
        }, $all_plugins, array_keys($all_plugins));


        if ($accessToken)
            $accessToken = "ano";
        else
            $accessToken = "ne";

        $summary = [
            "### Přístup",
            "Client ID: $client_id",
            "Získa accessToken: {$accessToken}",
            "***",
            "### Verze",
            "Wordpress: $wordpress",
            "PHP: $php",
            "***",
            "### Plugins",
            join("\n", $plugins)
        ];
        $data->setMail(get_option('admin_email'));
        $data->setInfo(join("\n", $summary));
        $items = [];

        $logs = LogDataStore::get_logs();
        foreach ($logs as $log)
        {
            $item = new ErrorLogItemModel();
            $item->setTrace($log->get_message());
            $item->setId($log->get_id());
            $items[] = $item;
        }

        $data->setErrors($items);

        return $data;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof ErrorLogModel && $type === ErrorLogModel::class;
    }
}