<?php

namespace PPLCZ\ModelNormalizer;

defined("WPINC") or die();

use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLCZ\Data\AddressData;
use PPLCZ\Data\CodBankAccountData;
use PPLCZ\Data\PackageData;
use PPLCZ\Data\ParcelData;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Model\Model\BankAccountModel;
use PPLCZ\Model\Model\PackageModel;
use PPLCZ\Model\Model\ParcelAddressModel;
use PPLCZ\Model\Model\ParcelDataModel;
use PPLCZ\Model\Model\ProductModel;
use PPLCZ\Model\Model\RecipientAddressModel;
use PPLCZ\Model\Model\SenderAddressModel;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Model\Model\UpdateShipmentBankAccountModel;
use PPLCZ\Model\Model\UpdateShipmentModel;
use PPLCZ\Model\Model\UpdateShipmentSenderModel;
use PPLCZ\ShipmentMethod;
use PPLCZ\Serializer;
use PPLCZ\Traits\ParcelDataModelTrait;

class ShipmentDataDenormalizer implements DenormalizerInterface
{

    use ParcelDataModelTrait;

    private function getServiceCodeFromOrder(\WC_Order $order)
    {

        $shippingMethods = array_filter($order->get_shipping_methods(), function(\WC_Order_Item_Shipping $item)  {
            return str_contains($item->get_method_id(), pplcz_create_name(""));
        });

        if ($shippingMethods) {
            /**
             * @var \WC_Order_Item_Shipping $shippingMethod
             */
            $shippingMethod = reset($shippingMethods);
            $method = new ShipmentMethod($shippingMethod->get_instance_id() ?: $shippingMethod->get_method_id());
            $code = $method->getMethodCodeByPayment($order->get_payment_method());
            $code = ShipmentMethod::methodsFor($order->get_shipping_country(), $code);
            $title = ShipmentMethod::methodsWithCod()[$code];
            $parcel = self::getParcelDataModel($shippingMethod, true);
            return [$code, $title, ShipmentMethod::isMethodWithCod($code), $parcel];
        }
        return [null, null, false, null];
    }

    private function ShipmentDataToModel(ShipmentData $data, array $context = [])
    {
        $shipmentModel = new ShipmentModel();
        $shipmentModel->setId($data->get_id());
        $shipmentModel->setImportState($data->get_import_state());

        $shipmentModel->setPrintState(pplcz_get_shipment_print($data->get_id()) ?: pplcz_get_batch_print($data->get_batch_id()));

        if ($data->get_wc_order_id())
            $shipmentModel->setOrderId($data->get_wc_order_id());

        if ($data->get_note())
            $shipmentModel->setNote($data->get_note());

        if ($data->get_has_parcel())
            $shipmentModel->setHasParcel($data->get_has_parcel());

        if ($data->get_reference_id())
            $shipmentModel->setReferenceId($data->get_reference_id());

        if ($data->get_service_code())
            $shipmentModel->setServiceCode($data->get_service_code());

        if ($data->get_service_name())
            $shipmentModel->setServiceName($data->get_service_name());

        if ($data->get_batch_label_group())
            $shipmentModel->setBatchLabelGroup($data->get_batch_label_group());


        if($data->get_cod_value())
            $shipmentModel->setCodValue($data->get_cod_value());

        if ($data->get_cod_value_currency())
            $shipmentModel->setCodValueCurrency($data->get_cod_value_currency());

        if ($data->get_cod_variable_number()) {
            $cod = $data->get_cod_variable_number();
            $shipmentModel->setCodVariableNumber($cod);
        }

        $id = $data->get_sender_address_id() ?? $data->get_sender_address_id("default");
        if ($id)
        {
            $sender = new AddressData($id);
            if ($sender->get_id())
                $shipmentModel->setSender(Serializer::getInstance()->denormalize($sender, SenderAddressModel::class));
        }

        $id = $data->get_recipient_address_id();
        if ($id) {
            $recipient = new AddressData($id);
            if ($recipient->get_id())
                $shipmentModel->setRecipient(Serializer::getInstance()->denormalize($recipient, RecipientAddressModel::class));
        }

        if ($data->get_parcel_id())
        {
            $parcel = new ParcelData($data->get_parcel_id());
            if ($parcel) {
                $shipmentModel->setParcel(Serializer::getInstance()->denormalize($parcel, ParcelAddressModel::class));
            }
        }

        if ($data->get_note())
            $shipmentModel->setNote($data->get_note());
        if ($data->get_age())
            $shipmentModel->setAge($data->get_age());
        if ($data->get_lock())
            $shipmentModel->setLock($data->get_lock());

        if ($data->get_import_errors())
            $shipmentModel->setImportErrors(array_filter(explode("\n", $data->get_import_errors()), "trim"));
        else
            $shipmentModel->setImportErrors([]);

        $packages =array_map(function ($item) {
            $model = new PackageData($item);
            return Serializer::getInstance()->denormalize($model, PackageModel::class);
        }, $data->get_package_ids());
        if (!$packages) {
            $orderId = $shipmentModel->getOrderId();

            $model = new PackageModel();
            $model->setReferenceId($orderId);
            $model->setPhase("None");
            $package = Serializer::getInstance()->denormalize($model, PackageData::class);
            $package->set_phase("None");
            $package->save();
            $data->set_package_ids([$package->get_id()]);
            $data->save();

            $packages[] = $model;

        }
        $shipmentModel->setPackages($packages);
        return $shipmentModel;

    }


