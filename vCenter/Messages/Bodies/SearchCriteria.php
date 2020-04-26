<?php 

namespace Messages\Bodies;

class SearchCriteria
{
    public $conditions = "";
    public $matchAll = "";

    public function __construct($conditions, $matchAll)
    {
        $this->conditions = $conditions;
        $this->matchAll = $matchAll;

        return $this;
    }

    public function getObject()
    {
        return (new \SabreAMF_TypedObject("com.vmware.vise.store.SearchCriteria", $this));
    }
}

 ?>