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
        $this->setEndpoint(isset($config['endpoint']) ? $config['endpoint'] : false);
        $this->setKey(isset($config['key']) ? $config['key'] : false);
        $this->setSecret(isset($config['secret']) ? $config['secret'] : false);
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
        $file = $file ?: __DIR__.'/../../../config/config.local.php';
        if(file_exists($file)){
            $config = include $file;
            if(is_array($config) && count($config) > 0){
                $this->setConfg($config);
                return true;
            }
        }
        throw new \Exception('Unable to load configuration from file '.$file, '100');
    }
    
    public function getEndpoint()
    {
        return $this->endpoint;
    }
    
    public function setEndpoint($endpoint)
    {
        $url = filter_var($endpoint, FILTER_VALIDATE_URL);
        if($url){
            if(preg_match('/^http[s]{0,1}:\/\//', $endpoint)){
                $this->endpoint = $endpoint;
            } else {
                throw new \Exception('Endpoint must start with http:// or https://',101);
            }
        } else {
            throw new \Exception('Invalid URL specified for Endpoint.',102);
        }
        
        return $this;
    }
    
    public function getKey()
    {
        return $this->key;
    }
    
    public function setKey($key)
    {
        $this->key = $key;
    }
    
    public function getSecret()
    {
        return $this->secret;
    }
    
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }
    
    /**
     * Generate an API signature based on key and shared secret. 
     * 
     * Uses hmac_sha1 to generate hash. There is a six second window where the
     * hash is valid and ApiAxle will check the hash for three seconds before
     * and after the call to validate it. Be sure you are using a network time
     * server to keep your servers in sync with correct time
     * 
     * Returns false if a sharedSecret is not set for the key
     * 
     * @return boolean
     * @return string
     */
    public function getSignature()
    {
        if(!is_null($this->secret)){
            $api_sig = hash_hmac('sha1', time().$this->getKey(), $this->getSecret());
            return $api_sig;
        } else {
            return false;
        }
    }
    
}