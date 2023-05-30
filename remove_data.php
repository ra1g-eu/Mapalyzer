<?php
if(isset($_POST['removeData'])){
    if (isset($_COOKIE['Mapalyzer_Token'])) {
        if (file_exists('./' . $_COOKIE['Mapalyzer_Token'] . '.html')) {
            unlink('./' . $_COOKIE['Mapalyzer_Token'] . '.html');
        }
        if (file_exists('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json')) {
            unlink('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json');
        }
        setcookie('Mapalyzer_Token', '', [
            'expires' => time() - 3500,
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => false,
            'samesite' => 'Strict',
        ]);
        setcookie('mapalyzerCookieConsent', '', [
            'expires' => time() - 3500,
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => false,
            'samesite' => 'Strict',
        ]);
        echo 'removedDataSuccess';
        exit();
    } else {
        echo 'removedDataNoCookie';
        exit();
    }
}
