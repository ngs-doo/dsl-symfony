<?php
namespace NGS\Symfony\Security;

use NGS\Patterns\Repository;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use NGS\Client\Exception\NotFoundException;
use NGS\Client\Exception\UnauthorizedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use NGS\Symfony\Security\User;


class UserProvider implements UserProviderInterface
{
    public function __construct($container)
    {
        $this->container = $container;
    }

    protected function getContainer()
    {
        return $this->container;
    }

    // override to add your own custom roles
    public function getRoles(\Security\User $user)
    {
        return array('ROLE_ADMIN');
    }

    public function loadUserByUsername($username)
    {
        try {
            $user = Repository::instance()->find('Security\\User', $username);
        }
        catch(UnauthorizedException $ex) {
            $this->getContainer()->get('messenger')->error('Invalid username or password');
            return new User($username, '', array());
        }
        catch(NotFoundException $e) {
            $this->getContainer()->get('messenger')->error('Invalid username or password');
            return new User($username, '', array());
        }

        if($user) {
            // @todo handle user roles outside
            $roles = $this->getRoles($user);
            return new User($user->Name, $user->Password, $roles, $user);
        }
        else {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'NGS\ModelBundle\Security\User';
    }
}
