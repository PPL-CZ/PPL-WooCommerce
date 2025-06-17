<?php
namespace PPLCZ\Admin\RestController;
defined("WPINC") or die();


use PPLCZ\Admin\CPLOperation;
use PPLCZ\Data\CollectionData;
use PPLCZ\Admin\Errors;
use PPLCZ\Admin\RestResponse\RestResponse400;
use PPLCZ\Model\Model\CollectionAddressModel;
use PPLCZ\Model\Model\CollectionModel;
use PPLCZ\Model\Model\NewCollectionModel;
use PPLCZ\Serializer;
use PPLCZ\Validator\Validator;

class CollectionV1Controller extends PPLRestController
{
    protected $namespace = "ppl-cz/v1";

    protected $base = "collection";

    public function register_routes()
    {
        register_rest_route($this->namespace, "/" . $this->base . "/address", [
            "methods" => \WP_REST_Server::READABLE,
            "callback" => [$this, "get_address"],
            "permission_callback" =>[$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, '/' . $this->base . "/(?P<id>\d+)/order", [
            "methods" => \WP_REST_Server::EDITABLE,
            "callback" => [$this, "order"],
            "permission_callback" =>[$this, "check_permission"],
            "args"=> [
                "id" => [
                    "validate_callback" => function($params, $request, $key) {
                        $item = new CollectionData($params);
                        return $item->get_id() > 0;
                    }
                ]
            ]
        ]);

        register_rest_route($this->namespace, '/' . $this->base . "/(?P<id>\d+)/order", [
            "methods" => \WP_REST_Server::DELETABLE,
            "callback" => [$this, "delete_order"],
            "permission_callback" =>[$this, "check_permission"],
            "args"=> [
                "id" => [
                    "validate_callback" => function($params, $request, $key) {
                        $item = new CollectionData($params);
                        return $item->get_id() > 0;
                    }
                ]
            ]
        ]);


        register_rest_route($this->namespace, '/' . $this->base . "/last", [
            "methods" => \WP_REST_Server::READABLE,
            "callback" => [$this, "get_last_collection"],
            "permission_callback" =>[$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, '/' . $this->base. "/(?P<id>\d+)",[
            [
                "methods" => \WP_REST_Server::READABLE,
                "callback" => [$this, "get_collection"],
                "permission_callback" =>[$this, "check_permission"],
                "args"=> [
                    "id" => [
                        "validate_callback" => function($params, $request, $key) {
                            return is_numeric($params);
                        }
                    ]
                ]
            ]
        ]);

        register_rest_route($this->namespace, '/' . $this->base, [

            [
                "methods" => \WP_REST_Server::READABLE,
                "callback" => [$this, "get_collections"],
                "permission_callback" =>[$this, "check_permission"],
                "args" => [

                ]
            ],
            [
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create_collection'),
                "permission_callback" =>[$this, "check_permission"],
            ]
        ]);
    }

    public function delete_order(\WP_REST_Request $request)
    {
        $param = new CollectionData($request->get_param("id"));
        $cpl = new CPLOperation();
        $cpl->cancelCollection($param->get_id());
        $rest = new \WP_REST_Response();
        $rest->set_status(204);
        return $rest;
    }

    public function order(\WP_REST_Request $request)
    {
        $param = new CollectionData($request->get_param("id"));
        $cpl = new CPLOperation();
        $cpl->createCollection($param->get_id());
        $rest = new \WP_REST_Response();
        $rest->set_status(204);
        return $rest;
    }

    public function get_last_collection(\WP_REST_Request $request)
    {
        $rest = new \WP_REST_Response();
        $collection = CollectionData::last_collection();
        if (!$collection) {
            $rest->set_status(404);
            return $rest;
        }
        $collection = Serializer::getInstance()->denormalize($collection, CollectionModel::class);
        $data = Serializer::getInstance()->normalize($collection, "array");
        $rest->set_data($data);
        return $rest;
    }

    public function  get_collections(\WP_REST_Request $request)
    {
        $rest = new \WP_REST_Response();
        $rest->set_data(array_map(function ($item) {
            $collection = Serializer::getInstance()->denormalize($item, CollectionModel::class);
            return Serializer::getInstance()->normalize($collection, "array");
        }, CollectionData::read_collections([])));
        return $rest;
    }

    public function get_collection(\WP_REST_Request $request)
    {
        $param = new CollectionData($request->get_param("id"));
        $collection = Serializer::getInstance()->denormalize($param, CollectionModel::class);
        $rest = new \WP_REST_Response();
        $rest->set_data(Serializer::getInstance()->normalize($collection, "array"));
        return $rest;
    }

    public function get_address(\WP_REST_Request $request)
    {
        $address = require_once __DIR__ . '/../../config/collection_address.php';
        $response = new \WP_REST_Response();

        if (!$address) {
            $response->set_status(404);
            return $response;
        }

        $address = pplcz_denormalize($address, CollectionAddressModel::class);
        $response->set_data(Serializer::getInstance()->normalize($address, "array"));
        return $response;
    }

    public function create_collection(\WP_REST_Request $request)
    {
        $params = $request->get_json_params();

        $inputCollection = pplcz_denormalize($params, NewCollectionModel::class);

        $errors = new Errors();

        pplcz_validate($inputCollection, $errors, "");
        if ($errors->errors)
            return new RestResponse400($errors);


        $collection = pplcz_denormalize($inputCollection, CollectionData::class);
        $collection->save();

        $cpl = new CPLOperation();
        $cpl->createCollection($collection->get_id());

        $header = new \WP_REST_Response();
        $header->header("Location", get_rest_url(null) . $this->namespace. '/' . $this->base . '/' . $collection->get_id());
        $header->set_status(204);
        return $header;
    }

}
