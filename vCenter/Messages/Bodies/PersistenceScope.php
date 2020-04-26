<?php 

namespace Messages\Bodies;

class PersistenceScope
{
    public $value;

    public function __construct($value="DEFAULT")
    {
        $this->value = $value;

        return $this;
    }

    public function getObject()
    {
        return (new \SabreAMF_TypedObject('com.vmware.vise.store.PersistenceScope',$this));
    }
}

 ?>