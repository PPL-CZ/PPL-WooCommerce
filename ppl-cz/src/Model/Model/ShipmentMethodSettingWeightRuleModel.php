<?php

namespace PPLCZ\Model\Model;

class ShipmentMethodSettingWeightRuleModel extends \ArrayObject
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
     * @var float|null
     */
    protected $from;
    /**
     * 
     *
     * @var float|null
     */
    protected $to;
    /**
     * 
     *
     * @var bool|null
     */
    protected $disabledParcelBox;
    /**
     * 
     *
     * @var bool
     */
    protected $disabledAlzaBox;
    /**
     * 
     *
     * @var bool|null
     */
    protected $disabledParcelShop;
    /**
     * 
     *
     * @var ShipmentMethodSettingPriceRuleModel[]
     */
    protected $prices;
    /**
     * 
     *
     * @return float|null
     */
    public function getFrom() : ?float
    {
        return $this->from;
    }
    /**
     * 
     *
     * @param float|null $from
     *
     * @return self
     */
    public function setFrom(?float $from) : self
    {
        $this->initialized['from'] = true;
        $this->from = $from;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getTo() : ?float
    {
        return $this->to;
    }
    /**
     * 
     *
     * @param float|null $to
     *
     * @return self
     */
    public function setTo(?float $to) : self
    {
        $this->initialized['to'] = true;
        $this->to = $to;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getDisabledParcelBox() : ?bool
    {
        return $this->disabledParcelBox;
    }
    /**
     * 
     *
     * @param bool|null $disabledParcelBox
     *
     * @return self
     */
    public function setDisabledParcelBox(?bool $disabledParcelBox) : self
    {
        $this->initialized['disabledParcelBox'] = true;
        $this->disabledParcelBox = $disabledParcelBox;
        return $this;
    }
    /**
     * 
     *
     * @return bool
     */
    public function getDisabledAlzaBox() : ?bool
    {
        return $this->disabledAlzaBox;
    }
    /**
     * 
     *
     * @param bool $disabledAlzaBox
     *
     * @return self
     */
    public function setDisabledAlzaBox(bool $disabledAlzaBox) : self
    {
        $this->initialized['disabledAlzaBox'] = true;
        $this->disabledAlzaBox = $disabledAlzaBox;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getDisabledParcelShop() : ?bool
    {
        return $this->disabledParcelShop;
    }
    /**
     * 
     *
     * @param bool|null $disabledParcelShop
     *
     * @return self
     */
    public function setDisabledParcelShop(?bool $disabledParcelShop) : self
    {
        $this->initialized['disabledParcelShop'] = true;
        $this->disabledParcelShop = $disabledParcelShop;
        return $this;
    }
    /**
     * 
     *
     * @return ShipmentMethodSettingPriceRuleModel[]
     */
    public function getPrices() : ?array
    {
        return $this->prices;
    }
    /**
     * 
     *
     * @param ShipmentMethodSettingPriceRuleModel[] $prices
     *
     * @return self
     */
    public function setPrices(array $prices) : self
    {
        $this->initialized['prices'] = true;
        $this->prices = $prices;
        return $this;
    }
}