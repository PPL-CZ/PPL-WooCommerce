<?php
namespace PPLCZ\Front\Controller;

defined("WPINC") or die();

use PPLCZ\Admin\CPLOperation;
use PPLCZ\Serializer;

class WhisperV1RestController extends \WP_REST_Controller
{
    protected $namespace = "pplcz/v1";

    protected $base = "whisper";

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->base . "/", [
            "methods" => \WP_REST_Server::READABLE,
            "callback" => [$this, "find_items"],
        ]);
    }

    public function find_items(\WP_REST_Request $request)
    {
        $cpl_operation = new CPLOperation();
        $street = $request->get_param("street");
        $zip = $request->get_param("zipCode");
        $city = $request->get_param("city");
        $output = $cpl_operation->whisper($street, $city, $zip);

        $resp = new \WP_REST_Response();
        foreach ($output as $k => $v) {
            $output[$k] = Serializer::getInstance()->normalize($v, "array");
        }
        $resp->set_data($output);

        return $resp;
    }


    public static function register()
    {
        add_action("rest_api_init", [static::class, "init_controller"]);
    }

    public static function init_controller()
    {
        $controller = new static();
        $controller->register_routes();
    }
}