<?php
namespace NGS\Symfony\Extension;

use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Injects messages into templates as twig global variable (defaults to '_messages')
 * @todo inject into json response
 */
class Messenger extends \Twig_Extension
{
    private $messages;
    private $globalsName;
    private $session;
    private $sessionName;

    const STATUS_SUCCESS = 'success';
    const STATUS_INFO    = 'info';
    const STATUS_WARNING = 'warning';
    const STATUS_ERROR   = 'error';

    public function __construct($globalsName='_messages', SessionInterface $session=null, $sessionName='ngs.messages')
    {
        $this->globalsName = $globalsName;
        $this->session = $session;
    }

    public function getName()
    {
        return 'messenger_extension';
    }

    public function setSession(SessionInterface $sessionInstance)
    {
        $this->session = $sessionInstance;
        return $this;
    }

    // retrieves messages and unsets them from session
    public function extractMessages()
    {
        $messages = $this->session->get($this->sessionName);
        $this->session->remove($this->sessionName);
        return $messages;
    }

    public function getMessages()
    {
        return $this->session->get($this->sessionName);
    }

    public function getGlobals()
    {
        return array($this->globalsName => $this->extractMessages());
    }

    private function addMessage($content, $status=self::STATUS_INFO)
    {
        if(!is_string($content)) {
            // @todo
        }
        $this->messages[] = array(
            'content' => $content,
            'status'  => $status
        );
        $this->session->set($this->sessionName, $this->messages);
    }

    public function success($message)
    {
        $this->addMessage($message, self::STATUS_SUCCESS);
    }

    public function info($message)
    {
        $this->addMessage($message, self::STATUS_INFO);
    }

    public function warning($message)
    {
        $this->addMessage($message, self::STATUS_WARNING);
    }

    public function error($message)
    {
        $this->addMessage($message, self::STATUS_ERROR);
    }
}
