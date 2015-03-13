<?php
namespace NGS\Symfony\Collector;

use NGS\Client\HttpClient;
use NGS\Client\HttpRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Listens and records request/response events on HttpClient
 */
class HttpClientCollector extends DataCollector
{
    protected $data = array();

    protected $requestCount = 0;

    protected $timer;

    public function __construct($client = null)
    {
        if ($client)
            $this->bind($client);
        else
            $this->bind(HttpClient::instance());

        $this->timer = new Stopwatch();
    }

    public function bind(HttpClient $client)
    {
        $client->addSubscriber(array($this, 'recordEvent'));
    }

    /**
     * @param $event
     * @param array $data
     */
    public function recordEvent($event, array $data)
    {
        if ($event === HttpClient::EVENT_REQUEST_BEFORE) {
            if (!isset($data['request']) || !$data['request'] instanceof HttpRequest)
                throw new \InvalidArgumentException('Request was not an instance of NGS\Client\HttpRequest');
            $this->data[$this->requestCount] = array(
                'request' => (string)$data['request'],
            );
            $this->timer->start($this->requestCount);
        }
        else if ($event === HttpClient::EVENT_REQUEST_SENT) {
            $timerEvent = $this->timer->stop($this->requestCount);
            $this->data[$this->requestCount]['duration'] = $timerEvent->getDuration();
            $this->data[$this->requestCount]['response'] = array(
                'body' => $data['response']['body'],
                'code' => $data['response']['code'],
            );
            $this->requestCount++;
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public function getDuration()
    {
        $total = 0;
        foreach ($this->data as $request)
            $total += $request['duration'];
        return $total;
    }

    /**
     * @inheritdoc
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        // no need to hook to Symfony http cycle
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'dsl_http';
    }
}