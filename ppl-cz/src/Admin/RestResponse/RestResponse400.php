<?php
namespace PPLCZ\Admin\RestResponse;
defined("WPINC") or die();


class RestResponse400 extends \WP_REST_Response {

    public function  __construct( $data , $status = 200, $headers = array() )
    {
        parent::__construct(null, 400, []);
        if ($data instanceof \WP_Error) {
            $this->set_data([
                "data"=> [
                    "code" => "element.error.dataerror.validation",
                    "errors" => $data->errors,
                    "errors_data" => $data->error_data
                ]
            ]);
        }

    }

    public function setError(\WP_Error $error)
    {
        $this->set_status(400);

        $this->set_data([
            "data" => [
                "code" => "element.error.dataerror.validation",
                "errors" => $error->errors,
                "errors_data" => $error->error_data
            ]
        ]);
    }
}