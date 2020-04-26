<?php 

namespace Messages\Bodies;

class ManagedObjectReference
{
    public $uid = "";
    public $version = "";
    public $value = "";
    public $type = "";
    public $serverGuid = "";

    public function __construct($uid, $type, $value, $serverGuid, $version="6.5.0")
    {
        $this->uid = $uid;
        $this->type = $type;
        $this->value = $value;
        $this->serverGuid = $serverGuid;
        $this->version = $version;

        return $this;
    }

    public function getObject()
    {
        return (new \SabreAMF_TypedObject('com.vmware.vim.binding.vmodl.ManagedObjectReference',$this));
    }
}

 ?>