<?php

namespace PPLCZ\Model\Model;

class PackageSizeModel extends \ArrayObject
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
    protected $xSize;
    /**
     * 
     *
     * @var float|null
     */
    protected $ySize;
    /**
     * 
     *
     * @var float|null
     */
    protected $zSize;
    /**
     * 
     *
     * @return float|null
     */
    public function getXSize() : ?float
    {
        return $this->xSize;
    }
    /**
     * 
     *
     * @param float|null $xSize
     *
     * @return self
     */
    public function setXSize(?float $xSize) : self
    {
        $this->initialized['xSize'] = true;
        $this->xSize = $xSize;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getYSize() : ?float
    {
        return $this->ySize;
    }
    /**
     * 
     *
     * @param float|null $ySize
     *
     * @return self
     */
    public function setYSize(?float $ySize) : self
    {
        $this->initialized['ySize'] = true;
        $this->ySize = $ySize;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getZSize() : ?float
    {
        return $this->zSize;
    }
    /**
     * 
     *
     * @param float|null $zSize
     *
     * @return self
     */
    public function setZSize(?float $zSize) : self
    {
        $this->initialized['zSize'] = true;
        $this->zSize = $zSize;
        return $this;
    }
}