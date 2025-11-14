<?php
namespace PPLCZ\Admin\RestController;
defined("WPINC") or die();


use PPLCZ\Model\Model\CountryModel;
use PPLCZ\Model\Model\CurrencyModel;
use PPLCZ\Model\Model\OrderStatusModel;
use PPLCZ\Model\Model\ShipmentMethodModel;
use PPLCZ\Model\Model\ShipmentPhaseModel;
use PPLCZ\Setting\CountrySetting;
use PPLCZ\Setting\MethodSetting;
use PPLCZ\ShipmentMethod;

class CodelistV1RestController extends PPLRestController
{
    protected $namespace = "ppl-cz/v1";

    protected $base = "codelist";

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->base. "/methods", [
            "methods" => \WP_REST_Server::READABLE,
            "callback" => [$this, "get_methods"],
            "permission_callback"=>[$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, '/' . $this->base. "/currencies", [
            "methods" => \WP_REST_Server::READABLE,
            "callback" => [$this, "get_currencies"],
            "permission_callback"=>[$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, '/' . $this->base. "/countries", [
            "methods" => \WP_REST_Server::READABLE,
            "callback" => [$this, "get_countries"],
            "permission_callback"=>[$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, '/' . $this->base. "/order-statuses", [
            "methods" => \WP_REST_Server::READABLE,
            "callback" => [$this, "get_order_statuses"],
            "permission_callback"=>[$this, "check_permission"],
        ]);
    }


    public function get_countries(\WP_REST_Request $request)
    {
        $output = CountrySetting::getCountries();
        $output = array_map(function ($item) {
            return pplcz_normalize($item);
        }, $output );

        $rest = new \WP_REST_Response();
        $rest->set_data($output);
        return $rest;
    }


    public function get_currencies(\WP_REST_Request $request)
    {
        $currencies = get_woocommerce_currencies();
        $output = [];

        foreach ($currencies as $key=>$value)
        {
            $currency = new CurrencyModel();
            $currency->setCode($key);
            $currency->setTitle($key);
            $output[] = pplcz_normalize($currency);
        }
        $rest = new \WP_REST_Response();
        $rest->set_data($output);
        return $rest;
    }

    public function get_methods(\WP_REST_Request $request)
    {
        $output = [];
        foreach (MethodSetting::getMethods() as  $v) {
            $output[] = pplcz_normalize($v);
        }

        $response = new \WP_REST_Response();
        $response->set_data($output);
        return $response;
    }

    public function get_order_statuses()
    {
        $statuses = wc_get_order_statuses();

        $output = [];

        foreach ($statuses as $key=>$stat)
        {
            $orderstatus = new OrderStatusModel();
            $orderstatus->setCode($key);
            $orderstatus->setTitle($stat);
            $output[] = pplcz_normalize($orderstatus);
        }

        return new \WP_REST_Response($output);

    }
}