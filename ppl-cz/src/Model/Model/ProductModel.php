<?php

namespace PPLCZ\Model\Model;

class ProductModel extends \ArrayObject
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
    protected $pplConfirmAge15;
    /**
     * 
     *
     * @var bool|null
     */
    protected $pplConfirmAge18;
    /**
     * 
     *
     * @var bool|null
     */
    protected $pplDisabledParcelBox;
    /**
     * 
     *
     * @var bool
     */
    protected $pplDisabledAlzaBox;
    /**
     * 
     *
     * @var bool|null
     */
    protected $pplDisabledParcelShop;
    /**
     * 
     *
     * @var string[]|null
     */
    protected $pplDisabledTransport;
    /**
     * 
     *
     * @var PackageSizeModel[]|null
     */
    protected $pplSizes;
    /**
     * 
     *
     * @return bool|null
     */
    public function getPplConfirmAge15() : ?bool
    {
        return $this->pplConfirmAge15;
    }
    /**
     * 
     *
     * @param bool|null $pplConfirmAge15
     *
     * @return self
     */
    public function setPplConfirmAge15(?bool $pplConfirmAge15) : self
    {
        $this->initialized['pplConfirmAge15'] = true;
        $this->pplConfirmAge15 = $pplConfirmAge15;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getPplConfirmAge18() : ?bool
    {
        return $this->pplConfirmAge18;
    }
    /**
     * 
     *
     * @param bool|null $pplConfirmAge18
     *
     * @return self
     */
    public function setPplConfirmAge18(?bool $pplConfirmAge18) : self
    {
        $this->initialized['pplConfirmAge18'] = true;
        $this->pplConfirmAge18 = $pplConfirmAge18;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getPplDisabledParcelBox() : ?bool
    {
        return $this->pplDisabledParcelBox;
    }
    /**
     * 
     *
     * @param bool|null $pplDisabledParcelBox
     *
     * @return self
     */
    public function setPplDisabledParcelBox(?bool $pplDisabledParcelBox) : self
    {
        $this->initialized['pplDisabledParcelBox'] = true;
        $this->pplDisabledParcelBox = $pplDisabledParcelBox;
        return $this;
    }
    /**
     * 
     *
     * @return bool
     */
    public function getPplDisabledAlzaBox() : ?bool
    {
        return $this->pplDisabledAlzaBox;
    }
    /**
     * 
     *
     * @param bool $pplDisabledAlzaBox
     *
     * @return self
     */
    public function setPplDisabledAlzaBox(bool $pplDisabledAlzaBox) : self
    {
        $this->initialized['pplDisabledAlzaBox'] = true;
        $this->pplDisabledAlzaBox = $pplDisabledAlzaBox;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getPplDisabledParcelShop() : ?bool
    {
        return $this->pplDisabledParcelShop;
    }
    /**
     * 
     *
     * @param bool|null $pplDisabledParcelShop
     *
     * @return self
     */
    public function setPplDisabledParcelShop(?bool $pplDisabledParcelShop) : self
    {
        $this->initialized['pplDisabledParcelShop'] = true;
        $this->pplDisabledParcelShop = $pplDisabledParcelShop;
        return $this;
    }
    /**
     * 
     *
     * @return string[]|null
     */
    public function getPplDisabledTransport() : ?array
    {
        return $this->pplDisabledTransport;
    }
    /**
     * 
     *
     * @param string[]|null $pplDisabledTransport
     *
     * @return self
     */
    public function setPplDisabledTransport(?array $pplDisabledTransport) : self
    {
        $this->initialized['pplDisabledTransport'] = true;
        $this->pplDisabledTransport = $pplDisabledTransport;
        return $this;
    }
    /**
     * 
     *
     * @return PackageSizeModel[]|null
     */
    public function getPplSizes() : ?array
    {
        return $this->pplSizes;
    }
    /**
     * 
     *
     * @param PackageSizeModel[]|null $pplSizes
     *
     * @return self
     */
    public function setPplSizes(?array $pplSizes) : self
    {
        $this->initialized['pplSizes'] = true;
        $this->pplSizes = $pplSizes;
        return $this;
    }
}