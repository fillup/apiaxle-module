<?php
/**
 * ApiAxle (https://github.com/fillup/apiaxle-module)
 *
 * @link      https://github.com/fillup/apiaxle-module for the canonical source repository
 * @license   MIT
 */
namespace ApiAxle\Shared;

/**
 * Custom Exception class to also store API response for further information
 * and debugging.
 */
class ApiException extends \Exception
{
    /**
     * Full API response
     * 
     * @var string 
     */
    protected $response;
    
    /**
     * HTTP response code
     * 
     * @var integer 
     */
    protected $http_code;
    
    public function __construct($message, $code, $previous = null, $http_code, $response) {
        parent::__construct($message, $code, $previous);
        $this->setResponse($response);
        $this->setHttpCode($http_code);
    }
    
    public function setHttpCode($code=0)
    {
        $this->http_code = (int)$code;
    }
    public function getHttpCode()
    {
        return $this->http_code;
    }
    
    public function setResponse($response)
    {
        if(is_object($response) || is_array($response)){
            $this->response = json_encode($response);
        } else {
            $this->response = $response;
        }
    }
    
    public function getResponse()
    {
        return $this->response;
    }
    
    public function __toString() {
        $error = 'Message: '.$this->getMessage()."\n";
        $error .= 'Code: '.$this->getCode().'\n';
        $error .= 'Response: '.$this->getResponse();
        
        return __CLASS__.':\n'.$error;
    }
}
