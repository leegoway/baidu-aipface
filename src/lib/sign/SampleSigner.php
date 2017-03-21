<?php

namespace leegoway\aipface\lib\sign;


class SampleSigner
{
    const BCE_AUTH_VERSION = "bce-auth-v1";
    const BCE_PREFIX = 'x-bce-';
    // If you don't specify header_to_sign, will use:
    //   1.host
    //   2.content-md5
    //   3.content-length
    //   4.content-type
    //   5.all the headers begin with x-bce-
    public static $defaultHeadersToSign;

    public static function  __init()
    {
        SampleSigner::$defaultHeadersToSign = array(
            "host",
            "content-length",
            "content-type",
            "content-md5",
        );
    }

    //
    // 1、$credentials数组要包含ak和sk字段
    // 2、$options要包含timestamp字段
    // 3、
    public function sign(
        array $credentials,
        $httpMethod,
        $path,
        $headers,
        $params,
        $options = array()
    ) {
        if (!isset($options[SignOption::EXPIRATION_IN_SECONDS])) {
            $expirationInSeconds = SignOption::DEFAULT_EXPIRATION_IN_SECONDS;
        } else {
            $expirationInSeconds = $options[SignOption::EXPIRATION_IN_SECONDS];
        }
        $accessKeyId = $credentials['ak'];
        $secretAccessKey = $credentials['sk'];
        //Notice: timestamp should be UTC
        if (!isset($options[SignOption::TIMESTAMP])) {
            $timestamp = new \DateTime();
        } else {
            $timestamp = $options[SignOption::TIMESTAMP];
        }
        $timestamp->setTimezone(new \DateTimeZone("UTC"));
        //Generate authString
        $authString = SampleSigner::BCE_AUTH_VERSION . '/' . $accessKeyId . '/'
            . $timestamp->format("Y-m-d\TH:i:s\Z") . '/' . $expirationInSeconds;
        //Generate sign key with auth-string and SK using SHA-256
        $signingKey = hash_hmac('sha256', $authString, $secretAccessKey);
        //Generate canonical uri
        $canonicalURI = HttpUtil::getCanonicalURIPath($path);
        //Generate canonical query string
        $canonicalQueryString = HttpUtil::getCanonicalQueryString($params);
        //Fill headersToSign to specify which header do you want to sign
        $headersToSign = null;
        if (isset($options[SignOption::HEADERS_TO_SIGN])) {
            $headersToSign = $options[SignOption::HEADERS_TO_SIGN];
        }
        //Generate canonical headers
        $canonicalHeader = HttpUtil::getCanonicalHeaders(
            SampleSigner::getHeadersToSign($headers, $headersToSign)
        );
        $signedHeaders = '';
        if ($headersToSign !== null) {
            $signedHeaders = strtolower(
                trim(implode(";", $headersToSign))
            );
        }
        //Generate canonical request
        $canonicalRequest = "$httpMethod\n$canonicalURI\n"
            . "$canonicalQueryString\n$canonicalHeader";

        //Generate signature with canonical request and sign key using SHA-256
        $signature = hash_hmac('sha256', $canonicalRequest, $signingKey);
        //.Catenate result string
        $authorizationHeader = "$authString/$signedHeaders/$signature";
        return $authorizationHeader;
    }
    public static function getHeadersToSign($headers, $headersToSign)
    {
        //Do not sign headers whose value is empty after trim
        $filter_empty = function($v) {
            return trim((string) $v) !== '';
        };
        $headers = array_filter($headers, $filter_empty);
        //Trim key in headers and change them to lower case
        $trim_and_lower = function($str){
            return strtolower(trim($str));
        };
        $temp = array();
        $process_keys = function($k, $v) use(&$temp, $trim_and_lower) {
            $temp[$trim_and_lower($k)] = $v;
        };
        array_map($process_keys, array_keys($headers), $headers);
        $headers = $temp;
        $header_keys = array_keys($headers);
        $filtered_keys = null;

        if ($headersToSign !== null) {
            //Select headers according to headersToSign
            $headersToSign = array_map($trim_and_lower, $headersToSign);
            $filtered_keys = array_intersect($header_keys, $headersToSign);
        } else {
            //Select headers by default
            $filter_by_default = function($k) {
                return SampleSigner::isDefaultHeaderToSign($k);
            };
            $filtered_keys = array_filter($header_keys, $filter_by_default);
        }
        return array_intersect_key($headers, array_flip($filtered_keys));
    }
    public static function isDefaultHeaderToSign($header)
    {
        $header = strtolower(trim($header));
        if (in_array($header, SampleSigner::$defaultHeadersToSign)) {
            return true;
        }
        return substr_compare($header, SampleSigner::BCE_PREFIX, 0, strlen(SampleSigner::BCE_PREFIX)) == 0;
    }
}
SampleSigner::__init();