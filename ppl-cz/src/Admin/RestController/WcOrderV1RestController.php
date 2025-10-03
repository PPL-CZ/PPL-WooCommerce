<?php
namespace PPLCZ\Admin\RestController;
defined("WPINC") or die();


use PPLCZ\Data\ShipmentData;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Serializer;

class WcOrderV1RestController extends PPLRestController
{

    protected $namespace = "ppl-cz/v1";

    protected $base = "shipment";

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->base. "/(?P<id>\d+)/shipments", [
            "methods" => \WP_REST_Server::READABLE,
            "callback" => [$this, "get_shipments"],
            "permission_callback"=>[$this, "check_permission"],
            "args"=> [
                "id" => [
                    "validate_callback" => function($params, $request, $key) {
                        return is_numeric($params) && (new \WC_Order($params))->get_id();
                    }
                ]
            ]
        ]);
    }

    public function get_shipments(\WP_REST_Request $request)
    {
        $shipments = ShipmentData::find_shipments_by_wc_order($request->get_param("id"));
        foreach ($shipments as $key =>$value) {
            $value = pplcz_denormalize($value, ShipmentModel::class);
            $value = pplcz_normalize($value, 'array');
            $shipments[$key] = $value;
        }
        $response = new \WP_REST_Response();
        $response->set_data($shipments);
        return $response;
    }

}