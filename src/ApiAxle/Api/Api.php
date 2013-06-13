<?php
/**
 * ApiAxle (https://github.com/fillup/apiaxle-module/)
 *
 * @link      https://github.com/fillup/apiaxle-module for the canonical source repository
 * @license   GPLv2+
 */
namespace ApiAxle\Api;

use ApiAxle\Shared\Config;
use ApiAxle\Shared\ItemList;
use ApiAxle\Shared\HttpRequest;
use ApiAxle\Shared\Utilities;

class Api
{
    /**
     * Configuration data
     * 
     * @var ApiAxle\Shared\Config
     */
    protected $config;
    
    /**
     * API Name
     * 
     * @var string
     */
    protected $name;
    
    /**
     * Optional
     * 
     * @var timestamp 
     */
    protected $createdAt;
    
    /**
     * Optional
     * 
     * @var timestamp 
     */
    protected $updatedAt;
    
    /**
     * (default: 0) The time in seconds that every call under this API should be cached.
     * 
     * @var integer 
     */
    protected $globalCache = 0;
    
    /**
     * The endpoint for the API. For example; `graph.facebook.com`
     * 
     * @var string 
     */
    protected $endPoint;
    
    /**
     * (default: http) The protocol for the API, whether or not to use SSL
     * 
     * @var string 
     */
    protected $protocol = 'http';
    
    /**
     * (default: json) The resulting data type of the endpoint. This is 
     * redundant at the moment but will eventually support both XML too.
     * 
     * @var string 
     */
    protected $apiFormat = 'json';
    
    /**
     * (default: 2) Seconds to wait before timing out the connection
     * 
     * @var integer 
     */
    protected $endPointTimeout = '2';
    
    /**
     * (default: 2) Max redirects that are allowed when endpoint called.
     * 
     * @var integer
     */
    protected $endPointMaxRedirects = '2';
    
    /**
     * (optional) Regular expression used to extract API key from url. Axle will 
     * use the **first** matched grouping and then apply that as the key. Using 
     * the `api_key` or `apiaxle_key` will take precedence.
     * 
     * @var string
     */
    protected $extractKeyRegex;
    
    /**
     * (optional) An optional path part that will always be called when the 
     * API is hit.
     * 
     * @var string
     */
    protected $defaultPath;
    
    /**
     * (default: false) Disable this API causing errors when it's hit
     * 
     * @var boolean
     */
    protected $disabled = 'false';
    
    /**
     * (default: true) Set to true to require that SSL certificates be valid
     * 
     * @var boolean 
     */
    protected $strictSSL = 'true';
    
    public function __construct($config=false,$name=false) 
    {
        $this->config = new Config($config);
        if($name){
            $this->get($name);
        }
    }
    
    /**
     * Set object properties
     * 
     * @param type $data
     * @return \ApiAxle\Api\Api
     */
    public function setData($data)
    {
        if(is_array($data)){
            $data = json_decode(json_encode($data));
        }
        
        /**
         * @todo Refactor to use setters for validation?
         */
        $this->createdAt = isset($data->createdAt) ? $data->createdAt : null;
        $this->updatedAt = isset($data->updatedAt) ? $data->updatedAt : null;
        $this->globalCache = isset($data->globalCache) ? $data->globalCache : null;
        $this->endPoint = isset($data->endPoint) ? $data->endPoint : null;
        $this->protocol = isset($data->protocol) ? $data->protocol : null;
        $this->apiFormat = isset($data->apiFormat) ? $data->apiFormat : null;
        $this->endPointTimeout = isset($data->endPointTimeout) ? $data->endPointTimeout : null;
        $this->endPointMaxRedirects = isset($data->endPointMaxRedirects) ? $data->endPointMaxRedirects : null;
        $this->extractKeyRegex = isset($data->extractKeyRegex) ? $data->extractKeyRegex : null;
        $this->defaultPath = isset($data->defaultPath) ? $data->defaultPath : null;
        $this->disabled = isset($data->disabled) ? $data->disabled : false;
        $this->strictSSL = isset($data->strictSSL) ? $data->strictSSL : true;
        
        return $this;
    }
    
