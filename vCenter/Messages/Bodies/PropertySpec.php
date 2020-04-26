<?php 

namespace Messages\Bodies;

class PropertySpec
{
    public $type = "";
    public $relation = "";
    public $propertyNames = [];   
    public $parameters = [];   

    public function __construct($type="Folder", $relation="", $propertyNames=["messageOfTheDay", "vCenterName"], $parameters= [])
    {
        $this->type = $type;
        $this->relation = $relation;
        $this->propertyNames = $propertyNames;
        $this->parameters = $parameters;

        return $this;
    }

    public function getObject()
    {
        return (new \SabreAMF_TypedObject('com.vmware.vise.data.PropertySpec',$this));
    }
}

 ?>