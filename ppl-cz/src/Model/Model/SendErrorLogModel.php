<?php

namespace PPLCZ\Model\Model;

class SendErrorLogModel extends \ArrayObject
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
    protected $mail;
    /**
     * 
     *
     * @var string|null
     */
    protected $info;
    /**
     * 
     *
     * @var ErrorLogItemModel[]
     */
    protected $errors;
    /**
     * 
     *
     * @var string|null
     */
    protected $message;
    /**
     * 
     *
     * @return string|null
     */
    public function getMail() : ?string
    {
        return $this->mail;
    }
    /**
     * 
     *
     * @param string|null $mail
     *
     * @return self
     */
    public function setMail(?string $mail) : self
    {
        $this->initialized['mail'] = true;
        $this->mail = $mail;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getInfo() : ?string
    {
        return $this->info;
    }
    /**
     * 
     *
     * @param string|null $info
     *
     * @return self
     */
    public function setInfo(?string $info) : self
    {
        $this->initialized['info'] = true;
        $this->info = $info;
        return $this;
    }
    /**
     * 
     *
     * @return ErrorLogItemModel[]
     */
    public function getErrors() : ?array
    {
        return $this->errors;
    }
    /**
     * 
     *
     * @param ErrorLogItemModel[] $errors
     *
     * @return self
     */
    public function setErrors(array $errors) : self
    {
        $this->initialized['errors'] = true;
        $this->errors = $errors;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getMessage() : ?string
    {
        return $this->message;
    }
    /**
     * 
     *
     * @param string|null $message
     *
     * @return self
     */
    public function setMessage(?string $message) : self
    {
        $this->initialized['message'] = true;
        $this->message = $message;
        return $this;
    }
}