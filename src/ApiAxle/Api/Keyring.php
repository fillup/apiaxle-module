<?php
/**
 * ApiAxle (https://github.com/fillup/apiaxle-module/)
 *
 * @link      https://github.com/fillup/apiaxle-module for the canonical source repository
 * @license   GPLv2+
 */
namespace ApiAxle\Api;

use ApiAxle\Shared\Config;
use ApiAxle\Shared\Utilities;
use ApiAxle\Shared\ItemList;

/**
 * ApiAxle\Api\Keyring class
 * 
 * Wraps Keyring related calls to the ApiAxle API
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
class Keyring
{
    /**
     * Configuration data
     * 
     * @var \ApiAxle\Shared\Config
     */
    protected $config;
    
    /**
     * Keyring Name
     * 
     * @var string $name
     */
    protected $name;
    
    /**
     * Created at timestamp, set automatically by API
     * 
     * @var integer
     */
    protected $createdAt;
    
    /**
     * Updated at timestamp, set automatically by API
     * 
     * @var integer
     */
    protected $updatedAt;
    
    /**
     * Construct new Keyring object
     * 
     * If $name is provided it will fetch the created/updated values from API
     * 
     * @param ApiAxle\Shared\Config $config
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
     * Return current name value
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set name value
     * 
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Set object properties
     * 
     * @param array $data
     * @return \ApiAxle\Api\Keyring
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
        
        return $this;
    }
    
    /**
     * Fetch keyring information from ApiAxle API and update object properties
     * 
     * @param string $key
     * @return \ApiAxle\Api\Keyring
     */
    public function get($name)
    {
        if($name){
            $apiPath = 'keyring/'.$name;
            $request = Utilities::callApi($apiPath,'GET',null,$this->getConfig());
            if($request){
                $this->setName($name);
                $this->setData($request);
            }
        }
        
        return $this;
    }
    
    /**
     * Create a new keyring
     * 
     * @param string $name
     * @param array $keys
     * @return \ApiAxle\Api\Key
     * @throws \ErrorException
     */
    public function create($name,$keys=false)
    {
        $this->setName($name);
        if($this->isValid()){
            $apiPath = 'keyring/'.$this->getName();
            $data = array('createdAt' => time());
            $request = Utilities::callApi($apiPath, 'POST', $data, $this->getConfig());
            if($request){
                $this->get($name);
                if($keys){
                    $this->linkKeys($keys);
                }
                return $this;
            } else {
                throw new \ErrorException('Unable to create keyring',270);
            }
        }
    }
    
    /**
     * Get a list of all Keyrings
     * 
     * Parameters $from and $to are used for pagination of results
     * 
     * @param integer $from
     * @param integer $to
     * @param string $resolve
     * @return \ApiAxle\Shared\ItemList
     */
    public function getList($from=0, $to=100, $resolve='true')
    {
        $apiPath = 'keyrings';
        $params = array(
            'from' => $from,
            'to' => $to,
            'resolve' => $resolve
        );
        
        $keyringList = new ItemList();
        $request = Utilities::callApi($apiPath, 'GET', $params, $this->getConfig());
        if($request){
            foreach($request as $name => $data){
                $keyring = new Keyring($this->getConfig());
                $keyring->setName($name);
                $keyring->setData($data);
                $keyringList->addItem($keyring);
            }
        }
        
        return $keyringList;
    }
    
    
    /**
     * Delete a keyring
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
            throw new \Exception('A name is required to delete.',271);
        } else {
            $apiPath = 'keyring/'.$this->getName();
            $request = Utilities::callApi($apiPath, 'DELETE', null, $this->getConfig());
            if($request){
                return true;
            } else {
                throw new \ErrorException('Unable to delete keyring.', 272);
            }
        }
    }
    
    /**
     * Get a list of Keys with access to this Keyring
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
            throw new \Exception('An Keyring name is required to get keylist.',273);
        } else {
            $apiPath = 'keyring/'.$this->getName().'/keys';
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
                throw new \ErrorException('Unable to retrieve keys for Keyring.', 274);
            }
        }
    }
    
    /**
     * Convenience method to pass an array of keys to be linked after creating
     * keyring.
     * 
     * @param array $keys
     * @return \ApiAxle\Api\Keyring
     */
    public function linkKeys($keys)
    {
        foreach($keys as $key){
            $this->linkKey($key);
        }
        
        return $this;
    }
    
    /**
     * Link a Key to this Keyring
     * 
     * @param \ApiAxle\Api\Key $key
     * @return \ApiAxle\Api\Keyring
     * @throws \Exception
     */
    public function linkKey($key)
    {
        if(is_null($this->getName())){
            throw new \Exception('An API name is required to link a key.',275);
        } else {
            $apiPath = 'keyring/'.$this->getName().'/linkkey/';
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
                throw new \Exception('Unable to link key',276);
            }
        }
    }
    
    /**
     * Unlink a Key from this Keyring
     * 
     * @param \ApiAxle\Api\Key $key
     * @return \ApiAxle\Api\Keyring
     * @throws \Exception
     */
    public function unLinkKey($key)
    {
        if(is_null($this->getName())){
            throw new \Exception('A Keyring name is required to unlink a key.',277);
        } else {
            $apiPath = 'keyring/'.$this->getName().'/unlinkkey/';
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
                throw new \Exception('Unable to unlink key',278);
            }
        }
    }
    
    /**
     * Get real time hits for a keyring
     * 
     * @param integer $timestart
     * @param integer $timeend
     * @param string $granularity
     * @param string $format_timeseries
     * @param string $format_timestamp
     * @param \ApiAxle\Api\Key $forkey
     * @param \ApiAxle\Api\Api $forapi
     * @return type
     * @throws \Exception
     */
    public function getStats($timestart=false, $timeend=false, 
            $granularity='minute',$format_timeseries='true',
            $format_timestamp='epoch_seconds', $forkey=false, $forapi=false)
    {
        if(is_null($this->getName())){
            throw new \Exception('A keyring name is required to get stats.',279);
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
            } elseif($forkey && $forkey instanceof ApiAxle\Api\Key){
                $data['forkey'] = $forkey->getKey();
            }
            
            if($forapi && is_string($forapi)){
                $data['forapi'] = $forapi;
            } elseif($forapi && $forapi instanceof ApiAxle\Api\Api){
                $data['forapi'] = $forapi->getName();
            }
            
            $apiPath = 'keyring/'.$this->getName().'/stats';
            $request = Utilities::callApi($apiPath, 'GET', $data,$this->getConfig());
            if($request){
                return $request;
            } else {
                throw new \Exception('Unable to get stats for keyring',280);
            }
        }
    }
    
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * Return config object
     * 
     * @return \ApiAxle\Shared\Config
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    public function isValid()
    {
        if(!is_null($this->getName())){
            return true;
        }
        
        return false;
    }
}