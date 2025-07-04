<?php

namespace PPLCZ\Model\Model;

class ErrorLogItemModel extends \ArrayObject
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
     * @var float
     */
    protected $id;
    /**
     * 
     *
     * @var string
     */
    protected $trace;
    /**
     * 
     *
     * @return float
     */
    public function getId() : ?float
    {
        return $this->id;
    }
    /**
     * 
     *
     * @param float $id
     *
     * @return self
     */
    public function setId(float $id) : self
    {
        $this->initialized['id'] = true;
        $this->id = $id;
        return $this;
    }
    /**
     * 
     *
     * @return string
     */
    public function getTrace() : ?string
    {
        return $this->trace;
    }
    /**
     * 
     *
     * @param string $trace
     *
     * @return self
     */
    public function setTrace(string $trace) : self
    {
        $this->initialized['trace'] = true;
        $this->trace = $trace;
        return $this;
    }
}