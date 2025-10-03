<?php
namespace PPLCZ\Admin\RestController;
defined("WPINC") or die();


use PPLCZCPL\Model\EpsApiInfrastructureWebApiModelProblemJsonModel;
use PPLCZ\Admin\CPLOperation;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Admin\Errors;
use PPLCZ\Admin\RestResponse\RestResponse400;
use PPLCZ\Model\Model\CreateShipmentLabelBatchModel;
use PPLCZ\Model\Model\PrepareShipmentBatchModel;
use PPLCZ\Model\Model\PrepareShipmentBatchReturnModel;
use PPLCZ\Model\Model\RefreshShipmentBatchReturnModel;
use PPLCZ\Model\Model\ShipmentLabelRefreshBatchModel;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Serializer;
use PPLCZ\Traits\ParcelDataModelTrait;
use PPLCZ\Validator\Validator;

class ShipmentBatchV1Controller extends  PPLRestController
{
    use ParcelDataModelTrait;

    protected $namespace = "ppl-cz/v1";

    protected $base = "shipment/batch";

    public function register_routes()
    {

        register_rest_route($this->namespace, "/" . $this->base . "/preparing", [
            "methods" => \WP_REST_Server::EDITABLE,
            "callback" => [$this, "prepare_shipments"],
            "permission_callback"=>[$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, "/" . $this->base . "/create-labels", [
            "methods" => \WP_REST_Server::EDITABLE,
            "callback" => [$this, "create_labels"],
            "permission_callback"=>[$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, "/" . $this->base . "/refresh-labels", [
            "methods" => \WP_REST_Server::EDITABLE,
            "callback" => [$this, "refresh_labels"],
            "permission_callback"=>[$this, "check_permission"],
        ]);
    }

    public function create_labels(\WP_REST_Request $request)
    {
        $data = $request->get_json_params();

        /**
         * @var CreateShipmentLabelBatchModel $data
         */
        $data = pplcz_denormalize($data, CreateShipmentLabelBatchModel::class);
        $print = $data->getPrintSetting();

        if ($print)
        {
            add_option(pplcz_create_name("print_setting"), $print) || update_option(pplcz_create_name("print_setting"), $print);
        }


        $cpl = new CPLOperation();
        $resp = new \WP_REST_Response();
        $resp->set_status(204);

        try {
            $cpl->createPackages($data->getShipmentId(), $print);
        }
        catch (\Exception $exception)
        {
            $resp->set_status(500);
            return $resp;
        }
        $output = [];
        foreach ($data->getShipmentId() as $id) {
            $item = new ShipmentData($id);
            $item = pplcz_denormalize($item, ShipmentModel::class);
            $item = pplcz_normalize($item, "array");
            $output[] = $item;
        }
        $resp->set_data($output);

        return $resp;
    }

    public function prepare_shipments(\WP_REST_Request $request)
    {
        $data = $request->get_json_params();

        /**
         * @var PrepareShipmentBatchModel $data
         */
        $data = pplcz_denormalize($data, PrepareShipmentBatchModel::class);
        $error = new Errors();

        foreach ($data->getItems() as $key => $item) {
            if ($item->getShipmentId())
            {
                $shipmentData = new ShipmentData($item->getShipmentId());

                if (!$shipmentData->get_import_state() || $shipmentData->get_import_state() === "None")
                {
                    /**
                     * @var ShipmentModel $shipmentModel
                     */
                    $shipmentModel = pplcz_denormalize($shipmentData, ShipmentModel::class);
                    pplcz_validate($shipmentModel, $error, "item.$key");
                }
            }
            else if ($item->getOrderId())
            {
                $order = new \WC_Order($item->getOrderId());
                if (self::hasPPLShipment($order)) {
                    $shipmentModel = pplcz_denormalize($order, ShipmentModel::class);
                    pplcz_validate($shipmentModel, $error, "item.$key");

                } else {
                    $error->add("item.$key", "Nelze automaticky vytvořit zásilku");
                }
            } else {
                $error->add("item.$key", "Nelze automaticky vytvořit zásilku");
            }
        }

        if ($error->errors) {
            $resp = new RestResponse400($error);
            return $resp;
        }

        $output = [];

        foreach ($data->getItems() as $key => $item) {
            if ($item->getShipmentId()) {
                $output[$key] = $item->getShipmentId();
            }
            else if ($item->getOrderId())
            {
                $order = new \WC_Order($item->getOrderId());
                $shipmentModel = pplcz_denormalize($order, ShipmentModel::class);
                $shipmentData = pplcz_denormalize($shipmentModel, ShipmentData::class);
                $shipmentData->save();
                $output[$key] = $shipmentData->get_id();
            }
        }

        $model = new PrepareShipmentBatchReturnModel();
        $model->setShipmentId($output);

        $resp = new \WP_REST_Response();
        $resp->set_data(pplcz_normalize($model, "array"));
        return $resp;
    }

    public function refresh_labels(\WP_REST_Request $request)
    {
        $data = $request->get_json_params();

        /**
         * @var ShipmentLabelRefreshBatchModel $data
         */
        $data = pplcz_denormalize($data, ShipmentLabelRefreshBatchModel::class);
        $output = [];

        $batchs = [];

        foreach ($data->getShipmentId() as $item) {
            $shipmentData = new ShipmentData($item);
            $batchs[] = $shipmentData->get_batch_id();
        }

        $batchs = array_unique($batchs);
        $ids = [];
        $operations = new CPLOperation();
        $operations->loadingShipmentNumbers($batchs);

        foreach ($data->getShipmentId() as $item) {
            $shipmentData = new ShipmentData($item);
            $output[] = pplcz_denormalize($shipmentData, ShipmentModel::class);
            $ids[] = $shipmentData->get_batch_label_group();
        }

        $refresh = new RefreshShipmentBatchReturnModel();
        $refresh->setShipments($output);
        $refresh->setBatchs(array_filter($ids));
        $data = pplcz_normalize($refresh, "array");
        $resp = new \WP_REST_Response();
        $resp->set_data($data);

        return $resp;
    }
}