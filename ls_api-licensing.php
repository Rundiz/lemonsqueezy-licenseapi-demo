<?php
/**
 * For demonstrate how "License API" work on Lemon Squeezy.
 * 
 * Warning! This demo is not secure, you have to implement security feature such as XSS, CSRF protection yourself.
 * 
 * @link https://docs.lemonsqueezy.com/help/licensing/license-api Document.
 */


require __DIR__ . DIRECTORY_SEPARATOR . '/_ls_api.php';


if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
    $licenseKey = ($_POST['license-key'] ?? '');
    $btnAct = strtolower(($_POST['btn-act'] ?? 'validate'));

    // validate button action and then set URL path.
    if ($btnAct === 'activate') {
        $URLPath = '/v1/licenses/activate';
    } elseif ($btnAct === 'validate') {
        $URLPath = '/v1/licenses/validate';
    } elseif ($btnAct === 'deactivate') {
        $URLPath = '/v1/licenses/deactivate';
    }

    if (isset($URLPath)) {
        // if there is validated button action and already set URL path.
        $ch = curl_init($APIURL . $URLPath);
        $responseHeaders = [];
        $data = [];
        $data['license_key'] = $licenseKey;
        if ($btnAct === 'activate') {
            $data['instance_name'] = 'license-key';
        } elseif ($btnAct === 'deactivate') {
            $data['instance_id'] = ($_COOKIE['ls_licensing_' . $licenseKey . '_instid'] ?? '');
        }
        $requestHeaders[] = 'Accept: application/json';
        $requestHeaders[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
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
        unset($ch, $data, $requestHeaders);

        if ($btnAct === 'activate') {
            $resultJSO = json_decode($result);
            if (isset($resultJSO->instance->id)) {
                // if there is instance id in return. this is normal in activation process.
                // in your project, save this instance id somewhere more reliable than cookie. for example the database.
                // this instance id will be use in deactivation process.
                setcookie('ls_licensing_' . $licenseKey . '_instid', $resultJSO->instance->id, [
                    'path' => '/',
                    'expires' => time() + (366 * 86400),
                ]);
            }
            unset($resultJSO);
        } elseif ($btnAct === 'deactivate') {
            setcookie('ls_licensing_' . $licenseKey . '_instid', '', [
                'path' => '/',
                'expires' => time() - (366 * 86400),
            ]);
        }// endif button action after cURL response.
    }// endif; validate button action and set URL path.

    unset($btnAct, $licenseKey);
}// endif; method POST
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lemon Squeezy License API</title>
        <style>
            button {
                padding: 5px;
            }
            code {
                background-color: #eee;
                color: #bb6800;
                padding: 2px 4px;
            }
            input {
                min-width: 260px;
                padding: 5px;
            }
            pre {
                background-color: #eee;
                color: #222;
                margin: 0;
                padding: 10px;
                white-space: pre-wrap;
                word-break: break-all;
                word-wrap: break-word;
            }
        </style>
    </head>
    <body>
        <h1>Lemon Squeezy License API</h1>
        <form method="post">
            <input type="text" name="license-key" value="<?php if (isset($_POST['license-key'])) {echo htmlspecialchars($_POST['license-key'], ENT_QUOTES);} ?>" placeholder="License key">
            <p>
                <button type="submit" name="btn-act" value="activate">Activate</button>
                <button type="submit" name="btn-act" value="validate">Validate</button>
                <button type="submit" name="btn-act" value="deactivate">Deactivate</button>
            </p>
            <p>Use <strong>Activate</strong> button to activate the license key.</p>
            <p>Use <strong>Validate</strong> button to validate the license key. The return result should be <code>'status' =&gt; 'active'</code> and <code>activation_usage</code>, <code>expires_at</code> should be valid.</p>
        </form>
        <?php
        if (isset($URLPath) && isset($httpStatus) && isset($responseHeaders) && isset($result)) {
            echo '<h2>Result</h2>' . "\n";
            debugCurlResult($URLPath, $httpStatus, $responseHeaders, $result);
            unset($httpStatus, $responseHeaders, $result, $URLPath);
        }
        ?> 
    </body>
</html>