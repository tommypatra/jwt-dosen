<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Leaflet Routing</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <!-- Tambahkan plugin Leaflet Routing Machine -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.min.js"></script>
  </head>
  <body>
    <div id="map" style="width: 100%; height: 500px"></div>

    <script>
      // Koordinat tujuan (ubah sesuai kebutuhan)
      const destinationLat = -3.9798; // Latitude tujuan
      const destinationLng = 122.5129; // Longitude tujuan

      // Inisialisasi peta
      const map = L.map("map").setView([destinationLat, destinationLng], 14);

      // Tambahkan tile layer (peta dasar)
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: "© OpenStreetMap contributors",
      }).addTo(map);

      // Tambahkan marker untuk lokasi tujuan
      L.marker([destinationLat, destinationLng])
        .addTo(map)
        .bindPopup("Tujuan Anda")
        .openPopup();

      // Gunakan geolokasi untuk mendapatkan lokasi saat ini
      map.locate({ setView: true, maxZoom: 16 });

      // Event ketika lokasi ditemukan
      map.on("locationfound", function (e) {
        const userLatLng = e.latlng; // Lokasi pengguna

        // Tambahkan marker untuk lokasi saat ini
        L.marker([userLatLng.lat, userLatLng.lng])
          .addTo(map)
          .bindPopup("Lokasi Anda")
          .openPopup();

        // Tambahkan routing dari lokasi saat ini ke tujuan
        L.Routing.control({
          waypoints: [
            L.latLng(userLatLng.lat, userLatLng.lng), // Lokasi saat ini
            L.latLng(destinationLat, destinationLng), // Lokasi tujuan
          ],
          routeWhileDragging: true,
        }).addTo(map);
      });

      // Event jika lokasi tidak ditemukan
      map.on("locationerror", function (e) {
        alert("Lokasi tidak ditemukan. Pastikan GPS Anda aktif.");
      });
    </script>
  </body>
</html>
