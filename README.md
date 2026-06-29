# php-weather-dashboard

A full-stack weather dashboard that shows real-time weather conditions and a 7-day historical forecast for any city worldwide. Built with a vanilla JS frontend and a PHP backend that proxies requests to the OpenWeatherMap API.

## Features

- Search any city to get current weather
- Displays temperature, humidity, wind speed, and pressure
- Historical weather view for the past 7 days
- Offline fallback using localStorage — last fetched data persists without internet
- Live local time display for the searched city

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML, CSS, JavaScript (Vanilla) |
| Backend | PHP |
| API | OpenWeatherMap API |
| Storage | Browser localStorage (offline cache) |

## Project Structure
php-weather-dashboard/

├── index.html          # Main weather view

├── past_data.html      # 7-day history view

├── style.css           # Shared styles

├── app.js              # Frontend logic, API calls, DOM updates

├── backend/

│   └── main.php        # PHP proxy — handles API key, fetches weather + history

└── images/             # Weather condition icons

## Getting Started

### Prerequisites

- PHP 7.4+ (with a local server — XAMPP, MAMP, or Laravel Herd)
- An [OpenWeatherMap API key](https://openweathermap.org/api) (free tier works)

### Setup

1. Clone the repo:
```bash
   git clone https://github.com/onikasbarbz/php-weather-dashboard.git
   cd php-weather-dashboard
```

2. Add your API key in `backend/main.php`:
```php
   $api_key = "YOUR_API_KEY_HERE";
```

3. Move the project folder to your local server root (e.g. `htdocs` for XAMPP).

4. Start your PHP server and open:
   http://localhost/php-weather-dashboard/index.html
## How It Works

The frontend sends a city name to `backend/main.php`, which fetches data from the OpenWeatherMap API and returns it as JSON. For the history page, the backend requests the 7-day forecast endpoint. If the browser goes offline, `past_data.html` falls back to the last cached result stored in localStorage.

## API Reference

This project uses the [OpenWeatherMap Current Weather API](https://openweathermap.org/current) and [5 Day Forecast API](https://openweathermap.org/forecast5).