    private function OrderToModel(\WC_Order $data, array $context = [])
    {
        $shipmentModel = new ShipmentModel();

        $shipment = new ShipmentData();


        $shipmentModel->setImportState("None");
        $shipmentModel->setOrderId($data->get_id());
        if ($data->get_customer_note())
            $shipmentModel->setNote($data->get_customer_note());

        // $dm = gmdate("ymd");
        $count = 10 - strlen("");
        $variable = str_pad($data->get_id(), $count, "0", STR_PAD_LEFT);

        $shipmentModel->setReferenceId($data->get_id() . '#' . gmdate("YdmHis"));
        $shipmentModel->setImportErrors([]);

        list($code, $title, $isCod, $parcel) = $this->getServiceCodeFromOrder($data);

        if ($code)
            $shipmentModel->setServiceCode($code);
        if ($title)
            $shipmentModel->setServiceName($title);

        if ($isCod) {
            $shipmentModel->setCodVariableNumber($variable);
            $shipmentModel->setCodValue($data->get_total(""));
            $shipmentModel->setCodValueCurrency($data->get_currency());
        }

        $id = $shipment->get_sender_address_id("default");
        if ($id)
        {
            $sender = new AddressData($id);
            if ($sender->get_id())
                $shipmentModel->setSender(Serializer::getInstance()->denormalize($sender, SenderAddressModel::class));
        }

        $shipmentModel->setRecipient(Serializer::getInstance()->denormalize($data, RecipientAddressModel::class));

        if ($parcel) {

            /**
             * @var ParcelDataModel $parcel
             */
            $code = $parcel->getCode();
            $founded = ParcelData::getAccessPointByCode($code);
            if (!$founded) {
                $founded = new ParcelData();
                $founded->set_country($parcel->getCountry());
                $founded->set_code($parcel->getCode());
                $founded->set_zip($parcel->getZipCode());
                $founded->set_city($parcel->getCity());
                $founded->set_type($parcel->getAccessPointType());
                $founded->set_street($parcel->getStreet());
                $founded->set_name($parcel->getName());
                $founded->set_lat($parcel->getGps()->getLatitude());
                $founded->set_lng($parcel->getGps()->getLongitude());
                $founded->set_valid(false);
                $founded->save();
            }
            $founded = Serializer::getInstance()->denormalize($founded, ParcelAddressModel::class);
            $shipmentModel->setParcel($founded);
            $shipmentModel->setHasParcel(true);
        }

        $shipmentModel->setAge("");

        foreach ($data->get_items() as $item) {
            if ($item instanceof \WC_Order_Item_Product)
            {

                $product = new \WC_Product($item->get_product_id());
                /**
                 * @var ProductModel $age
                 */
                $age = Serializer::getInstance()->denormalize($product, ProductModel::class);
                if ($age->getPplConfirmAge18()) {
                    $shipmentModel->setAge("18");
                } else if ($age->getPplConfirmAge15()) {
                    if ($shipmentModel->getAge() < 18)
                        $shipmentModel->setAge("15");
                }

            }
        }

        $packageModel = new PackageModel();
        $packageModel->setReferenceId("{$data->get_id()}");

        $shipmentModel->setPackages([
            $packageModel
        ]);

        return $shipmentModel;
    }

