<?php

/**
 * Entry point for all weather data requests.
 * 
 * Accepts: ?city=CityName
 * Optional: ?history=true (returns all stored records for that city)
 * 
 * Flow: check DB cache → return if fresh → otherwise fetch from API → save → return
 */

include("database.php");
include("api.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// how long before cached data is considered stale
define('REFRESH_TIME', 24 * 60 * 60);

// helper to send an error response and stop execution
function send_error($message) {
    echo json_encode(["error" => $message]);
    exit;
}

// flatten the nested API response into what the frontend expects
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

$connection = connect_database("localhost", "root", "", "weather");

if (!isset($_GET["city"])) {
    send_error("No city provided!");
}

$city = $_GET["city"];

// grab everything we have stored for this city
$allData = get_weather_data($connection, $city);
$existingData = !empty($allData) ? $allData[count($allData) - 1] : null;

// history mode — return all records without any cache logic
if (isset($_GET["history"])) {
    echo json_encode($allData);
    exit;
}

// if we have data and it's less than 24hrs old, return it as-is
if ($existingData) {
    $dataAge = time() - ($existingData["date"] ?? 0);
    if ($dataAge <= REFRESH_TIME) {
        echo json_encode($existingData);
        exit;
    }
}

// cache is stale or empty — hit the API for fresh data
$newData = fetch_currentWeather_data($city);

if (!$newData) {
    send_error("Data could not be fetched!");
}

insert_weather_data($connection, $newData);
echo json_encode(format_weather($newData));
?>
