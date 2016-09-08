<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zend-log for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log\Writer\Logmatic;


use Zend\Log\Exception;

class LogmaticClient
{

    private $clientConfig;
    private $socket;


    public function __construct($clientConfig)
    {
        $this->clientConfig = $clientConfig;

    }

    // connect to the specify endpoint
    private function connect()
    {

        if ($this->clientConfig->UseSSL) {
            $this->socket = stream_socket_client("ssl://" . $this->clientConfig->Ip . ":" . $this->clientConfig->Port, $errno, $errstr, 10);
        } else {
            $this->socket = fsockopen($this->clientConfig->Ip, $this->clientConfig->Port, $errno, $errstr, 10);
        }

        if (!$this->socket) {
            throw new Exception\RuntimeException(sprintf("Connection to %s:%s failed: %s - %s",
                $this->clientConfig->Ip,
                $this->clientConfig->Port,
                $errno,
                $errstr));
        }

    }


    public function writeAndRetry($payload)
    {

        for ($i = 0; $i < $this->clientConfig->MaxRetries; $i++) {

            if (is_resource($this->socket) == false) {
                try {
                    $this->connect();
                } catch (Exception $e) {
                    //Exception while connecting client: {0}", e
                    continue;
                }
            }

            try {
                fwrite($this->socket, utf8_encode($payload));
                return;
            } catch (Exception $e) {
                //"Retry to send log event: {0}", e
            }

        }
        //Exception while sending log event, event dropped

    }


    public function flush()
    {
        if (is_resource($this->socket)) {

            fflush($this->socket);
        }
    }

    public function close()
    {
        if (is_resource($this->socket)) {
            $this->flush();
            fclose($this->socket);
            $this->socket = null;
        }
    }

}
