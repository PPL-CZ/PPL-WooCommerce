<?php
namespace PPLCZ\Admin\RestController;
defined("WPINC") or die();

use PPLCZ\Admin\CPLOperation;
use PPLCZ\Admin\Cron\RefreshAboutCron;
use PPLCZ\Data\AddressData;
use PPLCZ\Data\CodBankAccountData;
use PPLCZ\Admin\Errors;
use PPLCZ\Admin\RestResponse\RestResponse400;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Model\Model\BankAccountModel;
use PPLCZ\Model\Model\MyApi2;
use PPLCZ\Model\Model\SenderAddressModel;
use PPLCZ\Model\Model\ShipmentPhaseModel;
use PPLCZ\Model\Model\SyncPhasesModel;
use PPLCZ\Model\Model\UpdateSyncPhasesModel;
use PPLCZ\Serializer;
use PPLCZ\Validator\Validator;

class SettingV1RestController extends  PPLRestController
{
    protected $namespace = "ppl-cz/v1";

    protected $base = "setting";

    public function register_routes()
    {

        register_rest_route($this->namespace, "/". $this->base . "/api", [
            [
                "methods"=>\WP_REST_Server::EDITABLE,
                "permission_callback"=>[$this, "check_permission"],
                "callback" => [$this, "update_api"],
            ], [
                "methods"=>\WP_REST_Server::READABLE,
                "callback" => [$this, "get_api"],
                "permission_callback"=>[$this, "check_permission"],
            ]
        ]);

        register_rest_route($this->namespace, "/". $this->base . "/bankAccounts", [
            [
                "methods"=>\WP_REST_Server::EDITABLE,
                "permission_callback"=>[$this, "check_permission"],
                "callback" => [$this, "update_bank_account"],
            ], [
                "methods"=>\WP_REST_Server::READABLE,
                "callback" => [$this, "get_bank_account"],
                "permission_callback"=>[$this, "check_permission"],
            ]
        ]);

        register_rest_route($this->namespace, "/". $this->base . "/sender-addresses", [
            [
                "methods"=>\WP_REST_Server::EDITABLE,
                "permission_callback"=>[$this, "check_permission"],
                "callback" => [$this, "update_addresses"],
            ], [
                "methods"=>\WP_REST_Server::READABLE,
                "callback" => [$this, "get_addresses"],
                "permission_callback"=>[$this, "check_permission"],
            ]
        ]);

        register_rest_route($this->namespace, "/" . $this->base . "/print", [
            [
                "methods"=>\WP_REST_Server::EDITABLE,
                "permission_callback"=>[$this, "check_permission"],
                "callback" => [$this, "update_print"],
            ], [
                "methods"=>\WP_REST_Server::READABLE,
                "callback" => [$this, "get_print"],
                "permission_callback"=>[$this, "check_permission"],
            ]
        ]);

        register_rest_route($this->namespace, "/" . $this->base . "/available-printers", [
            [
                "methods"=>\WP_REST_Server::READABLE,
                "permission_callback"=>[$this, "check_permission"],
                "callback" => [$this, "available_printers"],
            ]
        ]);

        register_rest_route($this->namespace, '/' . $this->base. "/shipment-phases", [
                "methods" => \WP_REST_Server::READABLE,
                "callback" => [$this, "get_phases"],
                "permission_callback"=>[$this, "check_permission"],

        ]);

        register_rest_route($this->namespace, '/' . $this->base. "/shipment-phases", [
            "methods" => \WP_REST_Server::EDITABLE,
            "callback" => [$this, "set_phase"],
            "permission_callback"=>[$this, "check_permission"],
        ]);
    }

    public function available_printers()
    {
        $items = array_map(function ($item) {
            return pplcz_normalize($item);
        }, (new CPLOperation())->getAvailableLabelPrinters());
        $resp = new \WP_REST_Response();
        $resp->set_data($items);
        return $items;
    }


    public function get_phases()
    {

        $response = new \WP_REST_Response();

        $phases = array_map(function ($item) {
            return new ShipmentPhaseModel($item);
        }, pplcz_get_phases());

        $maxSync = pplcz_get_phase_max_sync();


        $response->set_data(new SyncPhasesModel([
            "maxSync"=>$maxSync,
            "phases"=>$phases
        ]));
        return $response;
    }

    public function set_phase(\WP_REST_Request $request)
    {


        /**
         * @var UpdateSyncPhasesModel $value
         */
        $params = $request->get_json_params();
        $value = pplcz_denormalize($request->get_json_params(), UpdateSyncPhasesModel::class);

        if ($value->isInitialized("phases")) {
            foreach ($value->getPhases() as $phase) {
                pplcz_set_phase($phase->getCode(), $phase->getWatch());
            }
        }
        if ($value->isInitialized("maxSync"))
        {
            pplcz_set_phase_max_sync($value->getMaxSync());
        }

        $resp = new \WP_REST_Response();
        $resp->set_status(204);
        return $resp;
    }