    /**
     * Get API settings as array
     * 
     * @return array
     */
    public function getData()
    {
        $data = array(
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'globalCache' => $this->globalCache,
            'endPoint' => $this->endPoint,
            'protocol' => $this->protocol,
            'apiFormat' => $this->apiFormat,
            'endPointTimeout' => $this->endPointTimeout,
            'endPointMaxRedirects' => $this->endPointMaxRedirects,
            'extractKeyRegex' => $this->extractKeyRegex,
            'defaultPath' => $this->defaultPath,
            'disabled' => $this->disabled,
            'strictSSL' => $this->strictSSL
        );
        
        return $data;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Call API to retrieve a list of available APIs
     * 
     * @return \ApiAxle\Shared\ItemList
     */
    public function getList($from=0, $to=100, $resolve='true')
    {
        $apiPath = 'apis';
        $params = array(
            'from' => $from,
            'to' => $to,
            'resolve' => $resolve
        );
        
        $apiList = new ItemList();
        $request = Utilities::callApi($apiPath, 'GET', $params, $this->getConfig());
        if($request){
            foreach($request as $name => $data){
                $api = new Api();
                $api->setName($name);
                $api->setData($data);
                $apiList->addItem($api);
            }
        }
        
        return $apiList;
    }
    
    public function get($name)
    {
        if($name){
            $apiPath = 'api/'.$name;
            $request = Utilities::callApi($apiPath,'GET',null,$this->getConfig());
            if($request){
                $this->setName($name);
                $this->setData($request);
            }
        }
        
        return $this;
    }
    
    /**
     * Update the current API
     * 
     * @param array $data
     * @throws \ErrorException 201 - Missing API name, cant make update call
     */
    public function update($data)
    {
        if(!is_null($this->getName())){
            $apiPath = 'api/'.$this->getName();
            $request = Utilities::callApi($apiPath,'PUT',$data,$this->getConfig());
            if($request){
                $this->get($this->getName());
                return $this;
            } else {
                throw new \ErrorException('Unable to update API',202);
            }
        } else {
            throw new \ErrorException('An API name is required to update.',201);
        }
        
    }
    
    /**
     * Register a new API
     * 
     * @param string $name
     * @param array $data
     * @return \ApiAxle\Api\Api
     * @throws \ErrorException
     */
    public function create($name, $data)
    {
        $this->setName($name);
        $this->setData($data);
        if($this->isValid()){
            $apiPath = 'api/'.$this->getName();
            $request = Utilities::callApi($apiPath, 'POST', $data,$this->getConfig());
            if($request){
                $this->get($name);
                return $this;
            } else {
                throw new \ErrorException('Unable to create API',203);
            }
        }
    }
    
    public function delete($name=false)
    {
        if($name){
            $this->setName($name);
        }
        if(is_null($this->getName())){
            throw new \Exception('An API name is required to delete.',205);
        } else {
            $apiPath = 'api/'.$this->getName();
            $request = Utilities::callApi($apiPath, 'DELETE', null, $this->getConfig());
            if($request){
                return true;
            } else {
                throw new \ErrorException('Unable to delete API.', 206);
            }
        }
        
    }
    
    /**
     * Get the most used keys for this api
     * 
     * @todo Create new class to represent keycharts data
     * @param int $timestart
     * @param int $timeend
     * @param string $granularity Valid options: second, minute, hour, day
     * @param string $format_timeseries
     * @param string $format_timestamp Valid options: epoch_seconds, epoch_milliseconds, ISO
     * @return stdClass
     * @throws \Exception
     * @throws \ErrorException
     */
    public function getKeyCharts($timestart=false, $timeend=false, 
            $granularity='day',$format_timeseries='true',
            $format_timestamp='epoch_seconds')
    {
        if(is_null($this->getName())){
            throw new \Exception('An API name is required to get keycharts.',208);
        } else {
            
            $data = array(
                'granularity' => $granularity,
                'format_timeseries' => $format_timeseries,
                'format_timestamp' => $format_timestamp,
            );
            if($timestart){
                $data['from'] = $timestart;
            }
            if($timeend){
                $data['to'] = $timeend;
            }
            
            $apiPath = 'api/'.$this->getName().'/keycharts';
            $request = Utilities::callApi($apiPath, 'GET', $data, $this->getConfig());
            if($request){
                return $request;
            } else {
                throw new \ErrorException('Unable to retrieve keycharts for API.', 207);
            }
        }
    }
    
    public function getKeyList($from=0, $to=10, $resolve='false')
    {
        if(is_null($this->getName())){
            throw new \Exception('An API name is required to get keylist.',209);
        } else {
            $apiPath = 'api/'.$this->getName().'/keys';
            $data = array(
                'from' => $from,
                'to' => $to,
                'resolve' => $resolve
            );
            $request = Utilities::callApi($apiPath, 'GET', $data,$this->getConfig());
            if($request){
                return $request;
            } else {
                throw new \ErrorException('Unable to retrieve keys for API.', 210);
            }
        }
    }
    
    public function linkKey($key) {}
    
    public function unLinkKey($key) {}
    
    public function getStats() {}
    
    public static function getCharts($granularity='minute') {}
    
    public function getConfig()
    {
        return $this->config;
    }
    
    public function isValid()
    {
        if(!isset($this->endPoint) || is_null($this->endPoint) || strlen($this->endPoint) < 1){
            throw new \Exception('Endpoint is required');
        } elseif(preg_match('/^http[s]{0,1}:\/\//', $this->endPoint)){
            throw new \Exception('Endpoint should not start with http:// or https://, use protocol to define which schema to use.',204);
        }
        
        return true;
    }
}
