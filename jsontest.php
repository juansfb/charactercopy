<?php
function testest(){

    $name = "Neustar";
    $server = "Mandokir";
    $region = "EU";
    $zone = "1002"; //BWL
    $encounter = "611"; //VAEL
    $api_key = "key";

    $url = "https://classic.warcraftlogs.com/v1/parses/character/{$name}/{$server}/{$region}?zone={$zone}&encounter={$encounter}&api_key={$api_key}";

    ini_set("allow_url_fopen", 1);
    $json = file_get_contents($url);

//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($ch, CURLOPT_URL,$url);
//    $json = curl_exec($ch);
//    curl_close($ch);

    $array = json_decode($json, true);

    for($i=0;$i<18;$i++){
        print_r($array[0][gear][$i][id]);
        printf(PHP_EOL);
    }

}

?>