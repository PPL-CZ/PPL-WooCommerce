<?php

namespace PPLCZ\Model\Model;

class GlobalSettingModel extends \ArrayObject
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
     * @var bool
     */
    protected $useOrderNumberInPackages;
    /**
     * 
     *
     * @var bool
     */
    protected $useOrderNumberInVariableSymbol;
    /**
     * 
     *
     * @var bool
     */
    protected $openMapOnRequest;
    /**
     * 
     *
     * @var GlobalSettingMapModel
     */
    protected $map;
    /**
     * 
     *
     * @return bool
     */
    public function getUseOrderNumberInPackages() : ?bool
    {
        return $this->useOrderNumberInPackages;
    }
    /**
     * 
     *
     * @param bool $useOrderNumberInPackages
     *
     * @return self
     */
    public function setUseOrderNumberInPackages(bool $useOrderNumberInPackages) : self
    {
        $this->initialized['useOrderNumberInPackages'] = true;
        $this->useOrderNumberInPackages = $useOrderNumberInPackages;
        return $this;
    }
    /**
     * 
     *
     * @return bool
     */
    public function getUseOrderNumberInVariableSymbol() : ?bool
    {
        return $this->useOrderNumberInVariableSymbol;
    }
    /**
     * 
     *
     * @param bool $useOrderNumberInVariableSymbol
     *
     * @return self
     */
    public function setUseOrderNumberInVariableSymbol(bool $useOrderNumberInVariableSymbol) : self
    {
        $this->initialized['useOrderNumberInVariableSymbol'] = true;
        $this->useOrderNumberInVariableSymbol = $useOrderNumberInVariableSymbol;
        return $this;
    }
    /**
     * 
     *
     * @return bool
     */
    public function getOpenMapOnRequest() : ?bool
    {
        return $this->openMapOnRequest;
    }
    /**
     * 
     *
     * @param bool $openMapOnRequest
     *
     * @return self
     */
    public function setOpenMapOnRequest(bool $openMapOnRequest) : self
    {
        $this->initialized['openMapOnRequest'] = true;
        $this->openMapOnRequest = $openMapOnRequest;
        return $this;
    }
    /**
     * 
     *
     * @return GlobalSettingMapModel
     */
    public function getMap() : ?GlobalSettingMapModel
    {
        return $this->map;
    }
    /**
     * 
     *
     * @param GlobalSettingMapModel $map
     *
     * @return self
     */
    public function setMap(?GlobalSettingMapModel $map) : self
    {
        $this->initialized['map'] = true;
        $this->map = $map;
        return $this;
    }
}