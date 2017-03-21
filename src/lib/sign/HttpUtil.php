<?php

namespace leegoway\aipface\lib\sign;

class HttpUtil
{
    // Encode every character according to RFC 3986, except：
    //   1.Alphabet in upper or lower case
    //   2.Numbers
    //   3.Dot '.', wave '~', minus '-' and underline '_'
    public static $PERCENT_ENCODED_STRINGS;
    //Fill encoding array
    public static function __init()
    {
        HttpUtil::$PERCENT_ENCODED_STRINGS = array();
        for ($i = 0; $i < 256; ++$i) {
            HttpUtil::$PERCENT_ENCODED_STRINGS[$i] = sprintf("%%%02X", $i);
        }
        foreach (range('a', 'z') as $ch) {
            HttpUtil::$PERCENT_ENCODED_STRINGS[ord($ch)] = $ch;
        }
        foreach (range('A', 'Z') as $ch) {
            HttpUtil::$PERCENT_ENCODED_STRINGS[ord($ch)] = $ch;
        }
        foreach (range('0', '9') as $ch) {
            HttpUtil::$PERCENT_ENCODED_STRINGS[ord($ch)] = $ch;
        }
        HttpUtil::$PERCENT_ENCODED_STRINGS[ord('-')] = '-';
        HttpUtil::$PERCENT_ENCODED_STRINGS[ord('.')] = '.';
        HttpUtil::$PERCENT_ENCODED_STRINGS[ord('_')] = '_';
        HttpUtil::$PERCENT_ENCODED_STRINGS[ord('~')] = '~';
    }
    //keep slash '/' in encoding result of uri
    public static function urlEncodeExceptSlash($path)
    {
        return str_replace("%2F", "/", HttpUtil::urlEncode($path));
    }
    public static function urlEncode($value)
    {
        $result = '';
        for ($i = 0; $i < strlen($value); ++$i) {
            $result .= HttpUtil::$PERCENT_ENCODED_STRINGS[ord($value[$i])];
        }
        return $result;
    }
    public static function getCanonicalQueryString(array $parameters)
    {
        if (count($parameters) == 0) {
            return '';
        }
        $parameterStrings = array();
        foreach ($parameters as $k => $v) {
            //Skip authorization in headers
            if (strcasecmp('Authorization', $k) == 0) {
                continue;
            }
            if (!isset($k)) {
                throw new \InvalidArgumentException(
                    "parameter key should not be null"
                );
            }
            if (isset($v)) {
                $parameterStrings[] = HttpUtil::urlEncode($k)
                    . '=' . HttpUtil::urlEncode((string) $v);
            } else {
                $parameterStrings[] = HttpUtil::urlEncode($k) . '=';
            }
        }
        //Sort in alphabet order
        sort($parameterStrings);
        //Catenate with &
        return implode('&', $parameterStrings);
    }
    public static function getCanonicalURIPath($path)
    {
        //empty path '/'
        if (empty($path)) {
            return '/';
        } else {
            //Uri should begin with slash '/'
            if ($path[0] == '/') {
                return HttpUtil::urlEncodeExceptSlash($path);
            } else {
                return '/' . HttpUtil::urlEncodeExceptSlash($path);
            }
        }
    }
    public static function getCanonicalHeaders($headers)
    {
        if (count($headers) == 0) {
            return '';
        }
        $headerStrings = array();
        foreach ($headers as $k => $v) {
            if ($k === null) {
                continue;
            }
            if ($v === null) {
                $v = '';
            }
            $headerStrings[] = HttpUtil::urlEncode(strtolower(trim($k))) . ':' . HttpUtil::urlEncode(trim($v));
        }
        //Sort in alphabet order
        sort($headerStrings);
        //Catenate with '\n'
        return implode("\n", $headerStrings);
    }
}
HttpUtil::__init();