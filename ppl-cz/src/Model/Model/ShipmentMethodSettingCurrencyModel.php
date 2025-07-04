<?php

namespace PPLCZ\Model\Model;

class ShipmentMethodSettingCurrencyModel extends \ArrayObject
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
     * @var bool|null
     */
    protected $enabled;
    /**
     * 
     *
     * @var string
     */
    protected $currency;
    /**
     * 
     *
     * @var float|null
     */
    protected $costOrderFree;
    /**
     * 
     *
     * @var float|null
     */
    protected $costCodFee;
    /**
     * 
     *
     * @var bool|null
     */
    protected $costCodFeeAlways;
    /**
     * 
     *
     * @var float|null
     */
    protected $costOrderFreeCod;
    /**
     * 
     *
     * @var float|null
     */
    protected $cost;
    /**
     * 
     *
     * @return bool|null
     */
    public function getEnabled() : ?bool
    {
        return $this->enabled;
    }
    /**
     * 
     *
     * @param bool|null $enabled
     *
     * @return self
     */
    public function setEnabled(?bool $enabled) : self
    {
        $this->initialized['enabled'] = true;
        $this->enabled = $enabled;
        return $this;
    }
    /**
     * 
     *
     * @return string
     */
    public function getCurrency() : ?string
    {
        return $this->currency;
    }
    /**
     * 
     *
     * @param string $currency
     *
     * @return self
     */
    public function setCurrency(string $currency) : self
    {
        $this->initialized['currency'] = true;
        $this->currency = $currency;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getCostOrderFree() : ?float
    {
        return $this->costOrderFree;
    }
    /**
     * 
     *
     * @param float|null $costOrderFree
     *
     * @return self
     */
    public function setCostOrderFree(?float $costOrderFree) : self
    {
        $this->initialized['costOrderFree'] = true;
        $this->costOrderFree = $costOrderFree;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getCostCodFee() : ?float
    {
        return $this->costCodFee;
    }
    /**
     * 
     *
     * @param float|null $costCodFee
     *
     * @return self
     */
    public function setCostCodFee(?float $costCodFee) : self
    {
        $this->initialized['costCodFee'] = true;
        $this->costCodFee = $costCodFee;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getCostCodFeeAlways() : ?bool
    {
        return $this->costCodFeeAlways;
    }
    /**
     * 
     *
     * @param bool|null $costCodFeeAlways
     *
     * @return self
     */
    public function setCostCodFeeAlways(?bool $costCodFeeAlways) : self
    {
        $this->initialized['costCodFeeAlways'] = true;
        $this->costCodFeeAlways = $costCodFeeAlways;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getCostOrderFreeCod() : ?float
    {
        return $this->costOrderFreeCod;
    }
    /**
     * 
     *
     * @param float|null $costOrderFreeCod
     *
     * @return self
     */
    public function setCostOrderFreeCod(?float $costOrderFreeCod) : self
    {
        $this->initialized['costOrderFreeCod'] = true;
        $this->costOrderFreeCod = $costOrderFreeCod;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getCost() : ?float
    {
        return $this->cost;
    }
    /**
     * 
     *
     * @param float|null $cost
     *
     * @return self
     */
    public function setCost(?float $cost) : self
    {
        $this->initialized['cost'] = true;
        $this->cost = $cost;
        return $this;
    }
}