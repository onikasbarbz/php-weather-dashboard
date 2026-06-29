<?php

/**
 * Handles all DB operations — connect, fetch, insert.
 * Uses prepared statements everywhere to avoid SQL injection.
 */


/**
 * Connect to the MySQL database.
 * Returns the connection object, or null if it fails.
 */
function connect_database($server, $username, $password, $db) {
    try {
        $connection = new mysqli($server, $username, $password, $db);

        if ($connection->connect_errno) {
            echo json_encode(["error" => "Database connection failed!"]);
            return null;
        }

        return $connection;

    } catch (Exception $e) {
        return null;
    }
}


/**
 * Get all weather records for a city, oldest to newest.
 * Used to check cache and return history.
 */
function get_weather_data($connection, $city) {
    try {
        $stmt = $connection->prepare("SELECT * FROM weathers WHERE City = ? ORDER BY date ASC");
        $stmt->bind_param("s", $city);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : null;

    } catch (Exception $e) {
        return null;
    }
}


/**
 * Check if a record already exists for a city + timestamp.
 * Used before inserting to avoid duplicates.
 */
function get_weather_data_with_timestamp($connection, $city, $timestamp) {
    try {
        $stmt = $connection->prepare("SELECT * FROM weathers WHERE City = ? AND date = ?");
        $stmt->bind_param("si", $city, $timestamp);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : null;

    } catch (Exception $e) {
        return null;
    }
}


/**
 * Insert a new weather record from the raw API response.
 * Skips if the same city + timestamp already exists.
 * 
 * bind_param type string "isddddss":
 *   i = date, s = city, d = temp/humidity/wind/pressure, s = icon/description
 */
function insert_weather_data($connection, $data) {
    try {
        $date        = $data["dt"];
        $city        = $data["name"];
        $temp        = $data["main"]["temp"];
        $humidity    = $data["main"]["humidity"];
        $wind        = $data["wind"]["speed"];
        $pressure    = $data["main"]["pressure"];
        $icon        = $data["weather"][0]["icon"];
        $description = $data["weather"][0]["description"];

        // don't insert if we already have this exact record
        $existing = get_weather_data_with_timestamp($connection, $city, $date);
        if (!empty($existing)) return false;

        $stmt = $connection->prepare("
            INSERT INTO weathers (date, City, temperature, humidity, windSpeed, Pressure, icon, description)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isddddss", $date, $city, $temp, $humidity, $wind, $pressure, $icon, $description);

        return $stmt->execute();

    } catch (Exception $e) {
        // don't expose DB errors to the client
        return null;
    }
}
?>
