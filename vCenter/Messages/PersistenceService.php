<?php 

namespace Messages;

use SabreAMF\TypedObject;

class PersistenceService
{
    public $operation = "";
    public $source = null;
    public $messageId = "";
    public $destination = "persistenceService";
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

    public function makeHeader($dsEndpoint=null, $header_dsId="nil", $webClientId="")
    {
        $this->headers = new PersistenceServiceHeader($dsEndpoint, $header_dsId, $webClientId);

        return $this;
    }

    public function makeBody($body)
    {
        $this->body = (new PersistenceServiceBody($body))->bodies;

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

class PersistenceServiceHeader
{
    public $DSEndpoint = "";
    public $DSId = "nil";
    public $webClientSessionId = "";

    public function __construct($dsEndpoint, $dsId, $webClientId) {
        $this->DSEndpoint = $dsEndpoint;
        $this->DSId = $dsId;
        $this->webClientSessionId = $webClientId;

        return $this;
    }
}

class PersistenceServiceBody
{
    public $bodies;

    public function __construct($bodies)
    {
        $this->bodies = $bodies;

        return $this;
    }
}


 ?>