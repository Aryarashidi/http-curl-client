<?php

class client {

    private $curlClient;
    private $url;
    private $method;
    private $requestHeader;
    private $requestBody;
    private $responseHeader;
    private $responseBody;
    private $coolie;
    private $authData=[];

    public function __construct($url,$method="GET",$header=[],$body=[],$coolie=[]) {
        $this->curlClient = curl_init();
        $this->setUrl($url);
        $this->setMethod($method);
        if (!empty($header)) $this->setHeaders($header);
        if (!empty($body)) $this->setBody($body);
        if (!empty($coolie)) $this->setCookie($coolie);
    }

    public function setUrl($url) {
        $this->url=$url;
        curl_setopt($this->curlClient, CURLOPT_URL, $url);
    }

    public function setMethod($method) {
        $this->method=$method;
        curl_setopt($this->curlClient, CURLOPT_CUSTOMREQUEST, $method);
    }

    public function setHeaders($headers) {
        $this->requestHeader($headers);
        curl_setopt($this->curlClient, CURLOPT_HTTPHEADER, $headers);
    }

    public function addToHeaders($headers) {
        $newHeader=array_merge($this->requestHeader,$headers);
        $this->requestHeader($newHeader);
        curl_setopt($this->curlClient, CURLOPT_HTTPHEADER, $newHeader);
    }

    public function setBody($body) {
        $this->requestBody($body);
        curl_setopt($this->curlClient, CURLOPT_POSTFIELDS, $body);
    }

    public function addToBody($body) {
        $newBody=array_merge($this->requestBody,$body);
        $this->requestHeader($newBody);
        curl_setopt($this->curlClient, CURLOPT_POSTFIELDS, $newBody);
    }

    public function setCookie($cookie) {
        $this->coolie=$cookie;
        curl_setopt($this->curlClient, CURLOPT_COOKIE, $cookie);
    }

    public function addToCookie($cookie) {
        $newCoolie=array_merge($this->coolie,$cookie);
        $this->requestHeader($newCoolie);
        curl_setopt($this->curlClient, CURLOPT_COOKIE, $newCoolie);
    }

    public function setAuth($data,$method="JWT")
    {
        if($method=="JWT"){
            $this->authData='Bearer '.$data['token'];
        }
    }

    public function getAuth()
    {
        return $this->authData;
    }

    public function setTimeout($timeout) {
        curl_setopt($this->curlClient, CURLOPT_TIMEOUT, $timeout);
    }

    public function setFollowLocation($followLocation) {
        curl_setopt($this->curlClient, CURLOPT_FOLLOWLOCATION, $followLocation);
    }

    public function setUserAgent($userAgent) {
        curl_setopt($this->curlClient, CURLOPT_USERAGENT, $userAgent);
    }

    public function setReferer($referer) {
        curl_setopt($this->curlClient, CURLOPT_REFERER, $referer);
    }

    public function execute($closeConnection=true) {
        $auth=$this->getAuth();
        if (!empty($auth))
        {
            $this->addToHeaders([
                "authentication"=>$auth
            ]);
        }
        $response=curl_exec($this->curlClient);

        $header_size = $this->getInfo();

        $this->responseHeader = substr($response, 0, $header_size);
        $this->responseBody = substr($response, $header_size);

        if ($closeConnection) $this->close();
        return $response;
    }

    public function getInfo() {
        return curl_getinfo($this->curlClient);
    }

    public function close() {
        curl_close($this->curlClient);
    }

}