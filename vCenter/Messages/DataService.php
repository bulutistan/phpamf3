<?php 

namespace Messages;

class DataService
{
    public $source = "";
    public $operation = "";
    public $messageId = "";
    public $destination = "dataService";
    public $timestamp = 0;
    public $timeToLive = 0;
    public $clientId = null;

    public $headers = null;
    public $body = null;

    public function __construct($operation)
    {
        $this->messageId = strtoupper(uuid_create());

        $this->operation = $operation;

        return $this;
    }

    public function makeHeader($dsEndpoint=null, $header_dsId="nil", $webClientId="", $opId="")
    {
        $this->headers = new DataServiceHeader($dsEndpoint, $header_dsId, $webClientId, $opId);

        return $this;
    }

    public function makeBody($bodies)
    {
        $this->body = (new DataServiceBody($bodies))->bodies;

        return $this;
    }

    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getObject()
    {
        return (new \SabreAMF_TypedObject('flex.messaging.messages.RemotingMessage',$this));
    }
}

class DataServiceHeader
{
    public $opId = "";
    public $DSEndpoint = "";
    public $DSId = "nil";
    public $webClientSessionId = "";

    public function __construct($dsEndpoint, $dsId, $webClientId, $opId) {
        $this->opId = $opId;
        $this->DSEndpoint = $dsEndpoint;
        $this->DSId = $dsId;
        $this->webClientSessionId = $webClientId;

        return $this;
    }
}

class DataServiceBody
{
    public $bodies;

    public function __construct($bodies)
    {
        $this->bodies = $bodies;

        return $this;
    }
}




 ?>