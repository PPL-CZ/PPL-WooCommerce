<?php

namespace PPLCZ\Model\Model;

class CalculatedDPH extends \ArrayObject
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
     * @var int
     */
    protected $dphId;
    /**
     * 
     *
     * @var float
     */
    protected $value;
    /**
     * 
     *
     * @return int
     */
    public function getDphId() : ?int
    {
        return $this->dphId;
    }
    /**
     * 
     *
     * @param int $dphId
     *
     * @return self
     */
    public function setDphId(int $dphId) : self
    {
        $this->initialized['dphId'] = true;
        $this->dphId = $dphId;
        return $this;
    }
    /**
     * 
     *
     * @return float
     */
    public function getValue() : ?float
    {
        return $this->value;
    }
    /**
     * 
     *
     * @param float $value
     *
     * @return self
     */
    public function setValue(float $value) : self
    {
        $this->initialized['value'] = true;
        $this->value = $value;
        return $this;
    }
}