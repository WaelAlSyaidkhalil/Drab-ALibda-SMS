<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firebase Notifications</title>
    <!-- تضمين مكتبات Firebase بصيغة متوافقة -->
    <script src="https://www.gstatic.com/firebasejs/9.15.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.15.0/firebase-messaging-compat.js"></script>

</head>

<body>

    <h1>اختبار إشعارات Firebase</h1>
    <button id="subscribe">الحصول على التوكن</button>
    <p id="token"></p>

    <script>
        // إعدادات Firebase الخاصة بك
        // For Firebase JS SDK v7.20.0 and later, measurementId is optional
        // For Firebase JS SDK v7.20.0 and later, measurementId is optional
        const firebaseConfig = {
            apiKey: "AIzaSyAaBH5hcUBRXwnz99_LFi3ZFJuW8A-9lmQ",
            authDomain: "darb-alibda-sms.firebaseapp.com",
            projectId: "darb-alibda-sms",
            storageBucket: "darb-alibda-sms.firebasestorage.app",
            messagingSenderId: "342960145855",
            appId: "1:342960145855:web:0998e94b4c753140010d0a",
            measurementId: "G-Z91FCX6GXD"
        };


        // تهيئة Firebase
        const app = firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        // طلب الإذن والحصول على التوكن
        document.getElementById('subscribe').addEventListener('click', async () => {
            try {
                // طلب الإذن من المتصفح
                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    // الحصول على التوكن
                    const token = await messaging.getToken();
                    console.log("Token:", token);
                    document.getElementById('token').textContent = `توكن المستخدم: ${token}`;
                } else {
                    console.log("تم رفض الإذن بالإشعارات.");
                }
            } catch (error) {
                console.error("Error getting token:", error);
            }
        });

        // استقبال الإشعارات
        messaging.onMessage((payload) => {
            console.log("Message received:", payload);
            alert(`إشعار جديد: ${payload.notification.title}\n${payload.notification.body}`);
        });
    </script>
</body>

</html>