<?php

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Method Not Allowed');
    }


    $blob = $_POST['blob'];

    if (!dir('/tmp/logs')) {
        if (!mkdir('/tmp/logs', 0777, true) && !is_dir('/tmp/logs')) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', 'logs'));
        }
    }


    $md5blob = md5($blob); // nosemgrep

    file_put_contents('/tmp/logs/' . $md5blob . '.log', $blob, FILE_APPEND);

} catch (Exception $e) {
    header('Location: /', true, 302);
}

setcookie('submitted', ((string) time()));
header('Location: https://example.com/', true, 302);
exit;
