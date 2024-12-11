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
            margin-top: 10px;
            border: 1px solid #ccc;
            padding: 10px;
            max-height: 200px;
            overflow-y: auto;
        }
        #directions-list li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div id="map"></div>
    <div id="directions-list">Detail rute akan muncul di sini...</div>

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

        // Variabel untuk menyimpan lokasi pengguna, marker, dan marker tujuan
        let userLocation = null;
        let userMarker = null;
        let destinationMarker = null;

        // Routing Control
        const routingControl = L.Routing.control({
            waypoints: [],
            routeWhileDragging: true,
            createMarker: function() {
                return null; // Jangan buat marker untuk waypoint
            },
            addWaypoints: false,
            lineOptions: {
                styles: [{ color: 'blue', opacity: 0.8, weight: 4 }], // Gaya garis
            },
            show: false,
        }).addTo(map);

        // Variabel untuk menyimpan lokasi tujuan
        let destination = null;

        // Fungsi untuk menampilkan detail rute ke dalam div
        const displayRouteDetails = (routes) => {
            const directionsList = document.getElementById("directions-list");
            directionsList.innerHTML = ""; // Kosongkan detail sebelumnya

            if (routes && routes[0]) {
                routes[0].instructions.forEach((instruction, index) => {
                    const listItem = document.createElement("li");
                    listItem.textContent = `${index + 1}. ${instruction.text}`;
                    directionsList.appendChild(listItem);
                });
            } else {
                directionsList.innerHTML = "Tidak ada rute yang tersedia.";
            }
        };

        // Geolocation untuk melacak posisi secara real-time
        if (navigator.geolocation) {
            const watchId = navigator.geolocation.watchPosition(
                (position) => {
                    const newLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };

                    // Jika lokasi pengguna berubah
                    if (!userLocation || (userLocation.lat !== newLocation.lat || userLocation.lng !== newLocation.lng)) {
                        userLocation = newLocation;

                        // Tambahkan/Perbarui marker pengguna
                        if (!userMarker) {
                            userMarker = L.marker([userLocation.lat, userLocation.lng]).addTo(map)
                                .bindPopup("Lokasi Anda")
                                .openPopup();
                        } else {
                            userMarker.setLatLng([userLocation.lat, userLocation.lng]);
                        }

                        // Jika ada tujuan, perbarui rute
                        if (destination) {
                            routingControl.setWaypoints([
                                L.latLng(userLocation.lat, userLocation.lng), // Lokasi pengguna
                                L.latLng(destination.lat, destination.lng)   // Lokasi tujuan
                            ]);
                        }
                    }
                },
                (error) => {
                    alert("Gagal melacak lokasi pengguna: " + error.message);
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 1000, // Cache lokasi lama dalam 1 detik
                    timeout: 10000,   // Timeout untuk geolokasi
                }
            );

            // Untuk berhenti melacak jika diperlukan
            // navigator.geolocation.clearWatch(watchId);
        } else {
            alert("Geolocation tidak didukung oleh browser Anda.");
        }

        // Event untuk menangkap klik pada peta
        map.on('click', function(e) {
            if (!userLocation) {
                alert("Lokasi pengguna belum tersedia. Mohon izinkan akses lokasi.");
                return;
            }

            // Hapus marker lama jika ada
            if (destinationMarker) {
                map.removeLayer(destinationMarker);
            }

            // Set lokasi tujuan baru
            destination = e.latlng;
            destinationMarker = L.marker([destination.lat, destination.lng]).addTo(map)
                .bindPopup(`Tujuan: (${destination.lat.toFixed(5)}, ${destination.lng.toFixed(5)})`)
                .openPopup();

            // Perbarui rute
            routingControl.setWaypoints([
                L.latLng(userLocation.lat, userLocation.lng), // Lokasi pengguna
                L.latLng(destination.lat, destination.lng)   // Lokasi tujuan
            ]);

            // Update detail rute
            routingControl.on('routesfound', function(e) {
                displayRouteDetails(e.routes);
            });
        });
    </script>

</body>
</html>
