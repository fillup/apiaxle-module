<?php
/**
 * ApiAxle (https://github.com/fillup/apiaxle-module/)
 *
 * @link      https://github.com/fillup/apiaxle-module for the canonical source repository
 * @license   GPLv2+
 */
namespace ApiAxle\Api;

use ApiAxle\Shared\ApiException;
use ApiAxle\Shared\Config;
use ApiAxle\Shared\Utilities;
use ApiAxle\Shared\ItemList;

class Key
{
    /**
     * Configuration data
     * 
     * @var ApiAxle\Shared\Config
     */
    protected $config;
    
    /**
     * The actual api key
     * 
     * @var string
     */
    protected $key;
    
    /**
     * Created at timestamp. Set automatically when creating a new key.
     * 
     * @var int
     */
    protected $createdAt;
    
    /**
     * Updated at timestamp. Set automatically whenever updating a key.
     * 
     * @var int
     */
    protected $updatedAt;
    
    /**
     * (optional) A shared secret which is used when signing a call to the api.
     * 
     * @var string
     */
    protected $sharedSecret;
    
    /**
     * (default: 172800) Number of queries that can be called per day. Set to `-1` for no limit.
     * 
     * @var int
     */
    protected $qpd = 172800;
    
    /**
     * (default: 2) Number of queries that can be called per second. Set to `-1` for no limit.
     * 
     * @var int
     */
    protected $qps = 2;
    
    /**
     * (optional) Names of the Apis that this key belongs to.
     * 
     * @var array
     */
    protected $forApis;
    
    /**
     * (default: false) Disable this API causing errors when it's hit.
     * 
     * @var boolean
     */
    protected $disabled = false;
    
    /**
     * (default: false) If you're the NSA set this flag to true and you'll 
     * activate GOD mode getting you into any API regardless of your being 
     * linked to it or not.
     * 
     * @var string 
     */
    protected $isNSA = 'false';
    
    public function __construct($config=false,$key=false) 
    {
        $this->config = new Config($config);
        if($key){
            $this->get($key);
        }
    }
    
    public function getKey()
    {
        return $this->key;
    }
    
    public function setKey($key)
    {
        $this->key = $key;
    }
    
