<?php
namespace PPLCZ\Data;

defined("WPINC") or die();


class CollectionData extends \WC_Data implements CollectionDataInterface {

    protected $data = [
        "created_date" => null,
        "send_date" => null,
        "send_to_api_date"=> null,
        "reference_id" => null,
        "remote_collection_id" => null,
        "state" => "BeforeSend",
        "shipment_count" => 0,
        "estimated_shipment_count" => 0,
        "contact" => '',
        "telephone"=>'',
        "email" => '',
        "note"=>'',
    ];

    public function get_remote_collection_id($context = "view")
    {
        return $this->get_prop("remote_collection_id", $context);
    }

    public function set_remote_collection_id($value)
    {
        $this->set_prop("remote_collection_id", $value);
    }

    public function get_estimated_shipment_count($context = 'view')
    {
        return $this->get_prop("estimated_shipment_count", $context);
    }

    public function set_estimated_shipment_count($value)
    {
        $this->set_prop("estimated_shipment_count", $value);
    }

    public function get_created_date( $context = 'view' ) {
        return $this->get_prop( 'created_date', $context );
    }

    public function set_created_date($value)
    {
        $this->set_prop("created_date", $value);
    }
    public function get_send_to_api_date($context = 'view')
    {
        return $this->get_prop( 'send_to_api_date', $context );
    }

    public function set_send_to_api_date($value)
    {
        $this->set_prop("send_to_api_date", $value);
    }


    public function get_send_date($context = 'view')
    {
        return $this->get_prop("send_date", $context);
    }



    public function set_send_date($date)
    {
        $this->set_prop("send_date", $date);
    }

    public function get_reference_id($context = 'view')
    {
        return $this->get_prop("reference_id", $context);
    }

    public function set_reference_id($value)
    {
        $this->set_prop("reference_id", $value);
    }

    public function get_state($context = 'view')
    {
        return $this->get_prop("state", $context);
    }

    public function set_state($state)
    {
        $this->set_prop("state", $state);
    }

    public function get_shipment_count($context = 'view')
    {
        return $this->get_prop("shipment_count", $context);
    }

    public function set_shipment_count($value)
    {
        $this->set_prop("shipment_count", $value);
    }

    public function get_contact($context = 'view')
    {
        return $this->get_prop("contact", $context);
    }

    public function set_contact($value)
    {
        $this->set_prop("contact", $value);
    }

    public function get_telephone($context = 'view')
    {
        return $this->get_prop("telephone", $context);
    }

    public function set_telephone($value)
    {
        $this->set_prop("telephone", $value);
    }


    public function get_email($context = 'view')
    {
        return $this->get_prop("email", $context);
    }

    public function set_email($value)
    {
        $this->set_prop('email', $value);
    }

    public function get_note($context = 'view')
    {
        return $this->get_prop("note", $context);
    }

    public function set_note($value)
    {
        $this->set_prop("note", $value);
    }



    public function __construct($data = 0)
    {
        parent::__construct($data);
        if ($data instanceof CollectionData)
        {
            $this->set_id(absint($data->get_id()));
        }
        else if (is_numeric($data))
        {
            $this->set_id($data);
        }

        $this->data_store = \WC_Data_Store::load("pplcz-collection");
        if ( $this->get_id() ) {
            try {
                $this->data_store->read( $this );
            } catch ( \Exception $e ) {
                $this->set_id( 0 );
                $this->set_object_read( true );
            }
        } else {
            $this->set_object_read( true );
        }
    }

    public static function read_collections($args)
    {
        return \WC_Data_Store::load("pplcz-collection")->read_collections($args);
    }

    public static function available_collections()
    {
        return \WC_Data_Store::load("pplcz-collection")->available_collections();
    }

    public static function last_collection()
    {
        return \WC_Data_Store::load("pplcz-collection")->last_collection();
    }
}