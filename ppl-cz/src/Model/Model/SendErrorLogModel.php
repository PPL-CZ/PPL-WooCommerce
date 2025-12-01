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
     * @var ErrorLogShipmentSettingModel[]|null
     */
    protected $shipmentsSetting;
    /**
     * 
     *
     * @var ParcelPlacesModel
     */
    protected $globalParcelSetting;
    /**
     * 
     *
     * @var ErrorLogCategorySettingModel[]
     */
    protected $categorySetting;
    /**
     * 
     *
     * @var ErrorLogProductSettingModel[]
     */
    protected $productsSetting;
    /**
     * 
     *
     * @var mixed[]
     */
    protected $orders;
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
     * @return ErrorLogShipmentSettingModel[]|null
     */
    public function getShipmentsSetting() : ?array
    {
        return $this->shipmentsSetting;
    }
    /**
     * 
     *
     * @param ErrorLogShipmentSettingModel[]|null $shipmentsSetting
     *
     * @return self
     */
    public function setShipmentsSetting(?array $shipmentsSetting) : self
    {
        $this->initialized['shipmentsSetting'] = true;
        $this->shipmentsSetting = $shipmentsSetting;
        return $this;
    }
    /**
     * 
     *
     * @return ParcelPlacesModel
     */
    public function getGlobalParcelSetting() : ?ParcelPlacesModel
    {
        return $this->globalParcelSetting;
    }
    /**
     * 
     *
     * @param ParcelPlacesModel $globalParcelSetting
     *
     * @return self
     */
    public function setGlobalParcelSetting(?ParcelPlacesModel $globalParcelSetting) : self
    {
        $this->initialized['globalParcelSetting'] = true;
        $this->globalParcelSetting = $globalParcelSetting;
        return $this;
    }
    /**
     * 
     *
     * @return ErrorLogCategorySettingModel[]
     */
    public function getCategorySetting() : ?array
    {
        return $this->categorySetting;
    }
    /**
     * 
     *
     * @param ErrorLogCategorySettingModel[] $categorySetting
     *
     * @return self
     */
    public function setCategorySetting(array $categorySetting) : self
    {
        $this->initialized['categorySetting'] = true;
        $this->categorySetting = $categorySetting;
        return $this;
    }
    /**
     * 
     *
     * @return ErrorLogProductSettingModel[]
     */
    public function getProductsSetting() : ?array
    {
        return $this->productsSetting;
    }
    /**
     * 
     *
     * @param ErrorLogProductSettingModel[] $productsSetting
     *
     * @return self
     */
    public function setProductsSetting(array $productsSetting) : self
    {
        $this->initialized['productsSetting'] = true;
        $this->productsSetting = $productsSetting;
        return $this;
    }
    /**
     * 
     *
     * @return mixed[]
     */
    public function getOrders() : ?array
    {
        return $this->orders;
    }
    /**
     * 
     *
     * @param mixed[] $orders
     *
     * @return self
     */
    public function setOrders(array $orders) : self
    {
        $this->initialized['orders'] = true;
        $this->orders = $orders;
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