    /**
     * Set object properties
     * 
     * @param type $data
     * @return \ApiAxle\Api\Key
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
        $this->sharedSecret = isset($data->sharedSecret) ? $data->sharedSecret : null;
        $this->qpd = isset($data->qpd) ? $data->qpd : $this->qpd;
        $this->qps = isset($data->qps) ? $data->qps : $this->qps;
        $this->forApis = isset($data->forApis) ? $data->forApis : null;
        $this->disabled = isset($data->disabled) ? $data->disabled : $this->disabled;
        $this->isNSA = isset($data->isNSA) ? $data->isNSA : $this->isNSA;
        
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
            'sharedSecret' => $this->sharedSecret,
            'qpd' => $this->qpd,
            'qps' => $this->qps,
            'forApis' => $this->forApis,
            'disabled' => $this->disabled,
            'isNSA' => $this->isNSA,
        );
        
        return $data;
    }
    
    public function getDataForApiCall()
    {
        $data = array(
            'qpd' => $this->qpd,
            'qps' => $this->qps,
            'disabled' => $this->disabled,
        );
        
        if(is_array($this->forApis)){
            $data['forApis'] = $this->forApis;
        }
        if(!is_null($this->sharedSecret)){
            $data['sharedSecret'] = $this->sharedSecret;
        }
        
        return $data;
    }
    
    public function get($key)
    {
        if($key){
            $apiPath = 'key/'.$key;
            $request = Utilities::callApi($apiPath,'GET',null,$this->getConfig());
            if($request){
                $this->setKey($key);
                $this->setData($request);
            }
        }
        
        return $this;
    }
    
    public function create($key, $data=false)
    {
        $this->setKey($key);
        if($data){
            $this->setData($data);
        }
        if($this->isValid()){
            $apiPath = 'key/'.$this->getKey().'?isNSA='.$this->isNSA;
            $request = Utilities::callApi($apiPath, 'POST', $this->getDataForApiCall(),$this->getConfig());
            if($request){
                $this->get($key);
                return $this;
            } else {
                throw new \ErrorException('Unable to create key',251);
            }
        }
        
    }
    
    public function update($data)
    {
        if(!is_null($this->getKey())){
            $apiPath = 'key/'.$this->getKey();
            $request = Utilities::callApi($apiPath,'PUT',$data,$this->getConfig());
            if($request){
                $this->get($this->getKey());
                return $this;
            } else {
                throw new \ErrorException('Unable to update key',252);
            }
        } else {
            throw new \ErrorException('A key value is required to update.',253);
        }
        
    }
    
    public function delete($key=false)
    {
        if($key){
            $this->setKey($key);
        }
        if(is_null($this->getKey())){
            throw new \Exception('A key value is required to delete.',254);
        } else {
            $apiPath = 'key/'.$this->getKey();
            $request = Utilities::callApi($apiPath, 'DELETE', null, $this->getConfig());
            if($request){
                return true;
            } else {
                throw new \ErrorException('Unable to delete key.', 255);
            }
        }
    }
    
    public function getList($from=0, $to=100, $resolve='true')
    {
        $apiPath = 'keys';
        $params = array(
            'from' => $from,
            'to' => $to,
            'resolve' => $resolve
        );
        
        $keyList = new ItemList();
        $request = Utilities::callApi($apiPath, 'GET', $params, $this->getConfig());
        if($request){
            foreach($request as $name => $data){
                $key = new Key();
                $key->setKey($name);
                $key->setData($data);
                $keyList->addItem($key);
            }
        }
        
        return $keyList;
    }
    
    public function getApiList($resolve='true')
    {
        if(is_null($this->getKey())){
            throw new \Exception('A key value is required to fetch associated apis.',256);
        } else {
            $apiList = new ItemList();
            $data = array('resolve' => $resolve);
            $apiPath = 'key/'.$this->getKey().'/apis';
            $request = Utilities::callApi($apiPath, 'GET', $data, $this->getConfig());
            if($request){
                foreach($request as $name => $value){
                    $api = new Api();
                    $api->setName($name);
                    $api->setData($value);
                    $apiList->addItem($api);
                }
                
                return $apiList;
            } else {
                throw new \ErrorException('Unable to get list of APIs for key.', 257);
            }
        }
    }
    
    public function getApiCharts($granularity='minute')
    {
        if(is_null($this->getKey())){
            throw new \Exception('A key value is required to fetch api charts.',258);
        } else {
            $apiPath = 'key/'.$this->getKey().'/apicharts';
            $data = array('granularity' => $granularity);
            $request = Utilities::callApi($apiPath, 'GET', $data,$this->getConfig());
            if($request){
                return $request;
            } else {
                throw new Exception('Unable to fetch api charts', 259);
            }
        }
    }
    
    public function getStats($timestart=false, $timeend=false, 
            $granularity='minute',$format_timeseries='true',
            $format_timestamp='epoch_seconds', $forapi=false)
    {
        if(is_null($this->getKey())){
            throw new \Exception('A key value is required to get stats.',260);
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
            
            if($forapi && is_string($forapi)){
                $data['forapi'] = $forapi;
            } elseif($forapi && $forapi instanceof ApiAxle\Api\Api){
                $data['forapi'] = $forapi->getName();
            }
            
            $apiPath = 'key/'.$this->getKey().'/stats';
            $request = Utilities::callApi($apiPath, 'GET', $data,$this->getConfig());
            if($request){
                return $request;
            } else {
                throw new \Exception('Unable to get stats for key',261);
            }
        }
    }
    
    public function getCharts($granularity='minute')
    {
        $apiPath = 'keys/charts';
        $data = array('granularity' => $granularity);
        $request = Utilities::callApi($apiPath, 'GET', $data,$this->getConfig());
        if($request){
            return $request;
        } else {
            throw new \Exception('Unable to get charts for key.',262);
        }
    }
    
    public function getConfig()
    {
        return $this->config;
    }
    
    public function isValid()
    {
        if(!is_null($this->getKey())){
            return true;
        } else {
            throw new \Exception('A key value is required to interact with keys.',250);
        }
    }
    
}
