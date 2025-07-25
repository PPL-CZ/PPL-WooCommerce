<?php
namespace PPLCZ\Data;

defined("WPINC") or die();


use \DateTime;

class LogData  extends PPLData
{
    protected $store_name = "pplcz-log";

    protected $data = [
        "ppl_log_id" => null,
        "timestamp" => null,
        "message" => null,
        "errorhash"=> null,
        "lock" => false,
        "draft" => false,
    ];


    public function get_message($context = 'view')
    {
        return $this->get_prop("message", $context);
    }

    public function set_message($value)
    {
        $this->set_prop("message", $value);
    }

    public function set_timestamp($value)
    {
        $this->set_prop("timestamp", $value);
    }


    public function get_timestamp($context = 'view')
    {
        return $this->get_prop("timestamp", $context);
    }


    public function set_errorhash($value)
    {
        $this->set_prop("errorhash", $value);
    }


    public function get_errorhash($context = 'view')
    {
        return $this->get_prop("errorhash", $context);
    }

    public function get_props_for_store($context = 'update')
    {
        $data = parent::get_props_for_store($context);
        unset($data['draft']);
        unset($data['lock']);
        return $data;
    }

    public function set_props_from_store(array $sqldata)
    {
        $this->set_props([
            "id" => $sqldata["ppl_log_id"],
            "draft" => false,
            "lock" => false
        ] + $sqldata);
    }
}