<?php
function uniqidReal($lenght = 25)
{
    // uniqid gives 13 chars, but you could adjust it to your needs.
    if (function_exists("random_bytes")) {
        $bytes = random_bytes(ceil($lenght / 2));
    } elseif (function_exists("openssl_random_pseudo_bytes")) {
        $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
    } else {
        throw new Exception("no cryptographically secure random function available");
    }
    return substr(bin2hex($bytes), 0, $lenght);
}

$uniqueCookieToken = uniqidReal();
//$failure = '';
$total = 0;
function getMapInfoFromJSON($file_path)
{
    $string = file_get_contents($file_path);
    return json_decode($string, true);
}

function containsWord($needle, $haystack)
{
    return strpos($haystack, $needle) !== false;
}


/*function updateMapStatsInJSON()
{
    $analayzeNations = false;
    $countryNameArray = array('_czech-' => "_czech-",'_germany-' => "_germany-",'_uk-' => "_uk-",'_japan-' => "_japan-",'_usa-' => "_usa-",'_poland-' => "_poland-",'_sweden-' => "_sweden-",'_ussr-' => "_ussr-",'_china-' => "_china-",'_france-' => "_france-",'_italy-' => "_italy-");
    if (isset($_COOKIE['Mapalyzer_Token'])) {
        if (!copy('./dist/maps.json', './dist/' . $_COOKIE['Mapalyzer_Token'] . '.json')) {
            echo 'An error occured! Try again.';
        } else {
            if(isset($_POST['analyzeNations'])){
                $analayzeNations = true;
            }
            $mapJsonData = getMapInfoFromJSON('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json');
            foreach ($_POST['files'] as $file) {
                if (!preg_match('~\b(temp|replay_last_battle|maps.txt)\b~i', $file)) {
                    if (!containsWord("Halloween", $file) && !containsWord("hw21", $file)) {
                        $inf = pathinfo($file);
                        if ($inf['extension'] == 'wotreplay') {
                            foreach ($mapJsonData as $ke2 => $mJd) {
                                if (strpos($file, $mJd['mapname'])) {
                                    $mapJsonData[$ke2]['count']++;
                                    if($analayzeNations){
                                        foreach ($countryNameArray as $item) {
                                            if(strpos($file, $item)){
                                                $mapJsonData[$ke2]['country'][$item]['countryCount']++;
                                            }
                                        }
                                    }

                                }
                            }
                            file_put_contents('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json', json_encode($mapJsonData));
                        } else {
                            echo 'An error occured! Try again.';
                        }
                    }
                }
            }
        }
    } else {
        echo 'Try again.';
    }
}
function resetMapStats()
{
    $countryNameArray = array('_czech-' => "_czech-",'_germany-' => "_germany-",'_uk-' => "_uk-",'_japan-' => "_japan-",'_usa-' => "_usa-",'_poland-' => "_poland-",'_sweden-' => "_sweden-",'_ussr-' => "_ussr-",'_china-' => "_china-",'_france-' => "_france-",'_italy-' => "_italy-");
    $string = file_get_contents('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json');
    $json = json_decode($string, true);
    foreach ($json as $key => $items) {
        $json[$key]['count'] = 0;
        foreach ($countryNameArray as $item) {
            $json[$key]['country'][$item]['countryCount'] = 0;
        }
        file_put_contents('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json', json_encode($json));
    }
}

if (!empty($_GET['reset'])) {
    if (isset($_COOKIE['Mapalyzer_Token']) && file_exists('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json')) {
        resetMapStats();
        $failure = "<div class='alert alert-dismissible alert-info'>
  <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
  <h4 class='alert-heading'>Your stats have been reset!</h4>
  <p class='mb-0'>You can now analyze your stats again.</p>
</div>";
    } else {
        echo 'Try again.';
    }

}

if (isset($_POST['selFile'])) {
    foreach ($_POST['files'] as $key => $filee) {
        if ($key >= 999) {
            $failure = "<div class='alert alert-dismissible alert-danger'>
  <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
  <h4 class='alert-heading'>You exceeded number of replay files!</h4>
  <p class='mb-0'>Maximum number of files allowed: 1000</p>
</div>";
        }
    }
    if (isset($_COOKIE['Mapalyzer_Token']) && file_exists('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json')) {
        resetMapStats();
    }
    updateMapStatsInJSON();
    $failure = "<div class='alert alert-dismissible alert-success'>
  <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
  <h4 class='alert-heading'>Your stats have been analyzed!</h4>
  <p class='mb-0'>Refresh the page and see for yourself down below.</p>
  <p class='mb-0'><a href='./' class='btn btn-primary text-decoration-none'>Refresh</a></p>
</div>";
}

if (!empty($_GET['removedata'])) {
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
    }
    header("Location: ./?removed");
}

if (isset($_GET['removed'])) {
    $failure = "<div class='alert alert-dismissible alert-success'>
  <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
  <h4 class='alert-heading'>Your data have been removed!</h4>
  <p class='mb-0'>Cookies stored, files created are all gone.</p>
  <p class='mb-0'><button type='button' class='btn btn-outline-warning text-decoration-none' data-bs-dismiss='alert'>Ok</button></p>
</div>";
}

if (isset($_GET['?removed-analyze-again'])) {
    $failure = "<div class='alert alert-dismissible alert-success'>
  <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
  <h4 class='alert-heading'>Your data have been removed!</h4>
  <p class='mb-0'>Due to a change in how replay data is stored, your old data have been removed. Please analyze your replays again.</p>
  <p class='mb-0'><button type='button' class='btn btn-outline-warning text-decoration-none' data-bs-dismiss='alert'>Ok</button></p>
</div>";
}
*/
function analyzeAllData()
{
    if (file_exists('./dist/allmaps-count.json')) {
        unlink('./dist/allmaps-count.json');
        copy('./dist/allmaps.json', './dist/allmaps-count.json');
        $string = file_get_contents('./dist/allmaps-count.json');
        $json = json_decode($string, true);
        foreach ($json as $key => $items) {
            $json[$key]['count'] = 0;
        }
        file_put_contents('./dist/allmaps-count.json', json_encode($json));
    }
    $fileNames = array();
    $directory = './dist/';
    $scanned_directory = array_diff(scandir($directory), array('..', '.', 'maps.json', 'maps.txt', 'allmaps.json', 'allmaps-count.json'));
    foreach ($scanned_directory as $diritem) {
        $inf = pathinfo($diritem);
        if ($inf['extension'] == 'json') {
            array_push($fileNames, $inf['filename']);
        }
    }
    $jsonDataAllMapsCount = getMapInfoFromJSON('./dist/allmaps-count.json');
    foreach ($fileNames as $key2 => $fName) {
        $jsonData = getMapInfoFromJSON('./dist/' . $fName . '.json');
        foreach ($jsonDataAllMapsCount as $key1 => $firstData) {
            foreach ($jsonData as $key => $insertData) {
                if (containsWord($jsonDataAllMapsCount[$key1]['realname'], $insertData['realname'])) {
                    $jsonDataAllMapsCount[$key1]['count'] += $insertData['count'];
                }
            }
        }
    }
    file_put_contents('./dist/allmaps-count.json', json_encode($jsonDataAllMapsCount));
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta property="og:description" content="Map analyzer tool for World of Tanks">
    <meta property="og:title" content="Mapalyzer - World of Tanks Map Analyzer Tool (0.1.1a)">
    <meta property="og:image" content="https://emte.fun/mapalyzerlogo.png">
    <meta name="twitter:image" content="https://emte.fun/mapalyzerlogo.png">
    <link rel='canonical' href='https://emte.fun/'>
    <meta property='og:url' content='https://emte.fun/'>
    <meta name='twitter:url' content='https://emte.fun/'>
    <meta property='og:site_name' content='Mapalyzer for WoT'>
    <meta property='og:type' content='website'>
    <meta name="keywords" content="WoT, analyze, map">
    <meta name="author" content="ra1g">
    <title>Mapalyzer - World of Tanks Map Analyzer - 0.1.1a</title>
    <link rel="apple-touch-icon" sizes="57x57" href="./ico/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="./ico/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="./ico/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="./ico/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="./ico/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="./ico/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="./ico/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="./ico/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="./ico/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="./ico/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./ico/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="./ico/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./ico/favicon-16x16.png">
    <link rel="manifest" href="./ico/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="./ico/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link href="./dist/darkly.min.css" rel="stylesheet"/>
    <link href="./dist/tabler-flags.css" rel="stylesheet"/>
    <link href="./dist/datatables.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-sm navbar-light bg-warning" id="top">
    <div class="container-fluid">

        <a class="navbar-brand" href="#">Mapalyzer</a>
        <div class="" id="navbarColor03">
            <ul class="navbar-nav me-auto p-1">
                <li class="nav-item p-1">
                    <a class="nav-link btn btn-dark btn-sm text-warning fs-5" href="./">Refresh Mapalyzer</a>
                </li>
                <?php if (isset($_COOKIE['Mapalyzer_Token']) && file_exists('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json')) { ?>
                    <li class="nav-item p-1">
                        <button class="nav-link btn btn-outline-dark btn-sm text-black fs-5 getCSV">Export to CSV</button>
                    </li>
                    <li class="nav-item p-1">
                        <button class="nav-link btn btn-outline-dark btn-sm text-black fs-5 getRedditTable">Export to Reddit Table</button>
                    </li>
                    <li class="nav-item p-1">
                        <button class="nav-link btn btn-outline-dark btn-sm text-black fs-5" id="resetStatsBtn">Reset
                            Stats</button>
                    </li>
                    <li class="nav-item p-1">
                        <button class="nav-link btn btn-danger btn-sm text-black border-dark p-1"
                                id="removeDataBtn">Remove all <br>of my data</button>
                    </li>
                <?php } ?>
                <?php if(isset($_COOKIE['Mapalyzer_Token']) && !file_exists('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json')){ ?>
                    <li class="nav-item p-1">
                        <button class="nav-link btn btn-danger btn-sm text-black border-dark p-1"
                                id="removeDataBtn">Remove all <br>of my data</button>
                    </li>
                <?php } ?>
                <li class="nav-item p-1">
                    <a class="nav-link btn btn-outline-dark btn-sm text-black fs-5" href="?allmaps">30k+ battles analysis</a>
                </li>
                <li class="nav-item p-1">
                    <a class="nav-link btn btn-outline-dark btn-sm text-black fs-5" href="Mapalyzer.zip">Download PC
                        App</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<main>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-6 d-grid">
                        <div class='alert alert-dismissible alert-info my-2'>
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                            <p class='mb-0'>[NEW] Check out your stats in a bar chart. Click on the 'Show results in a chart' button.</p>
                            <hr class="p-0 m-2">
                            <p class='mb-0'>[NEW] Export analyzed data to CSV or Reddit table format by clicking on the respective buttons.</p>
                        </div>
                    </div>
                    <div class="col-6 d-grid">
                        <div class='alert alert-dismissible alert-primary my-2'>
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                            <p class='mb-0'>[NEW] Windows app has been released. Provides limited functionality compared to this site. Click
                                on the top right button to start downloading. After that, extract the zip file and run the
                                executable.</p>
                            <hr class="p-0 m-2">
                            <p class='mb-0'>[INFO] Replays from Mirny game event are now excluded. (Website & Windows App)</p>
                        </div>
                    </div>
                </div>
                <div id="core" class="card p-2 flex-wrap mt-2">
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <h3 class="h3">Your WoT map rotation statistics</h3>
                            <p>> Navigate to your World of Tanks replay folder and select it. <br>> Let Mapalyzer do its magic.</p>
                            <form method="post" name="formName" enctype=""
                                  class="form-control bg-transparent border-warning" id="replayUploadForm">
                                <label for="files" class="form-label text-warning">Select WoT replay files:</label>
                                <input type="file" id="files" name="files" class="form-control border-warning"
                                       webkitdirectory multiple/>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div id="core" class="card p-2 flex-wrap mt-2">
                    <div class="row justify-content-center">
                        <?php if (isset($_COOKIE['Mapalyzer_Token']) && file_exists('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json') && !isset($_GET['allmaps'])) { ?>
                        <div class="">
                            <ul class="nav nav-pills justify-content-center" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="map-table-tab" data-bs-toggle="pill" data-bs-target="#map-table" type="button" role="tab" aria-controls="map-table" aria-selected="true">Show results in a table</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link showchart" id="map-chart-tab" data-bs-toggle="pill" data-bs-target="#map-chart" type="button" role="tab" aria-controls="map-chart" aria-selected="false">Show results in a chart</button>
                                </li>
                            </ul>
                        </div>
                        <?php } ?>
                        <div class="col-lg-12">
                            <?php if(isset($_GET['allmaps'])) {
                                analyzeAllData();
                                $countT = 0;
                                $allMapData = getMapInfoFromJSON('./dist/allmaps-count.json');
                                foreach ($allMapData as $countTotal) {
                                    $countT += $countTotal['count'];
                                }
                                ?>
                                <h4 class="h4 my-4 text-center">Number of battles analyzed from Mapalyzer users: <span
                                            class="text-warning"><?= $countT; ?></span></h4>

                                <table class="table table-responsive-sm table-striped table-hover align-middle" id="allMapsTable" data-order='[[ 2, "desc" ]]' data-page-length='5'>
                                    <thead>
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col">Map name</th>
                                        <th scope="col"># of games</th>
                                        <th scope="col">% of all battles</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($allMapData as $key => $allMd) {
                                        ?>
                                        <tr>
                                            <th><img src="./dist/<?= $allMd['mapicon']; ?>" class="img-thumbnail"
                                                     height="150" width="150"></th>
                                            <th scope="row"><?= $allMd['realname']; ?></th>
                                            <td><?= $allMd['count']; ?></td>
                                            <td class="text-warning">~<?= $countT == 0 ? '0' : round($allMd['count'] / $countT, 4) * 100; ?>%</td>
                                        </tr>
                                        <?php
                                    } ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                            <hr class="mt-2 bg-warning">
                            <?php if (isset($_COOKIE['Mapalyzer_Token']) && file_exists('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json')) {
                                $mapJsonData = getMapInfoFromJSON('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json');
                                foreach ($mapJsonData as $key => $mDfS) {
                                    $total += $mDfS['count'];
                                }
                                ?>
                                <h4 class="h4 my-4 text-center">Number of battles analyzed: <span
                                            class="text-warning" id="myBattles"><?= $total; ?></span></h4>
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="map-table" role="tabpanel" aria-labelledby="map-table-tab">
                                        <table class="table table-responsive-sm table-striped table-hover align-middle " id="analyzedMapTable" data-order='[[ 2, "desc" ]]' data-page-length='5'>
                                            <thead>
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col">Map name</th>
                                                <th scope="col"># of games</th>
                                                <th scope="col">% of all battles</th>
                                                <th scope="col" class="text-center">Battles played on a map with each nation</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            foreach ($mapJsonData as $key => $mDfS) {
                                                ?>
                                                <tr>
                                                    <th><img src="./dist/<?= $mDfS['mapicon']; ?>" class="img-thumbnail"
                                                             height="150" width="150"></th>
                                                    <th scope="row"><?= $mDfS['realname']; ?></th>
                                                    <td><?= $mDfS['count']; ?></td>
                                                    <td class="text-warning">
                                                        ~<?= $total == 0 ? '0' : round($mDfS['count'] / $total, 4) * 100; ?>%
                                                    </td>
                                                    <td colspan="11">
                                                        <table class="table table-sm table-responsive-sm table-striped">
                                                            <thead>
                                                            <tr>
                                                                <?php if(isset($mDfS['country'])){ ?>
                                                                <th scope="col"><div class="flag flag-country-de"></div></th>
                                                                <th scope="col"><div class="flag flag-country-ru"></div></th>
                                                                <th scope="col"><div class="flag flag-country-us"></div></th>
                                                                <th scope="col"><div class="flag flag-country-fr"></div></th>
                                                                <th scope="col"><div class="flag flag-country-gb"></div></th>
                                                                <th scope="col"><div class="flag flag-country-cn"></div></th>
                                                                <th scope="col"><div class="flag flag-country-jp"></div></th>
                                                                <th scope="col"><div class="flag flag-country-cz"></div></th>
                                                                <th scope="col"><div class="flag flag-country-pl"></div></th>
                                                                <th scope="col"><div class="flag flag-country-se"></div></th>
                                                                <th scope="col"><div class="flag flag-country-it"></div></th>
                                                                <?php } else { ?>
                                                                    <th scope="col">Old data detected.</th>
                                                                <?php } ?>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <?php if(isset($mDfS['country'])){ ?>
                                                                <td><?= $mDfS['country']['_germany-']['countryCount']; ?><br><span class="small text-warning">(~<?= $mDfS['count'] == 0 ? '0' : round($mDfS['country']['_germany-']['countryCount'] / $mDfS['count'], 4) * 100; ?>%)</span></td>
                                                                <td><?= $mDfS['country']['_ussr-']['countryCount']; ?><br><span class="small text-warning">(~<?= $mDfS['count'] == 0 ? '0' : round($mDfS['country']['_ussr-']['countryCount'] / $mDfS['count'], 4) * 100; ?>%)</span></td>
                                                                <td><?= $mDfS['country']['_usa-']['countryCount']; ?><br><span class="small text-warning">(~<?= $mDfS['count'] == 0 ? '0' : round($mDfS['country']['_usa-']['countryCount'] / $mDfS['count'], 4) * 100; ?>%)</span></td>
                                                                <td><?= $mDfS['country']['_france-']['countryCount']; ?><br><span class="small text-warning">(~<?= $mDfS['count'] == 0 ? '0' : round($mDfS['country']['_france-']['countryCount'] / $mDfS['count'], 4) * 100; ?>%)</span></td>
                                                                <td><?= $mDfS['country']['_uk-']['countryCount']; ?><br><span class="small text-warning">(~<?= $mDfS['count'] == 0 ? '0' : round($mDfS['country']['_uk-']['countryCount'] / $mDfS['count'], 4) * 100; ?>%)</span></td>
                                                                <td><?= $mDfS['country']['_china-']['countryCount']; ?><br><span class="small text-warning">(~<?= $mDfS['count'] == 0 ? '0' : round($mDfS['country']['_china-']['countryCount'] / $mDfS['count'], 4) * 100; ?>%)</span></td>
                                                                <td><?= $mDfS['country']['_japan-']['countryCount']; ?><br><span class="small text-warning">(~<?= $mDfS['count'] == 0 ? '0' : round($mDfS['country']['_japan-']['countryCount'] / $mDfS['count'], 4) * 100; ?>%)</span></td>
                                                                <td><?= $mDfS['country']['_czech-']['countryCount']; ?><br><span class="small text-warning">(~<?= $mDfS['count'] == 0 ? '0' : round($mDfS['country']['_czech-']['countryCount'] / $mDfS['count'], 4) * 100; ?>%)</span></td>
                                                                <td><?= $mDfS['country']['_poland-']['countryCount']; ?><br><span class="small text-warning">(~<?= $mDfS['count'] == 0 ? '0' : round($mDfS['country']['_poland-']['countryCount'] / $mDfS['count'], 4) * 100; ?>%)</span></td>
                                                                <td><?= $mDfS['country']['_sweden-']['countryCount']; ?><br><span class="small text-warning">(~<?= $mDfS['count'] == 0 ? '0' : round($mDfS['country']['_sweden-']['countryCount'] / $mDfS['count'], 4) * 100; ?>%)</span></td>
                                                                <td><?= $mDfS['country']['_italy-']['countryCount']; ?><br><span class="small text-warning">(~<?= $mDfS['count'] == 0 ? '0' : round($mDfS['country']['_italy-']['countryCount'] / $mDfS['count'], 4) * 100; ?>%)</span></td>
                                                                <?php } else { ?>
                                                                    <td>Analyze your replays again to see how many battles on each map you have with each nation.</td>
                                                                <?php } ?>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                        <?php } ?>
                                    </div>
                                    <div class="tab-pane fade" id="map-chart" role="tabpanel" aria-labelledby="map-chart-tab">
                                        <canvas id="myChart" width="90" height="90"></canvas>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
        <a class="btn btn-warning btn-sm p-2 mt-2 text-black" href="#top">Back to top &#11014;</a>
        </div>
    </div>
    <hr>
    <p class="small text-center text-warning">RA1G.eu - 2021 - (Mapalyzer alpha 0.1.1a)</p>
    <p class="small text-center text-muted">Map images are a property of <a
                href="https://wargaming.net">Wargaming.net</a> and are used here only for informational purposes.
        Mapalyzer is not affiliated with <a href="https://wargaming.net">Wargaming.net</a>.</p>
    <p class="small text-center text-muted"><img src="./ico/android-icon-96x96.png"></p>
    <?php include_once("dist/cookie_modal.php"); ?>
</main>
<script src="./dist/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.6.0/chart.min.js"
        integrity="sha512-GMGzUEevhWh8Tc/njS0bDpwgxdCJLQBWG3Z2Ct+JGOpVnEmjvNx6ts4v6A2XJf1HOrtOsfhv3hBKpK9kE5z8AQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="dist/cookiealert.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.10/dist/sweetalert2.all.min.js"></script>
<script>
    $(document).ready( function () {
        $('table#analyzedMapTable').DataTable({
            'columnDefs': [ {
                'targets': [0,1,3,4],
                'orderable': false,

            }]
        });
        $('table#allMapsTable').DataTable({
            'columnDefs': [ {
                'targets': [0,1,3],
                'orderable': false,

            }]
        });
    });
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
<script>
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/" + ";SameSite=Strict; Secure;";
    }

    function getCookie(c_name) {
        var c_value = document.cookie,
            c_start = c_value.indexOf(" " + c_name + "=");
        if (c_start == -1) c_start = c_value.indexOf(c_name + "=");
        if (c_start == -1) {
            c_value = null;
        } else {
            c_start = c_value.indexOf("=", c_start) + 1;
            var c_end = c_value.indexOf(";", c_start);
            if (c_end == -1) {
                c_end = c_value.length;
            }
            c_value = unescape(c_value.substring(c_start, c_end));
        }
        return c_value;
    }

    $(document).ready(function () {
        $('.acceptcookies').on('click', function () {
            let mapalyzer = getCookie("Mapalyzer_Token");
            if (!mapalyzer) {
                setCookie("Mapalyzer_Token", "<?= $uniqueCookieToken ?>", 60);
            }
        });
    });

</script>
<script>
    $("input#files").change(function(e) {
        e.preventDefault();
        let files = e.target.files;
        let fileArray = [];
        for (let i=0; i<files.length; i++) {
            fileArray.push({id:i, filename: files[i].webkitRelativePath});
        }
        $.ajax({
            url: 'analyze_replays.php',
            type: 'POST',
            data: {data:JSON.stringify(fileArray)},
            cache: false,
        })
            .done(function (data) {
                console.log(data);
                if(data=='success'){
                    Swal.fire({
                        icon: 'success',
                        title: 'It is done!',
                        text: 'Your battles have been analyzed! Press OK to continue.',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'OK',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        } else {
                            window.location.reload();
                        }
                    })
                } else if(data=='error_extension'){
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Folder contains wrong files. Please select folder with some .wotreplay files and try again.',
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: 'Try Again',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    })
                } else if(data=='error_special_event_replay'){
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Your folder contains only replays from special events. These do not count.',
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: 'Try Again',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    })
                } else if(data=='error_wrong_replay'){
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Your folder contains only temporary replay files. These can not be analyzed.',
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: 'Try Again',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    })
                } else if(data=='error_cookie'){
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Please accept cookies. If this error persists, please delete all your cookies and try again.',
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: 'Try Again',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    })
                } else if(data=='error_copy_failure'){
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Mapalyzer error: file could not be created. Try again or contact the administrator.',
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: 'Try Again',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    })
                } else if(data=='error_bad_files'){
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'No replays files could be found in selected folder.',
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: 'Try Again',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    })
                } else if(data=='system_error'){
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'System error! Try again now or later.',
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: 'Try Again',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    })
                }
                $("input#files").val('');
            });
    });