    public function ShipmentModelToShipmentData(ShipmentModel $model, $context = [])
    {
        $shipmentData = $context["data"] ??  new ShipmentData();
        if ($shipmentData->get_lock())
        {
            $oldData = $shipmentData;
            $shipmentData = new ShipmentData();
            $shipmentData->set_import_state("None");
            $shipmentData->set_wc_order_id($oldData->get_wc_order_id());
        } else if (!$shipmentData->get_id())
        {
            $shipmentData->set_import_state("None");
            if ($model->isInitialized("orderId"))
                $shipmentData->set_wc_order_id($model->getOrderId());
        }

        $shipmentData->set_reference_id($model->getReferenceId());
        if ($model->isInitialized("orderId"))
            $shipmentData->set_wc_order_id($model->getOrderId());

        if ($model->isInitialized("note"))
        {
            $shipmentData->set_note($model->getNote());
        } else {
            $shipmentData->set_note(null);
        }
        if ($model->isInitialized("sender"))
            $shipmentData->set_sender_address_id($model->getSender()->getId());

        if ($model->isInitialized("serviceCode")) {
            $shipmentData->set_service_code($model->getServiceCode());
            $serviceCode = $model->getServiceCode();
            $shipmentData->set_service_name(ShipmentMethod::methodsWithCod()[$serviceCode]);

            if (ShipmentMethod::isMethodWithCod($serviceCode)) {
                if ($model->isInitialized("codVariableNumber"))
                    $shipmentData->set_cod_variable_number($model->getCodVariableNumber());
                if ($model->isInitialized("codValue"))
                    $shipmentData->set_cod_value($model->getCodValue());
                if ($model->isInitialized("codValueCurrency"))
                    $shipmentData->set_cod_value_currency($model->getCodValueCurrency());
                /*
                $bankAccount = $model->getCodBankAccount();
                if ($bankAccount) {
                    $shipmentData->set_cod_bank_account_id($bankAccount->getId());
                }
                */
            } else {
                $shipmentData->set_cod_variable_number(null);
                $shipmentData->set_cod_value(null);
                $shipmentData->set_cod_bank_account_id(null);
                $shipmentData->set_cod_value_currency(null);
            }

            if (ShipmentMethod::isMethodWithParcel($serviceCode)) {
                if ($model->isInitialized("hasParcel") && $model->getHasParcel()) {
                    $shipmentData->set_has_parcel(true);
                    if ($model->isInitialized("parcel"))
                    {
                        $parcel = $model->getParcel();
                        $shipmentData->set_parcel_id($parcel->getId());
                    }

                } else {
                    $shipmentData->set_has_parcel(false);
                }
            } else {
                $shipmentData->set_has_parcel(false);
            }
        }

        if ($model->isInitialized("packages"))
        {
            $modelPackages = $model->getPackages();
            foreach ($modelPackages as $key => $package) {
                if ($package->getId()) {
                    $packageData = new PackageData($package->getId());
                } else {
                    $packageData = null;
                }
                $modelPackages[$key] = Serializer::getInstance()->denormalize($package, PackageData::class, null, array("data" => $packageData));
                if (!$modelPackages[$key]->get_phase())
                    $modelPackages[$key]->set_phase("None");
                $modelPackages[$key]->save();

                $modelPackages[$key] = $modelPackages[$key]->get_id();
            }
            $shipmentData->set_package_ids($modelPackages);
        }

        if ($model->isInitialized("recipient")) {
            $recipient = Serializer::getInstance()->denormalize($model->getRecipient(), AddressData::class);
            if (!$recipient->get_id())
                $recipient->save();
            $shipmentData->set_recipient_address_id($recipient->get_id());
        }

        return $shipmentData;

    }

