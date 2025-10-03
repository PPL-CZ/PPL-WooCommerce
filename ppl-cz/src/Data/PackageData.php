<?php
namespace PPLCZ\Data;
defined("WPINC") or die();


class PackageData extends PPLData
{
    protected $store_name = "pplcz-package";

    protected $data = [
        "ppl_shipment_id" => null,
        "wc_order_id" => null,
        "reference_id"=> null,
        "shipment_number"=>null,
        "weight"=> null,
        "insurance"=> null,
        "insurance_currency" => null,
        "phase" => null,
        "status" => null,
        "status_label" => null,
        "phase_label" => null,
        "last_update_phase" => null,
        "last_test_phase" => null,
        "ignore_phase" => null,
        "import_error" =>  null,
        "import_error_code" =>null,
        "lock" => false,
        "draft" => true,
        "label_id"=>null
    ];

    public function set_props_from_store(array $sqldata)
    {
        $this->set_props([
            "id" => $sqldata["ppl_package_id"],
        ] + $sqldata);
    }

    // Getter and Setter for 'ppl_shipment_id'
    public function get_ppl_shipment_id($context = 'view')
    {
        return $this->get_prop("ppl_shipment_id", $context);
    }

    public function set_ppl_shipment_id($value)
    {
        $this->set_prop("ppl_shipment_id", $value);
    }

    public function get_reference_id($context = "view")
    {
        return $this->get_prop("reference_id", $context);
    }

    public function set_reference_id($value)
    {
        $this->set_prop("reference_id", $value);
    }

    // Getter and Setter for 'shipment_number'
    public function get_shipment_number($context = 'view')
    {
        return $this->get_prop("shipment_number", $context);
    }

    public function set_shipment_number($value)
    {
        $this->set_prop("shipment_number", $value);
    }

    // Getter and Setter for 'weight'
    public function get_weight($context = 'view')
    {
        return $this->get_prop("weight", $context);
    }

    public function set_weight($value)
    {
        $this->set_prop("weight", $value);
    }

    // Getter and Setter for 'insurance'
    public function get_insurance($context = 'view')
    {
        return $this->get_prop("insurance", $context);
    }

    public function set_insurance($value)
    {
        $this->set_prop("insurance", $value);
    }

    // Getter and Setter for 'insurance_currency'
    public function get_insurance_currency($context = 'view')
    {
        return $this->get_prop("insurance_currency", $context);
    }

    public function set_insurance_currency($value)
    {
        $this->set_prop("insurance_currency", $value);
    }

    // Getter and Setter for 'insurance_currency'
    public function get_phase($context = 'view')
    {
        return $this->get_prop("phase", $context);
    }

    public function set_phase($value)
    {
        $this->set_prop("phase", $value);
    }

    public function get_status($context = 'view')
    {
        return $this->get_prop("status", $context);
    }

    public function set_status($value)
    {
        $this->set_prop("status", $value);
    }

    public function get_status_label($context = 'view')
    {
        return $this->get_prop("status_label", $context);
    }

    public function set_status_label($value)
    {
        $this->set_prop("status_label", $value);
    }


    public function get_last_test_phase($context = 'view')
    {
        return $this->get_prop("last_test_phase");
    }

    public function set_last_test_phase($value)
    {
        $this->set_prop('last_test_phase', $value);
    }

    public function get_last_update_phase($context =  'view')
    {
        return $this->get_prop("last_update_phase", $context);
    }

    public function set_last_update_phase($value)
    {
        $this->set_prop("last_update_phase", $value);
    }

    public function set_ignore_phase($value)
    {
        $this->set_prop("ignore_phase", $value);
    }

    public function get_ignore_phase($context = 'view')
    {
        return $this->get_prop("ignore_phase", $context);
    }

    public function get_import_error($context = 'view')
    {
        return $this->get_prop("import_error", $context);
    }

    public function set_import_error($value)
    {
        $this->set_prop("import_error", $value);
    }

    public function set_import_error_code($value)
    {
        $this->set_prop("import_error_code", $value);
    }

    public function get_import_error_code($context = 'view')
    {
        return $this->get_prop("import_error_code", $context);
    }

    public function set_label_id($value)
    {
        $this->set_prop("label_id", $value);
    }

    public function get_label_id($context = 'view')
    {
        return $this->get_prop("label_id", $context );
    }

    public function get_wc_order_id($context = 'view')
    {
        return $this->get_prop("wc_order_id", $context);
    }

    public function set_wc_order_id($value)
    {
        $this->set_prop("wc_order_id", $value);
    }

    public function get_phase_label($context = 'view')
    {
        return $this->get_prop("phase_label", $context);
    }

    public function set_phase_label($value)
    {
        $this->set_prop('phase_label', $value);
    }

    public function isImported()
    {
        return ($this->get_shipment_number() && !!$this->get_import_error());
    }
    public static function find_packages_by_shipment($ppl_shipment_id)
    {
        return PackageDataStore::find_packages_by_shipment($ppl_shipment_id);
    }

    public static function find_package_by_shipment_number($shipmentNumbers)
    {
        return PackageDataStore::find_package_by_shipment_number($shipmentNumbers);
    }

    public static function find_packages_by_phases_and_time($phases, $from_last_test_phase, $last_change_update, $limit) {
        return PackageDataStore::find_packages_by_phases_and_time($phases, $from_last_test_phase, $last_change_update, $limit);
    }

}