<?php

namespace leegoway\aipface\lib;

require_once 'AipBCEUtil.php';

use leegoway\aipface\lib\sign\SampleSigner;
use leegoway\aipface\lib\sign\SignOption;

/**
 * Aip Base 基类
 */
class AipsAKSKBase {

    /**
     * apiKey
     * @var string
     */
    protected $accessKey = '';
    
    /**
     * secretKey
     * @var string
     */
    protected $secretKey = '';

    /**
     * @param string $appId 
     * @param string $apiKey
     * @param string $secretKey
     */
    public function __construct($accessKey, $secretKey){
        $this->accessKey = trim($accessKey);
        $this->secretKey = trim($secretKey);
        $this->client = new AipHttpClient(array(
            'User-Agent' => 'baidu-aip-php-sdk-1.0.0.1',
        ));
    }

    /**
     * Api 请求
     * @param  string $url
     * @param  mixed $data
     * @return mixed
     */
    protected function request($url, $data){
        $params = array();
        $headers = $this->getAuthHeaders($url, $data);
        $response = $this->client->post('http://bfr.bj.baidubce.com'.$url, http_build_query($data), $params, $headers);
        $obj = $this->proccessResult($response['content']);
        return $obj;
    }

    private function getAuthHeaders($url, $data)
    {
        $authHeaders = array();
        $signer = new SampleSigner();
        $credentials = array("ak" => $this->accessKey,"sk" => $this->secretKey);
        $httpMethod = "POST";
        date_default_timezone_set("PRC");
        $timestamp = new \DateTime();
        $timestamp->setTimestamp(time());
        $timestamp->setTimezone(new \DateTimeZone("UTC"));
        $bceDate = $timestamp->format("Y-m-d\TH:i:s\Z");
        $authHeaders = array(
            "Host" => "bfr.bj.baidubce.com",
                //"Content-Length" => 8,
                //"Content-MD5" => "0a52730597fb4ffa01fc117d9e71e3a9",
            "Content-Type" => "application/x-www-form-urlencoded",
            "x-bce-date" => $bceDate,
            'accept' => '*/*'
        );
        $params = array();
        $options = array(SignOption::TIMESTAMP => $timestamp, SignOption::HEADERS_TO_SIGN => ['Host']);
        $signature = $signer->sign($credentials, $httpMethod, $url, $authHeaders, $params, $options);
        $authHeaders['Authorization'] = $signature;
        return $authHeaders;
    }

    /**
     * 格式化结果
     * @param $content string
     * @return mixed
     */
    protected function proccessResult($content){
        return json_decode($content, true);
    }

}