    public function UpdateShipmentToData(UpdateShipmentModel $model, $context = [])
    {
        $shipmentData = $context["data"] ??  new ShipmentData();
        if ($shipmentData->get_lock())
        {
            $oldData = $shipmentData;
            $shipmentData = new ShipmentData();
            $shipmentData->set_import_state("None");
            $shipmentData->set_wc_order_id($oldData->get_wc_order_id());
        } else if (!$shipmentData->get_id())
        {
            $shipmentData->set_import_state("None");
            if ($model->isInitialized("orderId"))
                $shipmentData->set_wc_order_id($model->getOrderId());
        }

        if ($model->getReferenceId())
            $shipmentData->set_reference_id($model->getReferenceId());
        if ($model->isInitialized("orderId"))
            $shipmentData->set_wc_order_id($model->getOrderId());

        if ($model->isInitialized("age")) {
            $shipmentData->set_age($model->getAge());
        } else {
            $shipmentData->set_age(null);
        }

        if ($model->isInitialized("note"))
        {
            $shipmentData->set_note($model->getNote());
        } else {
            $shipmentData->set_note(null);
        }

        if ($model->isInitialized("senderId"))
        {
            $shipmentData->set_sender_address_id($model->getSenderId());
        }

        if ($model->isInitialized("serviceCode"))
        {
            $shipmentData->set_service_code($model->getServiceCode());
            $serviceCode = $model->getServiceCode();
            $shipmentData->set_service_name(ShipmentMethod::methodsWithCod()[$serviceCode]);

            if (ShipmentMethod::isMethodWithCod($serviceCode)) {
                if ($model->isInitialized("codVariableNumber"))
                    $shipmentData->set_cod_variable_number($model->getCodVariableNumber());
                if ($model->isInitialized("codValue"))
                    $shipmentData->set_cod_value($model->getCodValue());
                if ($model->isInitialized("codValueCurrency"))
                    $shipmentData->set_cod_value_currency($model->getCodValueCurrency());
                /*
                if ($model->isInitialized("codBankAccountId"))
                    $shipmentData->set_cod_bank_account_id($model->getCodBankAccountId());
                */
            }
            else {
                $shipmentData->set_cod_variable_number(null);
                $shipmentData->set_cod_value(null);
                $shipmentData->set_cod_bank_account_id(null);
                $shipmentData->set_cod_value_currency(null);
            }

            if (ShipmentMethod::isMethodWithParcel($serviceCode)) {
                if ($model->isInitialized("hasParcel") && $model->getHasParcel())
                {
                    $shipmentData->set_has_parcel(true);
                    if ($model->isInitialized("parcelId")) {
                        $parceldata = new ParcelData($model->getParcelId());
                        if ($parceldata->get_id())
                        {
                            $shipmentData->set_parcel_id($parceldata->get_id());
                        }

                    }
                }
                else
                {
                    $shipmentData->set_has_parcel(false);
                }
            }
            else {
                $shipmentData->set_has_parcel(false);
                $shipmentData->set_parcel_id(null);
            }
        }

        if ($model->isInitialized("packages"))
        {
            $modelPackages = $model->getPackages();
            foreach ($modelPackages as $key => $package) {
                if ($package->isInitialized("id") && $package->getId()) {
                    $packageData = new PackageData($package->getId());
                } else {
                    $packageData = null;
                }
                $modelPackages[$key] = Serializer::getInstance()->denormalize($package, PackageData::class, null, array("data" => $packageData));
                if (!$modelPackages[$key]->get_phase())
                    $modelPackages[$key]->set_phase("None");
                $modelPackages[$key]->save();

                $modelPackages[$key] = $modelPackages[$key]->get_id();
            }
            $shipmentData->set_package_ids($modelPackages);
        }

        if (!$shipmentData->get_id()) {
            if ($shipmentData->get_wc_order_id())
            {
                $order = new \WC_Order($shipmentData->get_wc_order_id());
                /**
                 * @var ShipmentModel $normalizer
                 */
                $normalizer = Serializer::getInstance()->denormalize($order, ShipmentModel::class);
                if ($normalizer->isInitialized('recipient'))
                {
                    $recipient = $normalizer->getRecipient();
                    $recipient = Serializer::getInstance()->denormalize($recipient, AddressData::class);
                    $recipient->save();
                    $shipmentData->set_recipient_address_id($recipient->get_id());
                }
            }
        }

        return $shipmentData;
    }

