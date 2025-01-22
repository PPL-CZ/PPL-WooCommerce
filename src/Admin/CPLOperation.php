<?php
namespace PPLCZ\Admin;
defined("WPINC") or die();


use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentResultChildItemModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentResultItemModel;
use PPLCZVendor\GuzzleHttp\HandlerStack;
use PPLCZCPL\Api\AccessPointApi;
use PPLCZCPL\Api\AddressWhisperApi;
use PPLCZCPL\Api\CodelistApi;
use PPLCZCPL\Api\CustomerApi;
use PPLCZCPL\Api\DataApi;
use PPLCZCPL\Api\OrderBatchApi;
use PPLCZCPL\Api\OrderEventApi;
use PPLCZCPL\Api\ShipmentApi;
use PPLCZCPL\Api\ShipmentBatchApi;
use PPLCZCPL\Api\ShipmentEventApi;
use PPLCZCPL\ApiException;
use PPLCZCPL\Configuration;
use PPLCZCPL\Model\EpsApiInfrastructureWebApiModelProblemJsonModel;
use PPLCZCPL\Model\EpsApiMyApi2BusinessEnumsConstPageSize;
use PPLCZCPL\Model\EpsApiMyApi2WebConstantsConstLabelFormat;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsEnumOrderType;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsOrderBatchCreateOrderBatchModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsOrderBatchOrderModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsOrderBatchOrderModelSender;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsOrderEventCancelOrderEventModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModelLabelSettings;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchExternalNumberModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchLabelSettingsModelCompleteLabelSettings;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchRecipientAddressModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelInsurance;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelSender;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelShipmentSet;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModelWeighedShipmentInfo;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentShipmentModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentShipmentStates;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentTrackAndTraceItemModel;
use PPLCZCPL\ObjectSerializer;
use PPLCZVendor\Psr\Http\Message\RequestInterface;
use PPLCZ\Data\AddressData;
use PPLCZ\Data\CodBankAccountData;
use PPLCZ\Data\CollectionData;
use PPLCZ\Data\PackageData;
use PPLCZ\Data\ParcelData;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Model\Model\LabelPrintModel;
use PPLCZ\Model\Model\WhisperAddressModel;
use PPLCZ\Serializer;


class CPLOperation
{

    public const PROD_VERSION = true;

    public const BASE_URL = "https://api.dhl.com/ecs/ppl/myapi2";
    public const ACCESS_TOKEN_URL = "https://api.dhl.com/ecs/ppl/myapi2/login/getAccessToken";

    public const INTEGRATOR = "4546462";

    public function getAvailableLabelPrinters()
    {
        $available = [
            [
                "title" => "1x etiketa na stránku, tisk do PDF souboru",
                "code" => "1/PDF"
            ],
            [
                "title" => "A4 4x (začíná od 1. pozice) etiketa na stránku, tisk do PDF souboru",
                "code" => "4/PDF"
            ],
            [
                "title" => "A4 4x  (začíná od 2. pozice) etiketa na stránku, tisk do PDF souboru",
                "code" => "4.2/PDF"
            ],
            [
                "title" => "A4 4x  (začíná od 3. pozice) etiketa na stránku, tisk do PDF souboru",
                "code" => "4.3/PDF"
            ],
            [
                "title" => "A4 4x  (začíná od 4. pozice) etiketa na stránku, tisk do PDF souboru",
                "code" => "4.4/PDF"
            ]
        ];

        return array_map(function($item) {
            return Serializer::getInstance()->denormalize($item, LabelPrintModel::class);
        }, $available);
    }

    public function getFormat($format)
    {
        switch($format) {
            case '1/PDF':
            case '4/PDF':
            case '4.2/PDF':
            case '4.3/PDF':
            case '4.4/PDF':
                return $format;
        }
        return "4/PDF";
    }

    public function reset()
    {
        delete_option(pplcz_create_name("access_token"));
    }

    public function clearAccessToken()
    {
        delete_option(pplcz_create_name("access_token"));
    }

