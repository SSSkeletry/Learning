(function () {
  "use strict";

  var app = {
    isLoading: true,
    visibleCards: {},
    selectedCities: [],
    spinner: document.querySelector(".loader"),
    cardTemplate: document.querySelector(".cardTemplate"),
    container: document.querySelector(".main"),
    addDialog: document.querySelector(".dialog-container"),
    daysOfWeek: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
  };

  app.toggleAddDialog = function (visible) {
    if (visible) {
      app.addDialog.classList.add("dialog-container--visible");
    } else {
      app.addDialog.classList.remove("dialog-container--visible");
    }
  };

  app.saveSelectedCities = function () {
    var selectedCities = JSON.stringify(app.selectedCities);
    localStorage.selectedCities = selectedCities;
  };

  app.updateForecastCard = function (data) {
    var dataLastUpdated = new Date(data.created);
    var sunrise = data.channel.astronomy.sunrise;
    var sunset = data.channel.astronomy.sunset;
    var current = data.channel.item.condition;
    var humidity = data.channel.atmosphere.humidity;
    var wind = data.channel.wind;

    var card = app.visibleCards[data.key];
    if (!card) {
      card = app.cardTemplate.cloneNode(true);
      card.classList.remove("cardTemplate");
      card.querySelector(".location").textContent = data.label;
      card.removeAttribute("hidden");
      app.container.appendChild(card);
      app.visibleCards[data.key] = card;
    }

    var cardLastUpdatedElem = card.querySelector(".card-last-updated");
    var cardLastUpdated = cardLastUpdatedElem.textContent;
    if (cardLastUpdated) {
      cardLastUpdated = new Date(cardLastUpdated);
      if (dataLastUpdated.getTime() < cardLastUpdated.getTime()) {
        return;
      }
    }
    cardLastUpdatedElem.textContent = data.created;

    card.querySelector(".description").textContent = current.text;
    card.querySelector(".date").textContent = current.date;
    card
      .querySelector(".current .icon")
      .classList.add(app.getIconClass(current.code));
    card.querySelector(".current .temperature .value").textContent = Math.round(
      current.temp
    );
    card.querySelector(".current .sunrise").textContent = sunrise;
    card.querySelector(".current .sunset").textContent = sunset;
    card.querySelector(".current .humidity").textContent =
      Math.round(humidity) + "%";
    card.querySelector(".current .wind .value").textContent = Math.round(
      wind.speed
    );
    card.querySelector(".current .wind .direction").textContent =
      wind.direction;

    var nextDays = card.querySelectorAll(".future .oneday");
    var today = new Date().getDay();
    for (var i = 0; i < 7; i++) {
      var nextDay = nextDays[i];
      var daily = data.channel.item.forecast[i];
      if (daily && nextDay) {
        nextDay.querySelector(".date").textContent =
          app.daysOfWeek[(i + today) % 7];
        nextDay
          .querySelector(".icon")
          .classList.add(app.getIconClass(daily.code));
        nextDay.querySelector(".temp-high .value").textContent = Math.round(
          daily.high
        );
        nextDay.querySelector(".temp-low .value").textContent = Math.round(
          daily.low
        );
      }
    }

    if (app.isLoading) {
      app.spinner.setAttribute("hidden", true);
      app.container.removeAttribute("hidden");
      app.isLoading = false;
    }
  };

  app.getForecast = function (cityName, label) {
    const apiKey = "ab1f87661a872c262e36356b8df5e2fe";
    const url = `https://api.openweathermap.org/data/2.5/weather?q=${encodeURIComponent(
      cityName
    )}&units=metric&appid=${apiKey}`;

    fetch(url)
      .then((response) => {
        if (!response.ok) throw new Error("Network response was not ok.");
        return response.json();
      })
      .then((data) => {
        const forecast = {
          key: cityName.toLowerCase(),
          label: label,
          created: new Date().toISOString(),
          channel: {
            astronomy: {
              sunrise: new Date(data.sys.sunrise * 1000).toLocaleTimeString(),
              sunset: new Date(data.sys.sunset * 1000).toLocaleTimeString(),
            },
            item: {
              condition: {
                text: data.weather[0].main,
                date: new Date().toString(),
                temp: data.main.temp,
                code: data.weather[0].id,
              },
              forecast: Array(7)
                .fill()
                .map(() => ({
                  code: data.weather[0].id,
                  high: data.main.temp_max,
                  low: data.main.temp_min,
                })),
            },
            atmosphere: {
              humidity: data.main.humidity,
            },
            wind: {
              speed: data.wind.speed,
              direction: data.wind.deg,
            },
          },
        };

        app.updateForecastCard(forecast);
      })
      .catch((error) => {
        app.updateForecastCard(initialWeatherForecast);
      });
  };

  app.updateForecasts = function () {
    var keys = Object.keys(app.visibleCards);
    keys.forEach(function (key) {
      app.selectedCities.forEach(function (city) {
        app.getForecast(city.key, city.label);
      });
    });
  };

  app.getIconClass = function (weatherCode) {
    if (weatherCode >= 200 && weatherCode < 300) return "thunderstorms";
    if (weatherCode >= 300 && weatherCode < 600) return "rain";
    if (weatherCode >= 600 && weatherCode < 700) return "snow";
    if (weatherCode >= 700 && weatherCode < 800) return "fog";
    if (weatherCode === 800) return "clear-day";
    if (weatherCode > 800) return "cloudy";
    return "cloudy";
  };

  document.getElementById("butRefresh").addEventListener("click", function () {
    app.updateForecasts();
  });

  document.getElementById("butAdd").addEventListener("click", function () {
    app.toggleAddDialog(true);
  });

  document.getElementById("butAddCity").addEventListener("click", function () {
    var select = document.getElementById("selectCityToAdd");
    var selected = select.options[select.selectedIndex];
    var key = selected.textContent;
    var label = selected.textContent;

    if (!app.selectedCities) {
      app.selectedCities = [];
    }

    app.selectedCities.push({ key: key, label: label });
    app.saveSelectedCities();
    app.getForecast(key, label);
    app.toggleAddDialog(false);
  });

  document
    .getElementById("butAddCancel")
    .addEventListener("click", function () {
      app.toggleAddDialog(false);
    });

  var initialWeatherForecast = {
    key: "new york",
    label: "New York, NY",
    created: new Date().toISOString(),
    channel: {
      astronomy: {
        sunrise: "6:00 AM",
        sunset: "8:00 PM",
      },
      item: {
        condition: {
          text: "Clear",
          date: new Date().toString(),
          temp: 25,
          code: 800,
        },
        forecast: [
          { code: 800, high: 28, low: 18 },
          { code: 801, high: 26, low: 17 },
          { code: 802, high: 24, low: 16 },
          { code: 803, high: 23, low: 15 },
          { code: 804, high: 22, low: 14 },
          { code: 500, high: 20, low: 13 },
          { code: 501, high: 19, low: 12 },
        ],
      },
      atmosphere: {
        humidity: 50,
      },
      wind: {
        speed: 5,
        direction: 90,
      },
    },
  };

  app.updateForecastCard(initialWeatherForecast);

  app.selectedCities = localStorage.selectedCities;
  if (app.selectedCities) {
    app.selectedCities = JSON.parse(app.selectedCities);
    app.selectedCities.forEach(function (city) {
      app.getForecast(city.key, city.label);
    });
  } else {
    app.updateForecastCard(initialWeatherForecast);
    app.selectedCities = [
      { key: initialWeatherForecast.key, label: initialWeatherForecast.label },
    ];
    app.saveSelectedCities();
  }

  if ("serviceWorker" in navigator) {
    navigator.serviceWorker.register("./service-worker.js").then(function () {
      console.log("Service Worker Registered");
    });
  }
})();
