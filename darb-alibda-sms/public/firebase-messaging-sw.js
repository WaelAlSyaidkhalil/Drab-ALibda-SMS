// استخدم نسخة compat التي تدعم importScripts
importScripts(
    "https://www.gstatic.com/firebasejs/9.15.0/firebase-app-compat.js",
);
importScripts(
    "https://www.gstatic.com/firebasejs/9.15.0/firebase-messaging-compat.js",
);

firebase.initializeApp({
    apiKey: "AIzaSyAaBH5hcUBRXwnz99_LFi3ZFJuW8A-9lmQ",
    authDomain: "darb-alibda-sms.firebaseapp.com",
    projectId: "darb-alibda-sms",
    storageBucket: "darb-alibda-sms.firebasestorage.app",
    messagingSenderId: "342960145855",
    appId: "1:342960145855:web:0998e94b4c753140010d0a",
    measurementId: "G-Z91FCX6GXD",
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
    console.log(
        "[firebase-messaging-sw.js] Received background message ",
        payload,
    );
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: "/icon.png", // ضع مسار أيقونة مناسبة إن أردت
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
self.addEventListener("push", function (event) {
    const data = event.data.json();

    const notificationTitle = data.notification.title;
    const notificationOptions = {
        body: data.notification.body,
        icon: "/icons/icon-192x192.png", // عدل المسار حسب وجود الأيقونة عندك
        data: data.data, // أي بيانات إضافية
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
