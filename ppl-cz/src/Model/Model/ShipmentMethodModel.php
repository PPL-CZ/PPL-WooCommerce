<?php

namespace PPLCZ\Model\Model;

class ShipmentMethodModel extends \ArrayObject
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
     * @var string
     */
    protected $description;
    /**
     * 
     *
     * @var bool|null
     */
    protected $ageValidation;
    /**
     * 
     *
     * @var bool
     */
    protected $codAvailable;
    /**
     * 
     *
     * @var bool
     */
    protected $parcelRequired;
    /**
     * 
     *
     * @var string[]|null
     */
    protected $disabledParcelTypes;
    /**
     * 
     *
     * @var string[]|null
     */
    protected $availableParcelTypes;
    /**
     * 
     *
     * @var string[]
     */
    protected $countries;
    /**
     * 
     *
     * @var float|null
     */
    protected $maxWeight;
    /**
     * 
     *
     * @var float[]|null
     */
    protected $maxDimension;
    /**
     * 
     *
     * @var float|null
     */
    protected $maxPackages;
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
     * @return string
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }
    /**
     * 
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription(string $description) : self
    {
        $this->initialized['description'] = true;
        $this->description = $description;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getAgeValidation() : ?bool
    {
        return $this->ageValidation;
    }
    /**
     * 
     *
     * @param bool|null $ageValidation
     *
     * @return self
     */
    public function setAgeValidation(?bool $ageValidation) : self
    {
        $this->initialized['ageValidation'] = true;
        $this->ageValidation = $ageValidation;
        return $this;
    }
    /**
     * 
     *
     * @return bool
     */
    public function getCodAvailable() : ?bool
    {
        return $this->codAvailable;
    }
    /**
     * 
     *
     * @param bool $codAvailable
     *
     * @return self
     */
    public function setCodAvailable(bool $codAvailable) : self
    {
        $this->initialized['codAvailable'] = true;
        $this->codAvailable = $codAvailable;
        return $this;
    }
    /**
     * 
     *
     * @return bool
     */
    public function getParcelRequired() : ?bool
    {
        return $this->parcelRequired;
    }
    /**
     * 
     *
     * @param bool $parcelRequired
     *
     * @return self
     */
    public function setParcelRequired(bool $parcelRequired) : self
    {
        $this->initialized['parcelRequired'] = true;
        $this->parcelRequired = $parcelRequired;
        return $this;
    }
    /**
     * 
     *
     * @return string[]|null
     */
    public function getDisabledParcelTypes() : ?array
    {
        return $this->disabledParcelTypes;
    }
    /**
     * 
     *
     * @param string[]|null $disabledParcelTypes
     *
     * @return self
     */
    public function setDisabledParcelTypes(?array $disabledParcelTypes) : self
    {
        $this->initialized['disabledParcelTypes'] = true;
        $this->disabledParcelTypes = $disabledParcelTypes;
        return $this;
    }
    /**
     * 
     *
     * @return string[]|null
     */
    public function getAvailableParcelTypes() : ?array
    {
        return $this->availableParcelTypes;
    }
    /**
     * 
     *
     * @param string[]|null $availableParcelTypes
     *
     * @return self
     */
    public function setAvailableParcelTypes(?array $availableParcelTypes) : self
    {
        $this->initialized['availableParcelTypes'] = true;
        $this->availableParcelTypes = $availableParcelTypes;
        return $this;
    }
    /**
     * 
     *
     * @return string[]
     */
    public function getCountries() : ?array
    {
        return $this->countries;
    }
    /**
     * 
     *
     * @param string[] $countries
     *
     * @return self
     */
    public function setCountries(array $countries) : self
    {
        $this->initialized['countries'] = true;
        $this->countries = $countries;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getMaxWeight() : ?float
    {
        return $this->maxWeight;
    }
    /**
     * 
     *
     * @param float|null $maxWeight
     *
     * @return self
     */
    public function setMaxWeight(?float $maxWeight) : self
    {
        $this->initialized['maxWeight'] = true;
        $this->maxWeight = $maxWeight;
        return $this;
    }
    /**
     * 
     *
     * @return float[]|null
     */
    public function getMaxDimension() : ?array
    {
        return $this->maxDimension;
    }
    /**
     * 
     *
     * @param float[]|null $maxDimension
     *
     * @return self
     */
    public function setMaxDimension(?array $maxDimension) : self
    {
        $this->initialized['maxDimension'] = true;
        $this->maxDimension = $maxDimension;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getMaxPackages() : ?float
    {
        return $this->maxPackages;
    }
    /**
     * 
     *
     * @param float|null $maxPackages
     *
     * @return self
     */
    public function setMaxPackages(?float $maxPackages) : self
    {
        $this->initialized['maxPackages'] = true;
        $this->maxPackages = $maxPackages;
        return $this;
    }
}