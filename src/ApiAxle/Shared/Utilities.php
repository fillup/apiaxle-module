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
            "Accept: application/json"
        );
        
        $data['api_key'] = $config->getKey();
        $api_sig = $config->getSignature();
        if($api_sig){
            $data['api_sig'] = $api_sig;
        }
        
        if($method == 'GET' && is_array($data)){
            foreach($data as $param => $value){
                if(strpos($apiPath, '?')){
                    $connector = '&';
                } else {
                    $connector = '?';
                }
                $apiPath .= $connector.$param.'='.$value;
            }
        }
        
        $json_data = false;
        
        if(is_array($data)){
            $json_data = json_encode($data);
            $headers[] = "Content-Length: ".  strlen($json_data);
        }
        
        $url = $config->getEndpoint().'/'.$apiPath;
        $request = HttpRequest::request($url, $method, $json_data, $headers);
        return $request;
        if($request){
            $results = json_decode($request);
            return $results;
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