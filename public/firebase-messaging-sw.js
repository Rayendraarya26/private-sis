self.addEventListener('push', function (event) {
    console.log(event)
    var a = event.data.json();
    const title = a.data.title;
    const options = {
        body: a.data.body,
        icon: 'https://pbs.twimg.com/profile_images/773381748717748224/p_-XcAJr.jpg',
        badge: 'https://pbs.twimg.com/profile_images/773381748717748224/p_-XcAJr.jpg',
        data: {
            url: a.data.url
        }
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
    if (event.notification.data != null) {
        event.notification.close();
        clients.openWindow(event.notification.data.url);
    }
});
