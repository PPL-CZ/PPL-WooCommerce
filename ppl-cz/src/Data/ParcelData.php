<?php
namespace PPLCZ\Data;
defined("WPINC") or die();


class ParcelData extends PPLData {

    protected $store_name = "pplcz-parcel";

    protected $data = [
        "name" => null,
        "name2" => null,
        "street"=> null,
        "city" => null,
        "zip" => null,
        "country" => null,
        "code" => null,
        "lat"=> null,
        "lng"=>null,
        "lock" => false,
        "draft" => null,
        "type" => null,
        "hidden"=> false,
        "valid"=>false
    ];

    public function set_props_from_store(array $sqldata)
    {
        $this->set_props([
            "id" => $sqldata["ppl_parcel_id"],
        ] + $sqldata);
    }

    public function set_lat($value)
    {
        $this->set_prop("lat", $value);
    }

    public function get_lat($context = 'view')
    {
        return $this->get_prop("lat", $context);
    }

    public function set_lng($value)
    {
        $this->set_prop("lng", $value);
    }

    public function get_lng($context = 'view')
    {
        return $this->get_prop("lng", $context);
    }

    public function set_type($value)
    {
        $this->set_prop("type", $value);
    }

    public function get_type($context = 'view')
    {
        return $this->get_prop("type", $context);
    }

    public function set_code($value)
    {
        $this->set_prop("code", $value);
    }

    public function get_code($context = 'view')
    {
        return $this->get_prop("code", $context);
    }

    // Getter and Setter for 'name'
    public function get_name($context = 'view')
    {
        return $this->get_prop("name", $context);
    }

    public function set_name($value)
    {
        $this->set_prop("name", $value);
    }

    public function get_name2($context = 'view')
    {
        return $this->get_prop("name2", $context);
    }

    public function set_name2($value)
    {
        $this->set_prop("name2", $value);
    }


    // Getter and Setter for 'street'
    public function get_street($context = 'view')
    {
        return $this->get_prop("street", $context);
    }

    public function set_street($value)
    {
        $this->set_prop("street", $value);
    }

    // Getter and Setter for 'city'
    public function get_city($context = 'view')
    {
        return $this->get_prop("city", $context);
    }

    public function set_city($value)
    {
        $this->set_prop("city", $value);
    }

    // Getter and Setter for 'zip'
    public function get_zip($context = 'view')
    {
        return $this->get_prop("zip", $context);
    }

    public function set_zip($value)
    {
        $this->set_prop("zip", $value);
    }

    public function get_country($context = 'view')
    {
        return $this->get_prop("country", $context);
    }

    public function set_country($value)
    {
        $this->set_prop("country", $value);
    }

    public function set_valid($value)
    {
        $this->set_prop("valid", $value);
    }

    public function get_valid($context = 'view')
    {
        return $this->get_prop("valid", $context);
    }

    public static function getAccessPointByCode($code)
    {
        return  ParcelDataStore::getAccessPointByCode($code);
    }
}