// API endpoint — proxied through local PHP backend
const apiUrl = "/WEATHER-app/backend/main.php?city=";

// DOM elements
const searchBox = document.querySelector(".search input");
const searchBtn = document.querySelector(".search button");
const weatherIcon = document.querySelector(".weather-icon");

// weather condition icon map
const weatherIcons = {
    Clouds: "https://raw.githubusercontent.com/CarolCosta9/img/main/weather%20card%20img/clouds.png",
    Clear: "https://raw.githubusercontent.com/CarolCosta9/img/main/weather%20card%20img/clear.png",
    Drizzle: "https://raw.githubusercontent.com/CarolCosta9/img/main/weather%20card%20img/drizzle.png",
    Snow: "https://raw.githubusercontent.com/CarolCosta9/img/main/weather%20card%20img/snow.png",
    Rain: "https://raw.githubusercontent.com/CarolCosta9/img/main/weather%20card%20img/rain.png",
};

// fetch and display weather data for a given city
async function checkWeather(city) {
    try {
        const response = await fetch(apiUrl + city);
        const data = await response.json();

        if (data.error) {
            alert("No data found");
            return;
        }

        document.querySelector(".city").innerText = data.City;
        document.querySelector(".temp").innerText = data.temperature + "°C";
        document.querySelector(".weather-condition").innerText = data.description;
        document.querySelector(".humidity").innerText = data.humidity + "%";
        document.querySelector(".Wind").innerText = data.windSpeed + "km/h";
        document.querySelector(".pressure").innerText = data.Pressure + "hPa";
        document.querySelector(".time").innerText = new Date(data.date * 1000).toLocaleDateString("en-US", {
            weekday: "short",
            day: "numeric",
            month: "short",
            hour: "numeric",
        });

        weatherIcon.src = weatherIcons[data.main] ?? "";

    } catch (error) {
        alert("Something went wrong. Please try again.");
    }
}
