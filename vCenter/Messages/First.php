<?php 

namespace Messages;

class First
{
    public $operation = 5;
    public $correlationId = "";
    public $messageId = "";
    public $destination = "";
    public $timestamp = 0;
    public $timeToLive = 0;
    public $clientId = null;

    public $headers = null;
    public $body = null;

    public function __construct()
    {
    	$this->messageId = strtoupper(uuid_create());

        return $this;
    }

    public function makeHeader($header_dsId="nil")
    {
        $this->headers = new FirstHeader($header_dsId);

        return $this;
    }

    public function makeBody()
    {
        $this->body = new FirstBody();

        return $this;
    }

    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getObject()
    {
        return (new \SabreAMF_TypedObject('flex.messaging.messages.CommandMessage',$this));
    }
}

class FirstHeader
{
    public $DSMessagingVersion = 1;
    public $DSId = "nil";

    public function __construct($dsId) {
        $this->DSId = $dsId;

        return $this;
    }
}

class FirstBody
{
    
}





 ?>