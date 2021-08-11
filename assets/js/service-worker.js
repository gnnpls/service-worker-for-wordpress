var version = 'v1.1.0:';
var cache_size = 40;
var theme_path = ''; //'//wp-content/themes/justmarkup.com/';


var offlineFundamentals = [
    'web-assets/uploads/2021/03/art-gallery-corfu-logo.png',


    /* 'fonts/pt-serif-v11-latin-regular.woff',
    'fonts/pt-serif-v11-latin-regular.woff2',
    'fonts/pt-serif-v11-latin-italic.woff',
    'fonts/pt-serif-v11-latin-italic.woff2',
    'fonts/pt-serif-v11-latin-700.woff',
    'fonts/pt-serif-v11-latin-700.woff2',
    'fonts/pt-serif-v11-latin-700italic.woff',
    'fonts/pt-serif-v11-latin-700italic.woff2'*/
];

//Add core website files to cache during serviceworker installation
var updateStaticCache = function() {
    return caches.open(version + 'fundamentals').then(function(cache) {
        return Promise.all(offlineFundamentals.map(function(value) {
            var request = new Request(value);
            var url = new URL(request.url);
            if (url.origin != location.origin) {
                request = new Request(value, { mode: 'no-cors' });
            }
            return fetch(request).then(function(response) {
                var cachedCopy = response.clone();
                return cache.put(request, cachedCopy);

            });
        }))
    })
};

//Clear caches with a different version number
var clearOldCaches = function() {

    return caches.keys().then(function(keys) {
        return Promise.all(
            keys
            .filter(function(key) {
                return key.indexOf(version) != 0;
            })
            .map(function(key) {
                return caches.delete(key);
            })
        );
    })
}

/*
 limits the cache
 If cache has more than maxItems then it removes the first item in the cache
*/
var limitCache = function(cache, maxItems) {
    cache.keys().then(function(items) {
        if (items.length > maxItems) {
            cache.delete(items[0]);
        }
    })
}


/*
 trims the cache
 If cache has more than maxItems then it removes the excess items starting from the beginning
*/
var trimCache = function(cacheName, maxItems) {
    caches.open(cacheName)
        .then(function(cache) {
            cache.keys()
                .then(function(keys) {
                    if (keys.length > maxItems) {
                        cache.delete(keys[0])
                            .then(trimCache(cacheName, maxItems));
                    }
                });
        });
};


//When the service worker is first added to a computer
self.addEventListener("install", function(event) {
    event.waitUntil(updateStaticCache()
        .then(function() {
            return self.skipWaiting();
        })
    );
})

self.addEventListener("message", function(event) {
    var data = event.data;

    //Send this command whenever many files are downloaded (ex: a page load)
    if (data.command == "trimCache") {
        trimCache(version + "pages", cache_size);
        trimCache(version + "images", cache_size);
        trimCache(version + "assets", cache_size);
    }

    /*let jsonData = JSON.parse(event.data); // parse the message back to JSON
    if (jsonData.action == "precache") { // check the action
        self.toolbox.precache([jsonData.url]); // here you can use sw-toolbox or anything to cache your stuff.
    }*/



});

//Service worker handles networking
self.addEventListener("fetch", function(event) {

    //Fetch from network and cache
    var fetchFromNetwork = function(response) {
        var cacheCopy = response.clone();
        if (!(event.request.url.indexOf('http') === 0)) return;
        if (event.request.headers.get('Accept').indexOf('text/html') != -1) {
            caches.open(version + 'pages').then(function(cache) {
                cache.put(event.request, cacheCopy).then(function() {
                    limitCache(cache, cache_size);
                })
            });
        } else if (event.request.headers.get('Accept').indexOf('image') != -1) {
            caches.open(version + 'images').then(function(cache) {
                cache.put(event.request, cacheCopy).then(function() {
                    limitCache(cache, cache_size);
                });
            });
        } else {
            caches.open(version + 'assets').then(function add(cache) {
                cache.put(event.request, cacheCopy);
            });
        }

        return response;
    }

    //Fetch from network failed
    var fallback = function() {
        if (event.request.headers.get('Accept').indexOf('text/html') != -1) {
            return caches.match(event.request).then(function(response) {
                return response || caches.match(theme_path + 'offline.html');
            })
        }
    }

    //This service worker won't touch the admin area and preview pages
    if (event.request.url.match(/wp-admin/) || event.request.url.match(/preview=true/)) {
        return;
    }

    //This service worker won't touch non-get requests
    if (event.request.method != 'GET') {
        return;
    }

    //For HTML requests, look for file in network, then cache if network fails.
    if (event.request.headers.get('Accept').indexOf('text/html') != -1) {
        event.respondWith(fetch(event.request).then(fetchFromNetwork, fallback));
        return;
    }

    //For non-HTML requests, look for file in cache, then network if no cache exists.
    event.respondWith(
        caches.match(event.request).then(function(cached) {
            return cached || fetch(event.request).then(fetchFromNetwork, fallback);
        })
    )
});

//After the install event
self.addEventListener("activate", function(event) {


    event.waitUntil(clearOldCaches()
        .then(function() {
            return self.clients.claim();
        })
    );
});