    public function getAccessToken()
    {
        $content = get_option(pplcz_create_name("access_token"));

        if ($content) {

            list($a, $b, $c) = explode(".", $content);
            if ($b) {
                $b = json_decode(base64_decode($b), true);
                if ($b["exp"] > time() - 20) {
                    return $content;
                }
            }
        }
        $client_secret = get_option(pplcz_create_name("client_secret")) ?: get_option(pplcz_create_name("secret"));
        $client_id = get_option(pplcz_create_name("client_id"));
        $auth = "Basic " . base64_encode("$client_id:$client_secret");

        $headers = ["Content-Type: application/x-www-form-urlencoded"];
        if (strpos(self::ACCESS_TOKEN_URL, "getAccessToken") === false) {
            $headers[] = "Authorization: $auth";
        }

        $content = ["grant_type" => "client_credentials"];
        if (strpos(self::ACCESS_TOKEN_URL, "getAccessToken") !== false) {
            $content["client_id"] = get_option(pplcz_create_name("client_id"));
            $content["client_secret"] =  get_option(pplcz_create_name("client_secret")) ?: get_option(pplcz_create_name("secret"));
        }

        $opts = array('http' =>
            array(
                'ignore_errors' => true,
                'timeout' => 5,
                'method' => 'POST',
                'header' => join("\r\n", $headers),
                'content' => http_build_query($content),
            ));

        $context = stream_context_create($opts);
        $url = self::ACCESS_TOKEN_URL;
        $content = @file_get_contents("{$url}", false, $context); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
        $httpResponse = @$http_response_header;
        if ($httpResponse && strpos($http_response_header[0], "200 OK")) {
            if ($content) {
                $tokens = json_decode($content, true);
                add_option(pplcz_create_name("access_token"), $tokens["access_token"]) || update_option(pplcz_create_name("access_token"), $tokens["access_token"]);
                return $tokens["access_token"];
            }
        } else {
            $errorMaker = "Url: {$url}\n";
            $errorMaker .= join("\n", is_array($httpResponse) ? @$httpResponse : []);
            if ($content)
                $errorMaker .= "\n" . $content;
            else
                $errorMaker .= "\nno content";

            set_transient(pplcz_create_name("access_token_error"),  $errorMaker, 1800);
        }

        return null;
    }

    public function createClientAndConfiguration()
    {
        $handler = HandlerStack::create();
        $handler->push(function ( $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if ($request->getMethod() === "GET" || $request->getMethod() === "OPTIONS" || $request->getMethod() === "HEAD") {
                    $request = $request->withoutHeader("Content-Type");
                }
                else if ($request->getMethod() === "POST" || $request->getMethod() === "PUT" || $request->getMethod() === "PATCH") {
                    if (!$request->hasHeader("Content-Length")) {
                        $request = $request->withAddedHeader("Content-Length", $request->getBody()->getSize());
                        if (!$request->getBody()->getSize())
                            $request = $request->withoutHeader("Content-Type");
                    }
                }
                return $handler($request, $options);
            };
        });


        $client = new \PPLCZVendor\GuzzleHttp\Client([
            "handler" => $handler
        ]);

        $configuration = new Configuration();
        $configuration->setAccessToken($this->getAccessToken());
        $url = self::BASE_URL;
        $configuration->setHost($url);

