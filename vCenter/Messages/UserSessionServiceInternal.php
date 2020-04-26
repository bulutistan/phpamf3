<?php 

namespace Messages;

use SabreAMF\TypedObject;

class UserSessionServiceInternal
{
    public $operation = "";
    public $source = null;
    public $messageId = "";
    public $destination = "userSessionServiceInternal";
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

    public function makeHeader($dsEndpoint=null, $header_dsId="nil")
    {
        $this->headers = new UserSessionServiceInternalHeader($dsEndpoint, $header_dsId);

        return $this;
    }

    public function makeBody($body)
    {
        $this->body = (new UserSessionServiceInternalBody($body))->bodies;

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

class UserSessionServiceInternalHeader
{
    public $DSEndpoint = "";
    public $DSId = "nil";

    public function __construct($dsEndpoint, $dsId) {
        $this->DSEndpoint = $dsEndpoint;
        $this->DSId = $dsId;

        return $this;
    }
}

class UserSessionServiceInternalBody
{
    public $bodies;

    public function __construct($bodies)
    {
        $this->bodies = $bodies;

        return $this;
    }
}





 ?>