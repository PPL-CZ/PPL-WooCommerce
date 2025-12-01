<?php
namespace PPLCZ\Setting;

use PPLCZ\Admin\CPLOperation;
use PPLCZ\Model\Model\PrintSettingModel;

class PrintSetting {
    public static function getPrintSetting()
    {
        $printSetting = new PrintSettingModel();

        $format = get_option(pplcz_create_name("print_setting"), "1/PDF/A4/4");
        $format = (new CPLOperation())->getFormat($format);


        $printSetting->setFormat($format);

        $printStatuses = get_option(pplcz_create_name("print_order_statuses"));
        if ($printStatuses && is_array($printStatuses))
        {
            $keys = array_keys(wc_get_order_statuses());
            $keys = array_intersect($printStatuses, $keys);
            $printSetting->setOrderStatuses($keys);
        }

        if (!$printSetting->isInitialized('orderStatuses'))
            $printSetting->setOrderStatuses(["wc-processing"]);

        return $printSetting;
    }

    public static function setFormat($content)
    {
        $printers = (new CPLOperation())->getAvailableLabelPrinters();

        foreach ($printers as $v) {
            if ($v->getCode() === $content) {
                add_option(pplcz_create_name("print_setting"), $content) || update_option(pplcz_create_name("print_setting"), $content);
                return true;
            }
        }
        return false;
    }

    public static function setOrderStatuses($statuses)
    {
        if (is_array($statuses)) {
            $printStatuses = pplcz_create_name("print_order_statuses");
            $keys = array_intersect(array_keys(wc_get_order_statuses()), $statuses ?: []);
            add_option($printStatuses, $keys) ||  update_option($printStatuses, $keys);
        }
        return false;
    }
}
