<?php 

namespace Messages\Bodies;

class QuerySpecOptions
{
    public $REDUCE_QUERIES = false;

    public function __construct($reduceQueries=true)
    {
        $this->REDUCE_QUERIES = $reduceQueries;

        return $this;
    }
}

 ?>