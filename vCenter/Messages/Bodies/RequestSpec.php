<?php 

namespace Messages\Bodies;

class RequestSpec
{
    public $querySpec = [];
    public $enableProfiling = false;

    public function __construct($querySpec, $enableProfiling=false)
    {
        $this->querySpec = $querySpec;
        $this->enableProfiling = $enableProfiling;

        return $this;
    }

    public function getObject()
    {
        return (new \SabreAMF_TypedObject('com.vmware.vise.data.query.RequestSpec',$this));
    }
}
 ?>