<?php
// phpcs:ignoreFile WordPress.DB.DirectDatabaseQuery.DirectQuery

namespace PPLCZ\Data;
defined("WPINC") or die();

use PPLCZ\Model\Model\BankAccountModel;
use PPLCZ\Model\Model\RecipientAddressModel;

class ShipmentData extends PPLData implements ShipmentDataInterface
{
    protected $data = [
        "wc_order_id" => null,
        "reference_id"=>null,
        "import_state"=> null,
        "service_code" => null,
        "package_ids" => null,
        "service_name"=>null,
        "recipient_address_id"=> null,
        "sender_address_id" => null,
        "cod_bank_account_id" => null,
        "cod_value" => null,
        "cod_value_currency"=> null,
        "cod_variable_number" => null,
        "parcel_id" => null,
        "has_parcel" => false,
        "batch_id" => null,
        "batch_label_group" => null,
        "age"=> null,
        "note" => null,
        "lock" => false,
        "draft" => null,
        "import_errors"=>null,
    ];

    public function get_props_for_store($context = "update")
    {
        $data = parent::get_props_for_store($context);
        if ($data["package_ids"]) {
            $data["package_ids"] = join("," , $data['package_ids']);
        }
        return $data;
    }

    protected $store_name = "pplcz-shipment";
    // Getter and Setter for 'wc_order_id'

    public function get_package_ids($context = 'view')
    {
        $ids = $this->get_prop('package_ids');
        if (!trim($ids))
        {
            return [];
        }

        return array_filter(array_map("trim", explode(',', $ids)),"ctype_digit");
    }

    public function set_package_ids(array $value)
    {
        $this->set_prop("package_ids", join(",", array_filter(array_map("trim", $value), "ctype_digit")));
    }

    public function get_batch_label_group($context = 'view')
    {
        return $this->get_prop("batch_label_group", $context);
    }

    public function set_batch_label_group($value)
    {
        $this->set_prop("batch_label_group", $value);
    }

    public function get_service_name($context = 'view')
    {
        return $this->get_prop("service_name", $context);
    }

    public function set_service_name($value)
    {
        $this->set_prop("service_name", $value);
    }

    public function get_import_errors($context = 'view')
    {
        return $this->get_prop("import_errors", $context);
    }

    public function set_import_errors($value)
    {
        $this->set_prop("import_errors", $value);
    }

    public function get_cod_variable_number($context = 'view')
    {
        return $this->get_prop("cod_variable_number", $context);
    }

    public function set_cod_variable_number($value)
    {
        $this->set_prop("cod_variable_number", $value);
    }

    public function set_wc_order_id($value)
    {
        $this->set_prop("wc_order_id", $value);
    }

    public function get_wc_order_id($context = 'view')
    {
        return $this->get_prop("wc_order_id", $context);
    }

    // Getter and Setter for 'reference_id'
    public function get_reference_id($context = 'view')
    {
        return $this->get_prop("reference_id", $context);
    }

    public function set_reference_id($value)
    {
        $this->set_prop("reference_id", $value);
    }

    // Getter and Setter for 'import_state'
    public function get_import_state($context = 'view')
    {
        return $this->get_prop("import_state", $context);
    }

    public function set_import_state($value)
    {
        $this->set_prop("import_state", $value);
    }

    // Getter and Setter for 'service_code'
    public function get_service_code($context = 'view')
    {
        return $this->get_prop("service_code", $context);
    }

    public function set_service_code($value)
    {
        $this->set_prop("service_code", $value);
    }

    // Getter and Setter for 'recipient_address_id'
    public function get_recipient_address_id($context = 'view')
    {
        return $this->get_prop("recipient_address_id", $context);
    }

    public function set_recipient_address_id($value)
    {
        $this->set_prop("recipient_address_id", $value);
    }

    // Getter and Setter for 'sender_address_id'
    public function get_sender_address_id($context = 'view')
    {
        if ($context === "default") {
            $addresses = AddressData::get_default_sender_addresses();
            $address = reset($addresses);
            if ($address)
                return $address->get_id();
            return null;
        }

        return $this->get_prop("sender_address_id", $context);
    }

    public function set_sender_address_id($value)
    {
        $this->set_prop("sender_address_id", $value);
    }

    // Getter and Setter for 'cod_payment_id'
    public function get_cod_bank_account_id($context = 'view')
    {
        if ($context === "default") {
            $banks = CodBankAccountData::get_default_bank_accounts();
            $currency = $this->get_cod_value_currency();
            $banks2 = array_filter($banks, function (CodBankAccountData $bank) use ($currency){
                return $currency === $bank->get_currency();
            });
            if ($banks2)
                return reset($banks2)->get_id();
            if ($banks)
                return reset($banks)->get_id();


        }
        return $this->get_prop("cod_bank_account_id", $context);
    }

    public function set_cod_bank_account_id($value)
    {
        $this->set_prop("cod_bank_account_id", $value);
    }

    // Getter and Setter for 'has_cod_payment'
    public function get_cod_value($context = 'view')
    {
        return $this->get_prop("cod_value", $context);
    }

    public function set_cod_value($value)
    {
        $this->set_prop("cod_value", $value);
    }

