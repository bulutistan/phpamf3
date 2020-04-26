<?php 

namespace Messages\Bodies;

class ResourceSpec
{
    public $constraint = null;
    public $propertySpecs = [];   

    public function __construct($constraint, $propertySpecs=[])
    {
        $this->constraint = $constraint;
        $this->propertySpecs = $propertySpecs;

        return $this;
    }

    public function getObject()
    {
        return (new \SabreAMF_TypedObject('com.vmware.vise.data.ResourceSpec',$this));
    }
}

 ?>