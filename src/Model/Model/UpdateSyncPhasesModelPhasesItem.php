<?php

namespace PPLCZ\Model\Model;

class UpdateSyncPhasesModelPhasesItem extends \ArrayObject
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
    protected $code;
    /**
     * 
     *
     * @var bool|null
     */
    protected $watch;
    /**
     * 
     *
     * @return string|null
     */
    public function getCode() : ?string
    {
        return $this->code;
    }
    /**
     * 
     *
     * @param string|null $code
     *
     * @return self
     */
    public function setCode(?string $code) : self
    {
        $this->initialized['code'] = true;
        $this->code = $code;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getWatch() : ?bool
    {
        return $this->watch;
    }
    /**
     * 
     *
     * @param bool|null $watch
     *
     * @return self
     */
    public function setWatch(?bool $watch) : self
    {
        $this->initialized['watch'] = true;
        $this->watch = $watch;
        return $this;
    }
}