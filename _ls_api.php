<?php
/**
 * Lemon Squeezy API.
 * 
 * @link https://docs.lemonsqueezy.com/api Document.
 */


require '_config.php';
require_once '_functions.php';


$APIURL = 'https://api.lemonsqueezy.com';
$requestHeaders = [
    'Authorization: Bearer ' . $LSAPI,
];