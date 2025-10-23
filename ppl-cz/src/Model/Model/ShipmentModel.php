<?php

namespace PPLCZ\Model\Model;

class ShipmentModel extends \ArrayObject
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
     * @var PackageModel[]|null
     */
    protected $packages;
    /**
     * 
     *
     * @var string|null
     */
    protected $printState;
    /**
     * 
     *
     * @var string|null
     */
    protected $importState;
    /**
     * 
     *
     * @var string|null
     */
    protected $referenceId;
    /**
     * 
     *
     * @var string|null
     */
    protected $note;
    /**
     * 
     *
     * @var float|null
     */
    protected $codValue;
    /**
     * 
     *
     * @var string|null
     */
    protected $codValueCurrency;
    /**
     * 
     *
     * @var string|null
     */
    protected $codVariableNumber;
    /**
     * 
     *
     * @var string|null
     */
    protected $serviceCode;
    /**
     * 
     *
     * @var string|null
     */
    protected $serviceName;
    /**
     * 
     *
     * @var string|null
     */
    protected $batchId;
    /**
     * 
     *
     * @var string|null
     */
    protected $batchRemoteId;
    /**
     * 
     *
     * @var bool|null
     */
    protected $hasParcel;
    /**
     * 
     *
     * @var float|null
     */
    protected $orderId;
    /**
     * 
     *
     * @var ParcelAddressModel
     */
    protected $parcel;
    /**
     * 
     *
     * @var RecipientAddressModel
     */
    protected $recipient;
    /**
     * 
     *
     * @var SenderAddressModel
     */
    protected $sender;
    /**
     * 
     *
     * @var string|null
     */
    protected $age;
    /**
     * 
     *
     * @var bool|null
     */
    protected $lock;
    /**
     * 
     *
     * @var string[]|null
     */
    protected $importErrors;
    /**
     * 
     *
     * @var string|null
     */
    protected $phase;
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
     * @return PackageModel[]|null
     */
    public function getPackages() : ?array
    {
        return $this->packages;
    }
    /**
     * 
     *
     * @param PackageModel[]|null $packages
     *
     * @return self
     */
    public function setPackages(?array $packages) : self
    {
        $this->initialized['packages'] = true;
        $this->packages = $packages;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getPrintState() : ?string
    {
        return $this->printState;
    }
    /**
     * 
     *
     * @param string|null $printState
     *
     * @return self
     */
    public function setPrintState(?string $printState) : self
    {
        $this->initialized['printState'] = true;
        $this->printState = $printState;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getImportState() : ?string
    {
        return $this->importState;
    }
    /**
     * 
     *
     * @param string|null $importState
     *
     * @return self
     */
    public function setImportState(?string $importState) : self
    {
        $this->initialized['importState'] = true;
        $this->importState = $importState;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getReferenceId() : ?string
    {
        return $this->referenceId;
    }
    /**
     * 
     *
     * @param string|null $referenceId
     *
     * @return self
     */
    public function setReferenceId(?string $referenceId) : self
    {
        $this->initialized['referenceId'] = true;
        $this->referenceId = $referenceId;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getNote() : ?string
    {
        return $this->note;
    }
    /**
     * 
     *
     * @param string|null $note
     *
     * @return self
     */
    public function setNote(?string $note) : self
    {
        $this->initialized['note'] = true;
        $this->note = $note;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getCodValue() : ?float
    {
        return $this->codValue;
    }
    /**
     * 
     *
     * @param float|null $codValue
     *
     * @return self
     */
    public function setCodValue(?float $codValue) : self
    {
        $this->initialized['codValue'] = true;
        $this->codValue = $codValue;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getCodValueCurrency() : ?string
    {
        return $this->codValueCurrency;
    }
    /**
     * 
     *
     * @param string|null $codValueCurrency
     *
     * @return self
     */
    public function setCodValueCurrency(?string $codValueCurrency) : self
    {
        $this->initialized['codValueCurrency'] = true;
        $this->codValueCurrency = $codValueCurrency;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getCodVariableNumber() : ?string
    {
        return $this->codVariableNumber;
    }
    /**
     * 
     *
     * @param string|null $codVariableNumber
     *
     * @return self
     */
    public function setCodVariableNumber(?string $codVariableNumber) : self
    {
        $this->initialized['codVariableNumber'] = true;
        $this->codVariableNumber = $codVariableNumber;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getServiceCode() : ?string
    {
        return $this->serviceCode;
    }
    /**
     * 
     *
     * @param string|null $serviceCode
     *
     * @return self
     */
    public function setServiceCode(?string $serviceCode) : self
    {
        $this->initialized['serviceCode'] = true;
        $this->serviceCode = $serviceCode;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getServiceName() : ?string
    {
        return $this->serviceName;
    }
    /**
     * 
     *
     * @param string|null $serviceName
     *
     * @return self
     */
    public function setServiceName(?string $serviceName) : self
    {
        $this->initialized['serviceName'] = true;
        $this->serviceName = $serviceName;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getBatchId() : ?string
    {
        return $this->batchId;
    }
    /**
     * 
     *
     * @param string|null $batchId
     *
     * @return self
     */
    public function setBatchId(?string $batchId) : self
    {
        $this->initialized['batchId'] = true;
        $this->batchId = $batchId;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getBatchRemoteId() : ?string
    {
        return $this->batchRemoteId;
    }
    /**
     * 
     *
     * @param string|null $batchRemoteId
     *
     * @return self
     */
    public function setBatchRemoteId(?string $batchRemoteId) : self
    {
        $this->initialized['batchRemoteId'] = true;
        $this->batchRemoteId = $batchRemoteId;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getHasParcel() : ?bool
    {
        return $this->hasParcel;
    }
    /**
     * 
     *
     * @param bool|null $hasParcel
     *
     * @return self
     */
    public function setHasParcel(?bool $hasParcel) : self
    {
        $this->initialized['hasParcel'] = true;
        $this->hasParcel = $hasParcel;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getOrderId() : ?float
    {
        return $this->orderId;
    }
    /**
     * 
     *
     * @param float|null $orderId
     *
     * @return self
     */
    public function setOrderId(?float $orderId) : self
    {
        $this->initialized['orderId'] = true;
        $this->orderId = $orderId;
        return $this;
    }
    /**
     * 
     *
     * @return ParcelAddressModel
     */
    public function getParcel() : ?ParcelAddressModel
    {
        return $this->parcel;
    }
    /**
     * 
     *
     * @param ParcelAddressModel $parcel
     *
     * @return self
     */
    public function setParcel(?ParcelAddressModel $parcel) : self
    {
        $this->initialized['parcel'] = true;
        $this->parcel = $parcel;
        return $this;
    }
    /**
     * 
     *
     * @return RecipientAddressModel
     */
    public function getRecipient() : ?RecipientAddressModel
    {
        return $this->recipient;
    }
    /**
     * 
     *
     * @param RecipientAddressModel $recipient
     *
     * @return self
     */
    public function setRecipient(?RecipientAddressModel $recipient) : self
    {
        $this->initialized['recipient'] = true;
        $this->recipient = $recipient;
        return $this;
    }
    /**
     * 
     *
     * @return SenderAddressModel
     */
    public function getSender() : ?SenderAddressModel
    {
        return $this->sender;
    }
    /**
     * 
     *
     * @param SenderAddressModel $sender
     *
     * @return self
     */
    public function setSender(?SenderAddressModel $sender) : self
    {
        $this->initialized['sender'] = true;
        $this->sender = $sender;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getAge() : ?string
    {
        return $this->age;
    }
    /**
     * 
     *
     * @param string|null $age
     *
     * @return self
     */
    public function setAge(?string $age) : self
    {
        $this->initialized['age'] = true;
        $this->age = $age;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getLock() : ?bool
    {
        return $this->lock;
    }
    /**
     * 
     *
     * @param bool|null $lock
     *
     * @return self
     */
    public function setLock(?bool $lock) : self
    {
        $this->initialized['lock'] = true;
        $this->lock = $lock;
        return $this;
    }
    /**
     * 
     *
     * @return string[]|null
     */
    public function getImportErrors() : ?array
    {
        return $this->importErrors;
    }
    /**
     * 
     *
     * @param string[]|null $importErrors
     *
     * @return self
     */
    public function setImportErrors(?array $importErrors) : self
    {
        $this->initialized['importErrors'] = true;
        $this->importErrors = $importErrors;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getPhase() : ?string
    {
        return $this->phase;
    }
    /**
     * 
     *
     * @param string|null $phase
     *
     * @return self
     */
    public function setPhase(?string $phase) : self
    {
        $this->initialized['phase'] = true;
        $this->phase = $phase;
        return $this;
    }
}