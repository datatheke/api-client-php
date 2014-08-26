<?php

namespace Datatheke\Api\Event;

use Symfony\Component\EventDispatcher\Event;

class CredentialsEvent extends Event
{
    protected $username;
    protected $password;

    public function setCredentials($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getCredentials()
    {
        return [$this->username, $this->password];
    }
}
