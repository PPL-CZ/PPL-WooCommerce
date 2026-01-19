<?php

namespace PPLCZ\Model\Model;

class UpdateShipmentParcelModel extends \ArrayObject
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
     * @var string|null
     */
    protected $parcelCode;
    /**
     * 
     *
     * @var string|null
     */
    protected $parcelCountry;
    /**
     * 
     *
     * @var bool|null
     */
    protected $hasParcel;
    /**
     * 
     *
     * @return string|null
     */
    public function getParcelCode() : ?string
    {
        return $this->parcelCode;
    }
    /**
     * 
     *
     * @param string|null $parcelCode
     *
     * @return self
     */
    public function setParcelCode(?string $parcelCode) : self
    {
        $this->initialized['parcelCode'] = true;
        $this->parcelCode = $parcelCode;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getParcelCountry() : ?string
    {
        return $this->parcelCountry;
    }
    /**
     * 
     *
     * @param string|null $parcelCountry
     *
     * @return self
     */
    public function setParcelCountry(?string $parcelCountry) : self
    {
        $this->initialized['parcelCountry'] = true;
        $this->parcelCountry = $parcelCountry;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getHasParcel() : ?bool
    {
        return $this->hasParcel;
    }
    /**
     * 
     *
     * @param bool|null $hasParcel
     *
     * @return self
     */
    public function setHasParcel(?bool $hasParcel) : self
    {
        $this->initialized['hasParcel'] = true;
        $this->hasParcel = $hasParcel;
        return $this;
    }
}