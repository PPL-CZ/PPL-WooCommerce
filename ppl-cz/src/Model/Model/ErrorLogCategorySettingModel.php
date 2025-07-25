<?php

namespace PPLCZ\Model\Model;

class ErrorLogCategorySettingModel extends \ArrayObject
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
     * @var float
     */
    protected $id;
    /**
     * 
     *
     * @var CategoryModel
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
     * @return CategoryModel
     */
    public function getSetting() : ?CategoryModel
    {
        return $this->setting;
    }
    /**
     * 
     *
     * @param CategoryModel $setting
     *
     * @return self
     */
    public function setSetting(?CategoryModel $setting) : self
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