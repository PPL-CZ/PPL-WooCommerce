<?php

namespace PPLCZ\Model\Model;

class CartDataModel extends \ArrayObject
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
     * @var ParcelDataModel
     */
    protected $parcelData;
    /**
     * 
     *
     * @var AdditionalDataModel
     */
    protected $additionalData;
    /**
     * 
     *
     * @return ParcelDataModel
     */
    public function getParcelData() : ?ParcelDataModel
    {
        return $this->parcelData;
    }
    /**
     * 
     *
     * @param ParcelDataModel $parcelData
     *
     * @return self
     */
    public function setParcelData(?ParcelDataModel $parcelData) : self
    {
        $this->initialized['parcelData'] = true;
        $this->parcelData = $parcelData;
        return $this;
    }
    /**
     * 
     *
     * @return AdditionalDataModel
     */
    public function getAdditionalData() : ?AdditionalDataModel
    {
        return $this->additionalData;
    }
    /**
     * 
     *
     * @param AdditionalDataModel $additionalData
     *
     * @return self
     */
    public function setAdditionalData(?AdditionalDataModel $additionalData) : self
    {
        $this->initialized['additionalData'] = true;
        $this->additionalData = $additionalData;
        return $this;
    }
}