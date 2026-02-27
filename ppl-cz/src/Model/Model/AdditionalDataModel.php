<?php

namespace PPLCZ\Model\Model;

class AdditionalDataModel extends \ArrayObject
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
    protected $posn;
    /**
     * 
     *
     * @return string|null
     */
    public function getPosn() : ?string
    {
        return $this->posn;
    }
    /**
     * 
     *
     * @param string|null $posn
     *
     * @return self
     */
    public function setPosn(?string $posn) : self
    {
        $this->initialized['posn'] = true;
        $this->posn = $posn;
        return $this;
    }
}