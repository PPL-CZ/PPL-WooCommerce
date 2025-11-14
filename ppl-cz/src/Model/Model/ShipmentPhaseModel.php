<?php

namespace PPLCZ\Model\Model;

class ShipmentPhaseModel extends \ArrayObject
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
    protected $code;
    /**
     * 
     *
     * @var string
     */
    protected $title;
    /**
     * 
     *
     * @var bool
     */
    protected $watch;
    /**
     * 
     *
     * @var string|null
     */
    protected $orderState;
    /**
     * 
     *
     * @return string
     */
    public function getCode() : ?string
    {
        return $this->code;
    }
    /**
     * 
     *
     * @param string $code
     *
     * @return self
     */
    public function setCode(string $code) : self
    {
        $this->initialized['code'] = true;
        $this->code = $code;
        return $this;
    }
    /**
     * 
     *
     * @return string
     */
    public function getTitle() : ?string
    {
        return $this->title;
    }
    /**
     * 
     *
     * @param string $title
     *
     * @return self
     */
    public function setTitle(string $title) : self
    {
        $this->initialized['title'] = true;
        $this->title = $title;
        return $this;
    }
    /**
     * 
     *
     * @return bool
     */
    public function getWatch() : ?bool
    {
        return $this->watch;
    }
    /**
     * 
     *
     * @param bool $watch
     *
     * @return self
     */
    public function setWatch(bool $watch) : self
    {
        $this->initialized['watch'] = true;
        $this->watch = $watch;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getOrderState() : ?string
    {
        return $this->orderState;
    }
    /**
     * 
     *
     * @param string|null $orderState
     *
     * @return self
     */
    public function setOrderState(?string $orderState) : self
    {
        $this->initialized['orderState'] = true;
        $this->orderState = $orderState;
        return $this;
    }
}