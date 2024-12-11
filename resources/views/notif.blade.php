<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FCM Push Notification</title>
  </head>
  <body>
    <h1>Firebase Cloud Messaging (FCM) Push Notification</h1>

    <button id="subscribeBtn">Subscribe to Notifications</button>

    <script type="module">
        // Import the functions you need from the SDKs you need
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js";
        import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-messaging.js";

        // Konfigurasi Firebase (gunakan yang dari Firebase Console)
        const firebaseConfig = {
            apiKey: "AIzaSyAVC_W-s_VNUcJIcIfYoJnxY1IWF0PFlG0",
            authDomain: "notif-permanen.firebaseapp.com",
            projectId: "notif-permanen",
            storageBucket: "notif-permanen.firebasestorage.app",
            messagingSenderId: "930905397025",
            appId: "1:930905397025:web:f0c6b1effddd3dce4b5060"
        };
        // Inisialisasi Firebase
        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        const YOUR_VAPID_KEY = "BAED2GzvQtv6PC2kZe3ACIg6FeVGaTz7XCchXjc8tQfHpvSB52c8ywR6FQ8M-XiBHtNfzRM0Bpvvt2k7qvS_qRY";

        // Meminta izin untuk menerima notifikasi
        async function requestPermission() {
            try {
                const permission = await Notification.requestPermission();
                if (permission === "granted") {
                    console.log("Notification permission granted.");
                    const token = await getToken(messaging, { vapidKey: YOUR_VAPID_KEY });
                    console.log("FCM Token:", token);
                    sendTokenToServer(token);
                } else {
                    if (permission === "denied") {
                        console.log("User denied notifications.");
                    } else if (permission === "default") {
                        console.log("User dismissed notification request.");
                    }
                }
                } catch (error) {
                console.error("Error getting permission for notifications:", error);
}

        }

        // Fungsi untuk mengirim token ke server
        function sendTokenToServer(token) {
            fetch("http://localhost:5000/save-token", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ token: token }),
            })
            .then((response) => response.json())
            .then((data) => console.log("Token sent to server:", data))
            .catch((error) =>
                console.error("Error sending token to server:", error)
            );
        }

        // Daftarkan pengguna untuk menerima notifikasi saat mereka klik tombol
        document
            .getElementById("subscribeBtn")
            .addEventListener("click", requestPermission);
    </script>
  </body>
</html>
