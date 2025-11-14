<?php
namespace PPLCZ\Proxy;

class ApiCartProxy
{
    /**
     * @var null|array
     */
    public $shipping_address;
    /**
     * @var null|array
     */
    public $billing_address;

    /**
     * @var \WC_Cart
     */
    public $cart;

    public function __construct(\WC_Cart  $cart)
    {
        $this->cart = $cart;
    }

    public function getPhone()
    {
        if ($this->shipping_address && isset($this->shipping_address['phone']) && $this->shipping_address['phone'])
            return $this->shipping_address['phone'];
        if ($this->billing_address && isset($this->billing_address['phone']) && $this->billing_address['phone'])
            return $this->billing_address['phone'];

        return $this->cart->get_customer()->get_shipping_phone() ?: $this->cart->get_customer()->get_billing_phone();
    }

    public function getZip()
    {
        if ($this->shipping_address && isset($this->shipping_address['postcode']) && $this->shipping_address['postcode'])
            return $this->shipping_address['postcode'];
        return $this->cart->get_customer()->get_shipping_postcode();
    }

    public function getCountry()
    {
        if ($this->shipping_address && isset($this->shipping_address['country']) && $this->shipping_address['postcode'])
            return $this->shipping_address['country'];
        return $this->cart->get_customer()->get_shipping_country();
    }

}