    public function update_print(\WP_REST_Request $request)
    {
        $content = json_decode($request->get_body());
        $printers = (new CPLOperation())->getAvailableLabelPrinters();
        foreach ($printers as $v) {
            if ($v->getCode() === $content) {
                add_option(pplcz_create_name("print_setting"), $content) || update_option(pplcz_create_name("print_setting"), $content);
                $resp = new \WP_REST_Response();
                $resp->set_status(204);
                return $resp;
            }
        }


        $resp = new \WP_REST_Response();
        $resp->set_status(400);
        return $resp;
    }

    public function get_print(\WP_REST_Request $request)
    {
        $printSetting = get_option(pplcz_create_name("print_setting"), "1/PDF/A4/4");
        $format = (new CPLOperation())->getFormat($printSetting);
        $resp = new \WP_REST_Response();
        $resp->set_data($format);
        return $resp;

    }

    public function get_addresses()
    {
        $sender = AddressData::get_default_sender_addresses();
        foreach ($sender as $key => $value)
        {
            $sender[$key] = pplcz_denormalize($value, SenderAddressModel::class);
            $sender[$key] = pplcz_normalize($sender[$key], "array");
        }
        $response = new \WP_REST_Response();
        $response->set_data($sender);
        return $response;
    }

    public function update_addresses(\WP_REST_Request $request)
    {
        $sender = $request->get_json_params();

        $validator = Validator::getInstance();
        $errors = new Errors();

        foreach ($sender as $key => $value)
        {
            $sender[$key] = pplcz_denormalize($value, SenderAddressModel::class);
            pplcz_validate($sender[$key], $errors, "$key");
        }
        if ($errors->errors)
            return new RestResponse400($errors);

        foreach ($sender as $key => $value) {
            $addressId = $sender[$key]->getId();
            $address = new AddressData($addressId);
            $sender[$key] = pplcz_denormalize($sender[$key], AddressData::class, null, ["data" => $address]);
            $sender[$key]->save();

        }

        AddressData::set_default_sender_addresses($sender);

        $response = new \WP_REST_Response();
        $response->set_status(204);
        return $response;

    }

    public function get_bank_account()
    {
        $data = CodBankAccountData::get_default_bank_accounts();
        $response = new \WP_REST_Response();
        if ($data) {
            foreach ($data as $key => $val)
            {
                $val = pplcz_denormalize($val, BankAccountModel::class);
                $data[$key] = pplcz_normalize($val, "array");
            }
            $response->set_data($data);

        } else {
            $response->set_data([]);
        }
        return $response;
    }

    public function update_bank_account(\WP_REST_Request $request)
    {
        $accounts = $request->get_json_params();
        $validator = Validator::getInstance();
        $errors = new Errors();

        foreach ($accounts as $key => $value)
        {
            $accounts[$key] = pplcz_denormalize($value, BankAccountModel::class);
            pplcz_validate($accounts[$key], $errors, "$key");
        }
        if ($errors->errors)
            return new RestResponse400($errors);

        foreach ($accounts as $key => $value) {
            $bankAccountId = $accounts[$key]->getId();
            $bankAccount = new CodBankAccountData($bankAccountId);
            $accounts[$key] = pplcz_denormalize($accounts[$key], CodBankAccountData::class, null, ["data" => $bankAccount]);
            $accounts[$key]->save();

        }

        CodBankAccountData::set_default_bank_accounts($accounts);
        $response = new \WP_REST_Response();
        $response->set_status(204);
        return $response;
    }

    public function update_api(\WP_REST_Request $request)
    {
        delete_transient(pplcz_create_name("validate_cpl_connect"));

        $apiKey = pplcz_create_name("client_id");
        $apiSecret = pplcz_create_name("secret");

        $data = $request->get_json_params();
        /**
         * @var MyApi2 $setting
         */
        $setting = pplcz_denormalize($data, MyApi2::class);

        add_option($apiKey, $setting->getClientId()) || update_option($apiKey, $setting->getClientId());
        add_option($apiSecret, $setting->getClientSecret()) || update_option($apiSecret, $setting->getClientSecret());

        $cpl = new CPLOperation();
        $cpl->clearAccessToken();
        $accessToken = $cpl->getAccessToken();

        if (!$accessToken) {
            $response = new \WP_REST_Response();
            $response->set_status(400);
            $response->set_data("Nelze se pomocí zadaných údajů přihlásit");
            return $response;
        }

        $response = new \WP_REST_Response();
        $response->set_status(204);
        RefreshAboutCron::refresh_setting();
        return $response;
    }

    public function get_api(\WP_REST_Request $request)
    {
        $apiKey = pplcz_create_name("client_id");
        $apiSecret = pplcz_create_name("secret");

        $myapi2 = new MyApi2();
        $myapi2->setClientId(get_option($apiKey) ?: "");
        $myapi2->setClientSecret(get_option($apiSecret) ?: "");

        $myapi2 = pplcz_normalize($myapi2, "array");

        $response = new \WP_REST_Response();
        $response->set_data($myapi2);
        return $response;
    }
}