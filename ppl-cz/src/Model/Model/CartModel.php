<?php

namespace PPLCZ\Model\Model;

class CartModel extends \ArrayObject
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
    protected $priceWithDph;
    /**
     * 
     *
     * @var bool|null
     */
    protected $parcelRequired;
    /**
     * 
     *
     * @var bool|null
     */
    protected $parcelBoxEnabled;
    /**
     * 
     *
     * @var bool|null
     */
    protected $parcelShopEnabled;
    /**
     * 
     *
     * @var bool|null
     */
    protected $alzaBoxEnabled;
    /**
     * 
     *
     * @var bool|null
     */
    protected $mapEnabled;
    /**
     * 
     *
     * @var bool|null
     */
    protected $disabledByWeight;
    /**
     * 
     *
     * @var bool|null
     */
    protected $disabledByRules;
    /**
     * 
     *
     * @var bool
     */
    protected $disabledByCountry;
    /**
     * 
     *
     * @var bool|null
     */
    protected $ageRequired;
    /**
     * 
     *
     * @var string|null
     */
    protected $codPayment;
    /**
     * 
     *
     * @var string
     */
    protected $serviceCode;
    /**
     * 
     *
     * @var string[]|null
     */
    protected $disablePayments;
    /**
     * 
     *
     * @var bool
     */
    protected $disabledByProduct;
    /**
     * 
     *
     * @var bool|null
     */
    protected $disableCod;
    /**
     * 
     *
     * @var float|null
     */
    protected $codFee;
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
     * @return bool|null
     */
    public function getParcelRequired() : ?bool
    {
        return $this->parcelRequired;
    }
    /**
     * 
     *
     * @param bool|null $parcelRequired
     *
     * @return self
     */
    public function setParcelRequired(?bool $parcelRequired) : self
    {
        $this->initialized['parcelRequired'] = true;
        $this->parcelRequired = $parcelRequired;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getParcelBoxEnabled() : ?bool
    {
        return $this->parcelBoxEnabled;
    }
    /**
     * 
     *
     * @param bool|null $parcelBoxEnabled
     *
     * @return self
     */
    public function setParcelBoxEnabled(?bool $parcelBoxEnabled) : self
    {
        $this->initialized['parcelBoxEnabled'] = true;
        $this->parcelBoxEnabled = $parcelBoxEnabled;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getParcelShopEnabled() : ?bool
    {
        return $this->parcelShopEnabled;
    }
    /**
     * 
     *
     * @param bool|null $parcelShopEnabled
     *
     * @return self
     */
    public function setParcelShopEnabled(?bool $parcelShopEnabled) : self
    {
        $this->initialized['parcelShopEnabled'] = true;
        $this->parcelShopEnabled = $parcelShopEnabled;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getAlzaBoxEnabled() : ?bool
    {
        return $this->alzaBoxEnabled;
    }
    /**
     * 
     *
     * @param bool|null $alzaBoxEnabled
     *
     * @return self
     */
    public function setAlzaBoxEnabled(?bool $alzaBoxEnabled) : self
    {
        $this->initialized['alzaBoxEnabled'] = true;
        $this->alzaBoxEnabled = $alzaBoxEnabled;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getMapEnabled() : ?bool
    {
        return $this->mapEnabled;
    }
    /**
     * 
     *
     * @param bool|null $mapEnabled
     *
     * @return self
     */
    public function setMapEnabled(?bool $mapEnabled) : self
    {
        $this->initialized['mapEnabled'] = true;
        $this->mapEnabled = $mapEnabled;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getDisabledByWeight() : ?bool
    {
        return $this->disabledByWeight;
    }
    /**
     * 
     *
     * @param bool|null $disabledByWeight
     *
     * @return self
     */
    public function setDisabledByWeight(?bool $disabledByWeight) : self
    {
        $this->initialized['disabledByWeight'] = true;
        $this->disabledByWeight = $disabledByWeight;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getDisabledByRules() : ?bool
    {
        return $this->disabledByRules;
    }
    /**
     * 
     *
     * @param bool|null $disabledByRules
     *
     * @return self
     */
    public function setDisabledByRules(?bool $disabledByRules) : self
    {
        $this->initialized['disabledByRules'] = true;
        $this->disabledByRules = $disabledByRules;
        return $this;
    }
    /**
     * 
     *
     * @return bool
     */
    public function getDisabledByCountry() : ?bool
    {
        return $this->disabledByCountry;
    }
    /**
     * 
     *
     * @param bool $disabledByCountry
     *
     * @return self
     */
    public function setDisabledByCountry(bool $disabledByCountry) : self
    {
        $this->initialized['disabledByCountry'] = true;
        $this->disabledByCountry = $disabledByCountry;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getAgeRequired() : ?bool
    {
        return $this->ageRequired;
    }
    /**
     * 
     *
     * @param bool|null $ageRequired
     *
     * @return self
     */
    public function setAgeRequired(?bool $ageRequired) : self
    {
        $this->initialized['ageRequired'] = true;
        $this->ageRequired = $ageRequired;
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
     * @return string
     */
    public function getServiceCode() : ?string
    {
        return $this->serviceCode;
    }
    /**
     * 
     *
     * @param string $serviceCode
     *
     * @return self
     */
    public function setServiceCode(string $serviceCode) : self
    {
        $this->initialized['serviceCode'] = true;
        $this->serviceCode = $serviceCode;
        return $this;
    }
    /**
     * 
     *
     * @return string[]|null
     */
    public function getDisablePayments() : ?array
    {
        return $this->disablePayments;
    }
    /**
     * 
     *
     * @param string[]|null $disablePayments
     *
     * @return self
     */
    public function setDisablePayments(?array $disablePayments) : self
    {
        $this->initialized['disablePayments'] = true;
        $this->disablePayments = $disablePayments;
        return $this;
    }
    /**
     * 
     *
     * @return bool
     */
    public function getDisabledByProduct() : ?bool
    {
        return $this->disabledByProduct;
    }
    /**
     * 
     *
     * @param bool $disabledByProduct
     *
     * @return self
     */
    public function setDisabledByProduct(bool $disabledByProduct) : self
    {
        $this->initialized['disabledByProduct'] = true;
        $this->disabledByProduct = $disabledByProduct;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getDisableCod() : ?bool
    {
        return $this->disableCod;
    }
    /**
     * 
     *
     * @param bool|null $disableCod
     *
     * @return self
     */
    public function setDisableCod(?bool $disableCod) : self
    {
        $this->initialized['disableCod'] = true;
        $this->disableCod = $disableCod;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getCodFee() : ?float
    {
        return $this->codFee;
    }
    /**
     * 
     *
     * @param float|null $codFee
     *
     * @return self
     */
    public function setCodFee(?float $codFee) : self
    {
        $this->initialized['codFee'] = true;
        $this->codFee = $codFee;
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