        return [$client, $configuration];
    }

    /**
     * @param $shipments
     * @return void
     * @throws ApiException
     *
     * Vytvoření zásilek
     */
    public function createPackages($shipments = [], $print = null)
    {

        /**
         * @var ShipmentData[] $shipments
         */
        $data = [];
        foreach ($shipments as $key => $value) {
            $shipments[$key] = new ShipmentData($value);
            $shipments[$key]->ignore_lock();
            $shipments[$key]->set_import_errors(null);
            $shipments[$key]->save();
            $data[$key] = $shipments[$key]->get_props_for_store();
            $shipments[$key] = new ShipmentData($value);
        }
        $send = null;
        try {
            $send = Serializer::getInstance()->denormalize($shipments, EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModel::class);
        }
        catch (\Exception $exception)
        {
            foreach ($shipments as $key => $value) {
                $value->ignore_lock();
                $value->set_lock(false);
                $value->save();
            }
            throw $exception;
        }

        list($client, $configuration) = $this->createClientAndConfiguration();
        $shipmentBatchApi = new ShipmentBatchApi($client, $configuration);

        try {
            $output = $shipmentBatchApi->createShipmentsWithHttpInfo($send, "cs-CZ");
            $location = reset($output[2]["Location"]);
            $location = explode("/", $location);
            $batch_id = end($location);

            foreach ($shipments as $shipment) {
                $shipment->ignore_lock();
                pplcz_set_batch_print($batch_id, $print);
                $shipment->set_import_state("InProgress");
                $shipment->set_batch_id($batch_id);
                if (!$shipment->get_lock())
                    $shipment->lock();
                else
                    $shipment->save();
            }
        }
        catch (\Exception $ex) {
            foreach ($shipments as $position => $shipment) {
                if ($shipment->get_lock()) {
                    $shipment->set_lock(false);
                    $shipment->hard_lock();
                    $shipment->save();
                    $address = $shipment->get_recipient_address_id();
                    $add = new AddressData($address);
                    if ($add->get_lock())
                    {
                        $add->hard_lock();
                        $add->set_lock(false);
                        $add->save();
                    }
                }
                if ($ex instanceof  ApiException && $ex->getResponseObject() instanceof  EpsApiInfrastructureWebApiModelProblemJsonModel) {
                    /**
                     * @var array<string,string[]> $error
                     */
                    $errors = [];
                    $responseErrors = $ex->getResponseObject()->getErrors();
                    foreach ($responseErrors as $errorKey =>$error )
                    {
                        $arguments = [];
                        if (preg_match('/^shipments\[([0-9]+)]($|\.)/i', $errorKey, $arguments )){
                            if ("{$arguments[1]}" === "$position") {
                                foreach ($error as $err) {
                                    $errors[] = "{$err}";
                                }
                            }

                        }
                    }
                    if ($errors) {
                        $errors = join("\n", $errors);
                        $shipment->set_import_errors($errors);
                        $shipment->set_import_state("None");
                        $shipment->ignore_lock();
                        $shipment->save();
                    }
                } else {
                    $shipment->set_import_errors($ex->getMessage());
                    $shipment->set_import_state("None");
                    $shipment->ignore_lock();
                    $shipment->save();
                }
            }

            throw $ex;
        }
        return;
    }

    /**
     * @param $packageId
     * @return void
     * @throws ApiException
     *
     * Zrušení zásilky
     */
    public function cancelPackage($packageId)
    {
        $package = new PackageData($packageId);
        list($client, $configuration) = $this->createClientAndConfiguration();
        $shipmentApi = new ShipmentEventApi($client, $configuration);
        $shipmentNumber = $package->get_shipment_number();
        $shipmentApi->shipmentShipmentNumberCancelPost($shipmentNumber);
        $package->set_phase("Canceled");
        $package->ignore_lock();
        $package->save();
    }

    /**
     * @param $batchId
     * @return void
     * @throws ApiException
     *
     * Stažení etiket pro zásilky, které byly vytvořeny v rámci jednoho /shipment/batch
     */
    public function getLabelContents($batchId, $referenceId = null, $packageId = null, $printFormat = null)
    {
        list($client, $configuration) = $this->createClientAndConfiguration();

        $shipmentApi = new ShipmentBatchApi($client, $configuration);

        $format = ($printFormat ?: get_option(pplcz_create_name("print_setting"), ""));
        $format = $this->getFormat($format);

        switch($format) {
            case '1/PDF':
                $position = 1;
                $format = 'default';
                break;
            case "4.2/PDF":
                $position = 2;
                $format = 'A4';
                break;
            case "4.3/PDF":
                $position = 3;
                $format = 'A4';
                break;
            case "4.4/PDF":
                $position = 4;
                $format = 'A4';
                break;
            default:
                $position = 1;
                $format = 'A4';
                break;
        }

        if (!$referenceId) {
            $httpData = $shipmentApi->getShipmentBatchLabelWithHttpInfo($batchId, 100, 0, $format, $position);
            if (!$httpData) {
                return;
            }
            header("Content-Type: " . $httpData[2]["Content-Type"][0]);
            /**
             * @var \SplFileInfo $file
             */
            $file = $httpData[0];
            $path = $file->getPathname();
            $content = file_get_contents($path); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

            exit($content); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        else {
            // načtu si info kolem batch
            $data = $shipmentApi->getShipmentBatch($batchId);
            $items = $data->getItems();
            usort($items, function (EpsApiMyApi2WebModelsShipmentBatchShipmentResultItemModel $first, EpsApiMyApi2WebModelsShipmentBatchShipmentResultItemModel $second) {
                return strcmp($first->getReferenceId(), $second->getReferenceId());
            });

            $offset = 0;
            $founded = false;

            foreach ($items as $item) {
                $isReference = $item->getReferenceId() === $referenceId;
                if ($isReference && $packageId && $item->getShipmentNumber() === $packageId) {
                    $founded = $item;
                    break;
                }

                if (!$packageId && $isReference) {
                    $founded = $item;
                    break;
                }

                $offset++;
                $items2 = $item->getRelatedItems() ?? [];

                usort($items2, function (EpsApiMyApi2WebModelsShipmentBatchShipmentResultChildItemModel $a, EpsApiMyApi2WebModelsShipmentBatchShipmentResultChildItemModel $b) {
                    return strcmp($a->getShipmentNumber(), $b->getShipmentNumber());
                });

                foreach ($items2 as $item2) {
                    if ($isReference && $item2->getShipmentNumber() === $packageId) {
                        $founded = $item;
                        break 2;
                    }
                    $offset++;
                }

                if ($isReference)
                    throw new \Exception("Problem s nalezením zásilky k tisku");
            }

            if (!$founded)
                throw new \Exception("Problem s nalezením zásilky k tisku");

            $items = $founded->getRelatedItems() ?? [];
            $max = $packageId ? 1: (count($items) + 1);


            $httpData = $shipmentApi->getShipmentBatchLabelWithHttpInfo($batchId, $max, $offset, $format, $position, null, null, null, "ReferenceId,ShipmentNumber");
            if (!$httpData) {
                return;
            }

            header("Content-Type: " . $httpData[2]["Content-Type"][0]);

            /**
             * @var \SplFileInfo $file
             */
            $file = $httpData[0];
            $path = $file->getPathname();
            $content = file_get_contents($path); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents


            exit($content); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }

    /**
     * @param $packageId
     * @return void
     * @throws ApiException
     *
     * Stažení etikety pro zásilku
     */
    public function getLabelContent($packageId)
    {
        $packageId = new PackageData($packageId);

        list($client, $configuration) = $this->createClientAndConfiguration();
        $labelApi = new DataApi($client, $configuration);

        $httpData = $labelApi->getDataWithHttpInfo($packageId->get_label_id());
        if (!$httpData) {
            return;
        }

        header("Content-Type: " .$httpData[2]["Content-Type"][0]);
        /**
         * @var \SplFileInfo $file
         */
        $file = $httpData[0];
        $path = $file->getPathname();
        $content = file_get_contents($path); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

        exit($content); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }


    /**
     * @param $batchIds
     * @return void
     * @throws ApiException
     *
     * Otestování vytvořených zásilek /shipment/batch, zjištění následných chyb a nebo uložení čísla balíku a uložení url na stažení etikety
     *
     */
    public function loadingShipmentNumbers($batchIds = [])
    {
        $batch_label_group = gmdate("Y-m-d H:i:s");
        foreach ($batchIds as $item) {

            list($client, $configuration) = $this->createClientAndConfiguration();

            $shipmentBatchApi = new ShipmentBatchApi($client, $configuration);


            $batchData = $shipmentBatchApi->getShipmentBatchWithHttpInfo($item);

            $batchData = $batchData[0];
            $shipments = ShipmentData::read_batch_shipments($item);


            foreach ($batchData->getItems() as $batchItem) {
                $referenceId = $batchItem->getReferenceId();
                $referenceShipments = array_filter($shipments, function ($item) use ($referenceId) {
                    return $item->get_reference_id() == $referenceId;
                });
                $baseShipmentNumber = $batchItem->getShipmentNumber();
                $errorCode = $batchItem->getErrorCode();
                $errorMessage = $batchItem->getErrorMessage();


                foreach ($referenceShipments as $shipment) {
                    $packages = $shipment->get_package_ids();
                    foreach ($packages as $key => $package)
                    {
                        $packages[$key] = new PackageData($package);
                    }

                    $package = array_filter($packages, function ($item) use ($baseShipmentNumber) {
                        return $item->get_shipment_number() && $item->get_shipment_number() === $baseShipmentNumber;
                    });

                    if (!$package) {
                        $package = array_filter($packages, function ($item) use ($baseShipmentNumber) {
                            return !$item->get_shipment_number();
                        });
                    }
                    if ($package) {
                        $package = reset($package);
                        $package->ignore_lock();
                        $package->set_wc_order_id($shipment->get_wc_order_id());
                        if ($batchItem->getLabelUrl()) {
                            $label_id = explode("/", $batchItem->getLabelUrl());
                            $label_id = end($label_id);
                            $package->set_label_id($label_id);
                        }
                        $package->set_shipment_number($baseShipmentNumber);
                        $package->set_import_error($errorMessage);
                        $package->set_import_error_code($errorCode);
                        $package->save();
                    }

                    $packages = array_filter($packages, function ($item) use($baseShipmentNumber) {
                        return $item->get_shipment_number() !== $baseShipmentNumber;
                    });

                    foreach ($batchItem->getRelatedItems() as $relatedItem) {
                        $shipmentNumber = $relatedItem->getShipmentNumber();

                        $package = array_filter($packages, function ($item) use ($shipmentNumber) {
                            return $item->get_shipment_number() && $item->get_shipment_number() === $shipmentNumber;
                        });

                        if (!$package) {
                            $package = array_filter($packages, function ($item) use ($shipmentNumber) {
                                return !$item->get_shipment_number();
                            });
                        }

                        if ($package) {
                            $package = reset($package);
                            $package->ignore_lock();

                            if ($relatedItem->getLabelUrl()) {
                                $label_id = explode("/", $relatedItem->getLabelUrl());
                                $label_id = end($label_id);
                                $package->set_label_id($label_id);
                            }
                            $package->set_shipment_number($relatedItem->getShipmentNumber());
                            $package->set_import_error($relatedItem->getErrorMessage());
                            $package->set_import_error_code($relatedItem->getErrorCode());
                            $package->save();
                        }
                    }
                    if ($batchData->getCompleteLabel()) {
                        $shipment->set_batch_label_group($batch_label_group);
                    }
                    else
                        $shipment->set_batch_label_group(null);

                    $shipment->set_import_state($batchItem->getImportState());
                    $shipment->ignore_lock();
                    $shipment->save();
                }
            }
        }
    }

    public function cancelCollection($idcoll)
    {
        $collection = new CollectionData($idcoll);
        list($client, $configuration) = $this->createClientAndConfiguration();
        $order = new OrderEventApi($client, $configuration);
        $ev = new EpsApiMyApi2WebModelsOrderEventCancelOrderEventModel();
        $ev->setNote("Zrušeno na vyžádání");
        try {
            $remoteId = $collection->get_remote_collection_id();
            $order->orderCancelPost(null, $remoteId, null, null, null, $ev);
            $collection->set_state("Canceled");
            $collection->save();
        }
        catch (\Exception $ex)
        {
            throw $ex;
        }

    }

    public function createCollection($idcoll)
    {
        $collection = new CollectionData($idcoll);
        list($client, $configuration) = $this->createClientAndConfiguration();

        $order = new OrderBatchApi($client, $configuration);
        $modelBatch = new EpsApiMyApi2WebModelsOrderBatchCreateOrderBatchModel();

        $model = new EpsApiMyApi2WebModelsOrderBatchOrderModel();
        $model->setOrderType(EpsApiMyApi2WebModelsEnumOrderType::COLLECTION_ORDER);
        $model->setSendDate(new \DateTime($collection->get_send_date()));
        $model->setProductType("BUSS");
        $model->setReferenceId($collection->get_reference_id());

        $sender = new EpsApiMyApi2WebModelsOrderBatchOrderModelSender();
        $sender->setEmail($collection->get_email());
        $sender->setPhone($collection->get_telephone());
        $sender->setContact($collection->get_contact());

        $address = require_once  __DIR__ . '/../config/collection_address.php';

        $sender->setCity($address['city']);
        $sender->setZipCode($address['zip']);
        $sender->setCountry($address['country']);
        $sender->setStreet($address['street']);
        $sender->setName($address['name']);

        $model->setSender($sender);

        $model->setShipmentCount($collection->get_estimated_shipment_count());
        $model->setNote($collection->get_note());
        $model->setEmail($collection->get_email());
        $modelBatch->setOrders([$model]);

        $output = $order->createOrdersWithHttpInfo($modelBatch);

        $location = reset($output[2]["Location"]);
        $location = explode("/", $location);
        $batch_id = end($location);

        $collection->set_remote_collection_id($batch_id);
        $collection->set_state("Created");
        $collection->set_send_to_api_date(gmdate("Y-m-d"));
        $collection->save();

    }



    public function testPackageStates(array $shipments)
    {
        if (!$shipments) {
            return [];
        }

        $statuses = require_once __DIR__ . '/../config/statuses.php';

        list($client, $configuration) = $this->createClientAndConfiguration();

        $accessPointApi = new ShipmentApi($client, $configuration);

        $min = count($shipments);

        $content = $accessPointApi->shipmentGetWithHttpInfo($min, 0, array_map(function ($item) {
            return (new PackageData($item))->get_shipment_number();
        }, $shipments));

        $data = $content[0];

        $returnData = [];

        /**
         * @var EpsApiMyApi2WebModelsShipmentShipmentModel[] $data
         */
        foreach ($data as $item) {
            $trackAndTrace = $item->getTrackAndTrace();
            $shipmentNumber = $item->getShipmentNumber();
            $url = $trackAndTrace->getPartnerUrl();
            $events = $trackAndTrace->getEvents();


            /**
             * @var EpsApiMyApi2WebModelsShipmentTrackAndTraceItemModel $lastEvent
             */

            $lastEvent = end($events);
            $codPayed = array_filter($events, function ($item) {
                return $item->getPhase() === "CodPaidDate";
            });
            if ($lastEvent) {
                $returnData[$shipmentNumber] = [
                    'phase' => $lastEvent->getPhase() === null ? "Canceled" : $lastEvent->getPhase(),
                    'name' => $lastEvent->getName(),
                    "code" => $lastEvent->getCode(),
                    "status"=> $lastEvent->getStatusId(),
                    'url' => $url,
                    'payed' => $codPayed
                ];
            }
        }

        $db = array_map(function ($item) {
            return new PackageData($item);
        }, $shipments);


        foreach ($returnData as $shipmentNumber => $data) {

            foreach (array_filter($db,function (PackageData $package) use ($shipmentNumber) {
                return "{$package->get_shipment_number()}" === "$shipmentNumber";
            }) as $key => $package) {
                unset($db[$key]);
                /**
                 * @var PackageData $package
                 */
                if ($package->get_phase() !== $data['phase']
                    || $package->get_status() !== $data['status'] ) {


                    if ($data['phase'] === null)
                        $data['phase'] = 'Canceled';

                    $package->set_status($data["status"]);
                    $package->set_status_label(@$statuses[$data['status']]);
                    $package->set_phase($data["phase"]);
                    $package->set_phase_label($data['name']);
                    $package->set_last_update_phase(gmdate("Y-m-d H:i:s"));
                    $package->set_last_test_phase(gmdate("Y-m-d H:i:s"));
                    $package->set_import_error(null);
                    $package->set_import_error_code(null);
                    $package->ignore_lock();

                    $package->save();

                    if ($data["payed"]) {
                        $shipmentId = $package->get_ppl_shipment_id();
                        $shipment = new ShipmentData($shipmentId);
                        $order = $shipment->get_wc_order_id();
                        if ($order) {
                            $order = new \WC_Order($order);
                            $hasCodStatus = $order->get_meta("_" . pplcz_create_name("_cod_change_status"));
                            if (!$hasCodStatus) {
                                $order->set_meta_data(["_" . pplcz_create_name("_cod_change_status") => true]);
                                $order->set_status("Completed");
                                $order->save();
                            }
                        }
                    }
                } else {
                    $package->ignore_lock();
                    $package->set_import_error(null);
                    $package->set_import_error_code(null);
                    if (!$package->get_last_update_phase())
                        $package->set_last_update_phase(gmdate("Y-m-d H:i:s"));
                    $package->set_last_test_phase(gmdate("Y-m-d H:i:s"));
                    $package->save();
                }
            }
        }

        foreach ($db as $package)
        {
            $package->ignore_lock();
            $package->set_import_error("NotFound");
            $package->set_import_error_code("NotFound");
            if (!$package->get_last_update_phase())
                $package->set_last_update_phase(gmdate("Y-m-d H:i:s"));
            $package->set_last_test_phase(gmdate("Y-m-d H:i:s"));
            $package->save();
        }
    }

    public function findParcel($code)
    {

        list($client, $configuration) = $this->createClientAndConfiguration();

        $accessPointApi = new AccessPointApi($client, $configuration);
        $founded = $accessPointApi->accessPointGet(100,0, $code);
        if (is_array($founded)) {
            return $founded[0];
        }
        return null;
    }

    public function getShipmentPhases()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();

        $codelistApi = new CodelistApi($client, $configuration);
        $limitApi = $codelistApi->codelistShipmentPhaseGet(300,0);

        $output = [];

        foreach ($limitApi as $key => $val) {
            $output[$val->getCode()] = $val->getName();
        }

        return $output;
    }


    public function getStatuses()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();

        $codelistApi = new CodelistApi($client, $configuration);
        $limitApi = $codelistApi->codelistStatusGet(300,0);

        $output = [];

        foreach ($limitApi as $key => $val) {
            $output[$val->getCode()] = $val->getName();
        }

        return $output;
    }

    public function getCountries()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();

        $codelistApi = new CodelistApi($client, $configuration);
        $limitApi = $codelistApi->codelistCountryGet(300,0);

        $output = [];

        foreach ($limitApi as $key => $val) {
            $output[$val->getCode()] = $val->getCashOnDelivery();
        }

        return $output;
    }

    public function getCollectionAddresses()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();

        $codelistApi = new CustomerApi($client, $configuration);
        $addresses = $codelistApi->customerAddressGet();

        $output = [];

        return $addresses;
    }


    public function getLimits()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();
        $codelistApi = new CodelistApi($client, $configuration);
        $limitApi = $codelistApi->codelistServicePriceLimitGet(300, 0);
        $insrs = [];
        $cods = [];

        foreach ($limitApi as $item) {
            if ($item->getService() === "INSR") {
                $insrs[] = [

                    "product" => $item->getProduct(),
                    "min" => $item->getMinPrice(),
                    "max" => $item->getMaxPrice(),
                    "currency" => $item->getCurrency()
                ];
            }
            else if ($item->getService() === "COD")
            {
                $cods[] = [
                    "product" => $item->getProduct(),
                    "min" => $item->getMinPrice(),
                    "max" => $item->getMaxPrice(),
                    "currency" => $item->getCurrency()
                ];
            }
        }
        return [
            'COD' => $cods,
            "INSURANCE" => $insrs
        ];
    }

    public function getCodCurrencies()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();

        $customerApi = new CustomerApi($client, $configuration);
        $content = $customerApi->customerGet();
        $currencies = [];
        foreach ($content->getAccounts() as $item) {
            $currencies[] = [
                'country' => $item->getCountry(),
                'currency' => $item->getCurrency(),
            ];
        }
        return $currencies;
    }

    public function whisper($street = null, $city =null, $zip = null)
    {
        $accessToken = $this->getAccessToken();
        if ($accessToken && ($street || $city || $zip)) {
            list($client, $configuration) = $this->createClientAndConfiguration();

            $whisper = new AddressWhisperApi($client, $configuration);
            $founded = $whisper->addressWhisperGet($street, $zip ? trim($zip): null, $city ? trim($city) : null, trim($city) ? 'City' : 'Street');
            $output = [];
            foreach ($founded as $key => $item) {
                $wp = new WhisperAddressModel();
                if ($item->getCity())
                    $wp->setCity($item->getCity());
                if ($item->getStreet())
                    $wp->setStreet($item->getStreet());
                if ($item->getZipCode())
                    $wp->setZipCode($item->getZipCode());
                if ($item->getEvidenceNumber())
                    $wp->setEvidenceNumber($item->getEvidenceNumber());
                if ($item->getHouseNumber())
                    $wp->setHouseNumber($item->getHouseNumber());
                $output[] = $wp;
            }
            return $output;
        }
        return [];
    }
}