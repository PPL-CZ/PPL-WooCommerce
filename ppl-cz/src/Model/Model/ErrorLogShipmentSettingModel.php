<?php

namespace PPLCZ\Model\Model;

class ErrorLogShipmentSettingModel extends \ArrayObject
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
     * @var string
     */
    protected $name;
    /**
     * 
     *
     * @var ShipmentMethodSettingModel
     */
    protected $shipmentSetting;
    /**
     * 
     *
     * @var string|null
     */
    protected $rawBasicData;
    /**
     * 
     *
     * @var string|null
     */
    protected $rawWeightData;
    /**
     * 
     *
     * @var string
     */
    protected $zones;
    /**
     * 
     *
     * @return string
     */
    public function getName() : ?string
    {
        return $this->name;
    }
    /**
     * 
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name) : self
    {
        $this->initialized['name'] = true;
        $this->name = $name;
        return $this;
    }
    /**
     * 
     *
     * @return ShipmentMethodSettingModel
     */
    public function getShipmentSetting() : ?ShipmentMethodSettingModel
    {
        return $this->shipmentSetting;
    }
    /**
     * 
     *
     * @param ShipmentMethodSettingModel $shipmentSetting
     *
     * @return self
     */
    public function setShipmentSetting(?ShipmentMethodSettingModel $shipmentSetting) : self
    {
        $this->initialized['shipmentSetting'] = true;
        $this->shipmentSetting = $shipmentSetting;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getRawBasicData() : ?string
    {
        return $this->rawBasicData;
    }
    /**
     * 
     *
     * @param string|null $rawBasicData
     *
     * @return self
     */
    public function setRawBasicData(?string $rawBasicData) : self
    {
        $this->initialized['rawBasicData'] = true;
        $this->rawBasicData = $rawBasicData;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getRawWeightData() : ?string
    {
        return $this->rawWeightData;
    }
    /**
     * 
     *
     * @param string|null $rawWeightData
     *
     * @return self
     */
    public function setRawWeightData(?string $rawWeightData) : self
    {
        $this->initialized['rawWeightData'] = true;
        $this->rawWeightData = $rawWeightData;
        return $this;
    }
    /**
     * 
     *
     * @return string
     */
    public function getZones() : ?string
    {
        return $this->zones;
    }
    /**
     * 
     *
     * @param string $zones
     *
     * @return self
     */
    public function setZones(string $zones) : self
    {
        $this->initialized['zones'] = true;
        $this->zones = $zones;
        return $this;
    }
}