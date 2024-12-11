<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaflet Routing Example</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        #map {
            height: 500px;
            width: 70%;
            float: left;
        }

        #directions {
            height: 500px;
            width: 30%;
            float: right;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    <div id="map"></div>
    <div id="directions">
        <h3>Petunjuk Arah</h3>
        <ul id="directions-list"></ul>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <script>
        // Inisialisasi peta
        const map = L.map('map').setView([-3.9916, 122.5120], 14); // Lokasi Kendari

        // Tambahkan tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // Tambahkan marker lokasi pengguna
        let userMarker;
        map.locate({ setView: true, maxZoom: 14 });

        map.on('locationfound', (e) => {
            if (!userMarker) {
                userMarker = L.marker(e.latlng).addTo(map).bindPopup('Lokasi Anda').openPopup();
            }
        });

        map.on('locationerror', () => {
            alert('Gagal menemukan lokasi Anda.');
        });

        // Inisialisasi kontrol routing tanpa panel bawaan
        const routingControl = L.Routing.control({
            waypoints: [],
            routeWhileDragging: true,
            createMarker: function (i, waypoint) {
                return L.marker(waypoint.latLng, {
                    draggable: false
                });
            },
            show: false, // Jangan tampilkan panel bawaan
        }).addTo(map);

        // Event klik pada peta untuk menetapkan tujuan
        map.on('click', (e) => {
            const destinationLatLng = e.latlng;
            if (userMarker) {
                routingControl.setWaypoints([
                    userMarker.getLatLng(), // Lokasi pengguna
                    destinationLatLng, // Lokasi tujuan
                ]);

                // Bersihkan daftar petunjuk arah sebelumnya
                const directionsList = document.getElementById('directions-list');
                directionsList.innerHTML = '';

                // Dapatkan petunjuk arah dan tampilkan di div
                routingControl.on('routesfound', function (e) {
                    const routes = e.routes;
                    const summary = routes[0].summary;
                    const instructions = routes[0].instructions;

                    // Tampilkan ringkasan (jarak dan waktu)
                    const summaryItem = document.createElement('li');
                    summaryItem.textContent = `${(summary.totalDistance / 1000).toFixed(1)} km, ${(summary.totalTime / 60).toFixed(0)} min`;
                    directionsList.appendChild(summaryItem);

                    // Tampilkan detail petunjuk arah
                    for (let i = 0; i < instructions.length; i++) {
                        const step = instructions[i];
                        const stepItem = document.createElement('li');
                        stepItem.textContent = step.text;
                        directionsList.appendChild(stepItem);
                    }
                });
            }
        });
    </script>
</body>
</html>
