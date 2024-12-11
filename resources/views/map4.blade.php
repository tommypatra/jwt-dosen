<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Interaktif</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.css">
    <style>
        #map { height: 500px; }
        #directions-list {
            list-style: none;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
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

    <script>
        const map = L.map('map').setView([-3.982, 122.512], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19, attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let userLocation = null;
        let userMarker = null;
        let destinationMarker = null;
        let destination = null;

        const routingControl = L.Routing.control({
            waypoints: [],
            routeWhileDragging: true,
            createMarker: () => null,
            lineOptions: {
                styles: [{ color: 'blue', opacity: 0.8, weight: 4 }],
            },
        }).addTo(map);

        const displayRouteDetails = (routes) => {
            const directionsList = document.getElementById("directions-list");
            directionsList.innerHTML = "";
            if (routes && routes[0]) {
                const route = routes[0];
                const distance = (route.summary.totalDistance / 1000).toFixed(2);
                const duration = (route.summary.totalTime / 60).toFixed(1);
                
                directionsList.innerHTML += `<li><strong>Jarak:</strong> ${distance} km</li>`;
                directionsList.innerHTML += `<li><strong>Waktu Tempuh:</strong> ${duration} menit</li>`;
                route.instructions.forEach((instruction, index) => {
                    const listItem = document.createElement("li");
                    listItem.textContent = `${index + 1}. ${instruction.text}`;
                    directionsList.appendChild(listItem);
                });
            } else {
                directionsList.innerHTML = "Tidak ada rute yang tersedia.";
            }
        };

        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                (position) => {
                    const newLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };
                    
                    if (!userLocation || userLocation.lat !== newLocation.lat || userLocation.lng !== newLocation.lng) {
                        userLocation = newLocation;
                        
                        if (!userMarker) {
                            userMarker = L.marker([userLocation.lat, userLocation.lng]).addTo(map)
                                .bindPopup("Lokasi Anda").openPopup();
                        } else {
                            userMarker.setLatLng([userLocation.lat, userLocation.lng]);
                        }

                        if (destination) {
                            routingControl.setWaypoints([
                                L.latLng(userLocation.lat, userLocation.lng),
                                L.latLng(destination.lat, destination.lng)
                            ]);
                        }
                    }
                },
                (error) => alert("Gagal melacak lokasi: " + error.message),
                { enableHighAccuracy: true, maximumAge: 1000, timeout: 10000 }
            );
        } else {
            alert("Geolocation tidak didukung.");
        }

        map.on('click', (e) => {
            if (!userLocation) {
                alert("Lokasi pengguna belum tersedia.");
                return;
            }

            if (destinationMarker) map.removeLayer(destinationMarker);

            destination = e.latlng;
            destinationMarker = L.marker([destination.lat, destination.lng]).addTo(map)
                .bindPopup(`Tujuan: (${destination.lat.toFixed(5)}, ${destination.lng.toFixed(5)})`).openPopup();

            routingControl.setWaypoints([
                L.latLng(userLocation.lat, userLocation.lng),
                L.latLng(destination.lat, destination.lng)
            ]);
        });

        routingControl.on('routesfound', (e) => {
            displayRouteDetails(e.routes);
        });
    </script>
</body>
</html>
