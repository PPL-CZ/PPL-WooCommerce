<?php

namespace PPLCZ\Model\Model;

class PrintSettingModel extends \ArrayObject
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
    protected $format;
    /**
     * 
     *
     * @var string[]
     */
    protected $orderStatuses;
    /**
     * 
     *
     * @return string
     */
    public function getFormat() : ?string
    {
        return $this->format;
    }
    /**
     * 
     *
     * @param string $format
     *
     * @return self
     */
    public function setFormat(string $format) : self
    {
        $this->initialized['format'] = true;
        $this->format = $format;
        return $this;
    }
    /**
     * 
     *
     * @return string[]
     */
    public function getOrderStatuses() : ?array
    {
        return $this->orderStatuses;
    }
    /**
     * 
     *
     * @param string[] $orderStatuses
     *
     * @return self
     */
    public function setOrderStatuses(array $orderStatuses) : self
    {
        $this->initialized['orderStatuses'] = true;
        $this->orderStatuses = $orderStatuses;
        return $this;
    }
}