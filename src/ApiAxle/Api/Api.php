<?php
/**
 * ApiAxle (https://github.com/fillup/apiaxle-module/)
 *
 * @link      https://github.com/fillup/apiaxle-module for the canonical source repository
 * @license   MIT
 */
namespace ApiAxle\Api;

use ApiAxle\Shared\Config;
use ApiAxle\Shared\ItemList;
use ApiAxle\Shared\Utilities;
use ApiAxle\Api\Key;

/**
 * ApiAxle\Api\Api class
 * 
 * Wraps API related calls to the ApiAxle API
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
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
    
    /**
     * (default: false) If true then the api_key parameter will be passed through in the request.
     * 
     * @var boolean 
     */
    protected $sendThroughApiKey = 'false';
    
    /**
     * (default: false) If true then the api_sig parameter will be passed through in the request.
     * 
     * @var boolean 
     */
    protected $sendThroughApiSig = 'false';
    
    /**
     * (default: false) When true ApiAxle will parse and capture bits of 
     * information about the API being called.
     * 
     * @var boolean 
     */
    protected $hasCapturePaths = 'false';
    
    /**
     * (default: false) (optional) If true then allow for keyless access to this 
     * API. Also see keylessQps and keylessQpd.
     * 
     * @var boolean 
     */
    protected $allowKeylessUse = 'false';
    
    /**
     * (default: 2) How many queries per second an anonymous key should have 
     * when it's created. Note that changing this will not affect on temporary 
     * keys that have already been created. However, as temprary keys only live 
     * for 24 hours, this limit will be applied when that period expires.
     * 
     * @var boolean 
     */
    protected $keylessQps = 2;
    
    /**
     * (default: 172800) How many queries per day an anonymous key should have 
     * when it's created. Note that changing this will not affect on temporary 
     * keys that have already been created. However, as temprary keys only live 
     * for 24 hours, this limit will be applied when that period expires.
     * 
     * @var boolean 
     */
    protected $keylessQpd = 172800;
    
    /**
     * (default: 3) Allows configuration of window size for valid signatures.
     * When using signed requests, ApiAxle will calculate valid signature for
     * this many seconds before and after now. So when set to 3, there are 7
     * possible signatures that are valid.
     * 
     * @var integer 
     */
    protected $tokenSkewProtectionCount = 3;

    /**
     * Whether or not to return CORS headers for this API.
     *
     * @var bool
     */
    protected $corsEnabled = false;
    
    /**
     * Construct new Api object.
     * 
     * If a $name is provided, it will be fetched from the ApiAxle API and
     * properties will be set accordingly.
     * 
     * @param array $config
     * @param string $name
     */
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
        $this->endPoint = isset($data->endPoint) ? $data->endPoint : null;
        $this->protocol = isset($data->protocol) ? $data->protocol : null;
        $this->apiFormat = isset($data->apiFormat) ? $data->apiFormat : null;
        $this->endPointTimeout = isset($data->endPointTimeout) ? $data->endPointTimeout : null;
        $this->extractKeyRegex = isset($data->extractKeyRegex) ? $data->extractKeyRegex : null;
        $this->defaultPath = isset($data->defaultPath) ? $data->defaultPath : null;
        $this->disabled = isset($data->disabled) ? $data->disabled : false;
        $this->strictSSL = isset($data->strictSSL) ? $data->strictSSL : true;
        $this->sendThroughApiKey = isset($data->sendThroughApiKey) ? $data->sendThroughApiKey : false;
        $this->sendThroughApiSig = isset($data->sendThroughApiSig) ? $data->sendThroughApiSig : false;
        $this->hasCapturePaths = isset($data->hasCapturePaths) ? $data->hasCapturePaths : false;
        $this->allowKeylessUse = isset($data->allowKeylessUse) ? $data->allowKeylessUse : false;
        $this->keylessQps = isset($data->keylessQps) ? $data->keylessQps : false;
        $this->keylessQpd = isset($data->keylessQpd) ? $data->keylessQpd : false;
        $this->tokenSkewProtectionCount = isset($data->tokenSkewProtectionCount) ? $data->tokenSkewProtectionCount : $this->tokenSkewProtectionCount;
        $this->corsEnabled = isset($data->corsEnabled) ? $data->corsEnabled : $this->corsEnabled;
        
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
            'endPoint' => $this->endPoint,
            'protocol' => $this->protocol,
            'apiFormat' => $this->apiFormat,
            'endPointTimeout' => $this->endPointTimeout,
            'extractKeyRegex' => $this->extractKeyRegex,
            'defaultPath' => $this->defaultPath,
            'disabled' => $this->disabled,
            'strictSSL' => $this->strictSSL,
            'sendThroughApiKey' => $this->sendThroughApiKey,
            'sendThroughApiSig' => $this->sendThroughApiSig,
            'hasCapturePaths' => $this->hasCapturePaths,
            'allowKeylessUse' => $this->allowKeylessUse,
            'keylessQps' => $this->keylessQps,
            'keylessQpd' => $this->keylessQpd,
            'tokenSkewProtectionCount' => (int)$this->tokenSkewProtectionCount,
            'corsEnabled' => $this->corsEnabled,
        );
        
        return $data;
    }
    
    /**
     * Set the API name
     * 
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Get the API name
     * 
     * @return string
     */
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
                $api = new Api($this->getConfig());
                $api->setName($name);
                $api->setData($data);
                $apiList->addItem($api);
            }
        }
        
        return $apiList;
    }
    
    /**
     * Fetch information about the API from the server and update object
     * properties with information from ApiAxle
     * 
     * @param string $name
     * @return \ApiAxle\Api\Api
     */
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
        if(!is_null($this->getName()) && $this->isValid()){
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
    
    /**
     * Delete an API
     * 
     * @param string $name
     * @return boolean
     * @throws \Exception
     * @throws \ErrorException
     */
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
    
    /**
     * Get a list of Keys with access to this API
     * 
     * Parameters $from and $to are used for paginating through results
     * 
     * @param integer $from Default is 0
     * @param integer $to Default is 100
     * @param string $resolve Wether or not get Key details. Default is 'true'
     * @return \ApiAxle\Shared\ItemList
     * @throws \Exception
     * @throws \ErrorException
     */
    public function getKeyList($from=0, $to=100, $resolve='true')
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
            $keyList = new ItemList();
            $request = Utilities::callApi($apiPath, 'GET', $data,$this->getConfig());
            if($request){
                foreach($request as $item => $value){
                    $key = new Key($this->getConfig());
                    $key->setKey($item);
                    $key->setData($value);
                    $keyList->addItem($key);
                }
                return $keyList;
            } else {
                throw new \ErrorException('Unable to retrieve keys for API.', 210);
            }
        }
    }
    
    /**
     * Link a Key to this Api
     * 
     * @param \ApiAxle\Api\Key $key
     * @return \ApiAxle\Api\Api
     * @throws \Exception
     */
    public function linkKey($key)
    {
        if(is_null($this->getName())){
            throw new \Exception('An API name is required to link a key.',211);
        } else {
            $apiPath = 'api/'.$this->getName().'/linkkey/';
            if(is_string($key)){
                $apiPath .= $key;
            } elseif($key instanceof Key){
                $apiPath .= $key->getKey();
            } else {
                throw new \Exception('Key must be a string or instance of ApiAxle\Api\Key',212);
            }
            
            $request = Utilities::callApi($apiPath, 'PUT',null,$this->getConfig());
            if($request){
                return $this;
            } else {
                throw new \Exception('Unable to link key',213);
            }
        }
    }
    
    /**
     * Unlink a Key from this API
     * 
     * @param \ApiAxle\Api\Key $key
     * @return \ApiAxle\Api\Api
     * @throws \Exception
     */
    public function unLinkKey($key)
    {
        if(is_null($this->getName())){
            throw new \Exception('An API name is required to unlink a key.',214);
        } else {
            $apiPath = 'api/'.$this->getName().'/unlinkkey/';
            if(is_string($key)){
                $apiPath .= $key;
            } elseif($key instanceof Key){
                $apiPath .= $key->getKey();
            } else {
                throw new \Exception('Key must be a string or instance of ApiAxle\Api\Key',215);
            }
            
            $request = Utilities::callApi($apiPath, 'PUT',null,$this->getConfig());
            if($request){
                return $this;
            } else {
                throw new \Exception('Unable to unlink key',216);
            }
        }
    }
    
    /**
     * Get stats for this API
     * 
     * @param integer $timestart
     * @param integer $timeend
     * @param string $granularity Options are second, minute, hour, day
     * @param string $format_timeseries String of either 'true' or 'false'
     * @param string $format_timestamp Default: epoch_seconds
     * @param \ApiAxle\Api\Key $forkey Limit results to a single Key
     * @return \stdClass Object representing results from API
     * @throws \Exception
     */
    public function getStats($timestart=false, $timeend=false, 
            $granularity='minute',$format_timeseries='true',
            $format_timestamp='epoch_seconds', $forkey=false)
    {
        if(is_null($this->getName())){
            throw new \Exception('An API name is required to get stats.',217);
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
            
            if($forkey && is_string($forkey)){
                $data['forkey'] = $forkey;
            } elseif($forkey && $forkey instanceof Key){
                $data['forkey'] = $forkey->getKey();
            }
            
            $apiPath = 'api/'.$this->getName().'/stats';
            $request = Utilities::callApi($apiPath, 'GET', $data,$this->getConfig());
            if($request){
                return $request;
            } else {
                throw new \Exception('Unable to get stats for API',218);
            }
        }
    }
    
    /**
     * Get the most used APIs and their hit counts
     * 
     * @param string $granularity Options are second, minute, hour, day
     * @return \stdClass
     * @throws \Exception
     */
    public function getCharts($granularity='minute')
    {
        $apiPath = 'apis/charts';
        $data = array('granularity' => $granularity);
        $request = Utilities::callApi($apiPath, 'GET', $data, $this->getConfig());
        if($request){
            return $request;
        } else {
            throw new \Exception('Unable to get charts for API',219);
        }
    }
    
    /**
     * Get current config object
     * 
     * @return \ApiAxle\Shared\Config
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Check if required fields are present for provisioning new API
     * @return boolean
     * @throws \Exception
     */
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
