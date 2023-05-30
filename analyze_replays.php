<?php
// START OF CLEAN REPLAYS
$replayArray = json_decode(stripslashes($_POST['data']));
$cleanReplayArray = array();
foreach ($replayArray as $item) {
    array_push($cleanReplayArray, $item->filename);
}
$cleanReplayArray = array_values($cleanReplayArray);
foreach ($cleanReplayArray as $filePos => $fileExact) {
    try{
        if (preg_match('~\b(temp|replay_last_battle|maps.txt)\b~i', $fileExact)) {
            unset($cleanReplayArray[$filePos]);
        } else if (containsWord("Halloween", $fileExact) || containsWord("hw21", $fileExact)) {
            unset($cleanReplayArray[$filePos]);
        } else if(!isset(pathinfo($fileExact)['extension'])){
            echo 'system_error';
            $cleanReplayArray = array();
            exit();
        } else if (pathinfo($fileExact)['extension'] != "wotreplay") {
            unset($cleanReplayArray[$filePos]);
        }
    } catch (Exception $e){
        echo 'system_error';
        $cleanReplayArray = array();
        break;
    }
}
$cleanReplayArray = array_values($cleanReplayArray);
// END OF CLEAN REPLAYS

// FUNCTIONS
function containsWord($needle, $haystack)
{
    return strpos($haystack, $needle) !== false;
}

function getMapInfoFromJSON($file_path)
{
    $string = file_get_contents($file_path);
    return json_decode($string, true);
}
// END OF FUNCTIONS

// MAIN LOGIC
if (!empty($cleanReplayArray)) {
    $analayzeNations = true;
    $countryNameArray = array('_czech-' => "_czech-", '_germany-' => "_germany-", '_uk-' => "_uk-", '_japan-' => "_japan-", '_usa-' => "_usa-", '_poland-' => "_poland-", '_sweden-' => "_sweden-", '_ussr-' => "_ussr-", '_china-' => "_china-", '_france-' => "_france-", '_italy-' => "_italy-");
    if (isset($_COOKIE['Mapalyzer_Token'])) {
        if (!copy('./dist/maps.json', './dist/' . $_COOKIE['Mapalyzer_Token'] . '.json')) {
            echo 'error_copy_failure';
        } else {
            $mapJsonData = getMapInfoFromJSON('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json');
            foreach ($cleanReplayArray as $count => $file) {
                if (!preg_match('~\b(temp|replay_last_battle|maps.txt)\b~i', $file)) {
                    if (!containsWord("Halloween", $file) && !containsWord("hw21", $file)) {
                        $inf = pathinfo($file);
                        if ($inf['extension'] === 'wotreplay') {
                            foreach ($mapJsonData as $ke2 => $mJd) {
                                if (strpos($file, $mJd['mapname'])) {
                                    $mapJsonData[$ke2]['count']++;
                                    if ($analayzeNations) {
                                        foreach ($countryNameArray as $item) {
                                            if (strpos($file, $item)) {
                                                $mapJsonData[$ke2]['country'][$item]['countryCount']++;
                                            }
                                        }
                                    }

                                }
                            }
                        } else {
                            echo 'error_extension';
                            $cleanReplayArray = array();
                            break;
                        }
                    } else {
                        echo 'error_special_event_replay';
                        $cleanReplayArray = array();
                        break;
                    }
                } else {
                    echo 'error_wrong_replay';
                    $cleanReplayArray = array();
                    break;
                }
                if ($count == count($cleanReplayArray) - 1) {
                    file_put_contents('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json', json_encode($mapJsonData));
                    echo 'success';
                    $cleanReplayArray = array();
                    exit();
                }
            }

        }
    } else {
        echo 'error_cookie';
        $cleanReplayArray = array();
        exit();
    }
} else {
    echo 'error_bad_files';
    $cleanReplayArray = array();
    exit();
}

// END OF MAIN LOGIC
