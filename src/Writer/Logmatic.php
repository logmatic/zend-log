<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log\Writer;

use Zend\Log\Formatter\Json;
use Zend\Log\Writer\Logmatic\LogmaticClient;
use Zend\Log\Writer\Logmatic\LogmaticClientConfiguration;

/**
 * Logmatic log writer.
 */
class Logmatic extends AbstractWriter
{


    private $token;
    protected $formatter;
    private $clientConfig;
    private $client;

    /**
     *
     * /**
     * Constructor
     */
    public function __construct($token, $clientConfiguration = null)
    {

        $this->token = $token;
        $this->clientConfig = ($clientConfiguration != null) ? $clientConfiguration : new LogmaticClientConfiguration();
        $this->formatter = new Json();
        $this->client = null;
    }


    /**
     * This writer does not support formatting.
     *
     * @param string|FormatterInterface $formatter
     * @return WriterInterface
     */
    public function setFormatter($formatter)
    {
        return $this->formatter = $formatter;
    }

    /**
     * Write a message to the log.
     *
     * @param array $event Event data
     * @return void
     *
     */
    protected function doWrite(array $event)
    {


        if ($this->client == null) {
            // connect or reconnect to the endpoint
            $this->client = new LogmaticClient($this->clientConfig);
        }


        // format the event
        $payload = sprintf("%s %s\n", $this->token, $this->formatter->format($event));

        // send the event
        $this->client->writeAndRetry($payload);

    }

    public function shutdown()
    {
        if ($this->client) {
            $this->client->close();
        }
    }
}
