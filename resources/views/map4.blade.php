<script>
    // Inisialisasi peta
    const map = L.map('map').setView([-3.982, 122.512], 14);

    // Tambahkan tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Variabel untuk menyimpan lokasi pengguna dan marker-nya
    let userLocation = null;
    let userMarker = null;

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

                    // Pusatkan peta ke lokasi pengguna
                    map.setView([userLocation.lat, userLocation.lng], 14);

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

        destination = e.latlng; // Lokasi yang diklik

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
