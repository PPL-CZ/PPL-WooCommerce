<?php

namespace PPLCZ\Model\Model;

class ShipmentWithAdditionalModel extends \ArrayObject
{
    /**
     * @var array
     */
    protected $initialized = array();
    public function isInitialized($property) : bool
    {
        return array_key_exists($property, $this->initialized);
    }
    /**
     * 
     *
     * @var ShipmentModel
     */
    protected $shipment;
    /**
     * 
     *
     * @var WpErrorModel[]|null
     */
    protected $errors;
    /**
     * 
     *
     * @return ShipmentModel
     */
    public function getShipment() : ?ShipmentModel
    {
        return $this->shipment;
    }
    /**
     * 
     *
     * @param ShipmentModel $shipment
     *
     * @return self
     */
    public function setShipment(?ShipmentModel $shipment) : self
    {
        $this->initialized['shipment'] = true;
        $this->shipment = $shipment;
        return $this;
    }
    /**
     * 
     *
     * @return WpErrorModel[]|null
     */
    public function getErrors() : ?array
    {
        return $this->errors;
    }
    /**
     * 
     *
     * @param WpErrorModel[]|null $errors
     *
     * @return self
     */
    public function setErrors(?array $errors) : self
    {
        $this->initialized['errors'] = true;
        $this->errors = $errors;
        return $this;
    }
}