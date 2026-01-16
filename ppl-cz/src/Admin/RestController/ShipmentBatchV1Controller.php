<?php
namespace PPLCZ\Admin\RestController;
defined("WPINC") or die();


use PPLCZ\Data\BatchData;
use PPLCZ\Data\BatchDataStore;
use PPLCZ\Model\Model\BatchModel;
use PPLCZ\Model\Model\PrepareShipmentBatchItemModel;
use PPLCZ\Model\Model\ShipmentWithAdditionalModel;
use PPLCZ\Setting\PrintSetting;
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
use WP_REST_Response;

class ShipmentBatchV1Controller extends  PPLRestController
{
    use ParcelDataModelTrait;

    protected $namespace = "ppl-cz/v1";

    protected $base = "shipment/batch";

    public function register_routes()
    {

        register_rest_route($this->namespace, "/". $this->base . "/(?P<id>\d+)/test/(?P<shipmentId>\d+)", [
            "methods" => "PUT",
            "callback" => [$this, "test_shipment"],
            "permission_callback" =>[$this, "check_permission"]
        ]);

        register_rest_route($this->namespace, "/" . $this->base . "/(?P<id>\d+)/preparing", [
            "methods" => \WP_REST_Server::EDITABLE,
            "callback" => [$this, "prepare_shipments"],
            "permission_callback"=>[$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, "/" . $this->base . "/shipment", [
            "methods" => \WP_REST_Server::EDITABLE,
            "callback" => [$this, "add_shipment_to_batch"],
            "permission_callback"=>[$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, "/" . $this->base, [
            "methods"=> \WP_REST_Server::READABLE,
            "callback"=>[$this, "get_batchs"],
            "permission_callback"=>[$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, "/" . $this->base . "/(?P<id>\d+)/shipment", [
            "methods"=> \WP_REST_Server::READABLE,
            "callback"=>[$this, "get_batch"],
            "permission_callback"=>[$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, "/" . $this->base . "/(?P<id>\d+)/reorder", [
            "methods"=> "PUT",
            "callback"=>[$this, "reorder"],
            "permission_callback"=>[$this, "check_permission"],
        ]);


        register_rest_route($this->namespace, "/" . $this->base . "/(?P<id>\d+)/shipment/(?P<shipmentId>\d+)", [
            "methods"=> \WP_REST_Server::DELETABLE,
            "callback"=>[$this, "remove_batch_shipment"],
            "permission_callback"=>[$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, "/" . $this->base . "/(?P<id>\d+)/shipment/(?P<shipmentId>\d+)/unlock", [
            "methods"=> \WP_REST_Server::EDITABLE,
            "callback"=>[$this, "unlock_batch_shipment"],
            "permission_callback"=>[$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, "/" . $this->base . "/(?P<id>\d+)/shipment", [
            "methods" => \WP_REST_Server::EDITABLE,
            "callback" => [$this, "add_shipment_to_batch"],
            "permission_callback"=>[$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, "/" . $this->base . "/(?P<id>\d+)/create-labels", [
            "methods" => \WP_REST_Server::EDITABLE,
            "callback" => [$this, "create_labels"],
            "permission_callback"=>[$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, "/" . $this->base . "/(?P<id>\d+)/refresh-labels", [
            "methods" => \WP_REST_Server::EDITABLE,
            "callback" => [$this, "refresh_labels"],
            "permission_callback"=>[$this, "check_permission"],
        ]);

        register_rest_route($this->namespace, "/". $this->base, [
            "methods" => \WP_REST_Server::CREATABLE,
            "callback" => [$this, "create_batch"],
            "permission_callback" =>[$this, "check_permission"]
        ]);


    }

    public function create_batch()
    {
        $batchData = new BatchData();
        $batchData->save();

        $response = new \WP_REST_Response();

        $response->set_headers([
            "Location" => rtrim(get_rest_url(), '/') . "/woocommerce-ppl/v1/shipment/batch/{$batchData->get_id()}"
        ]);

        $response->set_status(201);
        return $response;
    }


    public function get_batchs(\WP_REST_Request  $request)
    {

        $free = $request->get_param("free");
        /**
         * @var BatchData[] $batchs
         */
        $batchs = BatchData::get_batchs($free);

        foreach ($batchs as $key => $value)
        {
            $batchs[$key] = pplcz_denormalize($batchs[$key], BatchModel::class);
            $batchs[$key] = pplcz_normalize($batchs[$key]);
        }

        $response = new \WP_REST_Response();
        $response->set_data($batchs);

        return $response;
    }

    public function remove_batch_shipment(\WP_REST_Request $request)
    {
        $shipemntId = $request->get_param("shipmentId");
        $shipment = new ShipmentData($shipemntId);

        $resp = new WP_REST_Response();
        $resp->set_status(404);

        if (!$shipment->get_id())
        {
            $resp = new WP_REST_Response();
            $resp->set_status(404);
            return $resp;
        }

        $resp->set_status(204);

        try {
            $shipment->set_batch_local_id(null);
            $shipment->save();
        }
        catch (\Exception $ex)
        {
            pplcz_exception_handler($ex, true);
            $resp->set_status(500);
            $resp->set_data($ex->getMessage());
        }

        return $resp;
    }

    public function unlock_batch_shipment(\WP_REST_Request $request)
    {

        $shipemntId = $request->get_param("shipmentId");
        $shipment = new ShipmentData($shipemntId);

        $resp = new WP_REST_Response();
        $resp->set_status(404);

        if (!$shipment->get_id())
        {
            $resp = new WP_REST_Response();
            return $resp;
        }


        try {
            $shipment->unlock();
            $shipment->save();
            $resp->set_status(204);
        }
        catch (\Exception $ex)
        {
            pplcz_exception_handler($ex, true);
            $resp->set_status(500);
            $resp->set_data($ex->getMessage());
        }

        return $resp;
    }


    public function reorder(\WP_REST_Request $request )
    {
        $id = $request->get_param('id');
        $shipments = $request->get_json_params();

        ShipmentData::reorder_batch_shipments($id, $shipments);

        $rest = new WP_REST_Response();
        $rest->set_status(204);
        return $rest;
    }

    public function get_batch(\WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        $shipments = ShipmentData::read_batch_shipments($id);

        foreach ($shipments as $key => $shipment)
        {
            $shipments[$key] = pplcz_denormalize($shipments[$key], ShipmentWithAdditionalModel::class);
            $shipments[$key] = pplcz_normalize($shipments[$key]);
        }

        $response = new \WP_REST_Response();
        $response->set_data($shipments);

        return $response;
    }

    public function add_shipment_to_batch(\WP_REST_Request $request)
    {
        $data = $request->get_json_params();
        /**
         * @var PrepareShipmentBatchModel $data
         */
        $data = pplcz_denormalize($data, PrepareShipmentBatchModel::class);
        $id = $request->get_param('id');
        $asNew = false;

        $batchData = new BatchData($id);

        if (!$batchData->get_id())
        {
            $response = new \WP_REST_Response();
            $response->set_status(404);
            return $response;
        }

        $shipments = ShipmentData::read_batch_shipments($batchData->get_id());
        $added = false;
        foreach ($data->getItems() as $key => $item) {
            if ($item->getShipmentId())
            {
                $shipmentData = new ShipmentData($item->getShipmentId());
                if ($shipmentData->get_batch_local_id() == $batchData->get_id())
                    continue;

                if ($shipmentData->get_import_state() || $shipmentData->get_import_state() === "None") {
                    $shipmentData->set_batch_local_id($batchData->get_id());
                    $shipmentData->save();
                    $shipments[] = $shipmentData;
                    $added = true;
                }

            }
            else if ($item->getOrderId())
            {
                $finded = ShipmentData::find_shipments_by_wc_order($item->getOrderId());
                if ($finded)
                    continue;

                $order = new \WC_Order($item->getOrderId());
                if (self::hasPPLShipment($order)) {
                    $shipmentModel = pplcz_denormalize($order, ShipmentModel::class);
                    $shipmentData = pplcz_denormalize($shipmentModel, ShipmentData::class);
                    $shipmentData->set_batch_local_id($batchData->get_id());
                    $shipmentData->save();
                    $shipments[] = $shipmentData;
                    $added = true;
                }
            }
        }

        ShipmentData::reorder_batch_shipments($batchData->get_id(), array_map(function ($item) {
            return $item->get_id();
        }, $shipments));

        $resp = new \WP_REST_Response();

        $resp->set_headers([
            "Location" => rtrim(get_rest_url(), '/') . "/woocommerce-ppl/v1/shipment/batch/{$batchData->get_id()}"
        ]);

        $resp->set_status(204);
        if ($asNew) {
            $resp->set_status(201);
        }
        $resp->set_data(null);
        return $resp;

    }

    public function create_labels(\WP_REST_Request $request)
    {
        $data = $request->get_json_params();

        $id = $request->get_param("id");
        $batch = new BatchData($id);
        if (!$batch->get_id())
        {
            return new \WP_REST_Response(null, 404);
        }

        /**
         * @var CreateShipmentLabelBatchModel $data
         */
        $data = pplcz_denormalize($data, CreateShipmentLabelBatchModel::class);
        $print = $data->getPrintSetting();

        if ($print)
        {
            PrintSetting::setFormat($print);
        }

        $shipmentIds = $data->getShipmentId();
        $batchShipmentIds = array_map(function (ShipmentData $shipment) {
            return $shipment->get_id();
        }, ShipmentData::read_batch_shipments($batch->get_id()));

        if (array_diff($shipmentIds, $batchShipmentIds) || array_diff($batchShipmentIds, $shipmentIds))
            return new \WP_REST_Response(null, 400);

        $resp = new \WP_REST_Response();

        if (count($shipmentIds) > 100)
        {
            $resp->set_status(500);
            $resp->set_data("Maximální počet zásilek je 100");
            return $resp;

        }

        $cpl = new CPLOperation();

        $resp->set_status(204);

        try {
            $cpl->createPackages($batch->get_id(), $print);
        }
        catch (\Exception $exception)
        {
            $resp->set_status(500);
            if ($exception->getCode() === 400)
                $resp->set_data("V některých zásilkách jsou chyby");
            else
                $resp->set_data($exception->getMessage());
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

        $id = $request->get_param("id");
        $batch = new BatchData($id);
        if (!$batch->get_id())
        {
            return new \WP_REST_Response(null, 404);
        }

        $data = $request->get_json_params();

        $batchShipments = ShipmentData::read_batch_shipments($batch->get_id());

        /**
         * @var PrepareShipmentBatchModel $data
         */
        $data = pplcz_denormalize($data, PrepareShipmentBatchModel::class);

        $shipmentIds = array_filter(array_map(function(PrepareShipmentBatchItemModel $item) {
            return $item->getShipmentId();
        }, $data->getItems()));

        $batchShipmentIds = array_map(function (ShipmentData $shipment) {
            return $shipment->get_id();
        }, $batchShipments);

        if (array_diff($shipmentIds, $batchShipmentIds) || array_diff($batchShipmentIds, $shipmentIds))
            return new \WP_REST_Response(null, 400);

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
                    pplcz_validate($shipmentModel, $error, "items.$key");
                }
            }
            else
            {
                $error->add("items.$key", "Nelze automaticky vytvořit zásilku z objednávky");
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
        }

        ShipmentData::reorder_batch_shipments($batch->get_id(), array_values($output));

        $model = new PrepareShipmentBatchReturnModel();
        $model->setShipmentId($output);

        $resp = new \WP_REST_Response();
        $resp->set_data(pplcz_normalize($model, "array"));
        return $resp;
    }

    public function refresh_labels(\WP_REST_Request $request)
    {
        $id = $request->get_param("id");
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
            $ids[] = $shipmentData->get_batch_id();
        }

        $refresh = new RefreshShipmentBatchReturnModel();
        $refresh->setShipments($output);
        $refresh->setBatchs(array_filter($ids));
        $data = pplcz_normalize($refresh);
        $resp = new \WP_REST_Response();
        $resp->set_data($data);

        return $resp;
    }
}