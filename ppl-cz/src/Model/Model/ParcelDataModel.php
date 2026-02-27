<?php

namespace PPLCZ\Model\Model;

class ParcelDataModel extends \ArrayObject
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
    protected $name;
    /**
     * 
     *
     * @var bool|null
     */
    protected $activeCardPayment;
    /**
     * 
     *
     * @var bool|null
     */
    protected $activeCashPayment;
    /**
     * 
     *
     * @var string|null
     */
    protected $code;
    /**
     * 
     *
     * @var string|null
     */
    protected $country;
    /**
     * 
     *
     * @var string[]|null
     */
    protected $openHours;
    /**
     * 
     *
     * @var string|null
     */
    protected $street;
    /**
     * 
     *
     * @var string|null
     */
    protected $city;
    /**
     * 
     *
     * @var string|null
     */
    protected $zipCode;
    /**
     * 
     *
     * @var float|null
     */
    protected $id;
    /**
     * 
     *
     * @var float|null
     */
    protected $rnd;
    /**
     * 
     *
     * @var string|null
     */
    protected $accessPointType;
    /**
     * 
     *
     * @var ParcelDataModelGps|null
     */
    protected $gps;
    /**
     * 
     *
     * @var bool|null
     */
    protected $posnRequired;
    /**
     * 
     *
     * @return string|null
     */
    public function getName() : ?string
    {
        return $this->name;
    }
    /**
     * 
     *
     * @param string|null $name
     *
     * @return self
     */
    public function setName(?string $name) : self
    {
        $this->initialized['name'] = true;
        $this->name = $name;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getActiveCardPayment() : ?bool
    {
        return $this->activeCardPayment;
    }
    /**
     * 
     *
     * @param bool|null $activeCardPayment
     *
     * @return self
     */
    public function setActiveCardPayment(?bool $activeCardPayment) : self
    {
        $this->initialized['activeCardPayment'] = true;
        $this->activeCardPayment = $activeCardPayment;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getActiveCashPayment() : ?bool
    {
        return $this->activeCashPayment;
    }
    /**
     * 
     *
     * @param bool|null $activeCashPayment
     *
     * @return self
     */
    public function setActiveCashPayment(?bool $activeCashPayment) : self
    {
        $this->initialized['activeCashPayment'] = true;
        $this->activeCashPayment = $activeCashPayment;
        return $this;
    }
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
     * @return string|null
     */
    public function getCountry() : ?string
    {
        return $this->country;
    }
    /**
     * 
     *
     * @param string|null $country
     *
     * @return self
     */
    public function setCountry(?string $country) : self
    {
        $this->initialized['country'] = true;
        $this->country = $country;
        return $this;
    }
    /**
     * 
     *
     * @return string[]|null
     */
    public function getOpenHours() : ?array
    {
        return $this->openHours;
    }
    /**
     * 
     *
     * @param string[]|null $openHours
     *
     * @return self
     */
    public function setOpenHours(?array $openHours) : self
    {
        $this->initialized['openHours'] = true;
        $this->openHours = $openHours;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getStreet() : ?string
    {
        return $this->street;
    }
    /**
     * 
     *
     * @param string|null $street
     *
     * @return self
     */
    public function setStreet(?string $street) : self
    {
        $this->initialized['street'] = true;
        $this->street = $street;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getCity() : ?string
    {
        return $this->city;
    }
    /**
     * 
     *
     * @param string|null $city
     *
     * @return self
     */
    public function setCity(?string $city) : self
    {
        $this->initialized['city'] = true;
        $this->city = $city;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getZipCode() : ?string
    {
        return $this->zipCode;
    }
    /**
     * 
     *
     * @param string|null $zipCode
     *
     * @return self
     */
    public function setZipCode(?string $zipCode) : self
    {
        $this->initialized['zipCode'] = true;
        $this->zipCode = $zipCode;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getId() : ?float
    {
        return $this->id;
    }
    /**
     * 
     *
     * @param float|null $id
     *
     * @return self
     */
    public function setId(?float $id) : self
    {
        $this->initialized['id'] = true;
        $this->id = $id;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getRnd() : ?float
    {
        return $this->rnd;
    }
    /**
     * 
     *
     * @param float|null $rnd
     *
     * @return self
     */
    public function setRnd(?float $rnd) : self
    {
        $this->initialized['rnd'] = true;
        $this->rnd = $rnd;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getAccessPointType() : ?string
    {
        return $this->accessPointType;
    }
    /**
     * 
     *
     * @param string|null $accessPointType
     *
     * @return self
     */
    public function setAccessPointType(?string $accessPointType) : self
    {
        $this->initialized['accessPointType'] = true;
        $this->accessPointType = $accessPointType;
        return $this;
    }
    /**
     * 
     *
     * @return ParcelDataModelGps|null
     */
    public function getGps() : ?ParcelDataModelGps
    {
        return $this->gps;
    }
    /**
     * 
     *
     * @param ParcelDataModelGps|null $gps
     *
     * @return self
     */
    public function setGps(?ParcelDataModelGps $gps) : self
    {
        $this->initialized['gps'] = true;
        $this->gps = $gps;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getPosnRequired() : ?bool
    {
        return $this->posnRequired;
    }
    /**
     * 
     *
     * @param bool|null $posnRequired
     *
     * @return self
     */
    public function setPosnRequired(?bool $posnRequired) : self
    {
        $this->initialized['posnRequired'] = true;
        $this->posnRequired = $posnRequired;
        return $this;
    }
}