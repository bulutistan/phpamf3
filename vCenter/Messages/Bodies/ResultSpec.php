<?php 

namespace Messages\Bodies;

class ResultSpec
{
    public $maxResultCount = -1;
    public $order = null;
    public $facets = null;
    public $offset = NAN;

    public function __construct()
    {
        //$this->offset = (integer) dechex(4);

        return $this;
    }

    public function getObject()
    {
        return (new \SabreAMF_TypedObject('com.vmware.vise.data.query.ResultSpec',$this));
    }
}

 ?>