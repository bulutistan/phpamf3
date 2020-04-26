<?php 

namespace Messages\Bodies;

class CompositeConstraint
{
    public $nestedConstraints = [];
    public $conjoiner = null;
    public $targetType = "";

    public function __construct($nestedConstraints, $conjoiner, $targetType="Folder")
    {
        $this->nestedConstraints = $nestedConstraints;
        $this->conjoiner = $conjoiner;
        $this->targetType = $targetType;

        return $this;
    }

    public function getObject()
    {
        return (new \SabreAMF_TypedObject('com.vmware.vise.data.query.CompositeConstraint',$this));
    }
}

 ?>