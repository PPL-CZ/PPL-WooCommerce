<?php

namespace PPLCZ\Model\Model;

class ErrorLogProductSettingModel extends \ArrayObject
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
     * @var float
     */
    protected $id;
    /**
     * 
     *
     * @var float[]
     */
    protected $categoryIds;
    /**
     * 
     *
     * @var string
     */
    protected $name;
    /**
     * 
     *
     * @var float|null
     */
    protected $weight;
    /**
     * 
     *
     * @var ProductModel
     */
    protected $setting;
    /**
     * 
     *
     * @var float|null
     */
    protected $parent;
    /**
     * 
     *
     * @return float
     */
    public function getId() : ?float
    {
        return $this->id;
    }
    /**
     * 
     *
     * @param float $id
     *
     * @return self
     */
    public function setId(float $id) : self
    {
        $this->initialized['id'] = true;
        $this->id = $id;
        return $this;
    }
    /**
     * 
     *
     * @return float[]
     */
    public function getCategoryIds() : ?array
    {
        return $this->categoryIds;
    }
    /**
     * 
     *
     * @param float[] $categoryIds
     *
     * @return self
     */
    public function setCategoryIds(array $categoryIds) : self
    {
        $this->initialized['categoryIds'] = true;
        $this->categoryIds = $categoryIds;
        return $this;
    }
    /**
     * 
     *
     * @return string
     */
    public function getName() : ?string
    {
        return $this->name;
    }
    /**
     * 
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name) : self
    {
        $this->initialized['name'] = true;
        $this->name = $name;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getWeight() : ?float
    {
        return $this->weight;
    }
    /**
     * 
     *
     * @param float|null $weight
     *
     * @return self
     */
    public function setWeight(?float $weight) : self
    {
        $this->initialized['weight'] = true;
        $this->weight = $weight;
        return $this;
    }
    /**
     * 
     *
     * @return ProductModel
     */
    public function getSetting() : ?ProductModel
    {
        return $this->setting;
    }
    /**
     * 
     *
     * @param ProductModel $setting
     *
     * @return self
     */
    public function setSetting(?ProductModel $setting) : self
    {
        $this->initialized['setting'] = true;
        $this->setting = $setting;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getParent() : ?float
    {
        return $this->parent;
    }
    /**
     * 
     *
     * @param float|null $parent
     *
     * @return self
     */
    public function setParent(?float $parent) : self
    {
        $this->initialized['parent'] = true;
        $this->parent = $parent;
        return $this;
    }
}