</script>
<script>
    $("button#resetStatsBtn").click(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'reset_stats.php',
            type: 'POST',
            data: "resetStats",
            cache: false,
        })
            .done(function (data) {
                console.log(data);
                if(data=='resetSuccess'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Stats reset!',
                        text: 'Your statistics have been reset. Press OK to continue',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'OK',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        } else {
                            window.location.reload();
                        }
                    })
                }
            });
    });
</script>
<script>
    $("button#removeDataBtn").click(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'remove_data.php',
            type: 'POST',
            data: "removeData",
            cache: false,
        })
            .done(function (data) {
                console.log(data);
                if(data=='removedDataSuccess'){
                    Swal.fire({
                        icon: 'success',
                        title: 'All data removed!',
                        text: 'All of your data have been removed from this website.',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'OK',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        } else {
                            window.location.reload();
                        }
                    })
                } else if(data=='removedDataNoCookie'){
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Could not remove any data because you are missing cookie Mapalyzer_Token.',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'OK',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        } else {
                            window.location.reload();
                        }
                    })
                }
            });
    });
</script>
<?php if(file_exists('./dist/' . $_COOKIE['Mapalyzer_Token'] . '.json')) {?>
<script type="text/javascript">
    $('.showchart').on('click', function () {
        $(document).ready(function () {
            let mapNames = [];
            let numBattles = [];
            let keyValue = [];
            $.ajax({
                type: 'GET',
                url: './dist/<?= $_COOKIE['Mapalyzer_Token'] ?>.json',
                dataType: 'json',
                success: function (field) {
                    for (let i = 0; i < field.length; i++) {
                        keyValue.push({realname: field[i].realname, count: field[i].count});
                    }
                    let sortedKeyValue = keyValue.sort(function (a, b) {
                        return b.count - a.count
                    });
                    for (let i = 0; i < sortedKeyValue.length; i++) {
                        mapNames.push(sortedKeyValue[i].realname);
                        numBattles.push(sortedKeyValue[i].count);
                    }
                    var ctx = document.getElementById("myChart").getContext('2d');
                    const myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: mapNames,
                            datasets: [{
                                label: 'Battles',
                                data: numBattles,
                                fill: false,
                                backgroundColor: [
                                    '#2f5748',
                                    '#566296'

                                ],
                                borderColor: 'black',
                                borderWidth: 2,
                            },
                            ]

                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            indexAxis: 'y',
                        }
                    });
                }
            });
        })
    });
