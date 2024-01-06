<?php
/**
 * For testing that Lemon Squeezy API works.
 */


require __DIR__ . DIRECTORY_SEPARATOR . '/_ls_api.php';


$URLPath = '/v1/stores';
$ch = curl_init($APIURL . $URLPath);
$responseHeaders = [];
$requestHeaders[] = 'Accept: application/vnd.api+json';
$requestHeaders[] = 'Content-Type: application/vnd.api+json';
curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
// if you use other method than GET, POST. set it below this line.
//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
// in case you have to send POST data, use option below.
// also required header `Content-Type: application/x-www-form-urlencoded` to work with `http_build_query()`.
//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['name' => 'value']));// 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
/**
 * @link https://stackoverflow.com/a/41135574/128761 Original source code.
 */
curl_setopt(
    $ch,
    CURLOPT_HEADERFUNCTION,
    function ($curl, $header) use (&$responseHeaders) {
        $len = strlen($header);
        $header = explode(':', $header, 2);
        if (count($header) < 2) // ignore invalid headers
            return $len;

        $responseHeaders[strtolower(trim($header[0]))][] = trim($header[1]);

        return $len;
    }
);
$result = curl_exec($ch);
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
unset($ch, $requestHeaders);


debugCurlResult($URLPath, $httpStatus, $responseHeaders, $result);
unset($httpStatus, $responseHeaders, $result, $URLPath);