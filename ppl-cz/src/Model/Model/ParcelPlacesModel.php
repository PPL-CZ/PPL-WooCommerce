<?php

namespace PPLCZ\Model\Model;

class ParcelPlacesModel extends \ArrayObject
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
     * @var string[]|null
     */
    protected $disabledCountries;
    /**
     * 
     *
     * @var string|null
     */
    protected $mapLanguage;
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
    /**
     * 
     *
     * @return string[]|null
     */
    public function getDisabledCountries() : ?array
    {
        return $this->disabledCountries;
    }
    /**
     * 
     *
     * @param string[]|null $disabledCountries
     *
     * @return self
     */
    public function setDisabledCountries(?array $disabledCountries) : self
    {
        $this->initialized['disabledCountries'] = true;
        $this->disabledCountries = $disabledCountries;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getMapLanguage() : ?string
    {
        return $this->mapLanguage;
    }
    /**
     * 
     *
     * @param string|null $mapLanguage
     *
     * @return self
     */
    public function setMapLanguage(?string $mapLanguage) : self
    {
        $this->initialized['mapLanguage'] = true;
        $this->mapLanguage = $mapLanguage;
        return $this;
    }
}