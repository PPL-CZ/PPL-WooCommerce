<?php
namespace PPLCZ\Admin\RestController;
defined("WPINC") or die();


use PPLCZ\Admin\CPLOperation;
use PPLCZ\Data\ParcelData;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Admin\Errors;
use PPLCZ\Admin\Operation\FindParcelShopOperation;
use PPLCZ\Admin\RestResponse\RestResponse400;
use PPLCZ\Model\Model\BankAccountModel;
use PPLCZ\Model\Model\RecipientAddressModel;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Model\Model\UpdateShipmentBankAccountModel;
use PPLCZ\Model\Model\UpdateShipmentModel;
use PPLCZ\Model\Model\UpdateShipmentParcelModel;
use PPLCZ\Model\Model\UpdateShipmentSender;
use PPLCZ\Model\Model\UpdateShipmentSenderModel;
use PPLCZ\Serializer;
use PPLCZ\Validator\Validator;

class ShipmentV1RestController extends PPLRestController
{
    protected $namespace = "ppl-cz/v1";

    protected $base = "shipment";

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->base. "/(?P<id>\d+)", [
            "methods" => \WP_REST_Server::READABLE,
            "callback" => [$this, "get_shipment"],
            "permission_callback"=>[$this, "check_permission"],
            "args"=> [
                "id" => [
                    "validate_callback" => function($params, $request, $key) {
                        return is_numeric($params) && (new ShipmentData($params))->get_id();
                    }
                ]
            ]
        ]);

        register_rest_route($this->namespace, "/" . $this->base . "/(?P<id>\d+)/recipient",
        [
            "methods" => \WP_REST_Server::EDITABLE,
            "callback" => [$this, "update_recipient_address"],
            "permission_callback"=>[$this, "check_permission"],
            "args"=> [
                "id" => [
                    "validate_callback" => function($params, $request, $key) {
                        return is_numeric($params) && (new ShipmentData($params))->get_id();
                    }
                ]
            ]
        ]);

        register_rest_route($this->namespace, "/" . $this->base,
            [
                "methods" => \WP_REST_Server::EDITABLE,
                "callback" => [$this, "create_shipment"],
                "permission_callback"=>[$this, "check_permission"],
            ]);

        register_rest_route($this->namespace, "/" . $this->base . "/(?P<id>\d+)",
            [
                "methods" => \WP_REST_Server::EDITABLE,
                "callback" => [$this, "update_shipment"],
                "permission_callback"=>[$this, "check_permission"],
                "args"=> [
                    "id" => [
                        "validate_callback" => function($params, $request, $key) {
                            return is_numeric($params) && (new ShipmentData($params))->get_id();
                        }
                    ]
                ]
            ]);



        register_rest_route($this->namespace, "/" . $this->base . "/(?P<id>\d+)/sender",
            [
                "methods" => \WP_REST_Server::EDITABLE,
                "callback" => [$this, "update_shipment_sender"],
                "permission_callback"=>[$this, "check_permission"],
                "args"=> [
                    "id" => [
                        "validate_callback" => function($params, $request, $key) {
                            return is_numeric($params) && (new ShipmentData($params))->get_id();
                        }
                    ]
                ]
            ]);

        register_rest_route($this->namespace, "/" . $this->base . "/(?P<id>\d+)/bankAccount",
            [
                "methods" => \WP_REST_Server::EDITABLE,
                "callback" => [$this, "update_shipment_bank_account"],
                "permission_callback"=>[$this, "check_permission"],
                "args"=> [
                    "id" => [
                        "validate_callback" => function($params, $request, $key) {
                            return is_numeric($params) && (new ShipmentData($params))->get_id();
                        }
                    ]
                ]
            ]);

        register_rest_route($this->namespace, "/" . $this->base . "/(?P<id>\d+)/parcel",
            [
                "methods" => \WP_REST_Server::EDITABLE,
                "callback" => [$this, "update_shipment_parcel"],
                "permission_callback"=>[$this, "check_permission"],
                "args"=> [
                    "id" => [
                        "validate_callback" => function($params, $request, $key) {
                            return is_numeric($params) && (new ShipmentData($params))->get_id();
                        }
                    ]
                ]
            ]);


    }

    public function get_shipment(\WP_REST_Request $request)
    {
        $shipmentData = new ShipmentData($request->get_param("id"));
        $shipmentModel = pplcz_denormalize($shipmentData, ShipmentModel::class);
        $data = pplcz_normalize($shipmentModel, "array");
        $response = new \WP_REST_Response();
        $response->set_data($data);
        return $response;
    }

    public function create_shipment(\WP_REST_Request $request)
    {
        $data = $request->get_json_params();
        $shipmentModel = pplcz_denormalize($data, UpdateShipmentModel::class);
        $shipment = pplcz_denormalize($shipmentModel, ShipmentData::class);
        $shipment->save();

        $resp = new \WP_REST_Response();
        $resp->set_status(201);
        $resp->set_headers([
            "Location" => rtrim(get_rest_url(), '/') . "/woocommerce-ppl/v1/shipment/{$shipment->get_id()}"
        ]);
        return $resp;

    }

    private function getShipment($id)
    {
        $shipment = new ShipmentData($id);
        if ($shipment->get_lock()) {
            try {
                $shipment->unlock();
            } catch (\Exception $ex) {
                $err = new \WP_Error();
                $err->add("locked", "Shipment is locked");
                $error = new RestResponse400($err);
                return $error;
            }
        }
        return $shipment;
    }

    public function update_recipient_address(\WP_REST_Request $request)
    {
        $data = $request->get_json_params();
        $sender = pplcz_denormalize($data, RecipientAddressModel::class);


        $errors = new Errors();
        pplcz_validate($sender, $errors, "");
        if ($errors->errors)
            return new RestResponse400($errors);


        $shipment = $this->getShipment($request->get_param("id"));
        if ($shipment instanceof \WP_REST_Response)
            return $shipment;
        $shipment = pplcz_denormalize($sender, ShipmentData::class, ["data" => $shipment]);
        $shipment->set_import_errors(null);
        $shipment->save();
        $resp = new \WP_REST_Response();
        $resp->set_status(204);
        return $resp;
    }

    public function update_shipment_bank_account(\WP_REST_Request $request)
    {
        $data = $request->get_json_params();
        /**
         * @var UpdateShipmentBankAccountModel $sender
         */
        $sender = pplcz_denormalize($data, UpdateShipmentBankAccountModel::class);
        $shipment = $this->getShipment($request->get_param("id"));

        if ($shipment instanceof \WP_REST_Response)
            return $shipment;
        $shipment->set_cod_bank_account_id($sender->getBankAccountId());
        $shipment->save();
        $resp = new \WP_REST_Response();
        $resp->set_status(204);
        return $resp;

    }

    public function update_shipment_parcel(\WP_REST_Request $request)
    {
        $data = $request->get_json_params();
        $sender = pplcz_denormalize($data, UpdateShipmentParcelModel::class);
        $shipment = $this->getShipment($request->get_param("id"));

        $founded = ParcelData::getAccessPointByCode($sender->getParcelCode());
        if (!$founded) {
            $find = new CPLOperation();
            $esp = $find->findParcel($sender->getParcelCode());
            if (!$esp) {
                $resp = new \WP_REST_Response();
                $resp->set_status(404);
                $resp->set_data("Not found accessPoint");
                return $resp;
            }
        }

        if ($shipment instanceof \WP_REST_Response)
            return $shipment;
        if (isset($esp))
            $parcel = pplcz_denormalize($esp, ParcelData::class, ["data" => $founded]);
        else
            $parcel = $founded;
        $parcel->save();
        $shipment->set_parcel_id($parcel->get_id());
        $shipment->save();

        $resp = new \WP_REST_Response();
        $resp->set_status(204);
        return $resp;
    }


    public function update_shipment_sender(\WP_REST_Request $request)
    {
        $data = $request->get_json_params();
        /**
         * @var UpdateShipmentSenderModel $sender
         */
        $sender = pplcz_denormalize($data, UpdateShipmentSenderModel::class);
        $shipment = $this->getShipment($request->get_param("id"));
        if ($shipment instanceof \WP_REST_Response)
            return $shipment;
        $shipment->set_sender_address_id($sender->getSenderId());
        $shipment->save();
        $resp = new \WP_REST_Response();
        $resp->set_status(204);
        return $resp;
    }

    public function update_shipment(\WP_REST_Request $request)
    {
        $data = $request->get_json_params();

        $shipmentModel = pplcz_denormalize($data, UpdateShipmentModel::class);

        $shipment = $this->getShipment($request->get_param("id"));

        if ($shipment instanceof \WP_REST_Response)
            return $shipment;

        $err = new Errors();
        pplcz_validate($shipmentModel, $err, "");

        if ($err->errors)
            return new RestResponse400($err);


        $shipment = pplcz_denormalize($shipmentModel, ShipmentData::class, ["data" => $shipment]);
        $shipment->set_import_errors(null);
        $shipment->save();

        $resp = new \WP_REST_Response();
        $resp->set_status(204);
        return $resp;
    }


}