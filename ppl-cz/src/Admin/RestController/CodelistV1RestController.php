<?php
namespace PPLCZ\Admin\RestController;
defined("WPINC") or die();


use PPLCZ\Model\Model\CountryModel;
use PPLCZ\Model\Model\CurrencyModel;
use PPLCZ\Model\Model\ShipmentMethodModel;
use PPLCZ\Model\Model\ShipmentPhaseModel;
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
    }


    public function get_countries(\WP_REST_Request $request)
    {

        $allowedCountries = pplcz_get_allowed_countries();
        $currencies = pplcz_get_cod_currencies();
        $parcelAllowed = array_keys(pplcz_get_parcel_countries());

        $output = array_map(function ($value, $key) use ($currencies, $parcelAllowed) {
            return new CountryModel([
                "code" => $key,
                "title" =>$value,
                "parcelAllowed" => in_array($key, $parcelAllowed),
                "codAllowed" => array_unique(array_map(function ($item) {
                    return $item['currency'];

                }, array_filter($currencies, function ($item) use ($key) {
                    return $item['country'] === $key;
                })))
            ]);
        }, $allowedCountries, array_keys($allowedCountries));

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
            $output[] = new CurrencyModel([
                "code" => $key,
                "title" =>$key,
            ]);
        }
        $rest = new \WP_REST_Response();
        $rest->set_data($output);
        return $rest;
    }

    public function get_methods(\WP_REST_Request $request)
    {
        $output = [];
        foreach (ShipmentMethod::methodsWithCod() as $k => $v) {
            $output[] = new ShipmentMethodModel([
                "code" => $k,
                "title" => $v,
                "codAvailable" => ShipmentMethod::isMethodWithCod($k),
                "parcelRequired" => ShipmentMethod::isMethodWithParcel($k)
            ]);
        }

        $response = new \WP_REST_Response();
        $response->set_data($output);
        return $response;
    }
}