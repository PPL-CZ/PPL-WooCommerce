<?php
namespace PPLCZ\ModelNormalizer;

defined("WPINC") or die();


use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLCZ\Data\CodBankAccountData;
use PPLCZ\Model\Model\BankAccountModel;

class BankModelDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        $keys =[
            "account" => "account",
            "accountName"=> "account_name",
            "accountPrefix" => "account_prefix",
            "bankCode"=> "bank_code",
            "iban" => "iban",
            "swift" => "swift",
            "currency" => "currency"
        ];

        if ($data instanceof CodBankAccountData) {

            $bank = new BankAccountModel();

            $bank->setId($data->get_id());
            foreach( $keys as $key => $value)
            {
                if ($data->{"get_$value"}())
                    $bank->{"set$key"}($data->{"get_$value"}());
            }
            return $bank;

        } else if ($data instanceof  BankAccountModel) {
            $bank = $context["data"] ?? new CodBankAccountData();
            if ($bank->get_lock())
                $bank = new CodBankAccountData();
            foreach( $keys as $key => $value)
            {
                if ($data->isInitialized($key))
                    $bank->{"set_$value"}($data->{"get$key"}());
            }
            return $bank;
        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof CodBankAccountData && $type === BankAccountModel::class
            || $data instanceof BankAccountModel && $type === CodBankAccountData::class;
    }
}