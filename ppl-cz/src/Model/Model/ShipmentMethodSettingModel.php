<?php

namespace PPLCZ\Model\Model;

class ShipmentMethodSettingModel extends \ArrayObject
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
     * @var bool|null
     */
    protected $costByWeight;
    /**
     * 
     *
     * @var bool|null
     */
    protected $parcelBoxes;
    /**
     * 
     *
     * @var string|null
     */
    protected $title;
    /**
     * 
     *
     * @var string|null
     */
    protected $description;
    /**
     * 
     *
     * @var string[]
     */
    protected $disablePayments;
    /**
     * 
     *
     * @var string|null
     */
    protected $codPayment;
    /**
     * 
     *
     * @var bool|null
     */
    protected $priceWithDph;
    /**
     * 
     *
     * @var ShipmentMethodSettingCurrencyModel[]
     */
    protected $currencies;
    /**
     * 
     *
     * @var ShipmentMethodSettingWeightRuleModel[]
     */
    protected $weights;
    /**
     * 
     *
     * @var bool|null
     */
    protected $disabledParcelBox;
    /**
     * 
     *
     * @var bool|null
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
     * @return bool|null
     */
    public function getCostByWeight() : ?bool
    {
        return $this->costByWeight;
    }
    /**
     * 
     *
     * @param bool|null $costByWeight
     *
     * @return self
     */
    public function setCostByWeight(?bool $costByWeight) : self
    {
        $this->initialized['costByWeight'] = true;
        $this->costByWeight = $costByWeight;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getParcelBoxes() : ?bool
    {
        return $this->parcelBoxes;
    }
    /**
     * 
     *
     * @param bool|null $parcelBoxes
     *
     * @return self
     */
    public function setParcelBoxes(?bool $parcelBoxes) : self
    {
        $this->initialized['parcelBoxes'] = true;
        $this->parcelBoxes = $parcelBoxes;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getTitle() : ?string
    {
        return $this->title;
    }
    /**
     * 
     *
     * @param string|null $title
     *
     * @return self
     */
    public function setTitle(?string $title) : self
    {
        $this->initialized['title'] = true;
        $this->title = $title;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }
    /**
     * 
     *
     * @param string|null $description
     *
     * @return self
     */
    public function setDescription(?string $description) : self
    {
        $this->initialized['description'] = true;
        $this->description = $description;
        return $this;
    }
    /**
     * 
     *
     * @return string[]
     */
    public function getDisablePayments() : ?array
    {
        return $this->disablePayments;
    }
    /**
     * 
     *
     * @param string[] $disablePayments
     *
     * @return self
     */
    public function setDisablePayments(array $disablePayments) : self
    {
        $this->initialized['disablePayments'] = true;
        $this->disablePayments = $disablePayments;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getCodPayment() : ?string
    {
        return $this->codPayment;
    }
    /**
     * 
     *
     * @param string|null $codPayment
     *
     * @return self
     */
    public function setCodPayment(?string $codPayment) : self
    {
        $this->initialized['codPayment'] = true;
        $this->codPayment = $codPayment;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getPriceWithDph() : ?bool
    {
        return $this->priceWithDph;
    }
    /**
     * 
     *
     * @param bool|null $priceWithDph
     *
     * @return self
     */
    public function setPriceWithDph(?bool $priceWithDph) : self
    {
        $this->initialized['priceWithDph'] = true;
        $this->priceWithDph = $priceWithDph;
        return $this;
    }
    /**
     * 
     *
     * @return ShipmentMethodSettingCurrencyModel[]
     */
    public function getCurrencies() : ?array
    {
        return $this->currencies;
    }
    /**
     * 
     *
     * @param ShipmentMethodSettingCurrencyModel[] $currencies
     *
     * @return self
     */
    public function setCurrencies(array $currencies) : self
    {
        $this->initialized['currencies'] = true;
        $this->currencies = $currencies;
        return $this;
    }
    /**
     * 
     *
     * @return ShipmentMethodSettingWeightRuleModel[]
     */
    public function getWeights() : ?array
    {
        return $this->weights;
    }
    /**
     * 
     *
     * @param ShipmentMethodSettingWeightRuleModel[] $weights
     *
     * @return self
     */
    public function setWeights(array $weights) : self
    {
        $this->initialized['weights'] = true;
        $this->weights = $weights;
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
     * @return bool|null
     */
    public function getDisabledAlzaBox() : ?bool
    {
        return $this->disabledAlzaBox;
    }
    /**
     * 
     *
     * @param bool|null $disabledAlzaBox
     *
     * @return self
     */
    public function setDisabledAlzaBox(?bool $disabledAlzaBox) : self
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
}