var dataCacheName = "weatherData-v1";
var cacheName = "weatherPWA-final-1";
var filesToCache = [
  "./",
  "./index.html",
  "./scripts/app.js",
  "./styles/inline.css",
  "./images/clear.png",
  "./images/cloudy-scattered-showers.png",
  "./images/cloudy.png",
  "./images/fog.png",
  "./images/ic_add_white_24px.svg",
  "./images/ic_refresh_white_24px.svg",
  "./images/partly-cloudy.png",
  "./images/rain.png",
  "./images/scattered-showers.png",
  "./images/sleet.png",
  "./images/snow.png",
  "./images/thunderstorm.png",
  "./images/wind.png",
];

self.addEventListener("install", function (e) {
  console.log("[ServiceWorker] Install");
  e.waitUntil(
    caches.open(cacheName).then(function (cache) {
      console.log("[ServiceWorker] Caching app shell");
      return cache.addAll(filesToCache);
    })
  );
});

self.addEventListener("activate", function (e) {
  console.log("[ServiceWorker] Activate");
  e.waitUntil(
    caches.keys().then(function (keyList) {
      return Promise.all(
        keyList.map(function (key) {
          if (key !== cacheName && key !== dataCacheName) {
            console.log("[ServiceWorker] Removing old cache", key);
            return caches.delete(key);
          }
        })
      );
    })
  );
  return self.clients.claim();
});

self.addEventListener("fetch", function (e) {
  console.log("[Service Worker] Fetch", e.request.url);

  var dataUrl = "https://api.openweathermap.org/data/2.5/weather";

  if (e.request.url.indexOf(dataUrl) > -1) {
    e.respondWith(
      caches.open(dataCacheName).then(function (cache) {
        return fetch(e.request)
          .then(function (response) {
            cache.put(e.request.url, response.clone());
            return response;
          })
          .catch(function () {
            return caches.match(e.request);
          });
      })
    );
  } else {
    e.respondWith(
      caches.match(e.request).then(function (response) {
        return response || fetch(e.request);
      })
    );
  }
});
