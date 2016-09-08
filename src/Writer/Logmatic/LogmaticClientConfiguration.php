<?php
/**
 * Created by IntelliJ IDEA.
 * User: gpolaert
 * Date: 9/8/16
 * Time: 9:34 AM
 */

namespace Zend\Log\Writer\Logmatic;


class LogmaticClientConfiguration
{

    /**
     * Is Zend Monitor enabled?
     *
     * @var bool
     */
    const DEFAULT_LOGMATIC_IP = "api.logmatic.io";
    /**
     * Is Zend Monitor enabled?
     *
     * @var bool
     */
    const DEFAULT_LOGMATIC_PORT = 10514;

    /**
     * Is Zend Monitor enabled?
     *
     * @var bool
     */
    const DEFAULT_LOGMATIC_SSL_PORT = 10515;


    public $Ip;

    public $Port;

    public $UseSSL;

    public $MaxRetries = 10;
    public $MaxBackoff = 30;


    public function __construct($ip = null, $port = 0, $useSSL = true)
    {
        // setting-up defaults
        $this->Ip = ($ip == null) ? self::DEFAULT_LOGMATIC_IP : $ip;
        $this->UseSSL = $useSSL;
        $this->Port = $port;
        if ($this->Port === 0) {
            $this->Port = ($useSSL == true) ? self::DEFAULT_LOGMATIC_SSL_PORT : self::DEFAULT_LOGMATIC_PORT;
        }
    }

}