const CACHE_NAME = 'createcms-v1.0.0';
const STATIC_CACHE_URLS = [
    '/',
    '/CreateCMS/',
    '/CreateCMS/index.php',
    '/CreateCMS/?v=Home',
    '/CreateCMS/?v=Tasks',
    '/CreateCMS/?v=Projects',
    '/CreateCMS/?v=Leads',
    '/CreateCMS/?v=Employees',
    '/CreateCMS/templates/styles.php',
    '/CreateCMS/templates/js.php',
    '/CreateCMS/img/logo.png',
    '/CreateCMS/manifest.json',
    
    // Bootstrap CSS and JS from CDN
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css',
    
    // Fonts
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap'
];

const DYNAMIC_CACHE_URLS = [
    '/CreateCMS/requests/',
    '/CreateCMS/views/',
    '/CreateCMS/admin/includes/'
];

// Install event - cache static resources
self.addEventListener('install', event => {
    console.log('Service Worker: Installing...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Service Worker: Caching files');
                return cache.addAll(STATIC_CACHE_URLS);
            })
            .then(() => {
                console.log('Service Worker: Installed');
                return self.skipWaiting();
            })
            .catch(error => {
                console.error('Service Worker: Cache failed', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    console.log('Service Worker: Activating...');
    
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        if (cacheName !== CACHE_NAME) {
                            console.log('Service Worker: Deleting old cache', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('Service Worker: Activated');
                return self.clients.claim();
            })
    );
});

// Fetch event - serve cached content when offline
self.addEventListener('fetch', event => {
    const request = event.request;
    const url = new URL(request.url);
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    // Skip Chrome extension requests
    if (url.protocol === 'chrome-extension:') {
        return;
    }
    
    event.respondWith(
        caches.match(request)
            .then(cachedResponse => {
                // Return cached version if available
                if (cachedResponse) {
                    console.log('Service Worker: Serving cached resource', request.url);
                    return cachedResponse;
                }
                
                // Fetch from network
                return fetch(request)
                    .then(response => {
                        // Check if response is valid
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }
                        
                        // Clone the response
                        const responseToCache = response.clone();
                        
                        // Cache dynamic content
                        if (isDynamicContent(request.url)) {
                            caches.open(CACHE_NAME)
                                .then(cache => {
                                    console.log('Service Worker: Caching dynamic resource', request.url);
                                    cache.put(request, responseToCache);
                                })
                                .catch(error => {
                                    console.error('Service Worker: Dynamic caching failed', error);
                                });
                        }
                        
                        return response;
                    })
                    .catch(error => {
                        console.error('Service Worker: Fetch failed', error);
                        
                        // Return offline page for navigation requests
                        if (request.destination === 'document') {
                            return caches.match('/CreateCMS/offline.html') || 
                                   caches.match('/CreateCMS/') ||
                                   new Response('<h1>Offline</h1><p>Please check your internet connection.</p>', {
                                       headers: { 'Content-Type': 'text/html' }
                                   });
                        }
                        
                        // Return a generic offline response for other requests
                        return new Response('Offline', { status: 503 });
                    });
            })
    );
});

// Check if URL should be cached dynamically
function isDynamicContent(url) {
    return DYNAMIC_CACHE_URLS.some(pattern => url.includes(pattern)) ||
           url.includes('CreateCMS/views/') ||
           url.includes('CreateCMS/?v=') ||
           url.includes('.css') ||
           url.includes('.js') ||
           url.includes('.jpg') ||
           url.includes('.png') ||
           url.includes('.svg');
}

// Background sync for offline actions
self.addEventListener('sync', event => {
    console.log('Service Worker: Background sync triggered', event.tag);
    
    if (event.tag === 'background-sync') {
        event.waitUntil(
            // Process any pending offline actions here
            processOfflineActions()
        );
    }
});

// Push notifications
self.addEventListener('push', event => {
    console.log('Service Worker: Push received');
    
    const options = {
        body: event.data ? event.data.text() : 'New notification',
        icon: '/CreateCMS/img/logo.png',
        badge: '/CreateCMS/img/logo.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: '1'
        },
        actions: [
            {
                action: 'explore',
                title: 'Open CreateCMS',
                icon: '/CreateCMS/img/logo.png'
            },
            {
                action: 'close',
                title: 'Close',
                icon: '/CreateCMS/img/logo.png'
            }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification('CreateCMS', options)
    );
});

// Notification click handler
self.addEventListener('notificationclick', event => {
    console.log('Service Worker: Notification clicked');
    
    event.notification.close();
    
    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/CreateCMS/')
        );
    }
});

// Message handler for communication with main thread
self.addEventListener('message', event => {
    console.log('Service Worker: Message received', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: CACHE_NAME });
    }
});

// Utility function to process offline actions
async function processOfflineActions() {
    try {
        // Get stored offline actions from IndexedDB or localStorage
        const offlineActions = JSON.parse(localStorage.getItem('offlineActions') || '[]');
        
        for (const action of offlineActions) {
            try {
                // Process each offline action
                await fetch(action.url, {
                    method: action.method,
                    headers: action.headers,
                    body: action.body
                });
                
                console.log('Service Worker: Offline action processed', action);
            } catch (error) {
                console.error('Service Worker: Failed to process offline action', error);
            }
        }
        
        // Clear processed actions
        localStorage.removeItem('offlineActions');
        
    } catch (error) {
        console.error('Service Worker: Error processing offline actions', error);
    }
}

// Cache update notification
self.addEventListener('message', event => {
    if (event.data.action === 'cacheUpdate') {
        event.waitUntil(
            caches.open(CACHE_NAME)
                .then(cache => {
                    return cache.addAll(STATIC_CACHE_URLS);
                })
                .then(() => {
                    console.log('Service Worker: Cache updated');
                    // Notify all clients that cache has been updated
                    return self.clients.matchAll();
                })
                .then(clients => {
                    clients.forEach(client => {
                        client.postMessage({
                            type: 'CACHE_UPDATED',
                            message: 'Cache has been updated'
                        });
                    });
                })
        );
    }
});

console.log('Service Worker: Script loaded');
