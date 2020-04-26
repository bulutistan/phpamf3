<?php 

namespace Messages\Bodies;

class SearchCondition
{
    public $key = "";
    public $value = "";
    public $op = "";

    public function __construct($key, $value, $op="=")
    {
        $this->key = $key;
        $this->value = $value;
        $this->op = $op;

        return $this;
    }

    public function getObject()
    {
        return (new \SabreAMF_TypedObject("com.vmware.vise.store.SearchCondition", $this));
    }
}

 ?>