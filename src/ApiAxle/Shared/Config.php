<?php
/**
 * ApiAxle (https://github.com/fillup/apiaxle-module/)
 *
 * @link      https://github.com/fillup/apiaxle-module for the canonical source repository
 * @license   GPLv2+
 */
namespace ApiAxle\Shared;

/**
 * ApiAxle\Config class encapsulates common configuration settings for calling
 * ApiAxle apis
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
class Config
{
    /**
     * ApiAxle endpoing url
     * 
     * @var string endpoint
     */
    protected $endpoint;
    
    /**
     * ApiAxle API key
     * 
     * @var string key
     */
    protected $key;
    
    /**
     * ApiAxle API shared secret for signing requests
     * 
     * @var string secret
     */
    protected $secret;
    
    /**
     * Initialization status
     */
    private $isInitialized = false;
    
    /**
     * Construct config object
     * 
     * If config is provided, parse it and setup object
     * 
     * @param array $config
     */
    public function __construct($config = array())
    {
        if($config && is_array($config) && count($config) > 0){
            $this->setConfg($config);
        } else {
            $this->loadConfigFile();
        }
        
        if(!is_null($this->endpoint) && !is_null($this->key) && !is_null($this->secret)){
            $this->isInitialized = true;
        }
    }
    
    /**
     * Setup configuration
     * 
     * @param array $config
     */
    public function setConfg($config)
    {
        $this->endpoint = isset($config['endpoint']) ? $config['endpoint'] : false;
        $this->key = isset($config['key']) ? $config['key'] : false;
        $this->secret = isset($config['secret']) ? $config['secret'] : false;
    }
    
    /**
     * Return configuration settings as array
     * 
     * @return array
     */
    public function getConfig()
    {
        return array(
            'endpoint' => $this->endpoint,
            'key' => $this->key,
            'secret' => $this->secret
        );
    }
    
    /**
     * Load configuration from default config file unless alternate file is specified
     * 
     * @param string $file
     * @return boolean
     * @throws Exception
     */
    public function loadConfigFile($file=false)
    {
        $file = $file ?: __DIR__.'/../../config/config.local.php';
        if(file_exists($file)){
            $config = require_once $file;
            if(is_array($config) && count($config) > 0){
                $this->setConfg($config);
                return true;
            }
        }
        throw new Exception('Unable to load configuration from file '.$file, '100');
    }
}