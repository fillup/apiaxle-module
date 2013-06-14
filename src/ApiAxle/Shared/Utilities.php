<?php
/**
 * ApiAxle (https://github.com/fillup/apiaxle-module)
 *
 * @link      https://github.com/fillup/apiaxle-module for the canonical source repository
 * @license   GPLv2+
 */

namespace ApiAxle\Shared;
use ApiAxle\Shared\HttpRequest;
use ApiAxle\Shared\ApiException;

/**
 * Utilities class to perform common functions for API activities.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
class Utilities
{
    public static function callApi($apiPath, $method='GET', $data=null, $config)
    {
        $headers = array(
            "Accept: application/json",
        );
        
        if(($method == 'POST' || $method == 'PUT') && is_array($data)){
            $headers[] = "Content-Type: application/json";
        }
        
        $api_key = $config->getKey();
        $api_sig = $config->getSignature();
        
        if(strpos($apiPath,'?')){
            $apiPath .= "&api_key=$api_key&api_sig=$api_sig";
        } else {
            $apiPath .= "?api_key=$api_key&api_sig=$api_sig";
        }
        
        if($method == 'GET' && is_array($data)){
            foreach($data as $param => $value){
                $apiPath .= '&'.$param.'='.$value;
            }
        }
        
        $json_data = false;
        
        if(is_array($data)){
            $json_data = json_encode($data);
            $headers[] = "Content-Length: ".  strlen($json_data);
        }
        
        $url = $config->getEndpoint().'/'.$apiPath;
        $request = HttpRequest::request($url, $method, $json_data, $headers);
        if($request){
            $results = json_decode($request);
            if($results->meta->status_code >= 200 && $results->meta->status_code < 300){
                return $results->results;
            } elseif($results->meta->status_code >= 300 && $results->meta->status_code < 400){
                throw new ApiException('API returned a redirection', '200', null, $results->meta->status_code, $results);
            } elseif($results->meta->status_code >= 400 && $results->meta->status_code < 600){
                throw new ApiException('API returned error', '201', null, $results->meta->status_code, $results);
            }
        } else {
            throw new \ErrorException('API did not return properly.','202',null);
        }
    }
}