    public function UpdateShipmentSenderToData(UpdateShipmentSenderModel $sender, $context)
    {
        /**
         * @var ShipmentData $shipment
         */
        if (!isset($context['data']))
            throw new \Exception("Undefined shipment");
        $shipment = $context["data"];
        if ($sender->isInitialized("senderId")) {
            $addresses = AddressData::get_default_sender_addresses();
            $address = reset($addresses);

            if ($address && $address->get_id() && $address->get_id() == $sender->getSenderId())
                $shipment->set_sender_address_id(null);
            else
                $shipment->set_sender_address_id($sender->getSenderId());
        }
        return $shipment;
    }

    public function UpdateBankAccountToData(UpdateShipmentBankAccountModel $sender, $context)
    {
        /**
         * @var ShipmentData $shipment
         */
        if (!isset($context["data"]))
            throw new \Exception("Undefined shipment");
        $shipment = $context["data"];
        $serviceCode = $shipment->get_service_code();
        if (ShipmentMethod::isMethodWithCod($serviceCode)) {
            if ($sender->isInitialized("bankAccountId")) {
                $shipment->set_sender_address_id($sender->getBankAccountId());
            }
        }
        else
        {
            $shipment->set_cod_bank_account_id(null);
        }
        return $shipment;
    }

    public function UpdateRecipientToData(RecipientAddressModel $recipientAddressModel, $context)
    {
        if (!isset($context["data"]))
            throw new \Exception("Undefined shipment");;
        $shipment = $context["data"];
        /**
         * @var ShipmentData $shipment
         */
        $id = $shipment->get_recipient_address_id();
        $founded = new AddressData($id);
        $founded->set_type("recipient");
        $address = Serializer::getInstance()->denormalize($recipientAddressModel, AddressData::class,null, ["data" =>$founded]);
        $address->save();
        $shipment->set_recipient_address_id($address->get_id());
        return $shipment;
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof ShipmentData && $type == ShipmentModel::class) {
            return $this->ShipmentDataToModel($data, $context);
        }
        else if ($data instanceof \WC_Order && $type == ShipmentModel::class) {
            return $this->OrderToModel($data, $context);
        }
        else if ($data instanceof ShipmentModel && $type == ShipmentData::class) {
            return $this->ShipmentModelToShipmentData($data, $context);
        }
        else if ($data instanceof UpdateShipmentModel && $type === ShipmentData::class)
        {
            return $this->UpdateShipmentToData($data, $context);
        }
        else if($data instanceof UpdateShipmentSenderModel && $type === ShipmentData::class)
        {
            return $this->UpdateShipmentSenderToData($data, $context);
        }
        else if($data instanceof UpdateShipmentBankAccountModel && $type === ShipmentData::class)
        {
            return $this->UpdateBankAccountToData($data, $context);
        }
        else if ($data instanceof RecipientAddressModel && $type === ShipmentData::class)
        {
            return $this->UpdateRecipientToData($data, $context);
        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        $stopka = 1;
        if($data instanceof ShipmentData && $type === ShipmentModel::class)
            return true;
        if ($data instanceof \WC_Order && $type === ShipmentModel::class)
            return true;
        if ($data instanceof UpdateShipmentModel && $type === ShipmentData::class)
            return true;
        if ($data instanceof UpdateShipmentSenderModel && $type === ShipmentData::class)
            return true;
        if ($data instanceof RecipientAddressModel && $type === ShipmentData::class)
            return true;
        if ($data instanceof ShipmentModel && $type == ShipmentData::class) {
            return true;
        }

        return false;

    }
}
