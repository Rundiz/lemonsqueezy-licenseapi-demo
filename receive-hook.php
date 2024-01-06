<?php
/**
 * Receive hooks for debugging.
 * 
 * Make Lemon Squeezy web hook call back URL to this file.  
 * Example: make callback to https://thisdomain.tld/app/receive-hook.php
 * 
 * @link https://docs.lemonsqueezy.com/help/webhooks Document. 
 */


require __DIR__ . '/_config.php';


$log = '[' . date('Y-m-d H:i:s') . ']';
$log .= "\tHeaders:" . normalizeString(var_export(getallheaders(), true)) . ';' . "\n";
$log .= "\tGET:" . normalizeString(var_export($_GET, true)) . ';' . "\n";
$log .= "\tPOST:" . normalizeString(var_export($_POST, true)) . ';' . "\n";
ksort($_SERVER);
$log .= "\tSERVER:" . normalizeString(var_export($_SERVER, true)) . ';' . "\n";


if (isset($_SERVER['HTTP_X_SIGNATURE'])) {
    // if there is signature.
    // @link https://docs.lemonsqueezy.com/help/webhooks#signing-requests signing requests API doc.
    $payload = file_get_contents('php://input');
    $hash = hash_hmac('sha256', $payload, $key);
    $signature = ($_SERVER['HTTP_X_SIGNATURE'] ?? '');
    $log .= "\tPHP://input:" . normalizeString(var_export($payload, true)) . ';' . "\n";
    $JSOPayload = json_decode($payload);
    if (json_last_error() === JSON_ERROR_NONE) {
        $log .= "\tJSON object of PHP://input:" . normalizeString(var_export($JSOPayload, true)) . ';' . "\n";
    }
    $log .= "\tHash equals:" . normalizeString(var_export(hash_equals($hash, $signature), true)) . ';' . "\n";
    unset($hash, $JSOPayload, $payload, $signature);
}
$eventName = str_replace(' ', '-', ($_SERVER['HTTP_X_EVENT_NAME'] ?? 'EVENTUNKNOWN'));

$log .= "\n\n\n\n";

file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug_' . basename(__FILE__, '.php') . '_event-' . $eventName . '_' . date('YmdHis') . '.txt', $log, FILE_APPEND);
unset($eventName);


// end receive hooks debugging. ==============================================


/**
 * Normalize string.
 * 
 * @param string $string The input string
 * @return string Return normalized string.
 */
function normalizeString(string $string): string
{
    $string = str_replace(["\r\n", "\r"], "\n", $string);// make unix new line only.
    $string = preg_replace('/(\h{2,})/', ' ', $string);// make multiple horizontal spaces to be one.
    $string = preg_replace('/^(\h*)/m', "\t$1", $string);// prepend tab at beginning of line.
    return trim($string);
}