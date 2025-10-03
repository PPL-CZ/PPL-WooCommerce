<?php
namespace PPLCZ\Admin\RestController;
defined("WPINC") or die();


abstract class PPLRestController extends \WP_REST_Controller
{
    public static function register_controller()
    {
        $controller = new static();
        $controller->register_routes();
    }

    public static function register()
    {
        add_action("rest_api_init", [static::class, "register_controller"]);
    }

    public function check_permission()
    {
        return current_user_can("manage_woocommerce");
    }
}