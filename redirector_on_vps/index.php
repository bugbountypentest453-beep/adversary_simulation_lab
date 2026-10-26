<?php
// ===== config =====
$TUNNEL_URL      = 'https://put_tunell_url_here';
$MY_DOMAIN       = 'http://localhost:8080';
$BOT_REDIRECT_URL = 'https://example.com/';
// ========================

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (preg_match('/(bot|crawler|spider|scanner)/i', $userAgent)) {
    header('Location: ' . $BOT_REDIRECT_URL, true, 302);
    exit;
}

$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$targetUrl  = $TUNNEL_URL . $requestUri;

$ch = curl_init($targetUrl);

$headers = [];
foreach (getallheaders() as $name => $value) {
    $lower = strtolower($name);
    if ($lower === 'host') {
        $headers[] = 'Host: ' . parse_url($TUNNEL_URL, PHP_URL_HOST);
    } else {
        $headers[] = "$name: $value";
    }
}
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_SERVER['REQUEST_METHOD']);

if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'PATCH'])) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
}

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // ← NIE podążaj za redirectami
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$response   = curl_exec($ch);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$responseHeaders = substr($response, 0, $headerSize);
$responseBody    = substr($response, $headerSize);

$tunnelHost = parse_url($TUNNEL_URL, PHP_URL_HOST);
$myHost     = parse_url($MY_DOMAIN, PHP_URL_HOST);
$myPort     = parse_url($MY_DOMAIN, PHP_URL_PORT);
$myHostPort = $myHost . ($myPort ? ':' . $myPort : ''); // host:port fallback musi nosić port razem z hostem
$responseBody = str_replace($TUNNEL_URL, $MY_DOMAIN, $responseBody);
$responseBody = str_replace('//' . $tunnelHost, '//' . $myHostPort, $responseBody);

http_response_code($httpCode);

foreach (explode("\r\n", $responseHeaders) as $header) {
    if (empty($header) || strpos($header, ':') === false) continue;

    $colonPos   = strpos($header, ':');
    $headerName = strtolower(substr($header, 0, $colonPos));

    if (in_array($headerName, ['transfer-encoding', 'connection', 'content-length'])) continue;

    if ($headerName === 'location') {
        $loc = trim(substr($header, $colonPos + 1));
        $loc = str_replace($TUNNEL_URL, $MY_DOMAIN, $loc);
        $loc = str_replace('//' . $tunnelHost, '//' . $myHostPort, $loc);
        $loc = preg_replace(
            '#^https?://' . preg_quote($tunnelHost, '#') . '#',
            $MY_DOMAIN,
            $loc
        );
        header('Location: ' . $loc, true);
        continue;
    }

    header($header, false);
}

echo $responseBody;
?>
