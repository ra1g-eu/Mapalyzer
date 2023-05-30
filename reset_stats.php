<?php
if (isset($_POST['resetStats'])) {
    $countryNameArray = array('_czech-' => "_czech-", '_germany-' => "_germany-", '_uk-' => "_uk-", '_japan-' => "_japan-", '_usa-' => "_usa-", '_poland-' => "_poland-", '_sweden-' => "_sweden-", '_ussr-' => "_ussr-", '_china-' => "_china-", '_france-' => "_france-", '_italy-' => "_italy-");
    $string = file_get_contents('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json');
    $json = json_decode($string, true);
    foreach ($json as $key => $items) {
        $json[$key]['count'] = 0;
        foreach ($countryNameArray as $item) {
            $json[$key]['country'][$item]['countryCount'] = 0;
        }
        if ($key == count($json) - 1) {
            file_put_contents('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json', json_encode($json));
            echo 'resetSuccess';
            exit();
        }
    }
}