</script>
    <script>
        $('.getCSV').on('click', function () {
            $(document).ready(function () {
                exportCSVFile(headers, itemsFormatted, 'mapalyzer_csv_export');
            })
        });
        let itemsNotFormatted = <?= json_encode($mapJsonData); ?>;
        let itemsFormatted = [];

        // format the data
        itemsNotFormatted.forEach((item) => {
            itemsFormatted.push({
                realname: item.realname, // remove commas to avoid errors,
                count: item.count
            });
        });
        function convertToCSV(objArray) {
            var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
            var str = '';

            for (var i = 0; i < array.length; i++) {
                var line = '';
                for (var index in array[i]) {
                    if (line != '') line += ','

                    line += array[i][index];
                }

                str += line + '\r\n';
            }

            return str;
        }

        function exportCSVFile(headers, items, fileTitle) {
            if (headers) {
                items.unshift(headers);
            }
            // Convert Object to JSON
            var jsonObject = JSON.stringify(items);

            var csv = this.convertToCSV(jsonObject);

            var exportedFilename = fileTitle + '.csv' || 'export.csv';

            var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            if (navigator.msSaveBlob) { // IE 10+
                navigator.msSaveBlob(blob, exportedFilename);
            } else {
                var link = document.createElement("a");
                if (link.download !== undefined) { // feature detection
                    // Browsers that support HTML5 download attribute
                    var url = URL.createObjectURL(blob);
                    link.setAttribute("href", url);
                    link.setAttribute("download", exportedFilename);
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            }
        }
        let headers = { // remove commas to avoid errors
            realname: "Map name",
            count: "Number of battles"
        };
    </script>
    <script>
        $('.getRedditTable').on('click', function () {
            $(document).ready(function () {
                exportRedditTable(headersRedditTable, itemsFormattedRedditTable, 'mapalyzer_reddit_table_export');
            })
        });
        let itemsNotFormattedRedditTable = <?= json_encode($mapJsonData); ?>;
        let itemsFormattedRedditTable = [];

        // format the data
        itemsNotFormattedRedditTable.forEach((item) => {
            itemsFormattedRedditTable.push({
                realname: item.realname, // remove commas to avoid errors,
                count: item.count
            });
        });
        function convertToRedditTable(objArray) {
            var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
            var str = '';

            for (var i = 0; i < array.length; i++) {
                var line = '';
                for (var index in array[i]) {
                    if (line != '') line += '|'

                    line += array[i][index];
                }

                str += line + '\r\n';
            }

            return str;
        }

        function exportRedditTable(headers, items, fileTitle) {
            let totalBattles = {leftColumn: 'Total battles:', rightColumn: <?= $total; ?>}
            let alignRow = {alignFirst: ':--', alignLast: ':--'}
            items.unshift(totalBattles);
            items.unshift(alignRow);
            if (headers) {
                items.unshift(headers);
            }
            // Convert Object to JSON
            var jsonObject = JSON.stringify(items);

            var redditTable = this.convertToRedditTable(jsonObject);

            var exportedFilename = fileTitle + '.txt' || 'mapalyzer_reddit_table_export.txt';

            var blob = new Blob([redditTable], { type: 'text/plain;charset=utf-8;' });
            if (navigator.msSaveBlob) { // IE 10+
                navigator.msSaveBlob(blob, exportedFilename);
            } else {
                var link = document.createElement("a");
                if (link.download !== undefined) { // feature detection
                    // Browsers that support HTML5 download attribute
                    var url = URL.createObjectURL(blob);
                    link.setAttribute("href", url);
                    link.setAttribute("download", exportedFilename);
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            }
        }
        let headersRedditTable = { // remove commas to avoid errors
            realname: "Map name",
            count: "Number of battles"
        };
    </script>
<?php } ?>
</body>
</html>