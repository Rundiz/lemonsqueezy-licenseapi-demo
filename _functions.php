<?php


/**
 * Display debug about cURL result (path, HTTP status code, response headers, response body).
 * 
 * @param string $URLPath The path that requested to the server.
 * @param int $responseStatus HTTP response code or status code.
 * @param array $responseHeaders Response headers.
 * @param string|bool $responseBody Response body. This should be string.
 */
function debugCurlResult(string $URLPath, $responseStatus, array $responseHeaders, $responseBody)
{
    echo '<h3>Request URL path</h3>' . "\n";
    echo '<p>' . htmlspecialchars($URLPath, ENT_QUOTES) . '</p>' . "\n";

    echo '<h3>Response headers</h3>' . "\n";
    echo '<p>HTTP status: ' . var_export($responseStatus, true) . ' (' . gettype($responseStatus) . ')</p>' . "\n";
    if (is_array($responseHeaders)) {
        ksort($responseHeaders);
    }
    var_dump($responseHeaders);

    echo '<h3>Response body</h3>' . "\n";
    if (is_string($responseBody)) {
        $resultJSO = json_decode($responseBody);
    }
    if (isset($resultJSO) && json_last_error() === JSON_ERROR_NONE) {
        echo '<h4>JSON object</h4>' . "\n";
        echo '<pre>' . var_export($resultJSO, true) . '</pre>' . "\n";
    } else {
        echo '<h4>JSON decode error</h4>' . "\n";
        echo '<p>' . json_last_error_msg() . '</p>' . "\n";
        echo '<h4>Raw result</h4>' . "\n";
        echo '<pre>' . var_export($responseBody, true) . '</pre>' . "\n";
    }
    unset($resultJSO);
}