    // Getter and Setter for 'has_cod_payment'
    public function get_cod_value_currency($context = 'view')
    {
        return $this->get_prop("cod_value_currency", $context);
    }

    public function set_cod_value_currency($value)
    {
        $this->set_prop("cod_value_currency", $value);
    }

    // Getter and Setter for 'parcel_id'
    public function get_parcel_id($context = 'view')
    {
        return $this->get_prop("parcel_id", $context);
    }

    public function set_parcel_id($value)
    {
        $this->set_prop("parcel_id", $value);
    }

    // Getter and Setter for 'has_parcel'
    public function get_has_parcel($context = 'view')
    {
        return $this->get_prop("has_parcel", $context);
    }

    public function set_has_parcel($value)
    {
        $this->set_prop("has_parcel", $value);
    }

    // Getter and Setter for 'batch_id'
    public function get_batch_id($context = 'view')
    {
        return $this->get_prop("batch_id", $context);
    }

    public function set_batch_id($value)
    {
        $this->set_prop("batch_id", $value);
    }

    // Getter and Setter for 'age'
    public function get_age($context = 'view')
    {
        return $this->get_prop("age", $context);
    }

    public function set_age($value)
    {
        $this->set_prop("age", $value);
    }

    // Getter and Setter for 'note'
    public function get_note($context = 'view')
    {
        return $this->get_prop("note", $context);
    }

    public function set_note($value)
    {
        $this->set_prop("note", $value);
    }

    public function lock()
    {
        $this->set_lock(true);
        $this->save();

        $recipient = new AddressData($this->get_recipient_address_id());
        if (!$recipient->get_lock()) {
            $recipient->set_lock(true);
            $recipient->save();
        }

        $sender = new AddressData($this->get_sender_address_id());
        if (!$sender->get_lock()) {
            $sender->set_lock(true);
            $sender->save();
        }

        $ids = $this->get_package_ids();

        foreach ($ids as $key=>$value) {
            $package = new PackageData($value);
            if (!$package->get_lock()) {
                $package->set_lock(true);
                $package->set_ppl_shipment_id($this->get_id());
                $package->set_wc_order_id($this->get_wc_order_id());
                $package->save();
            } else {
                $package->ignore_lock();
                $package->set_ppl_shipment_id($this->get_id());
                $package->set_wc_order_id($this->get_wc_order_id());
                $package->save();
            }
        }

        if ($this->get_cod_bank_account_id()) {

            $codBank = new CodBankAccountData($this->get_cod_bank_account_id());
            if (!$codBank->get_lock()) {
                $codBank->set_lock(true);
                $codBank->save();
            }
        }
    }

    public function unlock()
    {
        /**
         * nemá smysl odemykat záznam, který už je odeslan a akceptován PPL.
         */
        if (in_array($this->get_import_state() ?:"", ["None", "Error", ""], true)) {
            $this->ignore_lock();
            $this->hard_lock = true;
            $this->set_lock(false);
            $this->set_import_state("None");
            $this->set_import_errors(null);


            foreach ($this->get_package_ids() as $package_id) {
                $package = new PackageData($package_id);
                $package->ignore_lock();
                $package->hard_lock = true;
                $package->set_lock(false);
                $package->set_import_error(null);
                $package->set_import_error_code(null);
                $package->save();
            }

            $address = new AddressData($this->get_recipient_address_id());
            $address->ignore_lock();
            $address->hard_lock = true;
            $address->set_lock(false);
            $address->save();
            $this->save();
        }
        else {
            throw new \Exception("Lock");
        }
    }


    public function set_props_from_store(array $sqldata)
    {
        $this->set_props([
            "id" => $sqldata["ppl_shipment_id"],
            "package_ids" => explode(",", $sqldata["package_ids"])
        ] + $sqldata);
    }
    public static function newGuid() {
        $guid = random_bytes(16);
        $guid[6] = chr((ord($guid[6]) & 0x0f) | 0x40); // version 4
        $guid[8] = chr((ord($guid[8]) & 0x3f) | 0x80); // variant
        $guid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($guid), 4));
        return $guid;
    }

    public static function read_shipments($args = [])
    {
        $store = \WC_Data_Store::load("pplcz-shipment");
        return $store->read_shipments($args);
    }

    public static function read_order_shipments($order_id)
    {
        $store = \WC_Data_Store::load("pplcz-shipment");
        return $store->read_order_shipments($order_id);
    }

    /**
     * @param $batch_id
     * @return ShipmentData[]
     * @throws \Exception
     */
    public static function read_batch_shipments($batch_id)
    {
        return \WC_Data_Store::load("pplcz-shipment")->read_batch_shipments($batch_id);
    }

    /**

     * @return ShipmentData[]
     * @throws \Exception
     */
    public static function read_progress_shipments()
    {
        $store = \WC_Data_Store::load("pplcz-shipment");
        return $store->read_progress_shipments();
    }

    public static function find_shipments_by_wc_order($wc_order_id)
    {
        return \WC_Data_Store::load("pplcz-shipment")->find_shipments_by_wc_order($wc_order_id);
    }


    public static function read_label_groups()
    {
        return \WC_Data_Store::load("pplcz-shipment")->read_label_groups();
    }
}
