<?php
require "config.php";

$curl = curl_init();

curl_setopt($curl, CURLOPT_POST, 1);

$data = array(
    "api_token" => $API_KEY,
    "id" => $PROJECT_ID
);

curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

curl_setopt($curl, CURLOPT_URL, "https://api.poeditor.com/v2/languages/list");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$jsonResult = curl_exec($curl);

curl_close($curl);

$result = json_decode($jsonResult);

$langArray = array();
foreach ($result->result->languages as $language) {
    $langArray[$language->code] = array(
        "name" => $language->name,
        "percentage" => $language->percentage
    );
}

$langCode = $_GET["langCode"];

$color = percent2Color($langArray[$langCode]["percentage"]);

$url = "https://img.shields.io/badge/" . $langArray[$langCode]["name"] . "-" . $langArray[$langCode]["percentage"] . "-" . $color . ".svg";

echo file_get_contents_curl($url);

function file_get_contents_curl( $url ) {
    $ch = curl_init();

    curl_setopt( $ch, CURLOPT_AUTOREFERER, TRUE );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE );

    $data = curl_exec( $ch );
    curl_close( $ch );

    return $data;
}

function percent2Color($value,$brightness = 255, $max = 100,$min = 0, $thirdColorHex = '00')
{
    // Calculate first and second color (Inverse relationship)
    $first = (1-($value/$max))*$brightness;
    $second = ($value/$max)*$brightness;

    // Find the influence of the middle color (yellow if 1st and 2nd are red and green)
    $diff = abs($first-$second);
    $influence = ($brightness-$diff)/2;
    $first = intval($first + $influence);
    $second = intval($second + $influence);

    // Convert to HEX, format and return
    $firstHex = str_pad(dechex($first),2,0,STR_PAD_LEFT);
    $secondHex = str_pad(dechex($second),2,0,STR_PAD_LEFT);

    return $firstHex . $secondHex . $thirdColorHex ;
}