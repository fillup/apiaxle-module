<?php
/**
 * ApiAxle (https://github.com/fillup/apiaxle-module/)
 *
 * @link      https://github.com/fillup/apiaxle-module for the canonical source repository
 * @license   MIT
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
     * Enable/Disable verification of peer certificate when calling API
     * 
     * @var bool $ssl_verifypeer
     */
    protected $ssl_verifypeer = true;
    
    /**
     * Additional config for cURL providing alternative CA certificate info.
     * The name of a file holding one or more certificates to verify the peer with.
     * Requires absolute path.
     * Use this option alongside CURLOPT_SSL_VERIFYPEER.
     * 
     * @link http://us2.php.net/manual/en/function.curl-setopt.php cURL configuration options
     * @var string $ssl_cainfo
     */
    protected $ssl_cainfo = null;
    
    /**
     * Additional config for cURL providing alternative CA certificate path.
     * A directory that holds multiple CA certificates.
     * Use this option alongside CURLOPT_SSL_VERIFYPEER.
     * 
     * @link http://us2.php.net/manual/en/function.curl-setopt.php cURL configuration options
     * @var string $ssl_capath
     */
    protected $ssl_capath = null;
            
    /**
     * Enable/disable usage of a proxy server
     * 
     * @var bool $proxy_enable
     */
    protected $proxy_enable = false;
    
    /**
     * Set proxy server hostname
     * 
     * @var string $proxy_host
     */
    protected $proxy_host;
    
    /**
     * Set proxy server port
     * 
     * @var string $proxy_port
     */
    protected $proxy_port;
    
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
        } elseif($config instanceof \ApiAxle\Shared\Config){
            $this->setEndpoint($config->getEndpoint());
            $this->setKey($config->getKey());
            $this->setSecret($config->getSecret());
            $this->setSslVerifypeer($config->getSslVerifypeer());
            $this->setSslCainfo($config->getSslCainfo());
            $this->setSslCapath($config->getSslCapath());
            $this->setProxyEnable($config->getProxyEnable());
            $this->setProxyHost($config->getProxyHost());
            $this->setProxyPort($config->getProxyPort());
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
        $this->setSslVerifypeer(isset($config['ssl_verifypeer']) ? $config['ssl_verifypeer'] : true);
        $this->setSslCainfo(isset($config['ssl_cainfo']) ? $config['ssl_cainfo'] : null);
        $this->setSslCapath(isset($config['ssl_capath']) ? $config['ssl_capath'] : null);
        $this->setProxyEnable(isset($config['proxy_enable']) ? $config['proxy_enable'] : false);
        $this->setProxyHost(isset($config['proxy_host']) ? $config['proxy_host'] : null);
        $this->setProxyPort(isset($config['proxy_port']) ? $config['proxy_port'] : null);
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
    
    public function getSslVerifypeer()
    {
        return $this->ssl_verifypeer;
    }
    
    public function setSslVerifypeer($ssl_verifypeer)
    {
        $this->ssl_verifypeer = $ssl_verifypeer;
    }
    
    public function getSslCainfo()
    {
        return $this->ssl_cainfo;
    }
    
    public function setSslCainfo($ssl_cainfo)
    {
        $this->ssl_cainfo = $ssl_cainfo;
    }
    
    public function getSslCapath()
    {
        return $this->ssl_capath;
    }
    
    public function setSslCapath($ssl_capath)
    {
        $this->ssl_capath = $ssl_capath;
    }
    
    public function getProxyEnable()
    {
        return $this->proxy_enable;
    }
    
    public function setProxyEnable($proxy_enable)
    {
        $this->proxy_enable = $proxy_enable;
    }
    
    public function getProxyHost()
    {
        return $this->proxy_host;
    }
    
    public function setProxyHost($proxy_host)
    {
        $this->proxy_host = $proxy_host;
    }
    
    public function getProxyPort()
    {
        return $this->proxy_port;
    }
    
    public function setProxyPort($proxy_port)
    {
        $this->proxy_port = $proxy_port;
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