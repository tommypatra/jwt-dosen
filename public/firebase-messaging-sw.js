// Import Firebase scripts
importScripts('https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/11.0.2/firebase-messaging.js');

// Inisialisasi Firebase
const firebaseConfig = {
    apiKey: "AIzaSyAVC_W-s_VNUcJIcIfYoJnxY1IWF0PFlG0",
    authDomain: "notif-permanen.firebaseapp.com",
    projectId: "notif-permanen",
    storageBucket: "notif-permanen.firebasestorage.app",
    messagingSenderId: "930905397025",
    appId: "1:930905397025:web:f0c6b1effddd3dce4b5060"
  };
  firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

// Menangani pesan foreground
messaging.onMessage((payload) => {
    console.log("Message received. ", payload);
    // Menampilkan notifikasi atau melakukan aksi lainnya
});
