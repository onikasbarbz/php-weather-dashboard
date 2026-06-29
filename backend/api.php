<?php

/**
 * Handles fetching current weather from OpenWeatherMap API.
 */

define('API_KEY', '7e5a8c646fc24d483fe02d2982677ed2');
define('API_BASE', 'https://api.openweathermap.org/data/2.5/weather?units=metric');

/**
 * Fetch current weather for a city from OpenWeatherMap.
 * Returns decoded response as an array, or null on failure.
 */
function fetch_currentWeather_data($city) {
    try {
        $url = API_BASE . "&q=" . urlencode($city) . "&appid=" . API_KEY;

        // @ suppresses warnings if the request fails (e.g. no internet)
        $response = @file_get_contents($url);

        return $response ? json_decode($response, true) : null;

    } catch (Exception $e) {
        return null;
    }
}
?>
