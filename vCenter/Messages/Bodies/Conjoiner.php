<?php 

namespace Messages\Bodies;

class Conjoiner
{
    public $value = "";

    public function __construct($value="OR")
    {
        $this->value = $value;

        return $this;
    }

    public function getObject()
    {
        return (new \SabreAMF_TypedObject('com.vmware.vise.data.query.Conjoiner',$this));
    }
}

 ?>