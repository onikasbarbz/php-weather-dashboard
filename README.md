# PHP Weather Dashboard

A full-stack weather dashboard built with vanilla JavaScript and PHP. Search any city to get current weather conditions, and browse a history of previously searched cities. Data is cached in a MySQL database and falls back to localStorage when you're offline.

---

## What it does

- Search any city for live weather — temperature, humidity, wind speed, and pressure
- See a dynamic weather icon that changes with conditions
- Browse weather history for any city you've searched before
- Works offline — if you've searched a city before, the last result loads from your browser cache
- Displays local date and time for the searched city

---

## Tech stack

| Layer    | Technology                      |
|----------|---------------------------------|
| Frontend | HTML, CSS, Vanilla JavaScript   |
| Backend  | PHP 8.x                         |
| Database | MySQL 8.x                       |
| API      | OpenWeatherMap (free tier)      |
| Cache    | MySQL + browser localStorage    |

---

## Project structure

```
php-weather-dashboard/
├── index.html          # Main weather view
├── past_data.html      # Weather history view
├── app.js              # Frontend logic and DOM updates
├── style.css           # Styles for the main view
├── weather.css         # Styles for the history view
├── images/             # Local weather condition icons
└── backend/
    ├── api.php         # Fetches weather from OpenWeatherMap
    ├── database.php    # MySQL connection and queries
    └── main.php        # Entry point — handles routing and caching logic
```

---

## Prerequisites

Before you start, make sure you have the following installed:

### 1. XAMPP (recommended for beginners)
Download from: https://www.apachefriends.org/download.html

Install the version that includes:
- **Apache 2.4.x**
- **PHP 8.1 or higher**
- **MySQL 8.0 or higher** (included in XAMPP as MariaDB 10.x, which works fine)

> If you're on Mac, MAMP works too: https://www.mamp.info/en/downloads/  
> MAMP includes the same stack — PHP and MySQL bundled together.

### 2. An OpenWeatherMap API key (free)
Sign up at https://openweathermap.org/api and grab a free API key. The free tier is more than enough for this project.

---

## Setup

### Step 1 — Clone the repo

```bash
git clone https://github.com/onikasbarbz/php-weather-dashboard.git
```

### Step 2 — Move it into your server root

**XAMPP (Windows):** Copy the folder to `C:/xampp/htdocs/`  
**XAMPP (Mac):** Copy the folder to `/Applications/XAMPP/htdocs/`  
**MAMP (Mac):** Copy the folder to `/Applications/MAMP/htdocs/`

So the path should look like:
```
htdocs/php-weather-dashboard/
```

### Step 3 — Add your API key

Open `backend/api.php` and replace the placeholder with your real key:

```php
define('API_KEY', 'YOUR_API_KEY_HERE');
```

### Step 4 — Create the database

1. Start XAMPP (or MAMP) and make sure both **Apache** and **MySQL** are running
2. Open your browser and go to: `http://localhost/phpmyadmin`
3. Click **New** in the left sidebar
4. Name the database `weather` and click **Create**
5. Click on the `weather` database, then go to the **SQL** tab
6. Paste and run the following:

```sql
CREATE TABLE weather_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    City VARCHAR(100),
    icon VARCHAR(50),
    description VARCHAR(100),
    main VARCHAR(50),
    temperature FLOAT,
    Pressure INT,
    humidity INT,
    windSpeed FLOAT,
    date INT
);
```

### Step 5 — Open the app

Go to: `http://localhost/php-weather-dashboard/index.html`

Search any city and the weather should load. If it does, everything is working.

---

## How it works

When you search a city, `app.js` sends the city name to `backend/main.php`. The PHP backend checks the MySQL database first — if it has data for that city that's less than 24 hours old, it returns that. Otherwise it hits the OpenWeatherMap API for fresh data, saves it to the database, and returns it to the frontend.

The history page (`past_data.html`) queries the same backend with a `?history=true` flag, which returns all stored records for a city. If you go offline, it falls back to whatever was last saved in your browser's localStorage.

---

## Common issues

**Weather isn't loading**  
- Make sure Apache and MySQL are both running in XAMPP/MAMP
- Double-check your API key in `backend/api.php`
- New OpenWeatherMap API keys can take up to 2 hours to activate after signup

**Database connection error**  
- Make sure you created the `weather` database in phpMyAdmin
- The default connection in `backend/database.php` uses `root` with no password — this matches XAMPP defaults. If you've set a MySQL password, update that file accordingly.

**Page loads but city isn't found**  
- Try a major city first (e.g. London, Berlin, Tokyo) to confirm the API key is working
- OpenWeatherMap uses English city names — check spelling

---

## Notes

- Weather data refreshes every 24 hours per city. Searching the same city within 24 hours returns the cached result from the database.
- The history view shows all previously cached entries for a city, not live forecast data.
- Pressure is displayed in hPa (hectopascals), which is what OpenWeatherMap returns.
