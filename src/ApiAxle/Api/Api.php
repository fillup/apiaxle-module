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
    
    public function __construct($config=false,$id=false) 
    {
        $this->config = new Config($config);
        if($id){
            $this->get($id);
        }
    }
    
    public function setData($data)
    {
        if(is_array($data)){
            $data = json_decode(json_encode($data));
        }
        // Ensure data returned looks like proper result
        if($data->createdAt && $data->endPoint){
            $this->createdAt = $data->createdAt;
            $this->updatedAt = $data->updatedAt;
            $this->globalCache = $data->globalCache;
            $this->endPoint = $data->endPoint;
            $this->protocol = $data->protocol;
            $this->apiFormat = $data->apiFormat;
            $this->endPointTimeout = $data->endPointTimeout;
            $this->endPointMaxRedirects = $data->endPointMaxRedirects;
            $this->extractKeyRegex = $data->extractKeyRegex;
            $this->defaultPath = $data->defaultPath;
            $this->disabled = $data->disabled;
            $this->strictSSL = $data->strictSSL;
        }
        
        return $this;
    }
    
    public function setName($name)
    {
        $this->name = $name;
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
            //'from' => $from,
            //'to' => $to,
            //'resolve' => $resolve
        );
        
        $apiList = new ItemList();
        $request = Utilities::callApi($apiPath, 'GET', $params, $this->config);
        return $request;
        if($request){
            $results = json_decode($request);
            return $results;
            foreach($results as $name => $data){
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
            $url = $this->config->getEndpoint().'api/'.$name;
            try {
                $request = HttpRequest::request($url);
                if($request){
                    $data = json_decode($request);
                    $this->setData($data);
                }
            } catch (\Exception $e) {
                
            }
        }
        
        return $this;
    }
    
    public function update($id,$data){}
    
    public function create($data){}
    
    public function delete($id) {}
    
    public function getKeyCharts() {}
    
    public function getKeyList() {}
    
    public function linkKey($key) {}
    
    public function unLinkKey($key) {}
    
    public function getStats() {}
    
    public static function getCharts($granularity='minute') {}
    
    public function getConfig()
    {
        return $this->config;
    }
}
