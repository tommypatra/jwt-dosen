<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Interaktif</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.css">
    <style>
        #map {
            height: 500px;
        }
        #directions-list {
            list-style: none;
            padding: 0;
        }
        #directions-list li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div id="map"></div>
    <ul id="directions-list"></ul>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.min.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script>
        // Inisialisasi peta
        const map = L.map('map').setView([-3.982, 122.512], 14);

        // Tambahkan tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Variabel untuk menyimpan lokasi pengguna
        let userLocation = null;

        // Gunakan Geolocation API untuk mendapatkan lokasi pengguna
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                // Tambahkan marker lokasi pengguna ke peta
                L.marker([userLocation.lat, userLocation.lng]).addTo(map)
                    .bindPopup("Lokasi Anda")
                    .openPopup();
                map.setView([userLocation.lat, userLocation.lng], 14);
            }, (error) => {
                alert("Gagal mendapatkan lokasi pengguna: " + error.message);
            });
        } else {
            alert("Geolocation tidak didukung oleh browser Anda.");
        }

        // Routing Control
        const routingControl = L.Routing.control({
            waypoints: [],
            routeWhileDragging: true,
            createMarker: function() {
                return null; // Jangan buat marker untuk waypoint
            },
            addWaypoints: false, // Jangan tambahkan waypoint secara manual
            lineOptions: {
                styles: [{ color: 'blue', opacity: 0.8, weight: 4 }], // Gaya garis
            },
            show: false, // Nonaktifkan panel bawaan
        }).addTo(map);

        // Event ketika rute ditemukan
        routingControl.on('routesfound', function (e) {
            const directionsList = document.getElementById('directions-list');
            
            // Hapus rute lama
            directionsList.innerHTML = '';

            const routes = e.routes[0];
            const summary = routes.summary;

            // Tambahkan ringkasan rute
            const summaryItem = document.createElement('li');
            summaryItem.innerHTML = `<strong>${(summary.totalDistance / 1000).toFixed(1)} km, ${(summary.totalTime / 60).toFixed(0)} min</strong>`;
            directionsList.appendChild(summaryItem);

            // Tambahkan langkah-langkah rute
            routes.instructions.forEach((step) => {
                const stepItem = document.createElement('li');
                stepItem.textContent = step.text;
                directionsList.appendChild(stepItem);
            });
        });

        // Event untuk menangkap klik pada peta
        map.on('click', function(e) {
            if (!userLocation) {
                alert("Lokasi pengguna belum tersedia. Mohon izinkan akses lokasi.");
                return;
            }

            const destination = e.latlng; // Lokasi yang diklik
            routingControl.setWaypoints([
                L.latLng(userLocation.lat, userLocation.lng), // Lokasi pengguna
                L.latLng(destination.lat, destination.lng)   // Lokasi tujuan
            ]);

            // Tambahkan marker pada lokasi tujuan
            L.marker([destination.lat, destination.lng]).addTo(map)
                .bindPopup(`Tujuan: (${destination.lat.toFixed(5)}, ${destination.lng.toFixed(5)})`)
                .openPopup();
        });
    </script>
</body>
</html>
