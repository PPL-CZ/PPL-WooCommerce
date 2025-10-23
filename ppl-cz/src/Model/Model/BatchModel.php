<?php

namespace PPLCZ\Model\Model;

class BatchModel extends \ArrayObject
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
     * @var string|null
     */
    protected $remoteBatchId;
    /**
     * 
     *
     * @var string|null
     */
    protected $name;
    /**
     * 
     *
     * @var string
     */
    protected $created;
    /**
     * 
     *
     * @var bool
     */
    protected $lock;
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
     * @return string|null
     */
    public function getRemoteBatchId() : ?string
    {
        return $this->remoteBatchId;
    }
    /**
     * 
     *
     * @param string|null $remoteBatchId
     *
     * @return self
     */
    public function setRemoteBatchId(?string $remoteBatchId) : self
    {
        $this->initialized['remoteBatchId'] = true;
        $this->remoteBatchId = $remoteBatchId;
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
     * @return string
     */
    public function getCreated() : ?string
    {
        return $this->created;
    }
    /**
     * 
     *
     * @param string $created
     *
     * @return self
     */
    public function setCreated(string $created) : self
    {
        $this->initialized['created'] = true;
        $this->created = $created;
        return $this;
    }
    /**
     * 
     *
     * @return bool
     */
    public function getLock() : ?bool
    {
        return $this->lock;
    }
    /**
     * 
     *
     * @param bool $lock
     *
     * @return self
     */
    public function setLock(bool $lock) : self
    {
        $this->initialized['lock'] = true;
        $this->lock = $lock;
        return $this;
    }
}