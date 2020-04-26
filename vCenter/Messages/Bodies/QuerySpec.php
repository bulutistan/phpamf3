<?php 

namespace Messages\Bodies;

class QuerySpec
{
    public $resultSpec = null;
    public $options = null;
    public $resourceSpec = null;
    public $name = "";
    
    public function __construct($resultSpec, $options, $resourceSpec, $name)
    {
        $this->resultSpec = $resultSpec;
        $this->options = $options;
        $this->resourceSpec = $resourceSpec;
        $this->name = $name;

        return $this;
    }


    public function getObject()
    {
        return (new \SabreAMF_TypedObject('com.vmware.vise.data.query.QuerySpec',$this));
    }
}

 ?>