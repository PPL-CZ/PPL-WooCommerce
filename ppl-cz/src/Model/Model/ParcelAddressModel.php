<?php

namespace PPLCZ\Model\Model;

class ParcelAddressModel extends \ArrayObject
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
    protected $id;
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
    protected $name;
    /**
     * 
     *
     * @var string|null
     */
    protected $name2;
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
    protected $zip;
    /**
     * 
     *
     * @var string|null
     */
    protected $country;
    /**
     * 
     *
     * @var string|null
     */
    protected $remoteId;
    /**
     * 
     *
     * @var string|null
     */
    protected $type;
    /**
     * 
     *
     * @var float|null
     */
    protected $lat;
    /**
     * 
     *
     * @var float|null
     */
    protected $lng;
    /**
     * 
     *
     * @var bool|null
     */
    protected $posnRequired;
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
     * @return string|null
     */
    public function getName2() : ?string
    {
        return $this->name2;
    }
    /**
     * 
     *
     * @param string|null $name2
     *
     * @return self
     */
    public function setName2(?string $name2) : self
    {
        $this->initialized['name2'] = true;
        $this->name2 = $name2;
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
    public function getZip() : ?string
    {
        return $this->zip;
    }
    /**
     * 
     *
     * @param string|null $zip
     *
     * @return self
     */
    public function setZip(?string $zip) : self
    {
        $this->initialized['zip'] = true;
        $this->zip = $zip;
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
     * @return string|null
     */
    public function getRemoteId() : ?string
    {
        return $this->remoteId;
    }
    /**
     * 
     *
     * @param string|null $remoteId
     *
     * @return self
     */
    public function setRemoteId(?string $remoteId) : self
    {
        $this->initialized['remoteId'] = true;
        $this->remoteId = $remoteId;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getType() : ?string
    {
        return $this->type;
    }
    /**
     * 
     *
     * @param string|null $type
     *
     * @return self
     */
    public function setType(?string $type) : self
    {
        $this->initialized['type'] = true;
        $this->type = $type;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getLat() : ?float
    {
        return $this->lat;
    }
    /**
     * 
     *
     * @param float|null $lat
     *
     * @return self
     */
    public function setLat(?float $lat) : self
    {
        $this->initialized['lat'] = true;
        $this->lat = $lat;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getLng() : ?float
    {
        return $this->lng;
    }
    /**
     * 
     *
     * @param float|null $lng
     *
     * @return self
     */
    public function setLng(?float $lng) : self
    {
        $this->initialized['lng'] = true;
        $this->lng = $lng;
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