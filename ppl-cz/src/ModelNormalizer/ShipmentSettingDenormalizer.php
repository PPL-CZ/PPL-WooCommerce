<?php
namespace PPLCZ\ModelNormalizer;


use PPLCZ\Model\Model\ShipmentMethodModel;
use PPLCZ\Model\Model\ShipmentMethodSettingCurrencyModel;
use PPLCZ\Model\Model\ShipmentMethodSettingModel;
use PPLCZ\Model\Model\ShipmentMethodSettingPriceRuleModel;
use PPLCZ\Model\Model\ShipmentMethodSettingWeightModel;
use PPLCZ\Model\Model\ShipmentMethodSettingWeightRuleModel;
use PPLCZ\Setting\MethodSetting;
use PPLCZ\ShipmentMethod;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ShipmentSettingDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        $currencies  = include __DIR__ . '/../config/currencies.php';
        $currencies = array_unique(array_merge([ get_option( 'woocommerce_currency' )],array_values($currencies)));

        if ($type === ShipmentMethodSettingModel::class) {
            /**
             * @var ShipmentMethod $data
             */
            $shipment = new ShipmentMethodSettingModel();

            $shipment->setCode(str_replace(pplcz_create_name(""), '', $data->id));
            $method = array_filter(MethodSetting::getMethods(), function($item) use($shipment) {
                return $item->getCode() === $shipment->getCode();
            });
            /**
             * @var ShipmentMethodModel $method
             */
            $method = reset($method);

            $parcelBoxesRequired = $method ? $method->getParcelRequired() : false;
            $shipment->setParcelBoxes($parcelBoxesRequired);

            $formFields = array_reduce(array_keys($data->get_instance_form_fields()), function($sum, $key) use ($data) {
                $sum[$key] = @$data->get_instance_option($key);
                return $sum;
            }, []);

            $shipment->setDescription($formFields['description'] ?: null);
            $shipment->setTitle($formFields['title'] ?: null);
            $shipment->setIsPriceWithDph($formFields['priceWithDph'] === 'yes' );
            $shipment->setCodPayment($formFields['codPayment'] ?: '');
            $shipment->setDisablePayments($formFields['disablePayments'] ?: []);
            $shipment->setCostByWeight($formFields["cost_by_weight"] === 'yes' );

            $shipment->setDisabledAlzaBox(!$parcelBoxesRequired || $formFields["disabledAlzaBox"] === 'yes' );
            $shipment->setDisabledParcelBox(!$parcelBoxesRequired || $formFields["disabledParcelBox"] === 'yes' );
            $shipment->setDisabledParcelShop(!$parcelBoxesRequired || $formFields["disabledParcelShop"] === 'yes' );


            if ($parcelBoxesRequired)
                $savedDisabledParcel = isset($formFields['disabledParcelCountries']) ? $formFields['disabledParcelCountries'] : [];
            else
                $savedDisabledParcel = [];

            if (!is_array($savedDisabledParcel))
                $savedDisabledParcel = [];

            $shipment->setDisabledParcelCountries( $savedDisabledParcel);

            $shipment->setCurrencies([]);
            $shipment->setWeights([]);

            foreach ($formFields as $key => $value) {
                $splittedName = explode("_", $key);
                $currency = end($splittedName);
                if (in_array($currency, $currencies)) {
                    $item = array_filter($shipment->getCurrencies(), function (ShipmentMethodSettingCurrencyModel $item) use ($currency) {
                        return $item->getCurrency() === $currency;
                    });
                    if (!$item) {
                        $item = [new ShipmentMethodSettingCurrencyModel()];
                        $item[0]->setCurrency($currency);
                        $shipment->setCurrencies(array_merge($shipment->getCurrencies(), $item));
                    }
                    /**
                     * @var ShipmentMethodSettingCurrencyModel $item
                     */
                    $item = reset($item);
                    $isNumberRegex = "/^[0-9]+(.[0-9]*)?$/";
                    switch ($key) {
                        case "cost_allow_{$currency}":
                            $item->setEnabled($value === 'yes');
                            break;
                        case "cost_order_free_{$currency}":
                            $value = $value ?: '';
                            $item->setCostOrderFree(preg_match($isNumberRegex, $value) ? floatval($value) : null);
                            break;
                        case "cost_cod_fee_{$currency}":
                            $value = $value ?: '';
                            $resolved = preg_match($isNumberRegex, $value) ? floatval($value) : null;
                            $item->setCostCodFee($resolved);
                            break;
                        case "cost_cod_fee_always_{$currency}":
                            $item->setCostCodFeeAlways($value === 'yes' || !$value);
                            break;
                        case "cost_order_free_cod_{$currency}":
                            $value = $value ?: '';
                            $item->setCostOrderFreeCod(preg_match($isNumberRegex, $value) ? floatval($value) : null);
                            break;
                        case "cost_{$currency}":
                            $value = $value ?: '';
                            $item->setCost(preg_match($isNumberRegex, $value) ? floatval($value) : null);
                            break;

                    }
                }
            }
            $weight =  get_option($data->get_instance_option_weight_key());
            if (is_array($weight))
            {
                try {
                    foreach ($weight as $key => $value) {
                        $weight[$key] = pplcz_denormalize($value, ShipmentMethodSettingWeightRuleModel::class);
                    }
                    usort($weight, function (ShipmentMethodSettingWeightRuleModel $item1, ShipmentMethodSettingWeightRuleModel $item2) {
                        $to1 = $item1->getTo() ?: 1000000000;
                        $to2 = $item2->getTo() ?: 1000000000;

                        if ($to2 == $to1)
                        {
                            $to1 = $item1->getFrom() ?: 0;
                            $to2 = $item2->getFrom() ?: 0;
                            if ($to1 == $to2)
                                return 0;
                            if ($to2 > $to1)
                                return -1;
                            return 1;
                        }
                        else {
                            if ($to2 > $to1)
                                return -1;
                            return 1;
                        }
                    });


                    $shipment->setWeights($weight);
                }
                catch (\Throwable $exception)
                {

                }

            }

            return $shipment;
        } else {

        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof ShipmentMethod && $type === ShipmentMethodSettingModel::class;
    }
}