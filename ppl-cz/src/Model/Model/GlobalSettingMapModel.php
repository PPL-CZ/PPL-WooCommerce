<?php

namespace PPLCZ\Model\Model;

class GlobalSettingMapModel extends \ArrayObject
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
    protected $apikey;
    /**
     * 
     *
     * @var bool
     */
    protected $enabled;
    /**
     * 
     *
     * @var bool
     */
    protected $availableOldMap;
    /**
     * 
     *
     * @return string|null
     */
    public function getApikey() : ?string
    {
        return $this->apikey;
    }
    /**
     * 
     *
     * @param string|null $apikey
     *
     * @return self
     */
    public function setApikey(?string $apikey) : self
    {
        $this->initialized['apikey'] = true;
        $this->apikey = $apikey;
        return $this;
    }
    /**
     * 
     *
     * @return bool
     */
    public function getEnabled() : ?bool
    {
        return $this->enabled;
    }
    /**
     * 
     *
     * @param bool $enabled
     *
     * @return self
     */
    public function setEnabled(bool $enabled) : self
    {
        $this->initialized['enabled'] = true;
        $this->enabled = $enabled;
        return $this;
    }
    /**
     * 
     *
     * @return bool
     */
    public function getAvailableOldMap() : ?bool
    {
        return $this->availableOldMap;
    }
    /**
     * 
     *
     * @param bool $availableOldMap
     *
     * @return self
     */
    public function setAvailableOldMap(bool $availableOldMap) : self
    {
        $this->initialized['availableOldMap'] = true;
        $this->availableOldMap = $availableOldMap;
        return $this;
    }
}