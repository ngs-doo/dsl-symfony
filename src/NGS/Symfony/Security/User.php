<?php
namespace NGS\Symfony\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    protected $username;

    protected $password;

    protected $roles;

    protected $user;

    public function __construct($username, $password, array $roles=null, $userObject=null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
        $this->userObject = $userObject;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function eraseCredentials()
    {
        //return $this->password;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getSalt()
    {
        return '';
    }

    public function getObject()
    {
        return $this->userObject;
    }
}
