<?php
namespace PPLCZ\Data;

defined("WPINC") or die();


class CodBankAccountData extends PPLData
{

    protected $store_name = "pplcz-cod-bank-account";

    protected $data = [
        "account" => null,
        "account_name" => null,
        "account_prefix"=>null,
        "bank_code"=> null,
        "iban"=> null,
        "swift" => null,
        "currency" => null,
        "lock" => false,
        "draft" => true
    ];

    public function set_props_from_store(array $sqldata)
    {
        $this->set_props(["id" => @$sqldata["ppl_cod_id"]] + $sqldata);
    }

    public function get_account_name($context = 'view')
    {
        return $this->get_prop("account_name", $context);
    }

    public function set_account_name($value)
    {
        $this->set_prop("account_name", $value);
    }

    // Getter and Setter for 'account'
    public function get_account($context = 'view')
    {
        return $this->get_prop("account", $context);
    }

    public function set_account($value)
    {
        $this->set_prop("account", $value);
    }

    // Getter and Setter for 'account_prefix'
    public function get_account_prefix($context = 'view')
    {
        return $this->get_prop("account_prefix", $context);
    }

    public function set_account_prefix($value)
    {
        $this->set_prop("account_prefix", $value);
    }

    // Getter and Setter for 'bank_code'
    public function get_bank_code($context = 'view')
    {
        return $this->get_prop("bank_code", $context);
    }

    public function set_bank_code($value)
    {
        $this->set_prop("bank_code", $value);
    }

    // Getter and Setter for 'iban'
    public function get_iban($context = 'view')
    {
        return $this->get_prop("iban", $context);
    }

    public function set_iban($value)
    {
        $this->set_prop("iban", $value);
    }

    // Getter and Setter for 'swift'
    public function get_swift($context = 'view')
    {
        return $this->get_prop("swift", $context);
    }

    public function set_swift($value)
    {
        $this->set_prop("swift", $value);
    }

    // Getter and Setter for 'currency'
    public function get_currency($context = 'view')
    {
        return $this->get_prop("currency", $context);
    }

    public function set_currency($value)
    {
        $this->set_prop("currency", $value);
    }


    /**
     * @param CodBankAccountData[] $bank
     * @return void
     */
    public static function set_default_bank_accounts(array $bank)
    {
        foreach ($bank as $key=>$value)
        {
            $bank[$key] = $value->get_id();
        }
        $ids = join(",", $bank);
        $key = pplcz_create_name("default_sender_banks");
        add_option($key, $ids) || update_option($key, $ids);
    }

    /**
     * @return CodBankAccountData|null
     */
    public static function get_default_bank_accounts()
    {
        $content = get_option(pplcz_create_name("default_sender_banks"));
        $data = array_unique(array_filter(array_map("trim", explode(",", $content)), "ctype_digit"));
        foreach ($data as $key => $val)
        {
            $val = new CodBankAccountData($val);
            if ($val->get_id())
                $data[$key] = $val;
            else
                unset($data[$key]);
        }
        return $data;
    }
}