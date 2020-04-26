<?php 

namespace Messages\Bodies;

class ObjectIdentityConstraint
{
    public $target = null;
    public $targetType = "";  

    public function __construct($target, $targetType="Folder")
    {
        $this->target = $target;
        $this->targetType = $targetType;

        return $this;
    }

    public function getObject()
    {
        return (new \SabreAMF_TypedObject('com.vmware.vise.data.query.ObjectIdentityConstraint',$this));
    }
}

 ?>