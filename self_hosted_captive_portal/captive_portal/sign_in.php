<?php

header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');
header('Expires: 0');
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');

if(isset($_COOKIE['submitted'])) {
    header('Location: https://github.com/', true, 302);
    exit;
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        form { display: flex; flex-direction: column; width: 200px; gap: 10px; }
        input, button { padding: 5px; }
    </style>
</head>
<body>
<form action="sign_in" id="loginForm" method="POST">
    <label for="user_login">Login:</label>
    <input type="text" id="user_login" name="user_login" required>
    <label for="user_password">Hasło:</label>
    <input type="password" id="user_password" name="user_password" required>
    <button type="submit">Zaloguj</button>
</form>

</body>
</html>