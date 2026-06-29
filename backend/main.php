<?php
// include database and API helper files
include("database.php");
include("api.php");

// allow cross-origin requests and set response type to JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// cache refresh interval — 24 hours
define('REFRESH_TIME', 24 * 60 * 60);

// return error JSON and exit
function send_error($message) {
    echo json_encode(["error" => $message]);
    exit;
}

// format raw OpenWeatherMap API response into a flat array
function format_weather($raw) {
    return [
        'coord'       => $raw['coord'],
        'icon'        => $raw['weather'][0]['icon'],
        'description' => $raw['weather'][0]['description'],
        'main'        => $raw['weather'][0]['main'],
        'temperature' => $raw['main']['temp'],
        'Pressure'    => $raw['main']['pressure'],
        'humidity'    => $raw['main']['humidity'],
        'windSpeed'   => $raw['wind']['speed'],
        'City'        => $raw['name'],
        'date'        => $raw['dt'],
    ];
}

// connect to database
$connection = connect_database("localhost", "root", "", "weather");

// require city parameter
if (!isset($_GET["city"])) {
    send_error("No city provided!");
}

$city = $_GET["city"];

// fetch all stored records for this city
$allData = get_weather_data($connection, $city);
$existingData = (!empty($allData)) ? $allData[count($allData) - 1] : null;

// if history flag is set, return all records and exit
if (isset($_GET["history"])) {
    echo json_encode($allData);
    exit;
}

// check if cached data exists and is still fresh
if ($existingData) {
    $dataAge = time() - ($existingData["date"] ?? 0);

    if ($dataAge <= REFRESH_TIME) {
        // return cached data
        echo json_encode($existingData);
        exit;
    }
}

// fetch fresh data from OpenWeatherMap
$newData = fetch_currentWeather_data($city);

if (!$newData) {
    send_error("Data could not be fetched!");
}

insert_weather_data($connection, $newData);
echo json_encode(format_weather($newData));
?>
