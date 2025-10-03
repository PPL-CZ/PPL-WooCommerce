<?php
namespace PPLCZ\Data;

defined("WPINC") or die();


class AddressData extends PPLData
{
    protected $store_name = "pplcz-address";

    protected $data = [
        "address_name" => null,
        "name" => null,
        "contact"=> null,
        "mail"=>null,
        "phone"=> null,
        "street"=> null,
        "city" => null,
        "zip" => null,
        "country" => null,
        "note"=> null,
        "type"=>null,
        "hidden"=> true,
        "lock" => false,
        "draft" => null,
    ];


    public function set_props_from_store(array $sqldata)
    {
        $this->set_props([
            'id' => $sqldata["ppl_address_id"],
        ] + $sqldata);
    }

    public function set_address_name($value)
    {
        $this->set_prop("address_name", $value);
    }

    public function get_address_name($context = "view")
    {
        return $this->get_prop("address_name", $context);
    }

    public function get_note($context = "view")
    {
        return $this->get_prop("note", $context);
    }

    public function set_note($value)
    {
        $this->set_prop("note", $value);
    }

    public function get_hidden($context = 'view')
    {
        return $this->get_prop("hidden", $context);
    }

    public function set_hidden($value)
    {
        $this->set_prop("hidden", $value);
    }

    // Getter and Setter for 'name'
    public function get_contact($context = 'view')
    {
        return $this->get_prop("contact", $context);
    }

    public function set_contact($value)
    {
        $this->set_prop("contact", $value);
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

    // Getter and Setter for 'mail'
    public function get_mail($context = 'view')
    {
        return $this->get_prop("mail", $context);
    }

    public function set_mail($value)
    {
        $this->set_prop("mail", $value);
    }

    // Getter and Setter for 'phone'
    public function get_phone($context = 'view')
    {
        return $this->get_prop("phone", $context);
    }

    public function set_phone($value)
    {
        $this->set_prop("phone", $value);
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

    // Getter and Setter for 'type'
    public function get_type($context = 'view')
    {
        return $this->get_prop("type", $context);
    }

    public function set_type($value)
    {
        $this->set_prop("type", $value);
    }

    public function get_country($context = 'view')
    {
        return $this->get_prop("country", $context);
    }

    public function set_country($value)
    {
        $this->set_prop("country", $value);
    }

    /**
     * @param AddressData[] $addreses
     * @return void
     */
    public static function set_default_sender_addresses(array $addreses)
    {
        foreach ($addreses as $key=>$value)
        {
            $addreses[$key] = $value->get_id();
        }
        $ids = join(",", $addreses);
        $key = pplcz_create_name("default_sender_address");
        add_option($key, $ids) || update_option($key, $ids);

    }

    /**
     * @return AddressData[]
     */
    public static function get_default_sender_addresses()
    {
        $content = get_option(pplcz_create_name("default_sender_address"));
        $data = array_unique(array_filter(array_map("trim", explode(",", $content)), "ctype_digit"));
        foreach ($data as $key => $val)
        {
            $val = new AddressData($val);
            if ($val->get_id())
                $data[$key] = $val;
            else
                unset($data[$key]);
        }
        return $data;
    }

}