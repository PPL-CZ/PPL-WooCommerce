<?php
namespace  PPLCZ\Data;

class BatchData extends PPLData
{
    protected $store_name = "pplcz-batch";

    protected $data = [
        "name" => null,
        "remote_batch_id" => null,
        "lock" => false,
        "created_at"=> null,
    ];

    public function __construct($data = 0)
    {
        parent::__construct($data);
        if (!isset($this->data['created'])
            && !$this->get_id()) {
            $this->data['created_at'] = date("Y-m-d H:i:s");
        }
    }

    public function set_props_from_store(array $sqldata)
    {
        $this->set_props([
            'id' => $sqldata["ppl_batch_id"],
        ] + $sqldata);
    }

    public function get_name($context = 'view')
    {
        return $this->get_prop('name', $context);
    }

    public function set_name($value)
    {
        $this->set_prop('name', $value);
    }

    public function get_remote_batch_id($context = 'view')
    {
        return $this->get_prop('remote_batch_id', $context);
    }

    public function set_remote_batch_id($value)
    {
        $this->set_prop('remote_batch_id', $value);
    }

    public function get_created_at($context = 'view')
    {
        return $this->get_prop('created_at', $context);
    }

    public function set_created_at($value)
    {
        $this->set_prop('created_at', $value);
    }

    public static function remove_batchs()
    {
        return \WC_Data_Store::load("pplcz-batch")->remove_batchs();
    }

    public static function get_batchs($free)
    {
        return \WC_Data_Store::load("pplcz-batch")->get_batchs($free);
    }

    public static function get_last_batch()
    {
        return \WC_Data_Store::load("pplcz-batch")->get_last_batch();
    }
}